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
    private function getMonths($month = null)
    {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        if ($month) {
            return $months[$month - 1];
        }
        return $months;
    }

    private function getYears()
    {
        $years = CostCenter::where('type', 'department')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->unique();

        if (count($years->unique()->toArray()) > 0) {
            return $years->push(date('Y') + 1)->unique()->toArray();
        }

        return [date('Y'), date('Y') + 1];
    }

    public function index()
    {
        $query = CostCenter::where('type', 'department')
            ->orderBy('updated_at', 'desc')
            ->where('year', date('Y'));

        // hitung pendapatan tahun berjalan
        $yearlyMargin = Project::whereHas('costCenters', function ($query) {
            $query->where('type', 'project')
                ->where('year', date('Y'));
        })
            ->where('status', 'Finished')
            ->with('financial')
            ->get()
            ->sum(function ($project) {
                return optional($project->financial)->margin ?? 0;
            });

        // untuk menampilkan total semua divisi
        $finishedRequests = ExpenseRequest::where('status', 'finish')
            ->where('category', 'department')
            ->whereHas('costCenter', function ($query) {
                $query->where('year', date('Y'));
            })
            ->with('items')
            ->get()
            ->map(function ($item) {
                return $item->items->sum('actual_amount');
            })
            ->values()
            ->toArray();

        $remainingAmount = 0;

        foreach ($finishedRequests as $request) {
            $remainingAmount += $request;
        }

        $expenseRequests = ExpenseRequest::where('status', 'finish')
            ->where('category', 'department')
            ->with('items')
            ->whereHas('costCenter', function ($query) {
                $query->where('year', date('Y'));
            })
            ->get()
            ->sum(function ($query) {
                return $query->items->sum('actual_amount');
            });

        $sums = [
            'debit' => formatRupiah($query->sum('amount_debit')),
            'credit' => formatRupiah($expenseRequests), // belum dihitung dari total pengajuan diterima
            'remaining' => formatRupiah($query->sum('amount_debit') - $expenseRequests),
            'yearly_margin' => formatRupiah($yearlyMargin)
        ];

        // hitung sum debit, credit, remainging
        // & tahun pendapatan berjalan per divisi
        $departmentIds = [1, 3, 5, 9];
        $year = date('Y');

        // Step: Hitung total debit, credit, remaining dari cost center type department
        $baseData = DB::table('departments as d')
            ->leftJoin('cost_centers as cc', function ($join) use ($year) {
                $join->on('d.id', '=', 'cc.department_id')
                    ->where('cc.type', 'department')
                    ->where('cc.year', $year);
            })
            ->whereIn('d.id', $departmentIds)
            ->groupBy('d.id', 'd.name')
            ->select(
                'd.id as department_id',
                'd.name as department_name',
                DB::raw('COALESCE(SUM(cc.amount_debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(cc.amount_credit), 0) as total_credit'),
                DB::raw('COALESCE(SUM(cc.amount_remaining), 0) as total_remaining')
            )
            ->get()
            ->keyBy('department_id');

        // Step: Ambil project_id unik yang punya cost center project tahun berjalan
        $projectIds = DB::table('cost_centers as cc')
            ->join('projects as p', 'cc.project_id', '=', 'p.id')
            ->where('cc.type', 'project')
            ->where('cc.year', $year)
            ->where('p.status', 'Finished')
            ->pluck('p.id')
            ->unique()
            ->values();

        // Step: Ambil total margin hanya untuk project unik tersebut
        $projectMargins = DB::table('projects as p')
            ->join('project_financials as pf', 'p.id', '=', 'pf.project_id')
            ->whereIn('p.id', $projectIds)
            ->whereIn('p.department_id', $departmentIds)
            ->groupBy('p.department_id')
            ->select(
                'p.department_id',
                DB::raw('SUM(pf.margin) as total_yearly')
            )
            ->pluck('total_yearly', 'department_id');

        $sums['departments'] = $baseData->map(function ($row) use ($projectMargins) {
            $total_yearly = $projectMargins[$row->department_id] ?? 0;
            $expenseRequests = ExpenseRequest::where('status', 'finish')
                ->where('category', 'department')
                ->whereHas('costCenter', function ($query) use ($row) {
                    $query->where('department_id', $row->department_id)
                        ->where('year', date('Y'));
                })
                ->with('items')
                ->get()
                ->sum(function ($query) {
                    return $query->items->sum('actual_amount');
                });

            return [
                'department_id' => $row->department_id,
                'department_name' => $row->department_name,
                'total_debit' => formatRupiah($row->total_debit),
                'total_credit' => formatRupiah($expenseRequests),
                'total_remaining' => formatRupiah($row->total_debit - $expenseRequests),
                'yearly_margin' => formatRupiah($total_yearly),
            ];
        })->values();

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
            ->when($request->has('fromYear') && $request->has('toYear'), function ($query) use ($request) {
                $query->whereBetween('year', [$request->fromYear, $request->toYear]);
            })
            ->when(
                $request->has('departmentFilter') && $request->filled('departmentFilter'),
                function ($query) use ($request) {
                    $query->where('department_id', $request->departmentFilter);
                }
            )
            ->when($request->has('filterMonth'), function ($query) use ($request) {
                $query->where('month', $request->filterMonth);
            });

        if ($request->ajax()) {
            $datatableQuery = clone $query;
            $datatableQuery = $datatableQuery->when(Auth::user()->role_id == 3, function ($query) {
                $query->where('department_id', Auth::user()->department_id);
            });

            return DataTables::of($datatableQuery)
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

        $expenseRequests = ExpenseRequest::where('status', 'finish')
            ->where('category', 'department')
            ->whereHas('costCenter', function ($query) {
                $query->where('year', date('Y'))
                    ->where('type', 'department');
            })
            ->with('items')
            ->get()
            ->sum(function ($query) {
                return $query->items->sum('actual_amount');
            });

        $sums = [
            'debit' => formatRupiah($query->sum('amount_debit')),
            'credit' => formatRupiah($expenseRequests),
            'remaining' => formatRupiah($query->sum('amount_debit') - $expenseRequests),
        ];

        $months = $this->getMonths();

        return view('cost-center.transactions_rab_general_debet', compact('departments', 'months', 'years', 'costCenterCategories', 'sums', 'months'));
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

            /**
             * Jika RAB baru bukanlah uang kas,
             * maka tambah nominalnya juga ke uang kas
             */
            if ($request->category != 1) {
                $cashCostCenter = CostCenter::where('cost_center_category_id', 1)
                    ->where('department_id', $departmentId)
                    ->where('year', $request->year)
                    ->where('month', $request->month)
                    ->first();

                if (!$cashCostCenter) {
                    $request->category = 1;
                    $note = '<small>Dibuat oleh: ' . Auth::user()->username
                        . '<br/>Tanggal: ' . date('d-m-Y') . '</small>'
                        . '<hr style="margin:0"/><small><span class="text-danger">RAB dikurangi: '
                        . formatRupiah((int) $request->nominal)
                        . '</span><br/>Untuk RAB Baru: ' . $codeRef
                        . '</span><br/>Oleh: ' . Auth::user()->username
                        . '<br/>Tanggal: ' . date('d-m-Y')
                        . '</small>';

                    $dataCash = [
                        'department_id' => $departmentId,
                        'cost_center_category_id' => $request->category,
                        'type' => 'department',
                        'code_ref' => $this->generateCodeRef($departmentId, $request),
                        'name' => 'Uang Kas Bulan ' . $this->getMonths($request->month),
                        'amount_debit' => $request->nominal,
                        'month' => $request->month,
                        'year' => $request->year,
                        'detail' => $note,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    CostCenter::insert($dataCash);
                } else {
                    // masukkan note ditambah rab baru
                    $note = $cashCostCenter->detail ? $cashCostCenter->detail . '<hr style="margin:0"/>' : '';
                    $note .= '<small><span class="text-success">RAB ditambah: '
                        . formatRupiah((int) $request->nominal)
                        . '</span><br/>Oleh: ' . Auth::user()->username
                        . '<br/>Tanggal: ' . date('d-m-Y')
                        . '</small>';

                    // masukkan note dikurangi rab baru
                    $note .= '<hr style="margin:0"/><small><span class="text-danger">RAB dikurangi: '
                        . formatRupiah((int) $request->nominal)
                        . '</span><br/>Untuk RAB Baru: ' . $codeRef
                        . '</span><br/>Oleh: ' . Auth::user()->username
                        . '<br/>Tanggal: ' . date('d-m-Y')
                        . '</small>';

                    $cashCostCenter->amount_debit += $request->nominal;
                    $cashCostCenter->detail = $note;
                    $cashCostCenter->save();
                }
            }

            $dataRAB = [
                'department_id' => $departmentId,
                'cost_center_category_id' => $request->category,
                'type' => 'department',
                'code_ref' => $this->generateCodeRef($departmentId, $request),
                'name' => $request->name,
                'amount_debit' => $request->category != 1 ? 0 : $request->nominal,
                'amount_credit' => $request->category == 1 ? 0 : $request->nominal,
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
                    'amount_debit' => $costCenter->cost_center_category == 1 ? $currentDebit : 0,
                    'amount_credit' => $costCenter->cost_center_category == 1 ? 0
                        : $costCenter->amount_credit + (int) $request->new_nominal,
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
                    'detail' => '<small>Dibuat oleh: ' . Auth::user()->username
                        . '<br/>Tanggal: ' . date('d-m-Y') . '</small>',
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
            ->with(['costCenter', 'items', 'user', 'department'])
            ->where('category', 'department')
            ->when(Auth::user()->role_id == 3, function ($query) {
                $query->where('department_id', Auth::user()->department_id);
            })
            ->when($request->filled('fromYear') && $request->filled('toYear'), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    Carbon::createFromDate($request->fromYear, 1, 1)->startOfYear(),
                    Carbon::createFromDate($request->toYear, 12, 31)->endOfYear()
                ]);
            })
            ->when(
                $request->filled('departmentFilter'),
                function ($query) use ($request) {
                    $query->where('department_id', $request->departmentFilter);
                }
            )
            ->orderBy('created_at', 'desc');

        // yearly margin dari project
        $yearlyMargin = Project::where('status', 'Finished')
            ->whereHas('costCenters', function ($query) {
                $query->where('type', 'project')
                    ->where('year', date('Y'));
            })
            ->with('financial')
            ->get()
            ->sum(function ($project) {
                return optional($project->financial)->margin ?? 0;
            });

        $expenseRequests = ExpenseRequest::where('status', 'finish')
            ->where('category', 'department')
            ->with('items')
            ->whereHas('costCenter', function ($query) {
                $query->where('year', date('Y'));
            })
            ->get()
            ->sum(function ($query) {
                return $query->items->sum('actual_amount');
            });

        // sum total keseluruhan RAB dari semua department
        $sums = [
            'debit' => formatRupiah($query->sum('amount_debit')),
            'credit' => formatRupiah($expenseRequests), // belum dihitung dari total pengajuan diterima
            'remaining' => formatRupiah($query->sum('amount_debit') - $expenseRequests),
            'yearly_margin' => formatRupiah($yearlyMargin)
        ];

        $categories = CostCenterCategory::with(['costCenters' => function ($query) {
            $query->where('type', 'department')
                ->where('year', date('Y'));
        }])->get();

        $sums['categories'] = $categories->map(function ($category) {
            $totalDebit = $category->costCenters->sum('amount_debit');
            $totalCredit = $category->costCenters->sum('amount_credit');

            return [
                'id' => $category->id,
                'code' => $category->code,
                'name' => '(' . $category->code . ') ' . $category->name,
                'total_debit' => formatRupiah($totalDebit),
                'total_credit' => formatRupiah($totalCredit),
            ];
        });

        if ($request->ajax()) {
            return DataTables::of($requests)
                ->addIndexColumn()
                ->addColumn('request_name', fn($item) => $item->title)
                ->addColumn('code_ref_request', fn($item) => $item->code_ref_request)
                ->addColumn('department', fn($item) => $item->department?->name)
                ->addColumn('user', fn($item) => $item->user?->name)
                ->addColumn('credit', fn($item) => formatRupiah($item->total_amount))
                ->addColumn('remaining', function ($item) {
                    return formatRupiah($item->total_amount - $item->items?->sum('actual_amount'));
                })
                ->addColumn('report_file', function ($item) {
                    return $item->report_file
                        ? '<a href="' . asset('storage/' . $item->report_file) . '" target="_blank" class="btn btn-sm btn-danger"><i class="fa fa-file-pdf"></i></a>'
                        : '-';
                })
                ->rawColumns(['report_file'])
                ->make(true);
        }

        // dd($requests->get());
        $years = $this->getYears();
        $departments = Department::all()->except([2, 4, 6, 7, 8]);

        return view('cost-center.transactions_rab_general_credit', compact('sums', 'years', 'departments'));
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
        if (Auth::user()->role_id == 3 && Auth::user()->department_id != $id) {
            return redirect()->back()->with([
                'pesan' => 'Anda tidak memiliki hak untuk mengakses halaman tersebut',
                'level-alert' => 'alert-danger'
            ]);
        }
        try {
            $query = CostCenter::where('type', 'department')
                ->orderBy('updated_at', 'desc')
                ->where('year', date('Y'))
                ->where('department_id', $id);

            // sum total keseluruhan RAB dari semua department
            $yearlyMargin = Project::whereHas('costCenters', function ($query) use ($id) {
                $query->where('type', 'project')
                    ->where('year', date('Y'));
            })
                ->where('status', 'Finished')
                ->where('department_id', $id)
                ->with('financial')
                ->get()
                ->sum(function ($project) {
                    return optional($project->financial)->margin ?? 0;
                });

            $expenseRequests = ExpenseRequest::where('status', 'finish')
                ->where('category', 'department')
                ->where('department_id', $id)
                ->with('items')
                ->whereHas('costCenter', function ($query) {
                    $query->where('year', date('Y'));
                })
                ->get()
                ->sum(function ($query) {
                    return $query->items->sum('actual_amount');
                });

            $sums = [
                'debit' => formatRupiah($query->sum('amount_debit')),
                'credit' => formatRupiah($expenseRequests), // belum dihitung dari total pengajuan diterima
                'remaining' => formatRupiah($query->sum('amount_debit') - $expenseRequests),
                'yearly_margin' => formatRupiah($yearlyMargin)
            ];

            $categories = CostCenterCategory::with(['costCenters' => function ($query) use ($id) {
                $query->where('type', 'department')
                    ->where('department_id', $id)
                    ->where('year', date('Y'));
            }])->get();

            $sums['categories'] = $categories->map(function ($category) {
                $total_debit = $category->costCenters->sum('amount_debit');
                $total_credit = $category->costCenters->sum('amount_credit');

                return [
                    'id' => $category->id,
                    'code' => $category->code,
                    'name' => '(' . $category->code . ') ' . $category->name,
                    'total_debit' => formatRupiah($total_debit),
                    'total_credit' => formatRupiah($total_credit),
                ];
            });

            $department = Department::where('id', $id)->first();

            return view('cost-center.transactions_rab_in_department', compact('sums', 'department'));
        } catch (\Exception $e) {
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
            $projectIds = $projects->pluck('id')->toArray();

            $totalDebit = 0;
            $totalCredit = 0;
            $totalRemaining = 0;
            $totalYearlyMargin = 0;
            $totalCompany = 0;
            $totalDepreciation = 0;
            $totalCashDepartment = 0;
            $totalTeamBonus = 0;
            $totalSP2D = 0;
            $totalVAT = 0;
            $totalTAX = 0;

            foreach ($projects->get() as $project) {
                foreach ($project->costCenters as $costCenter) {
                    $totalDebit += $costCenter->amount_debit;
                    $totalCredit += $costCenter->amount_credit;
                }

                $financial = $project->financial;
                $profit = $project->profit;

                if ($financial) {
                    $margin = $financial->margin;
                    $totalYearlyMargin += $margin;

                    // SP2D
                    $sp2d = $financial->sp2d_amount;
                    $totalSP2D += $sp2d;

                    // VAT (PPN)
                    $vatPercent = (float) $financial->vat_percent;
                    $vat = $financial->job_value * ($vatPercent / (100 + $vatPercent));
                    $totalVAT += $vat;

                    // TAX (PPh)
                    $taxPercent = (float) $financial->tax_percent;
                    $tax = ($financial->job_value - $vat) * ($taxPercent / 100);
                    $totalTAX += $tax;

                    if ($profit) {
                        $totalCompany += $margin * ($profit->percent_company / 100);
                        $totalDepreciation += $margin * ($profit->percent_depreciation / 100);
                        $totalCashDepartment += $margin * ($profit->percent_cash_department / 100);
                        $totalTeamBonus += $margin * ($profit->percent_team_bonus / 100);
                    }
                }
            }

            // sum total actual amount of expense requests
            $actualAmountRequests = ExpenseRequest::where('status', 'finish')
                ->where('department_id', $department->id)
                ->whereIn('project_id', $projectIds)
                ->with('items')
                ->get()
                ->sum(function ($request) {
                    return $request->items()->sum('actual_amount');
                });

            $totalAmount = [
                'total_debit' => formatRupiah($totalDebit),
                'total_credit' => formatRupiah($totalCredit),
                'total_remaining' => formatRupiah($totalDebit - $actualAmountRequests),
                'total_actual_amount' => formatRupiah($actualAmountRequests),
                'total_yearly_margin' => formatRupiah($totalYearlyMargin),
                'total_company' => formatRupiah($totalCompany),
                'total_depreciation' => formatRupiah($totalDepreciation),
                'total_cash_department' => formatRupiah($totalCashDepartment),
                'total_team_bonus' => formatRupiah($totalTeamBonus),
                'total_sp2d' => formatRupiah($totalSP2D),
                'total_vat' => formatRupiah($totalVAT),
                'total_tax' => formatRupiah($totalTAX),
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
                        $vatInNumberFormat = (float) $item->financial?->vat_percent;
                        $vatValue = $item->financial?->job_value * ($vatInNumberFormat / 100);
                        return formatRupiah($vatValue);
                    })
                    ->addColumn('pph', function ($item) {
                        $taxInNumberFormat = (float) $item->financial?->tax_percent;
                        $taxValue = $item->financial?->job_value * ($taxInNumberFormat / 100);
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

            // sum actual amount atau pengajuan terealisasi
            $actualAmountRequests = ExpenseRequest::where('project_id', $id)
                ->where('status', 'finish')
                ->with('items')
                ->get()
                ->sum(function ($request) {
                    return $request->items()->sum('actual_amount');
                });

            $totalAmount = [
                'total_debit' => formatRupiah($costCenters->sum('amount_debit')),
                'total_actual_amount' => formatRupiah($actualAmountRequests),
                'total_credit' => formatRupiah($costCenters->sum('amount_credit')),
                'total_remaining' => formatRupiah($costCenters->sum('amount_debit') - $actualAmountRequests),
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
                ->where('project_id', $id) // avoid to get uang kas project
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
            ], 500);
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

            $query = CostCenter::where('type', 'department')
                ->where('department_id', $id)
                ->orderBy('updated_at', 'desc')
                ->where('year', date('Y'));

            // yearly margin dari project
            $yearlyMargin = Project::where('status', 'Finished')
                ->whereHas('costCenters', function ($query) {
                    $query->where('type', 'project')
                        ->where('year', date('Y'));
                })
                ->with('financial')
                ->get()
                ->sum(function ($project) {
                    return optional($project->financial)->margin ?? 0;
                });

            $expenseRequests = ExpenseRequest::where('status', 'finish')
                ->where('category', 'department')
                ->with('items')
                ->whereHas('costCenter', function ($query) {
                    $query->where('year', date('Y'));
                })
                ->get()
                ->sum(function ($query) {
                    return $query->items->sum('actual_amount');
                });

            // sum total keseluruhan RAB dari semua department
            $sums = [
                'debit' => formatRupiah($query->sum('amount_debit')),
                'credit' => formatRupiah($expenseRequests),
                'remaining' => formatRupiah($query->sum('amount_debit') - $expenseRequests),
                'yearly_margin' => formatRupiah($yearlyMargin)
            ];

            if ($request->ajax()) {
                $requests = ExpenseRequest::whereIn('status', ['finish', 'checking', 'reported'])
                    ->with(['costCenter', 'items', 'user', 'department'])
                    ->whereHas('costCenter', function ($query) {
                        $query->where('year', date('Y'));
                    })
                    ->where('category', 'department')
                    ->where('department_id', $department->id)
                    ->orderBy('created_at', 'desc');

                return DataTables::of($requests)
                    ->addIndexColumn()
                    ->addColumn('date', fn($item) => $item->use_date->format('Y-m-d'))
                    ->addColumn('title', fn($item) => $item->title)
                    ->addColumn('code_ref_request', fn($item) => $item->code_ref_request)
                    ->addColumn('user', fn($item) => $item->user->name)
                    ->addColumn('limit', fn($item) => formatRupiah($item->costCenter->amount_credit))
                    ->addColumn('total_amount', fn($item) => formatRupiah($item->total_amount))
                    ->addColumn('actual_amount', fn($item) => formatRupiah($item->items->sum('actual_amount')))
                    ->addColumn('remaining', function ($item) {
                        if ($item->status == 'finish') {
                            return formatRupiah($item->total_amount - $item->items->sum('actual_amount'));
                        }
                        return '-';
                    })
                    ->addColumn('report_file', function ($item) {
                        return $item->report_file ? '<a class="btn btn-sm btn-danger" href="' . asset('storage/' . $item->report_file) . '" target="_blank"><i class="fa fa-file-pdf"></i></a>' : '-';
                    })
                    ->addColumn('status', fn($item) => $item->status)
                    ->rawColumns(['report_file'])
                    ->make(true);
            }

            return view('cost-center.transactions_rab_in_department_general', compact('department', 'sums'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    public function getProjectBudgetPlanRequestsTable(Request $request, $projectId, $costCenterId)
    {
        try {
            if ($request->ajax()) {
                $requests = ExpenseRequest::where('project_id', $projectId)
                    ->whereIn('status', ['finish', 'checking', 'reported'])
                    ->where('cost_center_id', $costCenterId)
                    ->where('category', 'project')
                    ->with(['user', 'items']);

                return DataTables::of($requests)
                    ->addIndexColumn()
                    ->addColumn('date', fn($item) => $item->use_date->format('Y-m-d'))
                    ->addColumn('title', fn($item) => $item->title)
                    ->addColumn('code', fn($item) => $item->code_ref_request)
                    ->addColumn('user', fn($item) => $item->user?->name)
                    ->addColumn('credit', fn($item) => formatRupiah($item->total_amount))
                    ->addColumn('used_amount', fn($item) => formatRupiah($item->items?->sum('actual_amount')))
                    ->addColumn('report_file', function ($item) {
                        if ($item->report_file) {
                            return '<a href="' . asset('storage/' . $item->report_file) . '" target="_blank" class="btn btn-danger btn-sm"><i class="fa fa-file-pdf"></i></a>';
                        }
                        return '-';
                    })
                    ->addColumn('status', fn($item) => $item->status)
                    ->rawColumns(['report_file'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
