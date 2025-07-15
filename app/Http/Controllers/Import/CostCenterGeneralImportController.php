<?php

namespace App\Http\Controllers\Import;

use App\Models\CostCenter;
use App\Models\Department;
use App\Models\CostCenterCategory;

use App\Http\Controllers\Controller;
use App\Imports\CostCenterGeneralImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CostCenterGeneralImportController extends Controller
{
    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/uploads/files/templates/template_cost_center_general.xlsx');

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
        }, 'template_cost_center_general.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_cost_center_general.xlsx"',
        ]);
    }

    public function import(Request $request) {
        try {
            $request->validate([
                'file' => 'bail|required|mimes:xlsx|max:122880'
            ]);

            Excel::import(new CostCenterGeneralImport, $request->file('file'));

            return redirect()->back()->with(['pesan' => 'Data berhasil diimport!', 'level-alert' => 'alert-success']);
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with(['pesan' => 'Terjadi kesalahan: ' . $e->getMessage(), 'level-alert' => 'alert-danger']);
        }
    }
}
