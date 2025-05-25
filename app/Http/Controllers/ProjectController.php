<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CostCenter;
use App\Models\CostCenterCategories;
use App\Models\CostCenterSub;
use App\Models\Department;
use App\Models\Income;
use App\Models\Project;
use App\Models\ProjectFinancial;
use App\Models\ProjectNetProfit;
use App\Models\ProjectOtherCost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = Carbon::now()->toFormattedDateString('d/m/y');
        $projects = Project::with('pic')->where('status', '!=', 'Finished')->get();

        return view('project.index', compact('projects', 'today'));
    }

    public function archive()
    {
        $today = Carbon::now()->toFormattedDateString('d/m/y');
        $projects = Project::where('status', 'Finished')->get();

        return view('project.archive', compact('projects', 'today'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('id', '!=', '1')->get();
        $departments = Department::all()->except([2, 4, 6, 7, 8]);
        $clients = Client::all();
        $costCenterCategories = CostCenterCategories::all();

        return view('project.create', compact('users', 'clients', 'departments', 'costCenterCategories'));
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
            'nilai_pekerjaan' => 'bail|required',
            'ppn' => 'bail|required',
            'pph' => 'bail|required',
            'pic' => 'bail|required',
            'assisten' => 'bail|required',
            'start' => 'bail|required',
            'deadline' => 'bail|required',
            'profit_perusahaan' => 'bail|required|max:40',
            'profit_penyusutan' => 'bail|required|max:20',
            'profit_divisi' => 'bail|required|max:20',
            'profit_bonus' => 'bail|required|max:30',
            'othercosts' => 'bail|array',
            'othercosts.*.item_name' => 'bail|required|string|max:255', // Nama Item Biaya Lain-lain
            'othercosts.*.unit_price' => 'bail|required|numeric|min:0', // Nominal Item Biaya Lain-lain
            'items' => 'bail|array', // RAB
            'items.*.item_type' => 'bail|required|string|max:255', // ID Cost Center Category
            'items.*.item_name' => 'bail|required|string|max:255', // Nama Item RAB
            'items.*.unit_price' => 'bail|required|numeric|min:0', // Nominal Item RAB
        ]);

        $calculateProfit = $request->profit_perusahaan + $request->profit_penyusutan + $request->profit_divisi + $request->profit_bonus;

        if ($calculateProfit > 100) {
            return redirect()->back()->with(['pesan' => 'Persentase keuntungan tidak boleh melebihi 100%', 'level-alert' => 'alert-warning']);
        }

        DB::beginTransaction();

        try {
            $addProject = Project::create([
                'name' => $request->name,
                'client_id' => $request->client,
                'department_id' => $request->department_id,
                'kode' => Str::random(5),
                'creative_brief' => $request->creative_brief,
                'user_id' => $request->pic,
                'status' => 'On Going', // Status dihilangkan dari view, set ke default saja jadi On Going
                'urgency' => 'High', // Urgency dihilangkan dari view, set default sebagai High
                'start' => $request->start,
                'deadline' => $request->deadline,
                'assisten_id' => implode(',', $request->assisten)
            ]);

            if ($addProject) {
                /**
                 * Masukkan nilai pekerjaan, ppn, pph,
                 * sp2d, margin, dan biaya lain-lain ke project_financials
                 */
                $addFinancials = ProjectFinancial::create([
                    'project_id' => $addProject->id,
                    'job_value' => $request->nilai_pekerjaan,
                    'vat_percent' => $request->ppn,
                    'tax_percent' => $request->pph,
                    'sp2d_amount' => $request->sp2d,
                    'margin' => $request->margin
                ]);

                /**
                 * Masukkan item RAB ke cost_center_subs
                 */
                $departmentId   = Auth::user()->department_id;
                $department = Department::where('id', $departmentId)->first();
                $costCenterSubs = $request->items;
                $costCenter = CostCenter::where('department_id', $departmentId)->first();
                $costCenterId   = $costCenter->id;
                $projectId      = $addProject->id;

                $preparedCostCenterSubsData = array_map(
                    function ($item, $index) use ($costCenterId, $projectId, $department) {
                        $costCenterCategory = CostCenterCategories::where('id', $item['item_type'])->first();
                        $ref = $department->code . '.' . $costCenterCategory->code . '.' . now()->format('Y') . '/';
                        return [
                            'cost_center_id' => $costCenterId,
                            'project_id'     => $projectId,
                            'department_id'  => $department->id,
                            'cost_center_category_ref' => $ref,
                            'cost_center_category_code'      => $costCenterCategory->code,
                            'name'      => $item['item_name'],
                            'amount'     => $item['unit_price'],
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    },
                    $costCenterSubs,
                    array_keys($costCenterSubs)
                );

                $addCostCenterSubs = CostCenterSub::insert($preparedCostCenterSubsData);

                if ($addFinancials && $addCostCenterSubs) {
                    /**
                     * Masukkan biaya lain-lain
                     */
                    $preparedOtherCostsData = array_map(
                        function ($item, $index) use ($addFinancials) {
                            return [
                                'project_financial_id' => $addFinancials->id,
                                'name' => $item['item_name'],
                                'amount' => $item['unit_price'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        },
                        $request->othercosts,
                        array_keys($request->othercosts)
                    );

                    $addOtherCosts = ProjectOtherCost::insert($preparedOtherCostsData);

                    /**
                     * Masukkan net profit perusahaan
                     */
                    $addNetProfits = ProjectNetProfit::create([
                        'project_financial_id' => $addFinancials->id,
                        'company_percent' => $request->profit_perusahaan,
                        'depreciation' => $request->profit_penyusutan,
                        'cash_dept_percent' => $request->profit_divisi,
                        'team_bonus' => $request->profit_bonus
                    ]);

                    if ($addOtherCosts && $addNetProfits) {
                        DB::commit();
                        return redirect()->route('project.index')->with([
                            'pesan' => 'Project berhasil ditambahkan',
                            'level-alert' => 'alert-success'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'pesan' => 'Project gagal ditambahkan',
                'leve-alert' => 'alert-danger'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($kode)
    {
        $project = Project::where('kode', $kode)->first();
        $users = User::where('id', '!=', '1')->get();
        $clients = Client::all();
        $departments = Department::all()->except([2, 4, 6, 7, 8]);

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

    public function detail($kode)
    {
        $project = Project::where('kode', $kode)
            ->with(['financial.otherCosts', 'financial.netProfits', 'costCenterSubs'])
            ->first();
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

        $sp2d = new Income();
        $sp2d->department_id = $project->department_id;
        $sp2d->project_id = $project->id;
        $sp2d->category = 'sp2d';
        $sp2d->desc = 'pemasukan sp2d dari project ' . $project->name;
        $sp2d->amount = $request->sp2d;
        $sp2d->save();

        return redirect()->route('project.index')->with(['pesan' => 'Project Finished', 'level-alert' => 'alert-success']);
    }
}
