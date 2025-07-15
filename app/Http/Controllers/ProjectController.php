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
use App\Models\ProjectFinalization;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = Carbon::now()->toFormattedDateString('d/m/y');
        $projects = Project::with('pic')
            ->where('status', '!=', 'Finished')
            ->get();

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
            'profit_perusahaan' => 'nullable|numeric',
            'profit_penyusutan' => 'nullable|numeric',
            'profit_divisi' => 'nullable|numeric',
            'profit_bonus' => 'nullable|numeric',
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
                 * Masukkan item RAB ke cost_center
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

                /**
                 * Pastikan posisi uang kas selalu
                 * berada di index pertama
                 */
                foreach ($costCenterItems as $index => $item) {
                    $costCenterCategory = CostCenterCategory::where('code', 'BP')->first();

                    if (!is_null($item['kredit']) && $item['kredit'] != 0) {
                        $remainingAmount = (int) $item['kredit'];
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
                        'percent_depreciation' => (int) $request->profit_penyusutan,
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
            DB::rollBack();
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'leve-alert' => 'alert-danger'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($kode)
    {
        $project = Project::where('kode', $kode)
            ->with(['financial', 'profit', 'costCenters'])
            ->first();
        $users = User::where('id', '!=', '1')->get();
        $clients = Client::all();
        $departments = Department::all()->except([2, 4, 6, 7, 8]);
        $totalDebetProject = $project->costCenters->sum('amount_credit');

        return view('project.edit', compact('project', 'users', 'clients', 'departments', 'totalDebetProject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'client' => 'required',
            'creative_brief' => 'nullable|string',
            'nilai_pekerjaan' => 'required|numeric',
            'ppn' => 'required|numeric',
            'pph' => 'required|numeric',
            'pic' => 'required',
            'assisten' => 'required|array',
            'start' => 'required|date',
            'deadline' => 'required|date',
            'profit_perusahaan' => 'nullable|numeric',
            'profit_penyusutan' => 'nullable|numeric',
            'profit_divisi' => 'nullable|numeric',
            'profit_bonus' => 'nullable|numeric',
        ]);

        $calculateProfit = (int) $request->profit_perusahaan
            + (int) $request->profit_penyusutan
            + (int) $request->profit_divisi
            + (int) $request->profit_bonus;

        if ($calculateProfit > 100) {
            return redirect()->back()->with([
                'pesan' => 'Persentase keuntungan tidak boleh melebihi 100%',
                'level-alert' => 'alert-warning'
            ]);
        }

        DB::beginTransaction();

        try {
            $project = Project::findOrFail($id);
            $project->update([
                'name' => $request->name,
                'client_id' => $request->client,
                'user_id' => $request->pic,
                'start' => $request->start,
                'deadline' => $request->deadline,
                'assisten_id' => implode(',', $request->assisten)
            ]);

            $projectFinancial = ProjectFinancial::where('project_id', $project->id)->first();
            if ($projectFinancial) {
                $projectFinancial->update([
                    'job_value' => (int) $request->nilai_pekerjaan,
                    'vat_percent' => $request->ppn,
                    'tax_percent' => $request->pph,
                    'sp2d_amount' => (int) $request->sp2d,
                    'margin' => (int) $request->margin
                ]);
            }

            $costCenterItems = CostCenter::where('project_id', $project->id)->get();
            $remainingAmount = 0;

            foreach ($costCenterItems as $item) {
                // Perhitungan saldo tersisa per baris
                if (!is_null($item->amount_debit) && $item->amount_debit != 0) {
                    $remainingAmount = (int) $item->amount_debit;
                }

                if (!is_null($item->amount_credit) && $item->amount_credit != 0) {
                    $remainingAmount -= (int) $item->amount_credit;
                }

                $item->amount_remaining = $remainingAmount;
                $item->save();
            }

            // Update atau insert project profit
            ProjectProfit::updateOrCreate(
                ['project_id' => $project->id],
                [
                    'percent_company' => (int) $request->profit_perusahaan,
                    'percent_depreciation' => (int) $request->profit_penyusutan,
                    'percent_cash_department' => (int) $request->profit_divisi,
                    'percent_team_bonus' => (int) $request->profit_bonus,
                ]
            );

            DB::commit();

            return redirect()->route('project.index')->with([
                'pesan' => 'Data project berhasil diperbarui',
                'level-alert' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    public function detail($kode)
    {
        $project = Project::where('kode', $kode)
            ->with(['financial', 'profit', 'costCenters'])
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
                    ) . ($index == 1 ? '' : '/' . str_pad(((string)$index - 1 ), 4, '0', STR_PAD_LEFT)),
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

    public function downloadTemplateImport()
    {
        $filePath = storage_path('app/public/uploads/files/templates/template_cost_center_untuk_project.xlsx');

        if (!file_exists($filePath)) {
            return back()->with([
                'pesan' => 'Template tidak ditemukan.',
                'level-alert' => 'alert-danger'
            ]);
        }

        return response()->streamDownload(function () use ($filePath) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            readfile($filePath);
        }, 'template_cost_center_untuk_project.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_cost_center_untuk_project.xlsx"',
        ]);
    }

    public function finalization($kode)
    {
        $project = Project::where('kode', $kode)
            ->with('finalization')
            ->first();
        return view('project.finalization', compact('kode', 'project'));
    }

    public function storeFinalization(Request $request, $id)
    {
        try {
            $request->validate([
                'invoice' => 'bail|required|string|max:255',
                'e_faktur' => 'bail|required|string|max:255',
                'id_billing_ppn' => 'bail|required|string|max:255',
                'id_billing_pph' => 'bail|required|string|max:255',
                'ntpn_ppn' => 'bail|required|string|max:255',
                'ntpn_pph' => 'bail|required|string|max:255',
                'file_bast' => 'bail|required|mimes:pdf|max:122880'
            ]);

            DB::beginTransaction();

            $project = Project::where('id', $id)->first();
            $project->status = 'Finished';
            $project->save();

            $file = $request->file('file_bast');
            $filename = time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/files/project_finalizations', $filename, 'public');
            $projectFinalization = ProjectFinalization::where('project_id', $project->id)->first();

            if ($projectFinalization) {
                if ($projectFinalization->bast_file && Storage::disk('public')->exists($projectFinalization->bast_file)) {
                    Storage::disk('public')->delete($projectFinalization->bast_file);
                }

                $projectFinalization->invoice_number = $request->invoice;
                $projectFinalization->e_faktur = $request->e_faktur;
                $projectFinalization->id_billing_ppn = $request->id_billing_ppn;
                $projectFinalization->id_billing_pph = $request->id_billing_pph;
                $projectFinalization->ntpn_ppn = $request->ntpn_ppn;
                $projectFinalization->ntpn_pph = $request->ntpn_pph;
                $projectFinalization->bast_file = $path;
                $projectFinalization->save();

                DB::commit();

                return redirect()->route('project.finalization',$project->kode)
                    ->with([
                        'pesan' => 'Dokumen penyelesaian project berhasil diperbarui',
                        'level-alert' => 'alert-success'
                    ]);
            } else {
                $projectFinalization = new ProjectFinalization();
                $projectFinalization->project_id = $project->id;
                $projectFinalization->invoice_number = $request->invoice;
                $projectFinalization->e_faktur = $request->e_faktur;
                $projectFinalization->id_billing_ppn = $request->id_billing_ppn;
                $projectFinalization->id_billing_pph = $request->id_billing_pph;
                $projectFinalization->ntpn_ppn = $request->ntpn_ppn;
                $projectFinalization->ntpn_pph = $request->ntpn_pph;
                $projectFinalization->bast_file = $path;
                $projectFinalization->save();

                DB::commit();

                return redirect()->route('project.finalization', $project->kode)
                    ->with([
                        'pesan' => 'Dokumen penyelesaian project berhasil ditambahkan. Project dinyatakan selesai',
                        'level-alert' => 'alert-success'
                    ]);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }
}
