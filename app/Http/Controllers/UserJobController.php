<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

// Models
use App\Models\UserJob;
use App\Models\User;
use App\Models\Department;

use App\Imports\UserJobImport;
use App\Exports\UserJobExport;
use App\Exports\MyTaskExport;

use Jenssegers\Agent\Agent;

class UserJobController extends Controller
{
    /**
     * Penugasan ke orang lain
     */
    public function index(Request $request)
    {
        $agent = new Agent();
        $isMobile = $agent->isMobile();

        if ($request->ajax()) {
            $jobs = UserJob::with(['assigner', 'assignee.department']);

            // Filter berdasarkan status
            if ($request->status && $request->status != 'all') {
                if ($request->status == 'in_progress') {
                    $jobs->where(function ($q) {
                        $q->where('status', 'in_progress')->orWhere('status', 'planning');
                    });
                } elseif ($request->status == 'overdue') {
                    $jobs->whereDate('end_date', '<', Carbon::today())
                        ->whereNull('completed_at');
                } else {
                    $jobs->where('status', $request->status);
                }
            }

            // Filter berdasarkan staff
            if ($request->staff && $request->staff != 'all') {
                $jobs->where(function ($q) use ($request) {
                    $q->where('assignee_id', $request->staff)
                        ->orWhere('assigner_id', $request->staff);
                });
            }

            // Filter berdasarkan department
            if ($request->department && $request->department != 'all') {
                $jobs->where('department_id', $request->department);
            }

            // Filter berdasarkan role & department user login
            $roleId = Auth::user()->role_id;
            $departmentId = Auth::user()->department_id;
            if ($roleId == 3) {
                $jobs->where('department_id', $departmentId);
            } elseif (($roleId == 5 || $roleId == 4) && $departmentId != 8) {
                $jobs->where('assigner_id', Auth::id());
            }

            // Global search dari datatables
            $searchKeyword = $request->input('search.value');
            if (!empty($searchKeyword)) {
                $jobs->where('job_detail', 'like', '%' . $searchKeyword . '%');
            }

            $jobs->orderByDesc('created_at');

            // Hitung efisiensi jika ada filter
            $isStaffFilterValid = $request->has('staff') && $request->staff !== 'all';

            if ($isStaffFilterValid) {
                $efficiencyJobs = (clone $jobs)
                    ->where('assignee_id', $request->staff)
                    ->get();
            } else {
                $efficiencyJobs = (clone $jobs)->get();
            }

            $efficiencySum = 0;
            $efficiencyCount = 0;

            foreach ($efficiencyJobs as $job) {
                if (!$job->start_date || !$job->end_date) continue;

                $start = Carbon::parse($job->start_date);
                $end = Carbon::parse($job->end_date);

                if ($start->equalTo($end)) continue;

                $totalDuration = $start->diffInSeconds($end);
                $actualDuration = !$job->completed_at
                    ? $start->diffInSeconds(Carbon::today())
                    : $start->diffInSeconds(Carbon::parse($job->completed_at));

                if ($actualDuration == $totalDuration) {
                    $diffPercentage = 0;
                } elseif ($actualDuration < $totalDuration) {
                    $diffPercentage = abs((($totalDuration - $actualDuration) / $totalDuration) * 100);
                } else {
                    $diffPercentage = -1 * abs((($actualDuration - $totalDuration) / $totalDuration) * 100);
                }

                $debugLogs[] = [
                    'job_id' => $job->id,
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                    'completed_at' => $job->completed_at,
                    'total_duration' => $totalDuration,
                    'actual_duration' => $actualDuration,
                    'diff_percentage' => round($diffPercentage, 2)
                ];

                $efficiencySum += $diffPercentage;
                $efficiencyCount++;
            }

            $totalEfficiency = round($efficiencySum);

            return DataTables::of($jobs)
                ->addIndexColumn()
                ->addColumn('priority', function ($job) {
                    if ($job->is_priority) {
                        return '<img src="' . asset('assets/img/jangrik.gif') . '" width="40px" />';
                    }
                    return '';
                })
                ->addColumn('assigner', fn($job) => $job->assigner->name ?? '-')
                ->addColumn('assignee', fn($job) => $job->assignee->name ?? '-')
                ->addColumn('department', fn($job) => $job->assignee->department->name ?? '-')
                ->addColumn('job_detail', fn($job) => $job->job_detail)
                ->addColumn('start_date', fn($job) => $job->start_date)
                ->addColumn('end_date', fn($job) => $job->end_date)
                ->addColumn('completed_at', fn($job) => $job->completed_at ? Carbon::parse($job->completed_at)->format('Y-m-d') : '-')
                ->addColumn('time_remaining', function ($job) {
                    if (!$job->end_date || !$job->start_date) return '-';
                    $end = Carbon::parse($job->end_date);

                    if ($job->completed_at) {
                        $completed = Carbon::parse($job->completed_at);
                        if ($completed->equalTo($end)) return "0";
                        $diff = $end->diffInDays($completed);
                        return $completed->lt($end) ? "+" . (-1 * $diff) : (-1 * $diff);
                    }

                    $today = Carbon::today();
                    if ($today->gt($end)) return $today->diffInDays($end);
                    if ($today->eq($end)) return "0";
                    return ($end->diffInDays($today)) * -1;
                })
                ->addColumn('report_file', fn($job) =>
                $job->report_file
                    ? '<a href="' . asset('storage/' . $job->report_file) . '" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i></a>'
                    : '-')
                ->addColumn('feedback', fn($job) => $job->feedback ? nl2br($job->feedback) : '-')
                ->addColumn('completion_efficiency', function ($job) {
                    if (!$job->start_date || !$job->end_date) return '-';
                    $start = Carbon::parse($job->start_date);
                    $end = Carbon::parse($job->end_date);
                    if ($start->equalTo($end) && !$job->completed_at) return '0%';

                    $totalDuration = $start->diffInSeconds($end);
                    $actualDuration = !$job->completed_at
                        ? $start->diffInSeconds(Carbon::today())
                        : $start->diffInSeconds(Carbon::parse($job->completed_at));

                    $denom = $totalDuration > 0 ? $totalDuration : max($actualDuration, 1);
                    $diffPercentage = (($totalDuration - $actualDuration) / $denom) * 100;

                    return $actualDuration == $totalDuration ? "0%"
                        : ($actualDuration < $totalDuration ? "+" : "-") . round(abs($diffPercentage)) . "%";
                })
                ->addColumn('status', fn($job) => $job->status)
                ->addColumn('revisions', fn($job) => $job->notes ?? '-')
                ->addColumn('actions', function ($job) use ($isMobile) {
                    $buttons = '';
                    $userId = Auth::id();

                    if ($isMobile) {
                        if ($job->is_priority) {
                            $buttons .= '<img width="30px" src="' . asset('assets/img/jangrik.gif') . '" />';
                        }

                        $buttons .= '<button class="btn btn-sm btn-info m-1 d-block" title="Detail" onclick="modalDetailJob(' . $job->id . ')"><i class="fas fa-circle-info"></i></button>';

                        if ($job->report_file) {
                            $buttons .= '<a href="' . asset('storage/' . $job->report_file) . '" title="File Laporan" target="_blank" class="btn btn-sm btn-danger m-1 d-block"><i class="fas fa-file-pdf"></i></a>';
                        }
                    }

                    if ($job->assigner_id == $userId) {
                        $buttons .= '<button class="btn btn-sm btn-warning m-1 d-block d-md-inline-block" title="Adendum/Catatan" onclick="modalEdit(this)" data-id="' . $job->id . '"><i class="fas fa-pencil-alt"></i></button>';

                        if ($job->status != 'completed' && $job->status != 'checking') {
                            $buttons .= '</button><button class="btn btn-sm btn-secondary m-1 d-block d-md-inline-block" title="Hapus Tugas" onclick="deleteJob(this)" data-id="' . $job->id . '"><i class="fas fa-trash"></i></button>';
                        }
                    }

                    if ($job->status == 'checking' && $job->assigner_id == $userId) {
                        $buttons .= '<button class="btn btn-sm btn-success m-1 d-block d-md-inline-block" title="Approve/Revisi" onclick="modalApprove(this)" data-id="' . $job->id . '"><i class="fas fa-check"></i></button>';
                    }

                    return '<div class="text-nowrap">' . $buttons . '</div>';
                })
                ->setRowClass(function ($job) {
                    $statusClass = match ($job->status) {
                        'overdue' => 'table-danger',
                        'completed' => 'table-success',
                        'in_progress' => 'table-info',
                        'cancelled' => 'table-secondary',
                        'checking' => 'table-warning',
                        default => '',
                    };

                    $isLate = false;
                    if ($job->end_date && $job->start_date && !$job->completed_at) {
                        $isLate = Carbon::today()->gt(Carbon::parse($job->end_date));
                    }

                    return $isLate ? 'table-danger' : $statusClass;
                })
                ->with([
                    'total_efficiency' => $totalEfficiency,
                    'is_mobile' => $isMobile
                ])
                ->rawColumns(['actions', 'report_file', 'feedback', 'priority'])
                ->make(true);
        }

        $users = User::whereNotIn('id', [Auth::id(), 1, 2])->get();
        $departments = Department::select('id', 'name')->whereIn('id', [1, 3, 5, 8])->get();

        return view('jobs.index', compact('users', 'departments', 'isMobile'));
    }

