<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\ExpenseItem;
use App\Models\ExpenseRequest;
use App\Models\Income;
use App\Models\Project;
use App\Models\CostCenter;
use App\Models\Department;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    private function getExpenseSum($departmentId = null, $status, $isProject = false, $excludeCategory = null)
    {
        $query = ExpenseRequest::where('status', $status);

        if ($departmentId) $query->where('department_id', $departmentId);
        if ($isProject) $query->whereNull('category');
        else $query->whereNull('project_id');

        if ($excludeCategory) $query->where('category', '!=', $excludeCategory);

        return $status === 'finish'
            ? ExpenseItem::whereIn('expense_request_id', $query->pluck('id')->toArray())->sum('actual_amount')
            : $query->sum('total_amount');
    }

    public function index()
    {
        // $costCenters = Department::whereNotIn('id', [2, 4, 6, 7, 8])
        //     ->with('costCenters')
        //     ->get();

        // Global
        $household_expense = $this->getExpenseSum(null, 'finish', false)
            + $this->getExpenseSum(null, 'report', false);
        $project_expense = $this->getExpenseSum(null, 'finish', true)
            + $this->getExpenseSum(null, 'report', true);

        // Technology (department_id = 5)
        $techDept = 5;
        $technology_household_expense = $this->getExpenseSum($techDept, 'finish', false, 'Penyusutan')
            + $this->getExpenseSum($techDept, 'report', false, 'Penyusutan');
        $technology_project_expense = $this->getExpenseSum($techDept, 'finish', true)
            + $this->getExpenseSum($techDept, 'report', true);

        $technology_debts = Debt::where('department_id', $techDept)->latest()->get();
        $technology_income = Income::where('department_id', $techDept)->where('category', 'kas')->sum('amount');

        $technology_cash_balance = $technology_debts
            ->whereIn('category', ['debt', 'development'])
            ->sum('amount') + $technology_income;

        $technology_saldo = $technology_cash_balance - ($technology_household_expense + $technology_project_expense);

        return view('cost-center.index', compact(
            'technology_saldo',
            'technology_project_expense',
            'technology_household_expense',
            'technology_cash_balance',
            'technology_income',
            'technology_debts'
        ));
    }

    public function show(Request $request) {}

    public function edit() {}

    public function update(Request $request, $id) {}

    public function delete($id) {}
}
