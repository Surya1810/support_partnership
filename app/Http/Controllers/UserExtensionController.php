<?php

namespace App\Http\Controllers;

use App\Models\UserExtension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserExtensionController extends Controller
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
            'nik' => 'bail|required|numeric|',
            'npwp' => 'bail|required|numeric|',
            'phone' => 'bail|required|numeric|digits_between:10,15',
            'address' => 'bail|required|max:255',
            'religion' => 'bail|required|max:255',
            'gender' => 'bail|required|max:255',
            'pob' => 'bail|required|max:255',
            'dob' => 'bail|required|max:255',
            'hobby' => 'bail|required|max:255',
            'disease' => 'bail|required|max:255',
            'marriage' => 'bail|required|max:255',
            'language' => 'bail|required|max:255',
            'elementary' => 'bail|required|max:255',
            'junior_high' => 'bail|required|max:255',
            'senior_high' => 'bail|required|max:255',
            'college' => 'bail|required|max:255',
            'bank' => 'bail|required|max:255',
            'account' => 'bail|required|numeric|',
        ]);

        $old = session()->getOldInput();

        // Normalisasi nomor telepon
        $phoneNumber = $request->number;
        $normalizedNumber = ltrim($phoneNumber, '0'); // Hilangkan angka 0 di awal
        if (!str_starts_with($normalizedNumber, '62')) {
            $normalizedNumber = '62' . $normalizedNumber;
        }

        $supplier = new UserExtension();
        $supplier->user_id = Auth::user()->id;
        $supplier->nik = $request->nik;
        $supplier->npwp = $request->npwp;
        $supplier->phone = $normalizedNumber;
        $supplier->address = $request->address;
        $supplier->religion = $request->religion;
        $supplier->gender = $request->gender;
        $supplier->pob = $request->pob;
        $supplier->dob = $request->dob;
        $supplier->hobby = $request->hobby;
        $supplier->disease = $request->disease;
        $supplier->marriage = $request->marriage;
        $supplier->language = $request->language;
        $supplier->elementary = $request->elementary;
        $supplier->junior_high = $request->junior_high;
        $supplier->senior_high = $request->senior_high;
        $supplier->college = $request->college;
        $supplier->bank = $request->bank;
        $supplier->account = $request->account;
        $supplier->save();

        return redirect()->route('supplier.index')->with(['pesan' => 'Supplier created successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserExtension $userExtension)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserExtension $userExtension)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserExtension $userExtension)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserExtension $userExtension)
    {
        //
    }
}
