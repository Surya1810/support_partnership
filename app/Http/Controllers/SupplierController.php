<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        return view('supplier.index', compact('suppliers'));
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

        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->contact = $request->contact;
        $supplier->number = $normalizedNumber;
        $supplier->keyword = implode(",", $request->keyword);
        $supplier->desc = $request->desc;
        $supplier->save();

        return redirect()->route('supplier.index')->with(['pesan' => 'Supplier created successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
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

        $supplier = Supplier::find($id);

        // Normalisasi nomor telepon
        $phoneNumber = $request->number;
        $normalizedNumber = ltrim($phoneNumber, '0'); // Hilangkan angka 0 di awal
        if (!str_starts_with($normalizedNumber, '62')) {
            $normalizedNumber = '62' . $normalizedNumber;
        }

        $supplier->name = $request->name;
        $supplier->contact = $request->contact;
        $supplier->number = $normalizedNumber;
        // $supplier->keyword = implode(",", $request->keyword);
        $supplier->desc = $request->desc;
        $supplier->update();

        return redirect()->route('supplier.index')->with(['pesan' => 'Supplier updated successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        $supplier->delete();

        return redirect()->route('supplier.index')->with(['pesan' => 'Supplier & projects deleted successfully', 'level-alert' => 'alert-danger']);
    }
}
