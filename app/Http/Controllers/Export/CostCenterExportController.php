<?php

namespace App\Http\Controllers\Export;

use App\Exports\CostCenterDepartmentRequestExport;
use App\Exports\CostCenterGeneralCreditRealizationExport;
use App\Exports\CostCenterGeneralDebitRealizationExport;
use App\Exports\CostCenterGeneralDebitRealizations;
use App\Exports\CostCenterProjectBudgetPlanExport;
use App\Exports\CostCenterProjectBudgetPlanRequestExport;
use App\Exports\CostCenterProjectRealizationExport;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CostCenterExportController extends Controller
{
    public function departmentExpenseRequests($id) {
        $department = Department::where('id', $id)->first();
        $year = date('Y');
        $filename = strtoupper('LAPORAN PENGAJUAN - ' . $department->name) . ' - ' . $year . '.xlsx';

        return Excel::download(new CostCenterDepartmentRequestExport($id, $year), $filename);
    }

    public function projectBudgetPlan($id) {
        $project = Project::where('id', $id)->first();
        $year = date('Y');
        $filename = strtoupper('LAPORAN RAB PROJECT - ' . $project->name) . ' - ' . $year . '.xlsx';

        return Excel::download(new CostCenterProjectBudgetPlanExport($id, $year), $filename);
    }

    public function projectBudgetPlanRequests($id) {
        $project = Project::where('id', $id)->first();
        $year = date('Y');
        $filename = strtoupper('LAPORAN PENGAJUAN TEREALISASI PROJECT - ' . $project->name) . ' - ' . $year . '.xlsx';

        return Excel::download(new CostCenterProjectBudgetPlanRequestExport($id, $year), $filename);
    }

    public function projectRealizationsByDepartmentId($id) {
        $department = Department::where('id', $id)->first();
        $year = date('Y');
        $filename = strtoupper('LAPORAN REALISASI PROJECT - ' . $department->name) . ' - ' . $year . '.xlsx';

        return Excel::download(new CostCenterProjectRealizationExport($id, $year), $filename);
    }

    public function generalCreditRealizations(Request $request) {
        $year = date('Y');
        $filename = strtoupper('LAPORAN REALISASI GENERAL CREDIT') . ' - ' . $year . '.xlsx';
        $filter = [
            'fromYear' => Carbon::createFromDate($request->fromYear, 1, 1)->startOfYear(),
            'toYear' => Carbon::createFromDate($request->toYear, 12, 31)->endOfYear(),
            'departmentFilter' => $request->departmentFilter == 'undefined' ? null : $request->departmentFilter
        ];

        return Excel::download(new CostCenterGeneralCreditRealizationExport($year, $filter), $filename);
    }

    public function generalDebitRealizations(Request $request) {
        $year = date('Y');
        $filename = strtoupper('LAPORAN REALISASI GENERAL DEBIT') . ' - ' . $year . '.xlsx';
        $filter = [
            'fromYear' => $request->fromYear,
            'toYear' => $request->toYear,
            'departmentFilter' => $request->departmentFilter == 'undefined' ? null : $request->departmentFilter
        ];

        return Excel::download(new CostCenterGeneralDebitRealizationExport($year, $filter), $filename);
    }
}
