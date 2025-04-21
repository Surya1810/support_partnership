<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IzinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $my_izin = Izin::where('user_id', Auth::id())->get();
        $sisaCuti = 12 - Izin::where('user_id', Auth::id())
            ->where('jenis', 'Cuti Tahunan')
            ->where('status', 'approved')
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('lama');

        return view('izin.index', compact('my_izin', 'sisaCuti'));
    }

    public function approval()
    {
        $manager = User::where('role_id', 3)->pluck('id');

        if (Auth::user()->role_id == 1 || Auth::user()->department_id == 8) {
            $pending = Izin::where('status', 'pending')->get();
        } elseif (Auth::user()->role_id == 2) {
            $pending = Izin::where('status', 'pending')->whereIn('user_id', $manager)->get();
        } else {
            $pending = Izin::where('status', 'pending')->whereNotIn('user_id', $manager)->get();
        }
        $all = Izin::all();

        return view('izin.approval', compact('pending', 'all'));
    }

    public function approve($id)
    {
        $izin = Izin::findOrFail($id);

        // Pastikan pengajuan belum disetujui
        if ($izin->status === 'approved') {
            return redirect()->back()->with(['pesan' => 'Izin sudah disetujui sebelumnya', 'level-alert' => 'alert-warning']);
        }

        $izin->is_approved = true;
        $izin->status = 'approved';
        $izin->save();

        return redirect()->route('izin.approval')->with(['pesan' => 'Izin disetujui', 'level-alert' => 'alert-success']);
    }

    public function reject(Request $request, $id)
    {
        $izin = Izin::findOrFail($id);

        // Pastikan pengajuan belum disetujui
        if ($izin->status === 'reject') {
            return redirect()->back()->with(['pesan' => 'Izin sudah ditolak sebelumnya', 'level-alert' => 'alert-warning']);
        }

        $izin->is_approved = false;
        $izin->status = 'rejected';
        $izin->catatan = $request->input('reason');
        $izin->save();

        return redirect()->route('izin.approval')->with(['pesan' => 'Izin ditolak', 'level-alert' => 'alert-success']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required',
            'tanggal' => 'required',
            'keterangan' => 'required|string|max:255',
        ]);

        $izin = new Izin();
        $izin->user_id = Auth::user()->id;
        $izin->jenis = $request->jenis;
        $izin->tanggal = $request->tanggal;
        $izin->jam = $request->jam;
        $izin->lama = $request->lama;
        $izin->keterangan = $request->keterangan;
        $izin->save();

        return redirect()->route('izin.index')->with(['pesan' => 'Izin sedang diproses', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Izin $izin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Izin $izin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Izin $izin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Izin $izin)
    {
        //
    }
}
