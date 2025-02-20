<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function checkUserExtension()
    {
        $user = User::with('extension')->find(Auth::user()->id);

        if ($user && !$user->extension) {
            return response()->json(['hasExtension' => false]);
        }

        return response()->json(['hasExtension' => true]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('is_active', true)->orderBy('department_id')->get()->except(1);
        return view('employee.index', compact('users'));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
