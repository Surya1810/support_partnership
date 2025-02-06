<?php

namespace App\Http\Controllers;

use App\Imports\AssetsImport;
use App\Models\Asset;
use App\Models\Department;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::all();
        $tags = Tag::where('status', 'available')->get();
        $users = User::where('is_active', true)->get()->except(1);

        return view('asset.index', compact('assets', 'tags', 'users'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        //
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'pic' => 'required|exists:users,id'
        ]);
        try {
            Excel::import(new AssetsImport($request->pic), $request->file('file'));
            return back()->with(['pesan' => 'Assets imported successfully', 'level-alert' => 'alert-success']);
        } catch (\Exception $e) {
            return back()->with(['pesan' => 'Duplicated entry', 'level-alert' => 'alert-danger']);
        }
    }
}
