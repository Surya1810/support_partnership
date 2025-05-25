<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\Project;
use App\Models\CostCenterSub;
use App\Models\CostCenterCategories;
use App\Imports\CostCenterImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class CostCenterController extends Controller
{
    public function index()
    {
        $costCenters = CostCenter::with('department')->get();

        // Array config divisi dengan department_id sesuai database
        $divisionConfigs = [
            ['name' => 'Procurement', 'color' => 'blue', 'department_id' => 2, 'prefix' => 'Procurement', 'has_data' => true],
            ['name' => 'Konstruksi', 'color' => 'orange', 'department_id' => 3, 'prefix' => 'Construction', 'has_data' => true],
            ['name' => 'Teknologi', 'color' => 'indigo', 'department_id' => 4, 'prefix' => 'Technology', 'has_data' => true],
            ['name' => 'Rumah Tangga', 'color' => 'teal', 'department_id' => 1, 'prefix' => 'RumahTangga', 'has_data' => false],
        ];

        $calculateTotals = function ($collection) {
            return [
                'debit' => $collection->sum('debit_amount'),
                'kredit' => $collection->sum('credit_amount'),
                'saldo' => $collection->sum('balance_amount'),
                'pendapatan' => $collection->sum('current_year_revenue_amount'),
            ];
        };

        // Loop buat data divisions lengkap
        $divisions = collect($divisionConfigs)->map(function ($config) use ($costCenters, $calculateTotals) {
            $filteredCostCenters = $costCenters->where('department_id', $config['department_id']);
            $totals = $calculateTotals($filteredCostCenters);

            return [
                'name' => $config['name'],
                'color' => $config['color'],
                'department_id' => $config['department_id'],
                'data' => $config['has_data'] ? $filteredCostCenters : null,
                'debit' => $totals['debit'],
                'kredit' => $totals['kredit'],
                'saldo' => $totals['saldo'],
                'pendapatan' => $totals['pendapatan'],
            ];
        });

        // Total umum untuk semua cost centers
        $totalGeneral = $calculateTotals($costCenters);

        return view('cost-center.index', [
            'divisions' => $divisions,
            'totalDebitGeneral' => $totalGeneral['debit'],
            'totalKreditGeneral' => $totalGeneral['kredit'],
            'totalSaldoGeneral' => $totalGeneral['saldo'],
            'totalPendapatanGeneral' => $totalGeneral['pendapatan'],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|min:3',
            'amount' => 'required|numeric'
        ]);

        // create code
        $countTotalCostCenter = CostCenter::where('department_id', $request->department_id)->count();
        $currentYear = Carbon::now()->format('y');
        $codeCostCenter = str_pad($request->department_id, 2, '0', STR_PAD_LEFT)
            . '-' . $currentYear
            . '-' . $countTotalCostCenter + 1;

        DB::beginTransaction();
        $create = CostCenter::create([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => $codeCostCenter,
            'amount' => $request->amount
        ]);

        if ($create) {
            DB::commit();
            return redirect()->back()->with(['pesan' => "Cost Center Berhasil Ditambahkan", 'level-alert' => 'alert-success']);
        }

        DB::rollBack();
        return redirect()->back()->with(['pesan' => "Cost Center Gagal Ditambahkan", 'level-alert' => 'alert-danger']);
    }

    public function show($id)
    {
        $costCenter = CostCenter::with('subs')->where('id', $id)->first();

        if ($costCenter->exists()) {
            return response()->json($costCenter);
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'Cost center tidak ditemukan'
        ], 404);
    }

    public function update(Request $request, $id)
    {
        $costCenter = CostCenter::where('id', $id)->first();

        if (!$costCenter->exists()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Cost center tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        $update = $costCenter->update([
            'name' => $request->name,
            'amount' => $request->amount
        ]);

        if ($update) {
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Updated successfully'
            ]);
        }

        DB::rollBack();
        return response()->json([
            'status' => 'fail',
            'message' => 'Cost center gagal diperbarui'
        ], 500);
    }

    public function delete($id)
    {
        $costCenter = CostCenter::where('id', $id)->first();

        if ($costCenter->exists()) {
            DB::beginTransaction();
            $delete = CostCenter::where('id', $id)->delete();

            if ($delete) {
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Cost center berhasil dihapus'
                ]);
            }

            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => 'Cost center gagal dihapus'
            ], 500);
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'Cost center tidak ditemukan'
        ], 404);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls',
            'department_id' => 'required|numeric'
        ]);

        Excel::import(new CostCenterImport($request->department_id), $request->file('import_file'));

        return redirect()->back()->with(['pesan' => "Import data cost center berhasil", 'level-alert' => 'alert-success']);
    }

    public function storeSub(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:cost_centers,id',
            'name' => 'required',
            'amount' => 'required|numeric',
        ]);

        $parent = CostCenter::find($request->parent_id);

        $parent->subs()->create([
            'cost_center_id' => $request->parent_id,
            'name' => $request->name,
            'amount' => $request->amount
        ]);

        return response()->json(['status' => 'success']);
    }

    public function showSub($id)
    {
        return CostCenterSub::findOrFail($id);
    }

    public function updateSub(Request $request, $id)
    {
        $sub = CostCenterSub::findOrFail($id);
        $sub->update([
            'name' => $request->name,
            'amount' => $request->amount
        ]);

        return response()->json(['status' => 'success']);
    }

    public function destroySub($id)
    {
        $sub = CostCenterSub::findOrFail($id);
        $sub->delete();
        return response()->json(['status' => 'success']);
    }

    public function showTransactions($departmentId)
    {

        $department = Department::findOrFail($departmentId);
        $categories = CostCenterCategories::all()->keyBy('code');
        $costCenters = CostCenter::where('department_id', $departmentId)->get();
        $totalDebit = $costCenters->sum('debit_amount');
        $totalKredit = $costCenters->sum('credit_amount');
        $totalSaldo = $costCenters->sum('balance_amount');
        $totalPendapatan = $costCenters->sum('current_year_revenue_amount');

        // Group per kategori code dan hitung total per kategori
        $categoryData = [];

        foreach ($categories as $code => $category) {
            if ($code === 'KS') {
                // Skip kategori KS
                continue;
            }

            $filtered = $costCenters->where('category_code', $code);
            $amount = $filtered->sum('balance_amount');

            $categoryData[$code] = [
                'label' => $category->name,
                'amount' => $amount,
            ];
        }


        return view('cost-center.transaction', [
            'departmentName' => $department->name,
            'totalDebit' => $totalDebit,
            'totalKredit' => $totalKredit,
            'totalSaldo' => $totalSaldo,
            'totalPendapatan' => $totalPendapatan,
            'categoryData' => $categoryData,
        ]);
    }

    public function showProject(Request $request)
    {
        if ($request->ajax()) {
            $projects = Project::with(['department', 'user']);

            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('department', fn($row) => $row->department->name ?? '-')
                ->addColumn('pic', fn($row) => $row->user->name ?? '-')
                ->addColumn('rab_count', fn($row) => 0)
                ->addColumn('aksi', fn($row) => '
                <a href="' . route('cost-center.rab', $row->id) . '" class="text-blue-600 hover:text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
            ')
                ->rawColumns(['aksi'])
                ->make(true);
        }

        // Ambil total dari cost_centers
        $totalDebit = \App\Models\CostCenter::sum('debit_amount');
        $totalKredit = \App\Models\CostCenter::sum('credit_amount');
        $totalSaldo = \App\Models\CostCenter::sum('balance_amount');

        return view('cost-center.project', compact('totalDebit', 'totalKredit', 'totalSaldo'));
    }

    public function rab(Request $request)
    {
        if ($request->ajax()) {
            $query = CostCenterSub::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('pic', fn() => '<span class="text-gray-500 italic">Belum Diisi</span>')
                ->addColumn('note', fn() => '<span class="text-gray-500 italic">-</span>')
                ->addColumn('history', fn($row) => '<button class="bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded text-sm">History</button>')
                ->addColumn('aksi', function ($row) {
                    return '<button class="bg-yellow-400 hover:bg-yellow-500 px-2 py-1 rounded text-sm mr-1">Edit</button>' .
                        '<button class="bg-red-500 hover:bg-red-600 px-2 py-1 rounded text-sm text-white">Delete</button>';
                })
                ->editColumn('amount', fn($row) => 'Rp' . number_format($row->amount, 0, ',', '.'))
                ->editColumn('created_at', fn($row) => $row->created_at ? $row->created_at->format('d-m-Y') : '-')
                ->rawColumns(['pic', 'note', 'history', 'aksi'])
                ->make(true);
        }

        return view('cost-center.rab');
    }

    public function saldo(Request $request)
    {
        if ($request->ajax()) {
            $data = Project::with(['pic', 'kodeRef'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('debit', fn($row) => $row->debit ? 'Rp' . number_format($row->debit, 0, ',', '.') : '-')
                ->editColumn('kredit', fn($row) => $row->kredit ? 'Rp' . number_format($row->kredit, 0, ',', '.') : '-')
                ->editColumn('kwitansi', fn($row) => $row->kwitansi ? 'Upload' : '-')
                ->addColumn('pic', fn($row) => $row->pic->name ?? '-')
                ->addColumn('kode_ref', fn($row) => $row->kodeRef->code ?? '-')
                ->rawColumns(['debit', 'kredit', 'kwitansi'])
                ->make(true);
        }

        // Perhitungan ringkasan
        $costCenters = CostCenter::all();
        $totalDebit = $costCenters->sum('debit_amount');
        $totalKredit = $costCenters->sum('credit_amount');
        $totalSaldo = $costCenters->sum('balance_amount');
        $totalPendapatan = $costCenters->sum('current_year_revenue_amount');

        return view('cost-center.saldo', compact('totalDebit', 'totalKredit', 'totalSaldo', 'totalPendapatan'));
    }
}
