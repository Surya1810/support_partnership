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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jobs = UserJob::with(['assigner', 'assignee'])->orderBy('created_at', 'desc');
            $jobs = $jobs->get();

            if ($request->has('status') && $request->status !== 'all') {
                $jobs = $jobs->filter(function ($job) use ($request) {
                    return $job->status === $request->status;
                })->values();
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

                    if ($job->assigner_id === $userId) {
                        $buttons .= '<button class="btn btn-sm btn-warning mr-1" title="Edit" type="button" onclick="modalEdit(this)" data-id="' . $job->id . '">
                        <i class="fas fa-pencil-alt"></i>
                     </button>';
                    }

                    if ($job->assignee_id === $userId && $job->status !== 'completed') {
                        $buttons .= '<button class="btn btn-sm btn-success" title="Selesaikan" type="button" onclick="markJobDone(this)" data-id="' . $job->id . '">
                        <i class="fas fa-check"></i>
                     </button>';
                    }

                    return $buttons;
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status;
                    $label = ucwords(str_replace('_', ' ', $status));

                    $badgeColor = match ($status) {
                        'planning'    => 'secondary',
                        'in_progress' => 'info',
                        'completed'   => 'success',
                        'cancelled'   => 'dark',
                        'overdue'     => 'danger',
                        default       => 'light',
                    };

                    return '<span class="badge badge-' . $badgeColor . '">' . $label . '</span>';
                })
                ->setRowClass(function ($job) {
                    return match ($job->status) {
                        'overdue'     => 'table-danger',
                        'completed'   => 'table-success',
                        'in_progress' => 'table-info',
                        'cancelled' => 'table-secondary',
                        default       => '',
                    };
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        $users = User::where('id', '!=', Auth::id())->with('department')->get();
        return view('jobs.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'assignee_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'detail' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $assignee = User::where('id', $request->assignee_id)->first();

        if (!$assignee->exists()) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        DB::beginTransaction();

        try {
            UserJob::create([
                'assigner_id' => Auth::id(),
                'assignee_id' => $request->assignee_id,
                'department_id' => $assignee->department_id,
                'title' => $request->title,
                'job_detail' => $request->detail,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => 'Pengerjaan ke-1',
            ]);

            DB::commit();
            return response()->json(['message' => 'Berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal disimpan'], 500);
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

        if (Auth::user()->id !== $job->assigner_id) {
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

            if ($request->action === 'cancel') {
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

        if (Auth::user()->id !== $job->assignee_id) {
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
