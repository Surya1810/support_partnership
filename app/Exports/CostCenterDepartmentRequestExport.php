<?php

namespace App\Exports;

use App\Models\ExpenseRequest;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CostCenterDepartmentRequestExport implements FromView
{
    protected $departmentId;
    protected $year;

    public function __construct($departmentId, $year)
    {
        $this->departmentId = $departmentId;
        $this->year = $year;
    }

    public function view(): View
    {
        $requests = ExpenseRequest::whereIn('status', ['finish', 'checking', 'reported'])
            ->with(['costCenter', 'items', 'user', 'department'])
            ->whereHas('costCenter', function ($query) {
                $query->where('year', date('Y'));
            })
            ->where('category', 'department')
            ->where('department_id', $this->departmentId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cost-center.export.department_expense_requests', [
            'requests' => $requests
        ]);
    }
}
