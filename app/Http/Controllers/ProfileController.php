<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**Display the user's profile form.*/
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**Update the user's profile information.*/
    public function update(Request $request, $id)
    {
        $user = User::find(Auth::id());
        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'name' => 'required|unique:users,name,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->update();
        return redirect()->back()->with(['pesan' => 'Profile updated successfully', 'level-alert' => 'alert-success']);
    }
    /**Update the user's user password.*/
    public function password(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        $hashedPassword = Auth::user()->password;
        if (Hash::check($request->old_password, $hashedPassword)) {
            if (!Hash::check($request->new_password, $hashedPassword)) {
                $user = User::find(Auth::id());
                $user->password = Hash::make($request->new_password);
                $user->save();
                Auth::logout();
                return redirect()->route('login')->with(['pesan' => 'Password updated successfully', 'level-alert' => 'alert-success']);
            } else {
                return redirect()->back()->with(['pesan' => 'New password cannot be the same as old password', 'level-alert' => 'alert-danger']);
            }
        } else {
            return redirect()->back()->with(['pesan' => 'Current password not match', 'level-alert' => 'alert-danger']);
        }
    }

    /**Delete the user's account.*/
    public function destroy(Request $request, $id)
    {
        // $request->validate([
        //     'password' => 'required',
        //     'current_password' => 'required|same:password',
        // ]);

        // $user = User::find($id);

        // Auth::logout();
        // $user->delete();

        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        // return redirect()->route('login');
        return redirect()->back()->with([
            'pesan' => 'Please contact admin', 'level-alert' => 'alert-danger'
        ]);
    }


    // /**Update the user's profile information.*/
    // public function update($request): RedirectResponse
    // {
    //     $request->user()->fill($request->validated());

    //     if ($request->user()->isDirty('email')) {
    //         $request->user()->email_verified_at = null;
    //     }

    //     $request->user()->save();

    //     return Redirect::route('profile.edit')->with('status', 'profile-updated');
    // }

    // /**Delete the user's account.*/
    // public function destroy(Request $request): RedirectResponse
    // {
    //     $request->validateWithBag('userDeletion', ['password' => ['required', 'current_password'],]);

    //     $user = $request->user();

    //     Auth::logout();

    //     $user->delete();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return Redirect::to('/');
    // }
}
