<?php

namespace App\Http\Controllers;

use App\Events\TagScanned;
use App\Models\Asset;
use App\Models\Scan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('asset.scan');
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
    public function show(Scan $scan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scan $scan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scan $scan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scan $scan)
    {
        //
    }

    /**
     * Get API
     */
    public function scan(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*' => 'string'
        ]);

        $scannedTags = $request->input('data');

        $allAssets = Asset::all();

        foreach ($allAssets as $asset) {
            if (in_array($asset->rfid_number, $scannedTags)) {
                $asset->is_there = true;
            } else {
                $asset->is_there = false;
            }
            $asset->save();
        }

        // Simpan log ke rfid_logs
        foreach ($request->data as $data) {
            $rfid = new Scan();
            $rfid->rfid_number = $data;
            $rfid->save();
        }

        TagScanned::dispatch($scannedTags);

        return response()->json([
            'message' => 'RFID sent successfully',
        ]);
    }
}
