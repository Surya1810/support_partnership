<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'title' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $old = session()->getOldInput();

        Debt::create([
            'department_id' => $request['department'],
            'category' => 'development',
            'title' => $request['title'],
            'amount' => $request['amount'],
        ]);
        if ($request->department == 3) {
            return redirect()->route('construction.report')->with(['pesan' => 'Debt added successfully', 'level-alert' => 'alert-success']);
        } elseif ($request->department == 5) {
            return redirect()->route('technology.report')->with(['pesan' => 'Debt added successfully', 'level-alert' => 'alert-success']);
        } else {
            return redirect()->route('procurement.report')->with(['pesan' => 'Debt added successfully', 'level-alert' => 'alert-success']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Debt $debt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt)
    {
        //
    }
}
