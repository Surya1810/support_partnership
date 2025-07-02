<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CostCenter;
use App\Models\CostCenterCategory;
use App\Models\Department;
use App\Models\Income;
use App\Models\Project;
use App\Models\ProjectFinancial;
use App\Models\ProjectProfit;
use App\Models\User;

use App\Imports\ImportRABProject;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

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
            'creative_brief' => 'nullable|string',
            'nilai_pekerjaan' => 'bail|required',
            'ppn' => 'bail|required',
            'pph' => 'bail|required',
            'pic' => 'bail|required',
            'assisten' => 'bail|required',
            'start' => 'bail|required',
            'deadline' => 'bail|required',
            'profit_perusahaan' => 'nullable|max:40',
            'profit_penyusutan' => 'nullable|max:20',
            'profit_divisi' => 'nullable|max:20',
            'profit_bonus' => 'nullable|max:30',
            'items' => 'bail|array', // RAB
            'items.*.name' => 'bail|required|string|max:255',
            'items.*.bulan' => 'bail|required|string|max:255',
            'items.*.tahun' => 'bail|required|string|max:255',
            'items.*.debet' => 'nullable|string|max:255',
            'items.*.kredit' => 'nullable|string|max:255',
        ]);

        $calculateProfit = $request->profit_perusahaan + $request->profit_penyusutan + $request->profit_divisi + $request->profit_bonus;

        if ($calculateProfit > 100) {
            return redirect()->back()->with([
                'pesan' => 'Persentase keuntungan tidak boleh melebihi 100%',
                'level-alert' => 'alert-warning'
            ]);
        }

        DB::beginTransaction();

        try {
            $addProject = Project::create([
                'name' => $request->name,
                'client_id' => $request->client,
                'department_id' => $request->department_id,
                'kode' => Str::random(5),
                'creative_brief' => '-', // Creative Brief dihilangkan dari view, set default sebagai -
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
                    'job_value' => (int) $request->nilai_pekerjaan,
                    'vat_percent' => $request->ppn,
                    'tax_percent' => $request->pph,
                    'sp2d_amount' => (int) $request->sp2d,
                    'margin' => (int) $request->margin
                ]);

                /**
                 * Masukkan item RAB ke cost_center_subs
                 */
                $departmentId   = Auth::user()->department_id;
                $department = Department::where('id', $departmentId)->first();

                /**
                 * Simpan setiap items cost center project yang diimport
                 */
                $costCenterItems = $request->items;
                $projectId = $addProject->id;
                $remainingAmount = 0;
                $preparedCostCenterItemsData = [];

                foreach ($costCenterItems as $index => $item) {
                    $costCenterCategory = CostCenterCategory::where('code', 'BP')->first();

                    if (!is_null($item['debet']) && $item['debet'] != 0) {
                        $remainingAmount = (int) $item['debet'];
                    }

                    if (!is_null($item['kredit']) && $item['kredit'] != 0) {
                        $remainingAmount -= (int) $item['kredit'];
                    }

                    $preparedCostCenterItemsData[] = [
                        'department_id'  => $department->id,
                        'project_id'     => $projectId,
                        'cost_center_category_id' => $costCenterCategory->id,
                        'code_ref' => $item['kode_ref'],
                        'name'      => $item['name'],
                        'amount_debit'     => !is_null($item['debet']) ? (int) $item['debet'] : 0,
                        'amount_credit'     => !is_null($item['kredit']) ? (int) $item['kredit'] : 0,
                        'amount_remaining' => $remainingAmount,
                        'year'      => (int) $item['tahun'],
                        'month'      => (int) $item['bulan'],
                        'type' => 'project',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }

                $addCostCenterProject = CostCenter::insert($preparedCostCenterItemsData);

                if ($addFinancials && $addCostCenterProject) {
                    /**
                     * Masukkan net profit perusahaan
                     */
                    $addProfits = ProjectProfit::create([
                        'project_id' => $addProject->id,
                        'percent_company' => (int) $request->profit_perusahaan,
                        'percent_depresiation' => (int) $request->profit_penyusutan,
                        'percent_cash_department' => (int) $request->profit_divisi,
                        'percent_team_bonus' => (int) $request->profit_bonus,
                    ]);

                    if ($addProfits) {
                        DB::commit();

                        return redirect()->route('project.index')->with([
                            'pesan' => 'Project berhasil ditambahkan',
                            'level-alert' => 'alert-success'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            dd($e);
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

    /**
     * Wed, 02 July 2025
     */
    public function importRAB(Request $request)
    {
        $validatedRequest = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx',
        ]);

        if ($validatedRequest->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Pastikan format file RAB telah sesuai'
            ], 400);
        }

        try {
            $import = new ImportRABProject();
            Excel::import($import, $request->file('file'));
            $rows = $import->rows;
            $userDeparmentCode = Auth::user()->department->code;

            $data = [];
            $currentDate = Carbon::now()->format('Y-m-d');
            $totalDebetProject = 0;
            $totalLimitProject = 0;
            $lastCostCenterProject = CostCenter::where('department_id', Auth::user()->department_id)
                ->where('type', 'project')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastCostCenterProject) {
                $numberOfLastCostCenterProject = str_pad(
                    ((int) explode('/', $lastCostCenterProject)[1]) + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            } else {
                $numberOfLastCostCenterProject = '0001';
            }

            foreach ($rows as $index => $row) {
                if ($index == 0 || $row[0] == null || $row[1] == null) continue;

                $month = Carbon::create()->month($row[2])->format('F');
                $monthIndex = str_pad($row[2], 2, '0', STR_PAD_LEFT);
                $year = (int) trim($row[3]);

                if (!$month) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Pastikan nama bulan seuai dengan format pada template'
                    ], 400);
                }

                $data[] = [
                    'no' => $index,
                    'tanggal' => $currentDate,
                    'nama_item' => $row[1],
                    'bulan' => $month,
                    'bulan_index' => trim($row[2]),
                    'tahun' => $year,
                    'debet' => $row[4],
                    'limit' => $row[5],
                    'kode_ref' => (
                        $userDeparmentCode
                        . '.BP.'
                        . $monthIndex
                        . '-' . $year
                        . '/' . $numberOfLastCostCenterProject
                    ) . ($index == 1 ? '' : '/' . str_pad(((string)$index++), 4, '0', STR_PAD_LEFT)),
                ];

                $totalDebetProject += $row[4] ? (int) trim($row[4]) : 0;
                $totalLimitProject += $row[5] ? (int) trim($row[5]) : 0;
            }

            return response()->json([
                'items' => $data,
                'saldo' => [
                    'total_debet' => $totalDebetProject,
                    'total_limit' => $totalLimitProject
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
