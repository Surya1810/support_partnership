<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = Carbon::now()->toFormattedDateString('d/m/y');
        // $projects = Project::where('deadline', '>=', today())->get();
        $projects = Project::where('status', '!=', 'Finished')->orWhere('deadline', '>=', today())->get();

        return view('project.index', compact('projects', 'today'));
    }

    public function archive()
    {
        return view('project.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('id', '!=', '1')->get();
        $departments = Department::all()->except(8);
        $clients = Client::all();
        return view('project.create', compact('users', 'clients', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'bail|required',
            'client' => 'bail|required',
            'creative_brief' => 'bail|required',
            'pic' => 'bail|required',
            'assisten' => 'bail|required',
            'status' => 'bail|required',
            'urgency' => 'bail|required',
            'deadline' => 'bail|required',
            'start' => 'bail|required',
        ]);

        $old = session()->getOldInput();

        $project = new Project();
        $project->name = $request->name;
        $project->client_id = $request->client;
        $project->department_id = $request->department_id;
        $project->kode = (Str::random(5));
        $project->creative_brief = $request->creative_brief;
        $project->user_id = $request->pic;
        $project->status = $request->status;
        $project->urgency = $request->urgency;
        $project->deadline = $request->deadline;
        $project->start = $request->start;
        $project->assisten_id = implode(',', $request->assisten);
        $project->save();

        return redirect()->route('project.index')->with(['pesan' => 'Project created successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($kode)
    {
        $project = Project::where('kode', $kode)->first();
        $users = User::where('id', '!=', '1')->get();
        $clients = Client::all();
        $departments = Department::all()->except(8);

        return view('project.edit', compact('project', 'users', 'clients', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'bail|required',
            'client' => 'bail|required',
            'creative_brief' => 'bail|required',
            'pic' => 'bail|required',
            'assisten' => 'bail|required',
            'status' => 'bail|required',
            'urgency' => 'bail|required',
            'deadline' => 'bail|required',
            'start' => 'bail|required',
        ]);

        $project = Project::find($id);
        $project->name = $request->name;
        $project->client_id = $request->client;
        $project->department_id = $request->department_id;
        $project->creative_brief = $request->creative_brief;
        $project->user_id = $request->pic;
        $project->status = $request->status;
        $project->urgency = $request->urgency;
        $project->deadline = $request->deadline;
        $project->start = $request->start;
        $project->assisten_id = implode(',', $request->assisten);
        $project->update();

        return redirect()->route('project.index')->with(['pesan' => 'Project updated successfully', 'level-alert' => 'alert-warning']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $project = Project::find($id);

        $project->delete();

        return redirect()->route('project.index')->with(['pesan' => 'Project deleted successfully', 'level-alert' => 'alert-danger']);
    }

    public function detail($kode)
    {
        $project = Project::where('kode', $kode)->first();
        $asisten = explode(',', $project->assisten_id);
        $team = User::whereIn('id', $asisten)->get();

        return view('project.detail', compact('project', 'team'));
    }

    public function task($kode)
    {
        $project = Project::where('kode', $kode)->first();

        if ($project->status != 'Finished') {
            // find team
            $asisten = explode(',', $project->assisten_id);
            $team = User::whereIn('id', $asisten)->pluck('id');
            // find pic
            $pic = User::where('id', $project->user_id)->pluck('id');
            $pic_1 = explode(',', $pic);
            // find admin
            $admin = [1];

            $access = $pic->merge($team)->merge($admin);

            // Task
            $tasks = $project->tasks;
            // dd($tasks);

            return view('project.task', compact('project', 'access', 'tasks'));
        } else {
            abort(404);
        }
    }

    public function review($kode)
    {
        $project = Project::where('kode', $kode)->first();

        return view('project.review', compact('project'));
    }

    public function done(Request $request, $id)
    {
        $request->validate([
            'review' => 'bail|required',
        ]);
        $project = Project::find($id);

        $project->status = 'Finished';
        $project->review = $request->review;
        $project->save();

        return redirect()->route('project.index')->with(['pesan' => 'Project Finished', 'level-alert' => 'alert-success']);
    }
}
