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

use App\Imports\UserJobImport;
use App\Exports\UserJobExport;

class UserJobController extends Controller
{
    /**
     * Penugasan ke orang lain
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jobs = UserJob::with(['assigner', 'assignee'])
                ->when($request->has('status') && $request->status != 'all', function ($query) use ($request) {
                    if ($request->status == 'in_progress') {
                        $query->where('status', 'in_progress')
                            ->orWhere('status', 'planning');
                    } else if ($request->status == 'overdue') {
                        $today = Carbon::today();
                        $query->where('end_date', '<', $today)
                            ->where('completed_at', null);
                    } else {
                        $query->where('status', $request->status);
                    }
                })
                ->orderBy('created_at', 'desc');
            $roleId = Auth::user()->role_id;

            if ($roleId == 3) {
                $jobs = $jobs->where('department_id', Auth::user()->department_id);
            } else if ($roleId == 5) {
                $jobs = $jobs->where('assigner_id', Auth::id());
            }

            $clonedJobs = clone $jobs;

            $efficiencySum = 0;
            $efficiencyCount = 0;
            $hasAssigneeSearch = false;

            $searchKeyword = $request->input('search')['value'] ?? null;
            if (!empty($searchKeyword)) {
                $hasAssigneeSearch = User::where('name', 'like', "%{$searchKeyword}%")->exists();
            }

            if ($hasAssigneeSearch) {
                $clonedJobs = clone $jobs;
                $filteredJobs = $clonedJobs->whereHas('assignee', function ($query) use ($searchKeyword) {
                    $query->where('name', 'like', "%{$searchKeyword}%");
                })->get();

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
            }

            return DataTables::of($jobs)
                ->addIndexColumn()
                ->addColumn('assigner', fn($job) => $job->assigner->name ?? '-')
                ->addColumn('assignee', fn($job) => $job->assignee->name ?? '-')
                ->addColumn('department', fn($job) => $job->assignee->department->name ?? '-')
                ->addColumn('detail', fn($job) => $job->job_detail ?? '-')
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
                            $roundedPercentage = round($diffPercentage);
                            return $roundedPercentage == 0 ? "0%" : "+" . $roundedPercentage . "%";
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
                ->addColumn('actions', function ($job) {
                    $buttons = '';
                    $userId = Auth::id();

                    if ($job->assigner_id == $userId) {
                        $buttons .= '<button class="btn btn-sm btn-warning mr-1" title="Edit" type="button" onclick="modalEdit(this)" data-id="' . $job->id . '">
                        <i class="fas fa-pencil-alt"></i>
                     </button>';
                    }

                    if ($job->status == 'checking') {
                        $buttons .= '<button class="btn btn-sm btn-info m-1" title="Approve" type="button" onclick="modalApprove(this)" data-id="' . $job->id . '">
                        <i class="fas fa-check"></i>
                     </button>';
                    }

                    return $buttons;
                })
                ->filterColumn('assignee', function ($query, $keyword) {
                    $query->whereHas('assignee', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('department', function ($query, $keyword) {
                    $query->whereHas('assignee', function ($query) use ($keyword) {
                        $query->whereHas('department', function ($query) use ($keyword) {
                            $query->where('name', 'like', "%{$keyword}%");
                        });
                    });
                })
                ->with([
                    'total_efficiency' => (!empty($searchKeyword) && $efficiencyCount > 0)
                        ? round($efficiencySum)
                        : 0
                ])
                ->rawColumns(['actions', 'report_file'])
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
                ->addColumn('assigner', fn($job) => $job->assigner->name ?? '-')
                ->addColumn('assignee', fn($job) => $job->assignee->name ?? '-')
                ->addColumn('department', fn($job) => $job->assignee->department->name ?? '-')
                ->addColumn('detail', fn($job) => $job->job_detail ?? '-')
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
                ->with([
                    'total_efficiency' => ($efficiencyCount > 0)
                        ? round($efficiencySum)
                        : 0
                ])
                ->addColumn('actions', function ($job) {
                    return '<button class="btn btn-sm btn-warning mr-1" title="Upload Bukti Pekerjaan Selesai" type="button" onclick="modalUploadReportFile(' . $job->id . ')">
                        <i class="fas fa-upload"></i>
                    </button>';
                })
                ->rawColumns(['actions', 'report_file'])
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
                'report_file' => 'required|mimes:pdf,jpg,jpeg,png|max:10240'
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
