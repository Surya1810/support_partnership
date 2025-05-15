<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use Illuminate\Http\Request;

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

    public function show(Request $request) {}

    public function edit() {}

    public function update(Request $request, $id) {}

    public function delete($id) {}
}
