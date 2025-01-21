<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::where('is_active', true)->get();
        return view('client.index', compact('clients'));
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
            'position' => 'bail|required|max:255',
        ]);

        $old = session()->getOldInput();

        // Normalisasi nomor telepon
        $phoneNumber = $request->number;
        $normalizedNumber = ltrim($phoneNumber, '0'); // Hilangkan angka 0 di awal
        if (!str_starts_with($normalizedNumber, '62')) {
            $normalizedNumber = '62' . $normalizedNumber;
        }

        $client = new Client();
        $client->name = $request->name;
        $client->contact = $request->contact;
        $client->number = $normalizedNumber;
        $client->position = $request->position;
        $client->save();

        return redirect()->route('client.index')->with(['pesan' => 'Client created successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
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
            'position' => 'bail|required|max:255',
        ]);

        $client = Client::find($id);

        // Normalisasi nomor telepon
        $phoneNumber = $request->number;
        $normalizedNumber = ltrim($phoneNumber, '0'); // Hilangkan angka 0 di awal
        if (!str_starts_with($normalizedNumber, '62')) {
            $normalizedNumber = '62' . $normalizedNumber;
        }

        $client->name = $request->name;
        $client->contact = $request->contact;
        $client->number = $normalizedNumber;
        $client->position = $request->position;
        $client->update();

        return redirect()->route('client.index')->with(['pesan' => 'Client updated successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $client = Client::find($id);
        $client->delete();

        return redirect()->route('client.index')->with(['pesan' => 'Client & projects deleted successfully', 'level-alert' => 'alert-danger']);
    }
}