    public function myTasks(Request $request)
    {
        $agent = new Agent();
        $isMobile = $agent->isMobile();

        if ($request->ajax()) {
            $jobs = UserJob::with(['assigner', 'assignee'])
                ->where('assignee_id', Auth::user()->id)
                ->when($request->has('status') && $request->status != 'all', function ($query) use ($request) {
                    if ($request->status == 'in_progress') {
                        $query->where(function ($q) {
                            $q->where('status', 'in_progress')
                                ->orWhere('status', 'planning');
                        });
                    } else if ($request->status == 'overdue') {
                        $today = Carbon::today();
                        $query->where('end_date', '<', $today)
                            ->whereNull('completed_at');
                    } else {
                        $query->where('status', $request->status);
                    }
                })
                ->orderBy('created_at', 'desc');

            $efficiencySum = 0;
            $efficiencyCount = 0;
            $clonedJobs = clone $jobs;
            $filteredJobs = $clonedJobs->where('assignee_id', Auth::user()->id)->get();

            foreach ($filteredJobs as $job) {
                if (!$job->start_date || !$job->end_date) continue;

                $start = Carbon::parse($job->start_date);
                $end = Carbon::parse($job->end_date);
                if ($start->equalTo($end)) continue;

                $totalDuration = $start->diffInSeconds($end);

                if (!$job->completed_at) {
                    $today = Carbon::today();
                    $actualDuration = $start->diffInSeconds($today);
                    $diffPercentage = ($today->gt($end))
                        ? (($actualDuration - $totalDuration) / $totalDuration) * -100
                        : (($totalDuration - $actualDuration) / $totalDuration) * 100;
                } else {
                    $completed = Carbon::parse($job->completed_at);
                    $actualDuration = $start->diffInSeconds($completed);
                    $diffPercentage = (($totalDuration - $actualDuration) / $totalDuration) * 100;
                }

                $efficiencySum += $diffPercentage;
                $efficiencyCount++;
            }

            return DataTables::of($jobs)
                ->addIndexColumn()
                ->addColumn('priority', function ($job) {
                    if ($job->is_priority) {
                        return '<img src="' . asset('assets/img/jangrik.gif') . '" width="40px" />';
                    }
                })
                ->addColumn('assigner', fn($job) => $job->assigner->name ?? '-')
                ->addColumn('assignee', fn($job) => $job->assignee->name ?? '-')
                ->addColumn('department', fn($job) => $job->assignee->department->name ?? '-')
                ->addColumn('detail', fn($job) => $job->job_detail ?? '-')
                ->addColumn('completed_at', fn($job) => $job->completed_at ? Carbon::parse($job->completed_at)->format('Y-m-d') : '-')
                ->addColumn('feedback', function ($job) {
                    return $job->feedback ? nl2br($job->feedback) : '-';
                })
                ->addColumn('time_remaining', function ($job) {
                    if (!$job->end_date || !$job->start_date) return '-';

                    $endDate = Carbon::parse($job->end_date);

                    if ($job->completed_at) {
                        $completedAt = Carbon::parse($job->completed_at);

                        if ($completedAt->equalTo($endDate)) {
                            return "0";
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
                ->addColumn('report_file', function ($job) {
                    return $job->report_file ? '<a href="' . asset('storage/' . $job->report_file) . '" target="_blank" class="btn btn-sm btn-danger mr-1" title="Lihat Laporan" type="button">
                        <i class="fas fa-file-pdf"></i>
                    </a>' : '-';
                })
                ->addColumn('completion_efficiency', function ($job) {
                    if (!$job->start_date || !$job->end_date) return '-';

                    $start = Carbon::parse($job->start_date);
                    $end = Carbon::parse($job->end_date);

                    // Jika tidak ada rentang waktu
                    if ($start->equalTo($end) && !$job->completed_at) return '0%';

                    $totalDuration = $start->diffInSeconds($end);

                    // Jika belum selesai
                    if (!$job->completed_at) {
                        $today = Carbon::today();

                        if ($today->gt($end)) {
                            // Telat
                            $actualDuration = $start->diffInSeconds($today);
                            $diffPercentage = (($actualDuration - $totalDuration) / $totalDuration) * 100;
                            return round($diffPercentage) * -1 . "%";
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
                    $denom = $totalDuration > 0 ? $totalDuration : max($actualDuration, 1);
                    $diffPercentage = (($totalDuration - $actualDuration) / $denom) * 100;

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
                        'checking'    => 'table-warning',
                        default       => '',
                    };

                    // Evaluasi logika keterlambatan atau kecepatan
                    $isLate = false;

                    if ($job->end_date && $job->start_date) {
                        $end = Carbon::parse($job->end_date);

                        if ($job->completed_at) {
                            $completed = Carbon::parse($job->completed_at);
                            if ($completed->greaterThan($end)) {
                                $isLate = false;
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
                ->addColumn('revisions', fn($job) => $job->notes ?? '-')
                ->with([
                    'total_efficiency' => ($efficiencyCount > 0)
                        ? round($efficiencySum)
                        : 0
                ])
                ->addColumn('actions', function ($job) use ($isMobile) {
                    $buttons = '';

                    if ($isMobile) {
                        if ($job->is_priority) {
                            $buttons .= '<img width="30px" src="' . asset('assets/img/jangrik.gif') . '" />';
                        }

                        $buttons .= '<button class="btn btn-sm btn-info m-1 d-block" title="Detail" onclick="modalDetailJob(' . $job->id . ')"><i class="fas fa-circle-info"></i></button>';

                        $statusArr = ['completed', 'checking', 'revision'];
                        if (in_array($job->status, $statusArr)) {
                            $buttons .= '<a href="' . asset('storage/' . $job->report_file) . '" title="File Laporan" target="_blank" class="btn btn-sm btn-danger m-1 d-block"><i class="fas fa-file-pdf"></i></a>';
                        }
                    }

                    $statusArr = ['completed', 'checking', 'cancelled'];
                    if (!in_array($job->status, $statusArr)) {
                        $buttons .= '<button class="btn btn-sm btn-warning m-1 d-block d-md-inline-block" title="Upload Bukti Pekerjaan Selesai" type="button" onclick="modalUploadReportFile(' . $job->id . ')">
                            <i class="fas fa-upload"></i>
                        </button>';
                    }

                    return $buttons;
                })
                ->filterColumn('assigner', function ($query, $keyword) {
                    $query->whereHas('assigner', function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    });
                })
                ->rawColumns(['actions', 'report_file', 'feedback', 'priority'])
                ->make(true);
        }

        return view('jobs.my_tasks', compact('isMobile'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'assignee_id' => 'required|exists:users,id',
            'detail' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_priority' => 'nullable|boolean',
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
                'feedback' => 'Pengerjaan ke-1',
                'is_priority' => $request->is_priority ?? false
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
        $job = UserJob::with('assignee', 'assigner', 'department')
            ->where('id', $id)
            ->first();

        if (!$job) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

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
            $today = Carbon::today()->format('Y-m-d');

            if ($request->action == 'cancel') {
                /**
                 * 14 August 2025
                 * - ditugas dibatalkan menjadi dihapus saja
                 */
                $job->delete();
                DB::commit();
                return response()->json(['message' => 'Tugas berhasil dihapus']);
            } else if ($now->gte($start) && $now->lte($end)) {
                $status = 'in_progress';
            } else if ($now->gt($end)) {
                return response()->json([
                    'message' => 'Tanggal selesai tidak boleh kurang dari hari ini'
                ], 400);
            }

            $explodedFeedback = explode('-', $request->feedback);
            $feedback = 'Pengerjaan ke-' . $explodedFeedback[1]
                . "\n" . "tgl. $today s.d. " . $request->end_date . "\n" . $request->notes;
            $isPriority = filter_var($request->is_priority, FILTER_VALIDATE_BOOLEAN);

            $job->update([
                'title'       => $request->title,
                'job_detail'  => $request->job_detail,
                'end_date'    => $request->end_date,
                'feedback'    => $feedback,
                'status'      => $status,
                'is_priority' => $isPriority
            ]);

            DB::commit();
            return response()->json(['message' => 'Berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $job = UserJob::where('id', $id)->first();

            if (!$job) return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
            if (Auth::user()->id != $job->assigner_id) return response()->json(['message' => 'Anda tidak berhak menghapus pekerjaan ini'], 403);

            $job->delete();

            DB::commit();
            return response()->json(['message' => 'Tugas berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
    }

    public function markComplete(Request $request, $id)
    {
        $job = UserJob::findOrFail($id);

        if (Auth::user()->id != $job->assigner_id) {
            return response()->json(['message' => 'Anda tidak berhak menyelesaikan pekerjaan ini.'], 403);
        }

        $request->validate([
            'action' => 'required|in:approve,revisi',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            if ($request->action == 'approve') {
                $job->update([
                    'status' => 'completed',
                    'notes' => null,
                    'updated_at' => now(),
                ]);
            } elseif ($request->action == 'revisi') {
                $job->update([
                    'status' => 'revision',
                    'notes' => $request->notes,
                    'completed_at' => null,
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Pekerjaan berhasil diperbarui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500); // sementara
        }
    }

    /**
     * 24 June 2025
     */
    public function uploadFile(Request $request, $id)
    {
        try {
            $job = UserJob::findOrFail($id);

            if (Auth::user()->id != $job->assignee_id) {
                return redirect()
                    ->back()
                    ->with([
                        'pesan' => 'Anda tidak berhak mengupload file pekerjaan ini.',
                        'level-alert' => 'alert-warning'
                    ]);
            }

            $request->validate([
                'report_file' => 'required|mimes:zip,rar,xlsx,xls,csv,pdf,jpg,jpeg,png,7z|max:122880'
            ]);

            $file = $request->file('report_file');
            $filename = time() . '-' . Str::random(10) . '.' . $file->extension();
            $path = $file->storeAs('uploads/files/job_reports', $filename, 'public');

            if ($job->report_file && Storage::disk('public')->exists($job->report_file)) {
                Storage::disk('public')->delete($job->report_file);
            }

            $job->update([
                'report_file' => $path,
                'status' => 'checking',
                'completed_at' => Carbon::now()->format('Y-m-d'),
            ]);

            return redirect()
                ->back()
                ->with([
                    'pesan' => 'File laporan pekerjaan berhasil diupload.',
                    'level-alert' => 'alert-success'
                ]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with([
                    'pesan' => 'Gagal mengupload file laporan pekerjaan.',
                    'level-alert' => 'alert-danger'
                ]);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'jobs_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            DB::transaction(function () use ($request) {
                Excel::import(new UserJobImport, $request->file('jobs_file'));
            });

            return back()->with([
                'pesan' => 'Data berhasil diimport!',
                'level-alert' => 'alert-success',
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'pesan' => $e->getMessage(),
                'level-alert' => 'alert-danger',
            ]);
        }
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(
            new UserJobExport($startDate, $endDate),
            'LAPORAN PENUGASAN - ' . $startDate . ' - ' . $endDate . '.xlsx'
        );
    }

    public function exportMyTasks(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(
            new MyTaskExport($startDate, $endDate),
            'REKAP TUGAS SAYA - ' . $startDate . ' - ' . $endDate . '.xlsx'
        );
    }

    public function downloadTemplateImport()
    {
        $filePath = storage_path('app/public/uploads/files/templates/template_import_penugasan.xlsx');

        if (!file_exists($filePath)) {
            return back()->with([
                'pesan' => 'Template tidak ditemukan.',
                'level-alert' => 'alert-danger'
            ]);
        }

        return response()->streamDownload(function () use ($filePath) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            readfile($filePath);
        }, 'template_import_penugasan.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_import_penugasan.xlsx"',
        ]);
    }
}
