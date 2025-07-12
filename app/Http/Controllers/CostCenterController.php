<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

use App\Models\Project;
use App\Models\Department;
use App\Models\CostCenter;
use App\Models\ProjectProfit;
use App\Models\CostCenterCategory;
use App\Models\ExpenseRequest;
use App\Models\ProjectFinancial;

class CostCenterController extends Controller
{
    private function getMonths()
    {
        return ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    }

    private function getYears()
    {
        return [date('Y'), date('Y') + 1];
    }

    public function index()
    {
        $query = CostCenter::where('type', 'department')
            ->orderBy('updated_at', 'desc')
            ->where('year', date('Y'));

        // jangan lupa sum total
        // pendatapan tahun berjalan
        $sums = [
            'debit' => formatRupiah($query->sum('amount_debit')),
            'credit' => formatRupiah($query->sum('amount_credit')), // belum dihitung dari total pengajuan diterima
            'remaining' => formatRupiah($query->sum('amount_remaining')),
        ];

        // hitung sum debit, credit, remainging
        // & tahun pendapatan berjalan per divisi
        $departmentIds = [1, 3, 5, 9];
        $sums['departments'] = DB::table('departments as d')
            ->leftJoin('cost_centers as cc', function ($join) {
                $join->on('d.id', '=', 'cc.department_id')
                    ->where('cc.type', 'department')
                    ->where('cc.year', date('Y'));
            })
            ->select(
                'd.id as department_id',
                'd.name as department_name',
                DB::raw('COALESCE(SUM(cc.amount_debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(cc.amount_credit), 0) as total_credit'),
                DB::raw('COALESCE(SUM(cc.amount_remaining), 0) as total_remaining')
            )
            ->whereIn('d.id', $departmentIds)
            ->groupBy('d.id', 'd.name')
            ->get()
            ->map(function ($row) {
                return [
                    'department_id' => $row->department_id,
                    'department_name' => $row->department_name,
                    'total_debit' => formatRupiah($row->total_debit),
                    'total_credit' => formatRupiah($row->total_credit),
                    'total_remaining' => formatRupiah($row->total_remaining),
                    'total_yearly' => formatRupiah(0), // belum ada
                ];
            });


        return view('cost-center.index', compact('sums'));
    }

    public function indexCreateRABGeneral(Request $request)
    {
        $departments = Department::all()->except([2, 4, 6, 7, 8]);
        $costCenterCategories = CostCenterCategory::whereNot('id', 1)->get(); // ambil yang bukan uang kas
        $months = $this->getMonths();
        $years = $this->getYears();

        $query = CostCenter::where('type', 'department')
            ->orderBy('updated_at', 'desc')
            ->when(Auth::user()->role_id == 3, function ($query) {
                $query->where('department_id', Auth::user()->department_id);
            })
            ->where('year', date('Y'));

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', fn($item) => $item->name)
                ->addColumn('code_ref', fn($item) => $item->code_ref)
                ->addColumn('department', fn($item) => $item->department?->name)
                ->addColumn('month', fn($item) => $item->month_name)
                ->addColumn('debit', function ($item) {
                    /**
                     * jika cost center adalah uang kas
                     * maka tampilkan debit
                     * jika bukan maka tampilkan credit/limit rab
                     */
                    return $item->cost_center_category_id == 1
                        ? formatRupiah($item->amount_debit)
                        : formatRupiah($item->amount_credit);
                })
                ->addColumn('year', fn($item) => $item->year)
                ->addColumn('detail', fn($item) => $item->detail)
                ->rawColumns(['detail'])
                ->make(true);
        }

        $sums = [
            'debit' => formatRupiah($query->sum('amount_debit')),
            'credit' => formatRupiah($query->sum('amount_credit')),
            'remaining' => formatRupiah($query->sum('amount_debit') - $query->sum('amount_credit')),
        ];

