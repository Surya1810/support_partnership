<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Document;
use App\Models\ExpenseRequest;
use App\Models\Project;
use Illuminate\Http\Request;

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
        $assets  =   Asset::all()->count();

        return view('home.dashboard', compact('projects', 'applications', 'documents', 'assets'));
    }
}
