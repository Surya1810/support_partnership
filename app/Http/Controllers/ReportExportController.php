<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exports\ReportExport;

use App\Models\ExpenseRequest;
use Maatwebsite\Excel\Excel;

class ReportExportController extends Controller
{
    public function export(Request $request)
    {
        /**
         * ! Kode yang ada disini masih mentah
         * ! dan sedang di uji coba
         */
        $filter = $request->filter;

        // Awali query dengan model
        $query = ExpenseRequest::query();

        switch ($filter) {
            case 'user':
                $request->validate(['user_id' => 'required|exists:users,id']);
                $query->where('user_id', $request->user_id);
                break;

            case 'cost_center':
                /**
                 * ! Note: Tidak ada cost_center_id,
                 * ! adanya category dalam string
                 * ! berisi id cost_center yang di implode dengan ,
                 */
                $request->validate(['cost_center_id' => 'required|exists:cost_centers,id']);
                $query->where('cost_center_id', $request->cost_center_id);
                break;

            case 'department':
                $request->validate(['department' => 'required|string']);

                $query->whereHas('department', function ($q) use ($request) {
                    $q->where('name', $request->deparment);
                });
                break;

            case 'period':
                $request->validate([
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                ]);
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                break;

            case 'project':
                $request->validate(['project_id' => 'required|exists:projects,id']);
                $query->where('project_id', $request->project_id);
                break;

            default:
                return redirect()->back()->with('error', 'Filter tidak valid.');
        }

        // Ambil data dengan relasi penting (untuk export)
        $data = $query->with(['user', 'department', 'project', 'costCenter'])->get();

        return Excel::download(new ReportExport($data), 'report-' . $filter . '.xlsx');
    }
}
