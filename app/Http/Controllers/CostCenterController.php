<?php

namespace App\Http\Controllers;

use App\Models\CostCenterCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

use App\Models\Department;
use App\Models\CostCenter;
use App\Models\Project;

class CostCenterController extends Controller
{
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
        $costCenterCategories = CostCenterCategory::all();
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $years = [date('Y'), date('Y') + 1];

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
                ->addColumn('debit', fn($item) => formatRupiah($item->amount_debit))
                ->addColumn('year', fn($item) => $item->year)
                ->addColumn('detail', fn($item) => $item->detail)
                ->rawColumns(['detail'])
                ->make(true);
        }

        $sums = [
            'debit' => formatRupiah($query->sum('amount_debit')),
            'credit' => formatRupiah($query->sum('amount_credit')),
            'remaining' => formatRupiah($query->sum('amount_remaining')),
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
            dd($e->getMessage());
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
                $note .= '<small class="text-success">RAB ditambah: '
                    . formatRupiah((int) $request->new_nominal) . '</small>';
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
                    'name' => $request->name_new,
                    'amount_debit' => $request->nominal_new_rab,
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
                    . '</span><br/>Untuk RAB Baru: ' . $codeRef . '</small>';
                $updateRAB = CostCenter::where('id', $id)->update([
                    'amount_debit' => $costCenter->amount_debit - (int) $request->nominal_new_rab,
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

    public function indexTransactionCreditRABGeneral()
    {
        $query = CostCenter::where('type', 'department')
            ->orderBy('updated_at', 'desc')
            ->where('year', date('Y'));

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

        // dd($results);

        return view('cost-center.transactions_rab_general_credit', compact('sums'));
    }

    private function generateCodeRef($departmentId, $request)
    {
        // Get last RAB Department
        $lastRAB = CostCenter::where('department_id', $departmentId)
            ->where('cost_center_category_id', $request->category)
            ->orderBy('created_at', 'desc')
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

    public function indexDepartmentProjects(Request $request, $id) {
        try {
            $department = Department::where('id', $id)->first();

            if (!$department) {
                return redirect()->back()->with([
                    'pesan' => 'Divisi tidak ditemukan',
                    'level-alert' => 'alert-danger'
                ]);
            }

            if ($request->ajax()) {
                $projects = Project::whereHas('costCenters', function ($query) use ($department) {
                    $query->where('type', 'project')
                        ->where('department_id', $department->id)
                        ->where('year', date('Y'));
                    })
                    ->with(['financial', 'finalization', 'profit', 'costCenters']);

                return DataTables::of($projects)
                    ->addIndexColumn()
                    ->addColumn('title', fn ($item) => $item->name)
                    ->addColumn('job_value', fn ($item) => formatRupiah($item->financial?->job_value))
                    ->addColumn('rab', fn ($item) => formatRupiah($item->costCenters?->sum('amount_debit')))
                    ->addColumn('margin', fn ($item) => formatRupiah($item->financial?->margin))
                    ->addColumn('sp2d', fn ($item) => formatRupiah($item->financial?->sp2d_amount))
                    ->addColumn('ppn', fn ($item) => $item->financial?->vat_percent)
                    ->addColumn('pph', fn ($item) => $item->financial?->tax_percent)
                    ->addColumn('team_bonus', fn ($item) => $item->profit?->percent_team_bonus)
                    ->addColumn('depreciation', fn ($item) => $item->profit?->percent_depreciation)
                    ->addColumn('cash_department', fn ($item) => $item->profit?->percent_cash_department)
                    ->addColumn('pic', fn ($item) => $item->pic?->name)
                    ->addColumn('status', fn ($item) => $item->status)
                    ->make(true);
            }

            return view('cost-center.transactions_rab_in_department_projects', compact('department'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }
}
