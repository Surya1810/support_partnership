<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\ExpenseItem;
use App\Models\ExpenseRequest;
use App\Models\Income;
use App\Models\Project;

class CostCenterController extends Controller
{
    public function index() {
        return view('cost-center.index');
    }
}