        return view('cost-center.transactions_rab_general_debet', compact('departments', 'months', 'years', 'costCenterCategories', 'sums'));
    }

    public function storeRABGeneral(Request $request)
    {
        try {
            $request->validate([
                'department' => 'required|exists:departments,id',
                'category' => 'required|exists:cost_center_categories,id',
                'name' => 'required|string|max:255',
                'nominal' => 'required|numeric',
                'month' => 'required',
                'year' => 'required',
            ]);

            $departmentId = $request->department;
            $codeRef = $this->generateCodeRef($departmentId, $request);
            $note = '<small>Dibuat oleh: ' . Auth::user()->username . '<br/>Tanggal: ' . date('d-m-Y') . '</small>';
            $dataRAB = [
                'department_id' => $departmentId,
                'cost_center_category_id' => $request->category,
                'type' => 'department',
                'code_ref' => $codeRef,
                'name' => $request->name,
                'amount_debit' => $request->nominal,
                'amount_remaining' => $request->nominal,
                'month' => $request->month,
                'year' => $request->year,
                'detail' => $note,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $insertRAB = CostCenter::insert($dataRAB);

            if ($insertRAB) {
                DB::commit();
                return redirect()->back()->with([
                    'pesan' => 'RAB untuk divisi berhasil ditambahkan',
                    'level-alert' => 'alert-success'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    public function updateRABGeneral(Request $request, $id)
    {
        try {
            $request->validate([
                'update_type' => 'required|in:change_amount,change_to_new_rab',
            ]);

            DB::beginTransaction();

            $costCenter = CostCenter::where('id', $id)->first();

            if (!$costCenter) {
                return redirect()->back()->with([
                    'pesan' => 'RAB tidak ditemukan',
                    'level-alert' => 'alert-danger'
                ]);
            }

            if ($request->update_type == 'change_amount') {
                $request->validate([
                    'new_nominal' => 'required',
                ]);

                // create change + notes
                $note = $costCenter->detail ? $costCenter->detail . '<hr style="margin:0"/>' : '';
                $note .= '<small><span class="text-success">RAB ditambah: '
                    . formatRupiah((int) $request->new_nominal)
                    . '</span><br/>Oleh: ' . Auth::user()->username
                    . '<br/>Tanggal: ' . date('d-m-Y')
                    . '</small>';
                $currentDebit = $costCenter->amount_debit + (int) $request->new_nominal;
                $currentRemaining = $costCenter->amount_remaining + (int) $request->new_nominal;

                $updateRAB = CostCenter::where('id', $id)->update([
                    'amount_debit' => $currentDebit,
                    'amount_remaining' => $currentRemaining,
                    'detail' => $note,
                    'updated_at' => now(),
                ]);

                if ($updateRAB) {
                    DB::commit();
                    return redirect()->back()->with([
                        'pesan' => 'Nominal RAB berhasil diperbarui',
                        'level-alert' => 'alert-success'
                    ]);
                }
            }

            if ($request->update_type == 'change_to_new_rab') {
                $request->validate([
                    'department' => 'required|exists:departments,id',
                    'target' => 'required|exists:cost_centers,id',
                    'category' => 'required|exists:cost_center_categories,id',
                    'name_new' => 'required|string|max:255',
                    'nominal_new_rab' => 'required|numeric',
                    'month' => 'required',
                    'year' => 'required',
                ]);

                if ($costCenter->amount_remaining < $request->nominal_new_rab) {
                    return redirect()->back()->with([
                        'pesan' => 'Sisa saldo RAB awal tidak mencukupi',
                        'level-alert' => 'alert-danger'
                    ]);
                }

                $departmentId = $request->department;
                $codeRef = $this->generateCodeRef($departmentId, $request);
                $dataRAB = [
                    'department_id' => $departmentId,
                    'cost_center_category_id' => $request->category,
                    'type' => 'department',
                    'code_ref' => $codeRef,
                    'amount_credit' => $request->nominal_new_rab, // limit untuk rab baru
                    'amount_remaining' => $request->nominal_new_rab, // sisa saldo rab baru == limit pertama
                    'name' => $request->name_new,
                    'month' => $request->month,
                    'year' => $request->year,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // update keterangan RAB awal
                $note = $costCenter->detail ? $costCenter->detail . '<hr style="margin:0"/>' : '';
                $note .= '<small><span class="text-danger">RAB dikurangi: -'
                    . formatRupiah((int) $request->nominal_new_rab)
                    . '</span><br/>Untuk RAB Baru: ' . $codeRef
                    . '<br/>Oleh: ' . Auth::user()->username
                    . '<br/>Tanggal: ' . date('d-m-Y')
                    . '</small>';

                /**
                 * Kurangi sisa saldo RAB target
                 */
                $updateRABTarget = CostCenter::where('id', $id)->update([
                    'amount_remaining' => $costCenter->amount_remaining - (int) $request->nominal_new_rab,
                    'detail' => $note,
                    'updated_at' => now(),
                ]);

                $insertRAB = CostCenter::insert($dataRAB);

                if ($insertRAB && $updateRABTarget) {
                    DB::commit();
                    return redirect()->back()->with([
                        'pesan' => 'RAB berhasil dibagi ke RAB baru',
                        'level-alert' => 'alert-success'
                    ]);
                }
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    public function getRABGeneralJSON($id)
    {
        $costCenters = CostCenter::where('type', 'department')
            ->where('department_id', $id)
            ->where('year', date('Y'))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $costCenters
        ]);
    }

    public function indexTransactionCreditRABGeneral(Request $request)
    {
        $query = CostCenter::where('type', 'department')
            ->orderBy('updated_at', 'desc')
            ->where('year', date('Y'));

        $requests = ExpenseRequest::where('status', 'finish')
            ->with(['costCenter', 'items', 'user'])
            ->where('category', 'department')
            ->orderBy('created_at', 'desc');

        // dd($requests->get());

        // sum total keseluruhan RAB dari semua department
        $sums = [
            'debit' => formatRupiah($query->sum('amount_debit')),
            'credit' => formatRupiah($query->sum('amount_credit')), // belum dihitung dari total pengajuan diterima
            'remaining' => formatRupiah($query->sum('amount_remaining')),
        ];

        $categories = CostCenterCategory::with(['costCenters' => function ($query) {
            $query->where('type', 'department')
                ->where('year', date('Y'));
        }])->get();

        $sums['categories'] = $categories->map(function ($category) {
            $total_debit = $category->costCenters->sum('amount_debit');

            return [
                'id' => $category->id,
                'name' => '(' . $category->code . ') ' . $category->name,
                'total_debit' => formatRupiah($total_debit)
            ];
        });

        if ($request->ajax()) {

        }

        // dd($results);

        return view('cost-center.transactions_rab_general_credit', compact('sums'));
    }

    private function generateCodeRef($departmentId, $request)
    {
        // Get last RAB Department
        $lastRAB = CostCenter::where('department_id', $departmentId)
            ->where('type', 'department')
            ->where('year', $request->year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRAB) {
            $explodedLastCodeRef = explode('/', $lastRAB->code_ref);
            $lastTransactionNumber = $explodedLastCodeRef[1];
            $currentTransactionNumber = str_pad((int) $lastTransactionNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $currentTransactionNumber = '0001';
        }

        // Create Code Ref
        $department = Department::where('id', $departmentId)->first();
        $costCenterCategory = CostCenterCategory::where('id', $request->category)->first();
        $monthIndex = str_pad($request->month, 2, '0', STR_PAD_LEFT);
        $codeRef = $department->code . '.' .  $costCenterCategory->code
            . '.' . $monthIndex . '-' . $request->year . '/' . $currentTransactionNumber;

        return $codeRef;
    }

    // * Detail Transaksi Cost Center per Divisi
    public function indexDepartment($id)
    {
        try {
            $query = CostCenter::where('type', 'department')
                ->orderBy('updated_at', 'desc')
                ->where('year', date('Y'))
                ->where('department_id', $id);

            // sum total keseluruhan RAB dari semua department
            $sums = [
                'debit' => formatRupiah($query->sum('amount_debit')),
                'credit' => formatRupiah($query->sum('amount_credit')), // belum dihitung dari total pengajuan diterima
                'remaining' => formatRupiah($query->sum('amount_remaining')),
            ];

            $categories = CostCenterCategory::with(['costCenters' => function ($query) use ($id) {
                $query->where('type', 'department')
                    ->where('department_id', $id)
                    ->where('year', date('Y'));
            }])->get();

            $sums['categories'] = $categories->map(function ($category) {
                $total_debit = $category->costCenters->sum('amount_debit');

                return [
                    'id' => $category->id,
                    'name' => '(' . $category->code . ') ' . $category->name,
                    'total_debit' => formatRupiah($total_debit)
                ];
            });

            $department = Department::where('id', $id)->first();

            return view('cost-center.transactions_rab_in_department', compact('sums', 'department'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    // view data project
    public function indexDepartmentProjects(Request $request, $id)
    {
        try {
            $department = Department::where('id', $id)->first();

            if (!$department) {
                return redirect()->back()->with([
                    'pesan' => 'Divisi tidak ditemukan',
                    'level-alert' => 'alert-danger'
                ]);
            }

            $projects = Project::whereHas('costCenters', function ($query) use ($department) {
                $query->where('type', 'project')
                    ->where('department_id', $department->id)
                    ->where('year', date('Y'));
            })
                ->where('status', 'Finished')
                ->with(['financial', 'finalization', 'profit', 'costCenters']);

            // get total debit, credit, remaining, and yearly margin
            $getProjects = clone $projects->get();
            $totalDebit = 0;
            $totalCredit = 0;
            $totalRemaining = 0; // belum fix
            $totalYearlyMargin = 0; // belum fix

            foreach ($getProjects as $project) {
                foreach ($project->costCenters as $costCenter) {
                    $totalDebit += $costCenter->amount_debit;
                    $totalCredit += $costCenter->amount_credit;
                }

                $margin = $project->financial->margin;
                $totalYearlyMargin += $margin;
            }

            $totalAmount = [
                'total_debit' => formatRupiah($totalDebit),
                'total_credit' => formatRupiah($totalCredit),
                'total_remaining' => formatRupiah($totalDebit - $totalCredit),
                'total_yearly_margin' => formatRupiah($totalYearlyMargin)
            ];

            if ($request->ajax()) {
                return DataTables::of($projects)
                    ->addIndexColumn()
                    ->addColumn('title', function ($item) {
                        return '<a href="' . route('cost-center.departments.projects.budget-plan', $item->id) . '">' . $item->name . '</a>';
                    })
                    ->addColumn('job_value', fn($item) => formatRupiah($item->financial?->job_value))
                    ->addColumn('rab', fn($item) => formatRupiah($item->costCenters?->sum('amount_debit')))
                    ->addColumn('margin', fn($item) => formatRupiah($item->financial?->margin))
                    ->addColumn('sp2d', fn($item) => formatRupiah($item->financial?->sp2d_amount))
                    ->addColumn('ppn', function ($item) {
                        $vatInNumberFormat = number_format($item->financial?->vat_percent, 2);
                        $vatValue = $item->financial?->sp2d_amount * ($vatInNumberFormat / 100);
                        return formatRupiah($vatValue);
                    })
                    ->addColumn('pph', function ($item) {
                        $taxInNumberFormat = number_format($item->financial?->tax_percent, 2);
                        $taxValue = $item->financial?->sp2d_amount * ($taxInNumberFormat / 100);
                        return formatRupiah($taxValue);
                    })
                    ->addColumn('pic', fn($item) => $item->pic?->name)
                    ->addColumn('status', fn($item) => $item->status)
                    ->rawColumns(['title'])
                    ->make(true);
            }

            return view('cost-center.transactions_rab_in_department_projects', compact('department', 'totalAmount'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    public function getProjectProfitTable($id)
    {
        try {
            $profit = ProjectProfit::where('project_id', $id)->first();

            if (!$profit) {
                return DataTables::of([])->make(true); // kosong
            }

            $projectFinancial = ProjectFinancial::find($id);
            $totalProjectValue = $projectFinancial->margin;

            $data = [
                [
                    'name' => 'Perusahaan',
                    'percent' => $profit->percent_company,
                    'idr' => $totalProjectValue * ($profit->percent_company / 100),
                ],
                [
                    'name' => 'Penyusutan',
                    'percent' => $profit->percent_depreciation,
                    'idr' => $totalProjectValue * ($profit->percent_depreciation / 100),
                ],
                [
                    'name' => 'Kas Departemen',
                    'percent' => $profit->percent_cash_department,
                    'idr' => $totalProjectValue * ($profit->percent_cash_department / 100),
                ],
                [
                    'name' => 'Bonus Tim',
                    'percent' => $profit->percent_team_bonus,
                    'idr' => $totalProjectValue * ($profit->percent_team_bonus / 100),
                ],
            ];

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('percent', fn($row) => (int) $row['percent'] . '%')
                ->editColumn('idr', fn($row) => formatRupiah($row['idr']))
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * * get RAB untuk Project tertentu
     */
    public function indexDepartmentProjectBudgetPlan(Request $request, $id)
    {
        try {
            $project = Project::where('id', $id)
                ->with(['department'])
                ->first();

            if (!$project) {
                return redirect()->back()->with([
                    'pesan' => 'Project tidak ditemukan',
                    'level-alert' => 'alert-danger'
                ]);
            }

            $costCenters = CostCenter::where('type', 'project')
                ->where('project_id', $project->id)
                ->where('year', date('Y'));

            $projectMargin = ProjectFinancial::where('project_id', $project->id)->first()->margin;

            $totalAmount = [
                'total_debit' => formatRupiah($costCenters->sum('amount_debit')),
                'total_credit' => formatRupiah($costCenters->sum('amount_credit')),
                'total_remaining' => formatRupiah($costCenters->sum('amount_debit') - $costCenters->sum('amount_credit')),
                'total_yearly_margin' => formatRupiah($projectMargin),
            ];

            if ($request->ajax()) {
                return DataTables::of($costCenters)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($item) {
                        return Carbon::parse($item->created_at)->format('Y-m-d');
                    })
                    ->addColumn('name', fn($item) => $item->name)
                    ->addColumn('code_ref', fn($item) => $item->code_ref)
                    ->addColumn('debit', function ($item) {
                        return $item->amount_debit > 0 ? formatRupiah($item->amount_debit) : '-';
                    })
                    ->addColumn('credit', function ($item) {
                        return $item->amount_credit > 0 ? formatRupiah($item->amount_credit) : '-';
                    })
                    ->addColumn('detail', function ($item) {
                        return $item->detail;
                    })
                    ->rawColumns(['detail'])
                    ->make(true);
            }

            // initial value for some fields in modal add and edit
            $projectCostCenters = $costCenters->get();
            $initialValues = [
                'department' => $project->department,
                'months' => $this->getMonths(),
                'years' => $this->getYears(),
                'amount_remaining' => $costCenters->sum('amount_debit') - $costCenters->sum('amount_credit'),
                'project_cost_centers' => $projectCostCenters,
                'cost_center_categories' => CostCenterCategory::whereNot('id', 1)->get(),
            ];

            return view(
                'cost-center.transactions_rab_in_department_projects_budget_plan',
                compact('project', 'totalAmount', 'initialValues')
            );
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    /**
     * * get RAB untuk pengajuan per divisi
     * * dan return dalam bentuk JSON
     */
    public function getCostCentersProjectJSON($id)
    {
        try {
            $costCenters = CostCenter::where('type', 'project')
                ->where('project_id', $id)// avoid to get uang kas project
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'cost_centers' => $costCenters
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], $e->status);
        }
    }

    /**
     * * store RAB untuk Project tertentu
     */
    public function storeRABProject(Request $request, $id)
    {
        try {
            // generate code ref rab project
            $projectCostCenters = CostCenter::where('project_id', $id)
                ->where('type', 'project')
                ->with(['department', 'category'])
                ->orderBy('id', 'desc')
                ->get();

            $totalRemaining = $projectCostCenters->sum('amount_debit') - $projectCostCenters->sum('amount_credit');

            if ($totalRemaining < $request->nominal) {
                return redirect()->back()->with([
                    'pesan' => 'Nominal tidak boleh melebihi sisa saldo milik project',
                    'level-alert' => 'alert-danger'
                ]);
            }

            $codeRef = $this->generateCoreRefProject($projectCostCenters[0], $request);

            DB::beginTransaction();

            $insert = CostCenter::insert([
                'department_id' => $projectCostCenters[0]->department_id,
                'project_id' => $projectCostCenters[0]->project_id,
                'cost_center_category_id' => $projectCostCenters[0]->cost_center_category_id,
                'type' => 'project',
                'code_ref' => $codeRef,
                'name' => $request->name,
                'amount_credit' => $request->nominal,
                'month' => $request->month,
                'year' => $request->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($insert) {
                $totalRemaining = $projectCostCenters->sum('amount_debit')
                    - ($projectCostCenters->sum('amount_credit') + (float) $request->nominal);
                $note = $projectCostCenters->last()->detail
                    ? $projectCostCenters->last()->detail . '<hr style="margin:0"/>' : '';
                $note .= '<small><span class="text-danger">Dikurangi: '
                    . formatRupiah((int) $request->nominal) . '</span>'
                    . '<br/>Untuk RAB baru: ' . $codeRef
                    . '<br/>Oleh: ' . Auth::user()->username
                    . '<br/>Tanggal: ' . date('d-m-Y')
                    . '</small>';

                $updateRemaining = CostCenter::where('id', $projectCostCenters->last()->id)
                    ->update([
                        'amount_remaining' => $totalRemaining,
                        'detail' => $note,
                        'updated_at' => now(),
                    ]);

                if ($updateRemaining) {
                    DB::commit();
                    return redirect()->back()->with([
                        'pesan' => 'RAB baru untuk project berhasil ditambahkan',
                        'level-alert' => 'alert-success'
                    ]);
                }
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    public function updateRABProject(Request $request, $id)
    {
        try {
            $request->validate([
                'update_type' => 'required|in:change_amount,change_to_new_rab',
            ]);

            DB::beginTransaction();

            $costCenter = CostCenter::where('id', $id)->first();

            if (!$costCenter) {
                return redirect()->back()->with([
                    'pesan' => 'RAB tidak ditemukan',
                    'level-alert' => 'alert-danger'
                ]);
            }

            if ($request->update_type == 'change_amount') {
                $request->validate([
                    'new_nominal' => 'required',
                ]);

                // create change + notes
                $note = $costCenter->detail ? $costCenter->detail . '<hr style="margin:0"/>' : '';
                $note .= '<small><span class="text-success">RAB ditambah: '
                    . formatRupiah((int) $request->new_nominal)
                    . '</span><br/>Oleh: ' . Auth::user()->username
                    . '<br/>Tanggal: ' . date('d-m-Y')
                    . '</small>';

                /**
                 * Cek dulu code ref, jika hanya ada satu nomor urut transaksi
                 * berarti untuk uang kas project
                 */
                $explodedCodeRef = explode('/', $costCenter->code_ref);

                if (array_key_exists(2, $explodedCodeRef)) {
                    /**
                     * RAB project lainnya yang bukan uang kas
                     * Credit di sini berarti limit
                     */
                    $currentCredit = $costCenter->amount_credit + (int) $request->new_nominal;
                    $currentRemaining = $costCenter->amount_remaining + (int) $request->new_nominal;
                    $updateRAB = CostCenter::where('id', $id)->update([
                        'name' => $request->name,
                        'amount_credit' => $currentCredit,
                        'amount_remaining' => $currentRemaining,
                        'detail' => $note,
                        'updated_at' => now(),
                    ]);

                    /**
                     * Update juga amount debit uang kas
                     */
                    $cashCostCenter = CostCenter::where('project_id', $costCenter->project_id)->first();
                    $currentDebit = $cashCostCenter->amount_debit + (int) $request->new_nominal;
                    $cashCostCenter->amount_debit = $currentDebit;
                    $cashCostCenter->save();
                } else {
                    /**
                     * RAB project uang kas
                     */
                    $currentDebit = $costCenter->amount_debit + (int) $request->new_nominal;
                    $currentRemaining = $costCenter->amount_remaining + (int) $request->new_nominal;
                    $updateRAB = CostCenter::where('id', $id)->update([
                        'name' => $request->name,
                        'amount_debit' => $currentDebit,
                        'amount_remaining' => $currentRemaining,
                        'detail' => $note,
                        'updated_at' => now(),
                    ]);
                }

                if ($updateRAB) {
                    DB::commit();
                    return redirect()->back()->with([
                        'pesan' => 'Nominal RAB berhasil diperbarui',
                        'level-alert' => 'alert-success'
                    ]);
                }
            }

            if ($request->update_type == 'change_to_new_rab') {
                $request->validate([
                    'department' => 'required|exists:departments,id',
                    'target' => 'required|exists:cost_centers,id',
                    'name_new' => 'required|string|max:255',
                    'nominal_new_rab' => 'required|numeric',
                    'month' => 'required',
                    'year' => 'required',
                ]);

                if ($costCenter->amount_remaining < $request->nominal_new_rab) {
                    return redirect()->back()->with([
                        'pesan' => 'Sisa saldo RAB awal tidak mencukupi',
                        'level-alert' => 'alert-danger'
                    ]);
                }

                $lastCostCenterProject = CostCenter::where('project_id', $costCenter->project_id)
                    ->orderBy('id', 'desc')
                    ->first();
                $codeRef = $this->generateCoreRefProject($lastCostCenterProject, $request);
                $dataRAB = [
                    'department_id' => $costCenter->department_id,
                    'project_id' => $costCenter->project_id,
                    'cost_center_category_id' => $lastCostCenterProject->cost_center_category_id,
                    'type' => 'project',
                    'code_ref' => $codeRef,
                    'name' => $request->name_new,
                    'amount_credit' => $request->nominal_new_rab,
                    'amount_remaining' => $request->nominal_new_rab,
                    'month' => $request->month,
                    'year' => $request->year,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // update keterangan RAB awal
                $note = $costCenter->detail ? $costCenter->detail . '<hr style="margin:0"/>' : '';
                $note .= '<small><span class="text-danger">RAB dikurangi: -'
                    . formatRupiah((int) $request->nominal_new_rab)
                    . '</span><br/>Untuk RAB Baru: ' . $codeRef
                    . '<br/>Oleh: ' . Auth::user()->username
                    . '<br/>Tanggal: ' . date('d-m-Y')
                    . '</small>';
                $updateRAB = CostCenter::where('id', $id)->update([
                    'amount_credit' => $costCenter->amount_credit - (int) $request->nominal_new_rab,
                    'amount_remaining' => $costCenter->amount_remaining - (int) $request->nominal_new_rab,
                    'detail' => $note,
                    'updated_at' => now(),
                ]);

                $insertRAB = CostCenter::insert($dataRAB);

                if ($insertRAB && $updateRAB) {
                    DB::commit();
                    return redirect()->back()->with([
                        'pesan' => 'RAB berhasil dibagi ke RAB baru',
                        'level-alert' => 'alert-success'
                    ]);
                }
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    private function generateCoreRefProject($lastRABProject, $request)
    {
        $explodedLastCodeRef = explode('/', $lastRABProject->code_ref);

        if (array_key_exists(2, $explodedLastCodeRef)) {
            $currentTransactionNumber = str_pad((int) $explodedLastCodeRef[2] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $lastTransactionNumber = $explodedLastCodeRef[1];
            $currentTransactionNumber = str_pad((int) $lastTransactionNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        $department = $lastRABProject->department;
        $costCenterCategory = $lastRABProject->category;
        $monthIndex = str_pad($request->month, 2, '0', STR_PAD_LEFT);
        $codeRef = $department->code . '.' .  $costCenterCategory->code
            . '.' . $monthIndex . '-' . $request->year . '/' . $explodedLastCodeRef[1]
            . '/' . $currentTransactionNumber;

        return $codeRef;
    }

    // view data kas
    public function indexDepartmentRequests(Request $request, $id)
    {
        try {
            $department = Department::where('id', $id)->first();

            if (!$department) {
                return redirect()->back()->with([
                    'pesan' => 'Divisi tidak ditemukan',
                    'level-alert' => 'alert-danger'
                ]);
            }

            if ($request->ajax()) {
                $generalRequests = CostCenter::where('type', 'department')
                    ->whereHas('expenses', function ($query) {
                        $query->with(['user', 'items']);
                    })
                    ->where('department_id', $department->id)
                    ->where('year', date('Y'))
                    ->with(['department', 'expenses']);
            }

            return view('cost-center.transactions_rab_in_department_general', compact('department'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }
}
