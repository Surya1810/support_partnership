<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use App\Models\Department;
use App\Models\ExpenseItem;
use App\Models\ExpenseRequest;
use App\Models\Project;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ExpenseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::all()->except([2, 4, 6, 7]);
        $my_expenses = ExpenseRequest::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved', 'processing', 'rejected'])->orderBy('created_at', 'desc')
            ->with('costCenter')
            ->get();
        $reports = ExpenseRequest::where('user_id', Auth::id())
            ->whereIn('status', ['report', 'checking', 'finish'])
            ->orderBy('created_at', 'desc')
            ->get();

        $user_department = Auth::user()->department_id;
        $limit = ExpenseRequest::where('department_id', $user_department)
            ->whereNotNull('status')
            ->whereNotIn('status', ['finish', 'rejected'])
            ->count();

        // get cost center depends on user department_id and project
        $projects = Project::where('department_id', $user_department)
            ->where('status', '!=', 'Finished')
            ->get();

        // jika user adalah bagian finance maka get general affair
        $generalCostCenters = CostCenter::where('department_id', $user_department == 8 ? 9 : $user_department)
            ->where('type', 'department')
            ->whereNot('cost_center_category_id', 1)
            ->where('year', date('Y'))
            ->get();

        return view(
            'finance.application',
            compact('departments', 'projects', 'my_expenses', 'reports', 'limit', 'generalCostCenters')
        );
    }

    public function approval()
    {
        if (Auth::user()->role_id == 1 || (Auth::user()->role_id == 2 || Auth::user()->department_id == 8)) {
            //query seluruh data
            $all_expenses = ExpenseRequest::with(['items', 'costCenter'])
                ->orderBy('created_at', 'desc')->get();
        } else {
            $all_expenses = [];
        }

        //query manager
        if (Auth::user()->department_id == 3) {
            $managerRequests = ExpenseRequest::where('status', 'pending')
                ->where('department_id', 3)
                ->where(function ($query) {
                    $query->where('total_amount', '<=', 150000)
                        ->orWhere('approved_by_manager', false);
                })
                ->where('user_id', '!=', Auth::user()->id)
                ->with('costCenter')
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif (Auth::user()->department_id == 5) {
            $managerRequests = ExpenseRequest::where('status', 'pending')
                ->where('department_id', 5)
                ->where(function ($query) {
                    $query->where('total_amount', '<=', 150000)
                        ->orWhere('approved_by_manager', false);
                })
                ->where('user_id', '!=', Auth::user()->id)
                ->with('costCenter')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $managerRequests = [];
        }

        //query direktur
        if (Auth::user()->id == 2) {
            $directorRequests = ExpenseRequest::where('status', 'pending')
                ->where(function ($query) {
                    $query->where('total_amount', '>', 150000)
                        ->orWhere('department_id', 1)
                        ->orWhereHas('user', function ($q) {
                            $q->where('role_id', 3);
                        });
                })
                ->where('approved_by_manager', true)
                ->orWhereIn('department_id', [8, 9])
                ->with('costCenter')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $directorRequests = [];
        }

        return view('finance.approval', compact('managerRequests', 'directorRequests', 'all_expenses',));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'bail|required',
            'category' => 'bail|required|',
            'use_date' => 'bail|required|max:255',
            'project_cost_center_id' => 'bail|nullable|exists:cost_centers,id',
            'reference_file' => 'bail|nullable|mimes:jpg,jpeg,png,webp,pdf|max:122880',
            'items' => 'required|array',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->user_id);

            $expenseRequest = new ExpenseRequest();
            $expenseRequest->user_id = $user->id;

            if (in_array($request->department_id, [1, 8, 9])) {
                $expenseRequest->department_id = $request->department_id;
                $expenseRequest->approved_by_manager = true;
            } else {
                $expenseRequest->department_id = $request->department_id == 8 ? 9 : $request->department_id;
            }

            $expenseRequest->title = $request->title;
            $explodedCategory = explode('#', $request->category);
            $category = $explodedCategory[1];

            if ($category == 'project') {
                $expenseRequest->project_id = $explodedCategory[0];
                $expenseRequest->cost_center_id = $request->project_cost_center_id;
            } else {
                $expenseRequest->project_id = null;
                $expenseRequest->cost_center_id = $explodedCategory[0];
            }

            $expenseRequest->category = $category;
            $expenseRequest->use_date = $request->use_date;

            if ($request->pencairan === 'saya') {
                $expenseRequest->bank_name = $user->extension->bank;
                $expenseRequest->account_number = $user->extension->account;
                $expenseRequest->account_holder_name = $user->name;
            } elseif ($request->pencairan === 'lain') {
                $expenseRequest->bank_name = $request->bank1;
                $expenseRequest->account_number = $request->rekening1;
                $expenseRequest->account_holder_name = $request->atas_nama;
            } elseif ($request->pencairan === 'va') {
                $expenseRequest->bank_name = $request->bank;
                $expenseRequest->account_number = $request->rekening;
                $expenseRequest->account_holder_name = '-';
            }

            if (Auth::user()->role_id == 3) {
                $expenseRequest->approved_by_manager = true;
            }

            if ($request->hasFile('reference_file')) {
                $file = $request->file('reference_file');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/files/pengajuan/references', $fileName, 'public');
                $expenseRequest->reference_file = $path;
            }

            $expenseRequest->code_ref_request = $this->generateCodeRefRequest($expenseRequest->cost_center_id);
            $expenseRequest->total_amount = 0;
            $expenseRequest->save();

            // --- CEK TOTAL AMOUNT ---
            $costCenter = CostCenter::findOrFail($expenseRequest->cost_center_id);
            $totalAmount = 0;

            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * (int) $item['unit_price'];
            }

            if ($totalAmount > $costCenter->amount_remaining) {
                // rollback & return error
                DB::rollBack();
                return redirect()->back()->withInput()->with([
                    'pesan' => 'Sisa saldo dari Cost Center tidak mencukupi.',
                    'level-alert' => 'alert-danger'
                ]);
            }

            // Simpan item jika aman
            foreach ($request->items as $item) {
                ExpenseItem::create([
                    'expense_request_id' => $expenseRequest->id,
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * (int) $item['unit_price'],
                ]);
            }

            // Update total pengajuan
            $expenseRequest->total_amount = $totalAmount;
            $expenseRequest->save();

            DB::commit();

            return redirect()->route('application.index')->with([
                'pesan' => 'Pengajuan berhasil dibuat!',
                'level-alert' => 'alert-success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with([
                'pesan' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage(),
                'level-alert' => 'alert-danger'
            ]);
        }
    }

    private function generateCodeRefRequest($costCenterId)
    {
        $costCenterTarget = CostCenter::where('id', $costCenterId)
            ->with('category')
            ->first();

        if ($costCenterTarget->category->code == 'BP' && $costCenterTarget->type == 'project') {
            return $costCenterTarget->code_ref;
        }

        $expenseRequest = ExpenseRequest::where('cost_center_id', $costCenterId)
            ->orderBy('id', 'desc')
            ->first();

        $currentTransactionNumber = '0001';

        if ($expenseRequest) {
            $lastCodeRefRequest = $expenseRequest->code_ref_request;
            $explodedLastCodeRefRequest = explode('/', $lastCodeRefRequest);

            if (array_key_exists(2, $explodedLastCodeRefRequest)) {
                $currentTransactionNumber = str_pad((int) $explodedLastCodeRefRequest[2] + 1, 4, '0', STR_PAD_LEFT);
            }
        }

        $codeRef = $costCenterTarget->code_ref . '/' . $currentTransactionNumber;

        return $codeRef;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);
        // Hapus semua item terkait menggunakan relasi
        $expenseRequest->items()->delete();
        // Hapus pengajuan
        $expenseRequest->delete();

        return redirect()->route('application.index')->with(['pesan' => 'Pengajuan deleted successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function approve($id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);

        // Pastikan pengajuan belum disetujui
        if ($expenseRequest->status === 'approved') {
            return redirect()->back()->with(['pesan' => 'Pengajuan telah disetujui sebelumnya.', 'level-alert' => 'alert-warning']);
        }

        // Ambil role pengguna dari auth
        $userRole = Auth::user()->role->name;

        // Logika untuk Manager
        if ($userRole === 'Manager') {
            if ($expenseRequest->total_amount > 150000) {
                // return redirect()->back()->with(['pesan' => 'Application only approved by director', 'level-alert' => 'alert-danger']);
                $expenseRequest->approved_by_manager = true;
            } else {
                $expenseRequest->approved_by_manager = true;
                $expenseRequest->approved_by_director = true;
            }
        }

        // Logika untuk Direktur
        if ($userRole === 'Director') {
            // if (!$expenseRequest->approved_by_manager && $expenseRequest->total_amount > 150000) {

            if (!$expenseRequest->approved_by_manager && $expenseRequest->total_amount > 150000) {
                return redirect()->back()->with(['pesan' => 'Pengajuan harus disetujui oleh manager terlebih dahulu', 'level-alert' => 'alert-danger']);
            }
            $expenseRequest->approved_by_director = true;
        }

        // Logika untuk Administrator (Bypass Approval)
        if ($userRole === 'Administrator') {
            $expenseRequest->approved_by_manager = true;
            $expenseRequest->approved_by_director = true;
            $expenseRequest->status = 'approved';

            // Kirim ke department finance
            $this->sendToFinance($expenseRequest);
            $expenseRequest->save();

            return redirect()->route('application.approval')->with(['pesan' => 'Pengajuan berhasil disetujui oleh admin', 'level-alert' => 'alert-success']);
        }

        // Cek apakah approval selesai
        if ($expenseRequest->approved_by_manager && $expenseRequest->approved_by_director) {
            $expenseRequest->status = 'approved';

            // Kirim ke department finance
            $this->sendToFinance($expenseRequest);
        }

        $expenseRequest->save();

        return redirect()->route('application.approval')->with(['pesan' => 'Pengajuan berhasil disetujui', 'level-alert' => 'alert-success']);
    }

    private function sendToFinance($expenseRequest)
    {
        $expenseRequest->status = 'processing';
        $expenseRequest->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $expenseRequest = ExpenseRequest::findOrFail($id);
        $expenseRequest->status = 'rejected';
        $expenseRequest->rejection_reason = $request->input('reason');
        $expenseRequest->save();

        return redirect()->route('application.approval')->with(['pesan' => 'Pengajuan rejected successfully', 'level-alert' => 'alert-danger']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function process($id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);

        /**
         * kurangin sisa saldo cost center
         * yang menjadi target dari pengajuan
         */
        $costCenter = CostCenter::find($expenseRequest->cost_center_id);

        if ($costCenter->amount_remaining < $expenseRequest->total_amount) {
            return redirect()->back()->with(['pesan' => 'Saldo cost center tidak mencukupi', 'level-alert' => 'alert-danger']);
        }

        $costCenter->amount_remaining -= $expenseRequest->total_amount;
        $costCenter->save();

        $expenseRequest->status = 'report';
        $expenseRequest->processed_by_finance = true;
        $expenseRequest->save();

        return redirect()->route('application.approval')->with(['pesan' => 'Pengajuan berhasil diproses', 'level-alert' => 'alert-success']);
    }

    public function report(Request $request, $id)
    {
        // Validasi awal input dasar
        $request->validate([
            'report_file' => 'required|mimes:jpg,jpeg,png,webp,pdf|max:122880',
            'actual_amounts' => 'required|array',
            'actual_amounts.*' => 'numeric|min:0',
        ]);

        $expenseRequest = ExpenseRequest::with('items')->findOrFail($id);

        // Upload file
        if ($request->hasFile('report_file')) {
            $reportFile = $request->file('report_file');
            $randomFileName = time() . '-' . Str::random(10) . '.' . $reportFile->getClientOriginalExtension();
            $path = $reportFile->storeAs('uploads/files/pengajuan/reports', $randomFileName, 'public');
            $expenseRequest->report_file = $path;
        }

        foreach ($request->actual_amounts as $itemId => $actualAmount) {
            $item = ExpenseItem::findOrFail($itemId);

            // Validasi terhadap total_price
            if ($actualAmount > $item->total_price) {
                return back()->withErrors([
                    "actual_amounts.{$itemId}" => "Nilai terpakai tidak boleh lebih besar dari nilai diajukan: Rp " . number_format($item->total_price, 0, ',', '.')
                ])->withInput();
            }

            // Simpan jika valid
            $item->update(['actual_amount' => $actualAmount]);
        }

        $expenseRequest->status = 'checking';
        $expenseRequest->reason_reject_report = null;
        $expenseRequest->save();

        return redirect()->route('application.index')->with([
            'pesan' => 'Laporan pengajuan berhasil dibuat',
            'level-alert' => 'alert-success'
        ]);
    }

    public function checking(Request $request, $id)
    {
        // dd($request->all());
        $expenseRequest = ExpenseRequest::findOrFail($id);

        if ($request->has('reason')) {
            $expenseRequest->status = 'report';
            $expenseRequest->reason_reject_report = $request->input('reason');
            $expenseRequest->save();

            return redirect()->back()->with(['pesan' => 'Laporan pengajuan berhasil ditolak', 'level-alert' => 'alert-success']);
        }

        $expenseRequest->status = 'finish';
        $expenseRequest->reason_reject_report = null;
        $expenseRequest->save();

        /**
         * jika ada sisa, maka kembalikan ke cost centernya
         */
        $costCenter = CostCenter::find($expenseRequest->cost_center_id);
        $remaining = $expenseRequest->total_amount - $expenseRequest->items()->sum('actual_amount');
        $costCenter->amount_remaining += $remaining;

        $note = $costCenter->detail ? $costCenter->detail . '<hr style="margin:0"/>' : '';
        $note .= '<small><span class="text-success">Saldo kembali: '
            . formatRupiah($remaining) . '</span>'
            . '<br/>Tanggal: ' . date('d-m-Y')
            . '<br/>Sumber: Sisa Pengajuan ' . $expenseRequest->code_ref_request
            . '</small>';
        $costCenter->detail = $remaining > 0 ? $note : null;
        $costCenter->save();

        return redirect()->back()->with(['pesan' => 'Laporan pengajuan berhasil disetujui', 'level-alert' => 'alert-success']);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'action' => 'required|in:approve,reject',
        ]);

        $userRole = Auth::user()->role->name;
        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($request->selected_ids as $id) {
            $expenseRequest = ExpenseRequest::find($id);

            if (!$expenseRequest) continue;

            // Skip jika sudah di-approve
            if ($expenseRequest->status === 'approved') continue;

            if ($request->action === 'approve') {
                if ($userRole === 'Manager') {
                    if ($expenseRequest->total_amount > 150000) {
                        $expenseRequest->approved_by_manager = true;
                    } else {
                        $expenseRequest->approved_by_manager = true;
                        $expenseRequest->approved_by_director = true;
                    }
                }

                if ($userRole === 'Director') {
                    if (!$expenseRequest->approved_by_manager && $expenseRequest->total_amount > 150000) {
                        continue; // Lewati jika belum disetujui manager
                    }
                    $expenseRequest->approved_by_director = true;
                }

                if ($userRole === 'Administrator') {
                    $expenseRequest->approved_by_manager = true;
                    $expenseRequest->approved_by_director = true;
                    $expenseRequest->status = 'approved';
                    $this->sendToFinance($expenseRequest);
                    $expenseRequest->save();
                    $approvedCount++;
                    continue;
                }

                // Cek apakah sudah lengkap approve
                if ($expenseRequest->approved_by_manager && $expenseRequest->approved_by_director) {
                    $expenseRequest->status = 'approved';
                    $this->sendToFinance($expenseRequest);
                }

                $expenseRequest->save();
                $approvedCount++;
            }

            if ($request->action === 'reject') {
                $expenseRequest->status = 'rejected';
                $expenseRequest->rejection_reason = 'Mass rejection';
                $expenseRequest->save();
                $rejectedCount++;
            }
        }

        return redirect()->back()->with(['pesan' => "$approvedCount pengajuan telah disetujui, dan $rejectedCount pengajuan ditolak.", 'level-alert' => 'alert-success']);
    }

    public function pdf($id)
    {
        $expenseRequest = ExpenseRequest::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('finance.pdf', compact('expenseRequest'))
            ->setPaper([0, 0, 595.28, 935.45], 'portrait'); // Ukuran F4 dalam mm

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Application_Report.pdf"');
    }
}
