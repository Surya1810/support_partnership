<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\ExpenseItem;
use App\Models\ExpenseRequest;
use App\Models\Finance;
use App\Models\Income;
use App\Models\Project;

use App\Models\User;
use App\Models\CostCenter;

use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Global
        $household_expense_finished = ExpenseRequest::where('status', 'finish')->whereNull('project_id')->pluck('id');
        $household_finished = ExpenseItem::whereIn('expense_request_id', $household_expense_finished->toArray())->sum('actual_amount');
        $household_report = ExpenseRequest::where('status', 'report')->whereNull('project_id')->sum('total_amount');
        $household_expense = $household_finished + $household_report;

        $project_expense_finished = ExpenseRequest::where('status', 'finish')->whereNull('category')->pluck('id');
        $project_finished = ExpenseItem::whereIn('expense_request_id', $project_expense_finished->toArray())->sum('actual_amount');
        $project_report = ExpenseRequest::where('status', 'report')->whereNull('category')->sum('total_amount');
        $project_expense = $project_finished + $project_report;

        $debts = Debt::whereIn('category', ['debt', 'development'])->sum('amount');
        $income = Income::where('category', 'kas')->sum('amount');

        $total_cash_balance =  $debts + $income;
        $total_saldo = $total_cash_balance - ($household_expense + $project_expense);

        //Procurement
        $procurement_household_expense_finished = ExpenseRequest::where('department_id', 1)->where('status', 'finish')->whereNull('project_id')->whereNot('category', 'Penyusutan')->pluck('id');
        $procurement_household_finished = ExpenseItem::whereIn('expense_request_id', $procurement_household_expense_finished->toArray())->sum('actual_amount');
        $procurement_household_report = ExpenseRequest::where('department_id', 1)->where('status', 'report')->whereNull('project_id')->whereNot('category', 'Penyusutan')->sum('total_amount');
        $procurement_household_expense = $procurement_household_finished + $procurement_household_report;

        $procurement_project_expense_finished = ExpenseRequest::where('department_id', 1)->where('status', 'finish')->whereNull('category')->pluck('id');
        $procurement_project_finished = ExpenseItem::whereIn('expense_request_id', $project_expense_finished->toArray())->sum('actual_amount');
        $procurement_project_report = ExpenseRequest::where('department_id', 1)->where('status', 'report')->whereNull('category')->sum('total_amount');
        $procurement_project_expense = $procurement_project_finished + $procurement_project_report;

        $procurement_debts = Debt::where('department_id', 1)->orderBy('created_at', 'desc')->get();
        $procurement_income = Income::where('category', 'kas')->where('department_id', 1)->sum('amount');

        $procurement_cash_balance =  $procurement_debts->whereIn('category', ['debt', 'development'])->sum('amount') + $procurement_income;
        $procurement_saldo = $procurement_cash_balance - ($procurement_household_expense + $procurement_project_expense);

        //Tech
        $technology_household_expense_finished = ExpenseRequest::where('department_id', 5)->where('status', 'finish')->whereNull('project_id')->whereNot('category', 'Penyusutan')->pluck('id');
        $technology_household_finished = ExpenseItem::whereIn('expense_request_id', $technology_household_expense_finished->toArray())->sum('actual_amount');
        $technology_household_report = ExpenseRequest::where('department_id', 5)->where('status', 'report')->whereNull('project_id')->whereNot('category', 'Penyusutan')->sum('total_amount');
        $technology_household_expense = $technology_household_finished + $technology_household_report;

        $technology_project_expense_finished = ExpenseRequest::where('department_id', 5)->where('status', 'finish')->whereNull('category')->pluck('id');
        $technology_project_finished = ExpenseItem::whereIn('expense_request_id', $project_expense_finished->toArray())->sum('actual_amount');
        $technology_project_report = ExpenseRequest::where('department_id', 5)->where('status', 'report')->whereNull('category')->sum('total_amount');
        $technology_project_expense = $technology_project_finished + $technology_project_report;

        $technology_debts = Debt::where('department_id', 5)->orderBy('created_at', 'desc')->get();
        $technology_income = Income::where('category', 'kas')->where('department_id', 5)->sum('amount');

        $technology_cash_balance =  $technology_debts->whereIn('category', ['debt', 'development'])->sum('amount') + $technology_income;
        $technology_saldo = $technology_cash_balance - ($technology_household_expense + $technology_project_expense);

        //Cons
        $construction_household_expense_finished = ExpenseRequest::where('department_id', 3)->where('status', 'finish')->whereNull('project_id')->whereNot('category', 'Penyusutan')->pluck('id');
        $construction_household_finished = ExpenseItem::whereIn('expense_request_id', $construction_household_expense_finished->toArray())->sum('actual_amount');
        $construction_household_report = ExpenseRequest::where('department_id', 3)->where('status', 'report')->whereNull('project_id')->whereNot('category', 'Penyusutan')->sum('total_amount');
        $construction_household_expense = $construction_household_finished + $construction_household_report;

        $construction_project_expense_finished = ExpenseRequest::where('department_id', 3)->where('status', 'finish')->whereNull('category')->pluck('id');
        $construction_project_finished = ExpenseItem::whereIn('expense_request_id', $project_expense_finished->toArray())->sum('actual_amount');
        $construction_project_report = ExpenseRequest::where('department_id', 3)->where('status', 'report')->whereNull('category')->sum('total_amount');
        $construction_project_expense = $construction_project_finished + $construction_project_report;

        $construction_debts = Debt::where('department_id', 3)->orderBy('created_at', 'desc')->get();
        $construction_income = Income::where('category', 'kas')->where('department_id', 3)->sum('amount');

        $construction_cash_balance =  $construction_debts->whereIn('category', ['debt', 'development'])->sum('amount') + $construction_income;
        $construction_saldo = $construction_cash_balance - ($construction_household_expense + $construction_project_expense);

        // untuk modal export
        $users = User::all();
        $costCenters = CostCenter::all();
        $projects = Project::all();

        return view('finance.index', compact('total_saldo', 'total_cash_balance', 'household_expense', 'project_expense', 'procurement_saldo', 'procurement_household_expense', 'procurement_project_expense', 'procurement_cash_balance', 'procurement_income', 'procurement_debts', 'technology_saldo', 'technology_project_expense', 'technology_household_expense', 'technology_cash_balance', 'technology_income',  'technology_debts', 'construction_saldo', 'construction_household_expense', 'construction_project_expense', 'construction_cash_balance', 'construction_income', 'construction_debts', 'users', 'costCenters', 'projects'));
    }

    public function procurement()
    {
        $household_expense_finished = ExpenseRequest::where('department_id', 1)->where('status', 'finish')->whereNull('project_id')->whereNot('category', 'Penyusutan')->pluck('id');
        $household_finished = ExpenseItem::whereIn('expense_request_id', $household_expense_finished->toArray())->sum('actual_amount');
        $household_report = ExpenseRequest::where('department_id', 1)->where('status', 'report')->whereNull('project_id')->whereNot('category', 'Penyusutan')->sum('total_amount');
        $household_expense = $household_finished + $household_report;

        $project_expense_finished = ExpenseRequest::where('department_id', 1)->where('status', 'finish')->whereNull('category')->pluck('id');
        $project_finished = ExpenseItem::whereIn('expense_request_id', $project_expense_finished->toArray())->sum('actual_amount');
        $project_report = ExpenseRequest::where('department_id', 1)->where('status', 'report')->whereNull('category')->sum('total_amount');
        $project_expense = $project_finished + $project_report;

        $debts = Debt::where('department_id', 1)->orderBy('created_at', 'desc')->get();
        $households = ExpenseRequest::where('department_id', 1)->whereIn('status', ['finish', 'report'])->where('category', '!=', 'Penyusutan')->orderBy('created_at', 'desc')->get();
        $projects = Project::where('department_id', 1)->orderBy('created_at', 'desc')->get();
        $income = Income::where('category', 'kas')->sum('amount');

        $cash_balance =  $debts->whereIn('category', ['debt', 'development'])->sum('amount') + $income;
        $saldo = $cash_balance - ($household_expense + $project_expense);

        return view('finance.procurement', compact('debts', 'cash_balance', 'saldo', 'income', 'households', 'projects', 'household_expense', 'project_expense'));
    }

    public function technology()
    {
        $household_expense_finished = ExpenseRequest::where('department_id', 5)->where('status', 'finish')->whereNull('project_id')->whereNot('category', 'Penyusutan')->pluck('id');
        $household_finished = ExpenseItem::whereIn('expense_request_id', $household_expense_finished->toArray())->sum('actual_amount');
        $household_report = ExpenseRequest::where('department_id', 5)->where('status', 'report')->whereNull('project_id')->whereNot('category', 'Penyusutan')->sum('total_amount');
        $household_expense = $household_finished + $household_report;

        $project_expense_finished = ExpenseRequest::where('department_id', 5)->where('status', 'finish')->whereNull('category')->pluck('id');
        $project_finished = ExpenseItem::whereIn('expense_request_id', $project_expense_finished->toArray())->sum('actual_amount');
        $project_report = ExpenseRequest::where('department_id', 5)->where('status', 'report')->whereNull('category')->sum('total_amount');
        $project_expense = $project_finished + $project_report;

        $debts = Debt::where('department_id', 5)->orderBy('created_at', 'desc')->get();
        $households = ExpenseRequest::where('department_id', 5)->whereIn('status', ['finish', 'report'])->where('category', '!=', 'Penyusutan')->orderBy('created_at', 'desc')->get();
        $projects = Project::where('department_id', 5)->orderBy('created_at', 'desc')->get();
        $income = Income::where('category', 'kas')->sum('amount');

        $cash_balance =  $debts->whereIn('category', ['debt', 'development'])->sum('amount') + $income;
        $saldo = $cash_balance - ($household_expense + $project_expense);

        return view('finance.technology', compact('debts', 'cash_balance', 'saldo', 'income', 'households', 'projects', 'household_expense', 'project_expense'));
    }

    public function construction()
    {
        $household_expense_finished = ExpenseRequest::where('department_id', 3)->where('status', 'finish')->whereNull('project_id')->whereNot('category', 'Penyusutan')->pluck('id');
        $household_finished = ExpenseItem::whereIn('expense_request_id', $household_expense_finished->toArray())->sum('actual_amount');
        $household_report = ExpenseRequest::where('department_id', 3)->where('status', 'report')->whereNull('project_id')->whereNot('category', 'Penyusutan')->sum('total_amount');
        $household_expense = $household_finished + $household_report;

        $project_expense_finished = ExpenseRequest::where('department_id', 3)->where('status', 'finish')->whereNull('category')->pluck('id');
        $project_finished = ExpenseItem::whereIn('expense_request_id', $project_expense_finished->toArray())->sum('actual_amount');
        $project_report = ExpenseRequest::where('department_id', 3)->where('status', 'report')->whereNull('category')->sum('total_amount');
        $project_expense = $project_finished + $project_report;

        $debts = Debt::where('department_id', 3)->orderBy('created_at', 'desc')->get();
        $households = ExpenseRequest::where('department_id', 3)->whereIn('status', ['finish', 'report'])->where('category', '!=', 'Penyusutan')->orderBy('created_at', 'desc')->get();
        $projects = Project::where('department_id', 3)->orderBy('created_at', 'desc')->get();
        $income = Income::where('category', 'kas')->sum('amount');

        $cash_balance =  $debts->whereIn('category', ['debt', 'development'])->sum('amount') + $income;
        $saldo = $cash_balance - ($household_expense + $project_expense);

        return view('finance.construction', compact('debts', 'cash_balance', 'saldo', 'income', 'households', 'projects', 'household_expense', 'project_expense'));;
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function pembagian(Request $request)
    {
        // dd($request);
        $request->validate([
            'kas' => 'required|numeric|min:0',
            'penyusutan' => 'required|numeric|min:0',
        ]);

        $old = session()->getOldInput();

        $project = Project::findOrFail($request['project']);

        Income::create([
            'department_id' => $request['department'],
            'project_id' => $request['project'],
            'category' => 'kas',
            'desc' => 'pemasukan dari project ' . $project->name,
            'amount' => $request['kas'],
        ]);

        Debt::create([
            'department_id' => $request['department'],
            'category' => 'payment',
            'title' => 'penyusutan dari project ' . $project->name,
            'amount' => $request['penyusutan'],
        ]);
        if ($request->department == 3) {
            return redirect()->route('construction.report')->with(['pesan' => 'Debt added successfully', 'level-alert' => 'alert-success']);
        } elseif ($request->department == 5) {
            return redirect()->route('technology.report')->with(['pesan' => 'Debt added successfully', 'level-alert' => 'alert-success']);
        } else {
            return redirect()->route('procurement.report')->with(['pesan' => 'Debt added successfully', 'level-alert' => 'alert-success']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Finance $finance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Finance $finance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Finance $finance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Finance $finance)
    {
        //
    }
}
