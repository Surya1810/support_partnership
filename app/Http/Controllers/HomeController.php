<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Client;
use App\Models\Document;
use App\Models\ExpenseRequest;
use App\Models\File;
use App\Models\Izin;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $projects = Project::all()->count();
        $applications = ExpenseRequest::all()->count();
        $documents  =   Document::all()->count();
        $files  =   File::all()->count();
        $izins = Izin::all()->count();
        $users = User::all()->except(1)->count();

        $manager = User::where('role_id', 3)->pluck('id');

        if (Auth::user()->role_id == 1 || Auth::user()->department_id == 8) {
            $my_approval_izin = Izin::where('status', 'pending')->get()->count();
        } elseif (Auth::user()->role_id == 2) {
            $my_approval_izin = Izin::where('status', 'pending')->whereIn('user_id', $manager)->get()->count();
        } else {
            $my_approval_izin = Izin::where('status', 'pending')->whereNotIn('user_id', $manager)->get()->count();
        }

        $my_approval_dana = collect(); // default kosong

        $user = Auth::user();

        if ($user->role_id == 1 || $user->department_id == 8) {
            // admin atau khusus bisa akses semua
            $my_approval_dana = ExpenseRequest::where('status', 'pending')->get()->count();
        } elseif (in_array($user->department_id, [3, 5])) {
            // manager approval logic
            $my_approval_dana = ExpenseRequest::where('status', 'pending')
                ->where('department_id', $user->department_id)
                ->where(function ($query) {
                    $query->where('total_amount', '<=', 150000)
                        ->orWhere('approved_by_manager', false);
                })
                ->where('user_id', '!=', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()->count();
        } elseif ($user->id == 2) {
            // direktur approval logic
            $my_approval_dana = ExpenseRequest::where('status', 'pending')
                ->where(function ($query) {
                    $query->where('total_amount', '>', 150000)
                        ->orWhere('department_id', 1)
                        ->orWhereHas('user', function ($q) {
                            $q->where('role_id', 3);
                        });
                })
                ->orderBy('created_at', 'desc')
                ->get()->count();
        }

        $clients = Client::all()->count();
        $suppliers = Supplier::all()->count();
        $partners = Partner::all()->count();

        return view('home.dashboard', compact('projects', 'applications', 'documents', 'files', 'izins', 'users', 'my_approval_dana', 'my_approval_izin', 'clients', 'suppliers', 'partners'));
    }
}
