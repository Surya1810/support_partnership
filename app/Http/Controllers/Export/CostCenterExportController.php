<?php

namespace App\Http\Controllers\Export;

use App\Exports\CostCenterDepartmentRequestExport;
use App\Exports\CostCenterGeneralCreditRealizationExport;
use App\Exports\CostCenterGeneralDebitRealizations;
use App\Exports\CostCenterProjectBudgetPlanExport;
use App\Exports\CostCenterProjectBudgetPlanRequestExport;
use App\Exports\CostCenterProjectRealizationExport;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Project;
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

    public function generalCreditRealizations() {
        $year = date('Y');
        $filename = strtoupper('LAPORAN REALISASI GENERAL CREDIT') . ' - ' . $year . '.xlsx';

        return Excel::download(new CostCenterGeneralCreditRealizationExport($year), $filename);
    }

    public function generalDebitRealizations() {
        $year = date('Y');
        $filename = strtoupper('LAPORAN REALISASI GENERAL DEBIT') . ' - ' . $year . '.xlsx';

        return Excel::download(new CostCenterGeneralDebitRealizations($year), $filename);
    }
}
