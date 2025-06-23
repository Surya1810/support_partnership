<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Models
use App\Models\UserJob;
use App\Models\User;

class UserJobController extends Controller
{
    /**
     * Penugasan ke orang lain
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jobs = UserJob::with(['assigner', 'assignee'])
                ->when($request->has('status') && $request->status !== 'all', function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->orderBy('created_at', 'desc');
            $roleId = Auth::user()->role_id;

            if ($roleId == 3) {
                $jobs = $jobs->where('department_id', Auth::user()->department_id);
            } else if ($roleId == 5) {
                $jobs = $jobs->where('assigner_id', Auth::id());
            }

            return DataTables::of($jobs)
                ->addIndexColumn()
                ->addColumn('assigner', fn($job) => $job->assigner->name ?? '-')
                ->addColumn('assignee', fn($job) => $job->assignee->name ?? '-')
                ->addColumn('department', fn($job) => $job->assignee->department->name ?? '-')
                ->addColumn('detail', fn($job) => $job->job_detail ?? '-')
                ->addColumn('actions', function ($job) {
                    $buttons = '';
                    $userId = Auth::id();

                    if ($job->assigner_id == $userId) {
                        $buttons .= '<button class="btn btn-sm btn-warning mr-1" title="Edit" type="button" onclick="modalEdit(this)" data-id="' . $job->id . '">
                        <i class="fas fa-pencil-alt"></i>
                     </button>';
                    }

                    return $buttons;
                })
                ->addColumn('time_remaining', function ($job) {
                    if (!$job->end_date || !$job->start_date) return '-';

                    $endDate = Carbon::parse($job->end_date);

                    if ($job->completed_at) {
                        $completedAt = Carbon::parse($job->completed_at);

                        if ($completedAt->equalTo($endDate)) {
                            return "0 days";
                        } elseif ($completedAt->lessThan($endDate)) {
                            $diff = $endDate->diffInDays($completedAt);
                            return "+" . $diff * -1; // plus
                        } else {
                            $diff = $completedAt->diffInDays($endDate);
                            return "{$diff}";
                        }
                    } else {
                        $today = Carbon::today();

                        if ($today->gt($endDate)) {
                            $diff = $today->diffInDays($endDate);
                            return "$diff";
                        } elseif ($today->eq($endDate)) {
                            return "0";
                        } else {
                            $diff = $endDate->diffInDays($today);
                            return $diff * -1;
                        }
                    }
                })
                ->addColumn('completion_efficiency', function ($job) {
                    if (!$job->start_date || !$job->end_date) return '-';

                    $start = Carbon::parse($job->start_date);
                    $end = Carbon::parse($job->end_date);

                    // Jika tidak ada rentang waktu
                    if ($start->equalTo($end)) return '-';

                    $totalDuration = $start->diffInSeconds($end);

                    // Jika belum selesai
                    if (!$job->completed_at) {
                        $today = Carbon::today();

                        if ($today->gt($end)) {
                            // Telat
                            $actualDuration = $start->diffInSeconds($today);
                            $diffPercentage = (($actualDuration - $totalDuration) / $totalDuration) * 100;
                            return "-" . round($diffPercentage) . "%";
                        } else {
                            // Belum selesai tapi masih dalam waktu
                            $actualDuration = $start->diffInSeconds($today);
                            $diffPercentage = (($totalDuration - $actualDuration) / $totalDuration) * 100;
                            return "+" . round($diffPercentage) . "%";
                        }
                    }

                    // Jika sudah selesai
                    $completed = Carbon::parse($job->completed_at);
                    $actualDuration = $start->diffInSeconds($completed);
                    $diffPercentage = (($totalDuration - $actualDuration) / $totalDuration) * 100;

                    if ($actualDuration < $totalDuration) {
                        return "+" . round($diffPercentage) . "%";
                    } elseif ($actualDuration > $totalDuration) {
                        return "-" . round(abs($diffPercentage)) . "%";
                    } else {
                        return "0%";
                    }
                })
                ->setRowClass(function ($job) {
                    $rowClass = '';

                    // Warna berdasarkan status
                    $statusClass = match ($job->status) {
                        'overdue'     => 'table-danger',
                        'completed'   => 'table-success',
                        'in_progress' => 'table-info',
                        'cancelled'   => 'table-secondary',
                        default       => '',
                    };

                    // Evaluasi logika keterlambatan atau kecepatan
                    $isLate = false;

                    if ($job->end_date && $job->start_date) {
                        $end = Carbon::parse($job->end_date);

                        if ($job->completed_at) {
                            $completed = Carbon::parse($job->completed_at);
                            if ($completed->greaterThan($end)) {
                                $isLate = true;
                            }
                        } else {
                            $today = Carbon::today();
                            if ($today->greaterThan($end)) {
                                $isLate = true;
                            }
                        }
                    }

                    // Jika terlambat → prioritaskan warna merah
                    if ($isLate) {
                        $rowClass = 'table-danger';
                    } elseif ($statusClass) {
                        $rowClass = $statusClass;
                    }

                    return $rowClass;
                })
                ->addColumn('revisions', fn($job) => $job->feedback ?? '-')
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        $users = User::whereNotIn('id', [Auth::id(), 1])->with('department')->get();
        return view('jobs.index', compact('users'));
    }

    public function myTasks(Request $request)
    {
        if ($request->ajax()) {
            $jobs = UserJob::with(['assigner', 'assignee'])
                ->where('assignee_id', Auth::user()->id)
                ->when($request->has('status') && $request->status !== 'all', function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->orderBy('created_at', 'desc');

            return DataTables::of($jobs)
                ->addIndexColumn()
                ->addColumn('assigner', fn($job) => $job->assigner->name ?? '-')
                ->addColumn('assignee', fn($job) => $job->assignee->name ?? '-')
                ->addColumn('department', fn($job) => $job->assignee->department->name ?? '-')
                ->addColumn('detail', fn($job) => $job->job_detail ?? '-')
                ->addColumn('actions', function ($job) {
                    return '<a href="' . '#' . '" class="btn btn-sm btn-warning mr-1" title="Edit" type="button" data-id="' . $job->id . '">
                        <i class="fas fa-pencil-alt"></i>
                    </a>';
                })
                ->addColumn('time_remaining', function ($job) {
                    if (!$job->end_date || !$job->start_date) return '-';

                    $endDate = Carbon::parse($job->end_date);

                    if ($job->completed_at) {
                        $completedAt = Carbon::parse($job->completed_at);

                        if ($completedAt->equalTo($endDate)) {
                            return "0 days";
                        } elseif ($completedAt->lessThan($endDate)) {
                            $diff = $endDate->diffInDays($completedAt);
                            return "+" . $diff * -1; // plus
                        } else {
                            $diff = $completedAt->diffInDays($endDate);
                            return "{$diff}";
                        }
                    } else {
                        $today = Carbon::today();

                        if ($today->gt($endDate)) {
                            $diff = $today->diffInDays($endDate);
                            return "$diff";
                        } elseif ($today->eq($endDate)) {
                            return "0";
                        } else {
                            $diff = $endDate->diffInDays($today);
                            return $diff * -1;
                        }
                    }
                })
                ->addColumn('completion_efficiency', function ($job) {
                    if (!$job->start_date || !$job->end_date) return '-';

                    $start = Carbon::parse($job->start_date);
                    $end = Carbon::parse($job->end_date);

                    // Jika tidak ada rentang waktu
                    if ($start->equalTo($end)) return '-';

                    $totalDuration = $start->diffInSeconds($end);

                    // Jika belum selesai
                    if (!$job->completed_at) {
                        $today = Carbon::today();

                        if ($today->gt($end)) {
                            // Telat
                            $actualDuration = $start->diffInSeconds($today);
                            $diffPercentage = (($actualDuration - $totalDuration) / $totalDuration) * 100;
                            return "-" . round($diffPercentage) . "%";
                        } else {
                            // Belum selesai tapi masih dalam waktu
                            $actualDuration = $start->diffInSeconds($today);
                            $diffPercentage = (($totalDuration - $actualDuration) / $totalDuration) * 100;
                            return "+" . round($diffPercentage) . "%";
                        }
                    }

                    // Jika sudah selesai
                    $completed = Carbon::parse($job->completed_at);
                    $actualDuration = $start->diffInSeconds($completed);
                    $diffPercentage = (($totalDuration - $actualDuration) / $totalDuration) * 100;

                    if ($actualDuration < $totalDuration) {
                        return "+" . round($diffPercentage) . "%";
                    } elseif ($actualDuration > $totalDuration) {
                        return "-" . round(abs($diffPercentage)) . "%";
                    } else {
                        return "0%";
                    }
                })
                ->setRowClass(function ($job) {
                    $rowClass = '';

                    // Warna berdasarkan status
                    $statusClass = match ($job->status) {
                        'overdue'     => 'table-danger',
                        'completed'   => 'table-success',
                        'in_progress' => 'table-info',
                        'cancelled'   => 'table-secondary',
                        default       => '',
                    };

                    // Evaluasi logika keterlambatan atau kecepatan
                    $isLate = false;

                    if ($job->end_date && $job->start_date) {
                        $end = Carbon::parse($job->end_date);

                        if ($job->completed_at) {
                            $completed = Carbon::parse($job->completed_at);
                            if ($completed->greaterThan($end)) {
                                $isLate = true;
                            }
                        } else {
                            $today = Carbon::today();
                            if ($today->greaterThan($end)) {
                                $isLate = true;
                            }
                        }
                    }

                    // Jika terlambat → prioritaskan warna merah
                    if ($isLate) {
                        $rowClass = 'table-danger';
                    } elseif ($statusClass) {
                        $rowClass = $statusClass;
                    }

                    return $rowClass;
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }
        return view('jobs.my_tasks');
    }

    public function store(Request $request)
    {
        $request->validate([
            'assignee_id' => 'required|exists:users,id',
            'detail' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $assignee = User::where('id', $request->assignee_id)->first();

        if (!$assignee) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        DB::beginTransaction();

        try {
            UserJob::create([
                'assigner_id' => Auth::id(),
                'assignee_id' => $request->assignee_id,
                'department_id' => $assignee->department_id,
                'job_detail' => $request->detail,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => 'Pengerjaan ke-1',
            ]);

            DB::commit();
            return response()->json(['message' => 'Berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal disimpan',
                'error' => $e->getMessage(), // tampilkan error
            ], 500);
        }
    }


    public function show($id)
    {
        $job = UserJob::findOrFail($id);
        return response()->json($job);
    }

    public function update(Request $request, $id)
    {
        $job = UserJob::where('id', $id)->first();

        if (Auth::user()->id != $job->assigner_id) {
            return response()->json(['message' => 'Anda tidak berhak mengubah pekerjaan ini.'], 403);
        }

        if (!$job->exists()) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        DB::beginTransaction();

        try {
            $now = Carbon::today();
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->startOfDay();

            if ($request->action == 'cancel') {
                $status = 'cancelled';
            } else if ($now->gte($start) && $now->lte($end)) {
                $status = 'in_progress';
            } else if ($now->gt($end)) {
                return response()->json([
                    'message' => 'Tanggal selesai tidak boleh kurang dari hari ini'
                ], 400);
            }

            $job->update([
                'title'       => $request->title,
                'job_detail'  => $request->job_detail,
                'end_date'    => $request->end_date,
                'feedback'    => $request->feedback,
                'notes'       => $request->notes,
                'status'      => $status,
            ]);

            DB::commit();
            return response()->json(['message' => 'Berhasil diperbarui']);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
    }

    public function markComplete($id)
    {
        $job = UserJob::findOrFail($id);

        if (Auth::user()->id != $job->assignee_id) {
            return response()->json(['message' => 'Anda tidak berhak menyelesaikan pekerjaan ini.'], 403);
        }

        DB::beginTransaction();

        try {
            $job->update([
                'status' => 'completed',
                'updated_at' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Pekerjaan berhasil ditandai selesai.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyelesaikan pekerjaan.'], 500);
        }
    }
}
