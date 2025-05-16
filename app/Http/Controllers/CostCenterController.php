<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Imports\CostCenterImport;
use Maatwebsite\Excel\Facades\Excel;

class CostCenterController extends Controller
{
    public function index()
    {
        $costCentersProcurement = CostCenter::where('department_id', 1)->get();
        $costCentersConstruction = CostCenter::where('department_id', 3)->get();
        $costCentersTechnology = CostCenter::where('department_id', 5)->get();

        return view(
            'cost-center.index',
            compact('costCentersProcurement', 'costCentersConstruction', 'costCentersTechnology')
        );
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
        $costCenter = CostCenter::where('id', $id)->first();

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
}
