<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = File::with('user')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $files = $query->paginate(12);

        // Ambil semua tahun unik dari created_at
        $years = File::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('files.index', compact('files', 'years'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('files.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:262144', // 256MB max
            'category' => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads/files', 'public');

        File::create([
            'name' => $request->name,
            'file_path' => $path,
            'category' => $request->category,
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->route('files.index')->with(['pesan' => 'Files uploaded successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(File $file)
    {
        return view('files.edit', compact('file'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, File $file)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        $file->update([
            'name' => $request->name,
            'category' => $request->category,
        ]);

        return redirect()->route('files.index')->with(['pesan' => 'Files updated successfully', 'level-alert' => 'alert-warning']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        // Tentukan path file yang ada di public storage
        $filePath = storage_path('app/public/' . $file->file_path);

        // Cek apakah file ada di path tersebut
        if (file_exists($filePath)) {
            // Menghapus file dari storage
            unlink($filePath);
        } else {
            return back()->with(['pesan' => 'File tidak ditemukan di storage.', 'level-alert' => 'alert-warning']);
        }

        // Menghapus data file dari database
        $file->delete();

        return back()->with(['pesan' => 'File berhasil dihapus.', 'level-alert' => 'alert-danger']);
    }
}
