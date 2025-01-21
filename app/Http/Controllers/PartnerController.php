<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partners = Partner::where('is_active', true)->get();
        return view('partner.index', compact('partners'));
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
            'name' => 'bail|required|max:255',
            'contact' => 'bail|required|max:255',
            'number' => 'bail|required|numeric|digits_between:10,15',
            'keyword' => 'bail|required|max:255',
            'desc' => 'bail|required|max:255',
        ]);

        $old = session()->getOldInput();

        // Normalisasi nomor telepon
        $phoneNumber = $request->number;
        $normalizedNumber = ltrim($phoneNumber, '0'); // Hilangkan angka 0 di awal
        if (!str_starts_with($normalizedNumber, '62')) {
            $normalizedNumber = '62' . $normalizedNumber;
        }

        $partner = new Partner();
        $partner->name = $request->name;
        $partner->contact = $request->contact;
        $partner->number = $normalizedNumber;
        $partner->keyword = implode(",", $request->keyword);
        $partner->desc = $request->desc;
        $partner->save();

        return redirect()->route('partner.index')->with(['pesan' => 'Partner created successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Partner $partner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partner $partner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'bail|required|max:255',
            'contact' => 'bail|required|max:255',
            'number' => 'bail|required|numeric|digits_between:10,15',
            // 'keyword' => 'bail|required|max:255',
            'desc' => 'bail|required|max:255',
        ]);

        $partner = Partner::find($id);

        // Normalisasi nomor telepon
        $phoneNumber = $request->number;
        $normalizedNumber = ltrim($phoneNumber, '0'); // Hilangkan angka 0 di awal
        if (!str_starts_with($normalizedNumber, '62')) {
            $normalizedNumber = '62' . $normalizedNumber;
        }

        $partner->name = $request->name;
        $partner->contact = $request->contact;
        $partner->number = $normalizedNumber;
        // $partner->keyword = implode(",", $request->keyword);
        $partner->desc = $request->desc;
        $partner->update();

        return redirect()->route('partner.index')->with(['pesan' => 'Partner updated successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $partner = Partner::find($id);
        $partner->delete();

        return redirect()->route('partner.index')->with(['pesan' => 'Partner & projects deleted successfully', 'level-alert' => 'alert-danger']);
    }
}
