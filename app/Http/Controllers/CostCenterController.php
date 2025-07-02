<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function index(){
        return view('cost-center.index');
    }

    public function indexCreateRABDepartment() {
        return view('cost-center.create_rab');
    }

    public function indexTransactionRABDepartment() {
        return view('cost-center.transactions_rab_department_credit');
    }
}
