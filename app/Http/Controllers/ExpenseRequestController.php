<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExpenseItem;
use App\Models\ExpenseRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::all()->except(8);

        //query saya
        $my_expenses = ExpenseRequest::where('user_id', Auth::id())->whereIn('status', ['pending', 'approved', 'processing', 'rejected'])->orderBy('created_at', 'desc')->get();
        $reports = ExpenseRequest::where('user_id', Auth::id())->whereIn('status', ['report', 'finish'])->orderBy('created_at', 'desc')->get();

        if (Auth::user()->role_id == 1 || (Auth::user()->role_id == 2 || Auth::user()->department_id == 8)) {
            //query seluruh data
            $all_expenses = ExpenseRequest::orderBy('created_at', 'desc')->get();
        } else {
            $all_expenses = [];
        }

        //query manager
        if (Auth::user()->department_id == 3) {
            $managerRequests = ExpenseRequest::where('status', 'pending')
                ->whereIn('department_id', [2, 3])
                ->where(function ($query) {
                    $query->where('total_amount', '<=', 150000)
                        ->orWhere('approved_by_manager', false);
                })
                ->where('user_id', '!=', Auth::user()->id)
                ->get();
        } elseif (Auth::user()->department_id == 5) {
            $managerRequests = ExpenseRequest::where('status', 'pending')
                ->whereIn('department_id', [5, 6, 7])
                ->where(function ($query) {
                    $query->where('total_amount', '<=', 150000)
                        ->orWhere('approved_by_manager', false);
                })
                ->where('user_id', '!=', Auth::user()->id)
                ->get();
        } else {
            $managerRequests = [];
        }

        //query direktur  
        if (Auth::user()->id == 2) {
            $directorRequests = ExpenseRequest::where('status', 'pending')
                ->where(function ($query) {
                    $query->where('total_amount', '>', 150000)
                        ->orWhereHas('user', function ($q) {
                            $q->where('role_id', 3);
                        });
                })
                ->orWhereIn('department_id', [1, 4, 8])
                ->get();
        } else {
            $directorRequests = [];
        }

        return view('finance.application', compact('departments', 'my_expenses', 'managerRequests', 'directorRequests', 'all_expenses', 'reports'));
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
        $request->validate([
            'department_id' => 'bail|required',
            'category' => 'bail|required|',
            'use_date' => 'bail|required|max:255',
            'items' => 'required|array',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $old = session()->getOldInput();

        $user = User::where('id', $request->user_id)->first();

        // Simpan data pengajuan biaya
        $expenseRequest = new ExpenseRequest();
        $expenseRequest->user_id = $request->user_id;
        $expenseRequest->department_id = $request->department_id;
        $expenseRequest->title = $request->title;
        $expenseRequest->category = $request->category;
        $expenseRequest->use_date = $request->use_date;
        if ($request->pencairan == 'saya') {
            $expenseRequest->bank_name = $user->extension->bank;
            $expenseRequest->account_number = $user->extension->account;
            $expenseRequest->account_holder_name = $user->name;
        } elseif ($request->pencairan == 'lain') {
            $expenseRequest->bank_name = $request->bank1;
            $expenseRequest->account_number = $request->rekening1;
            $expenseRequest->account_holder_name = $request->atas_nama;
        } elseif ($request->pencairan == 'va') {
            $expenseRequest->bank_name = $request->bank;
            $expenseRequest->account_number = $request->rekening;
            $expenseRequest->account_holder_name = '-';
        }
        if (Auth::user()->role_id === 3) {
            $expenseRequest->approved_by_manager = true;
        }
        $expenseRequest->total_amount = 0;
        $expenseRequest->save();

        $totalAmount = 0;

        // Simpan detail barang
        foreach ($request->items as $item) {
            $expenseItem = new ExpenseItem();
            $expenseItem->expense_request_id = $expenseRequest->id;
            $expenseItem->item_name = $item['item_name'];
            $expenseItem->quantity = $item['quantity'];
            $expenseItem->unit_price = $item['unit_price'];
            $expenseItem->total_price = $item['quantity'] * (int)$item['unit_price'];
            $expenseItem->save();

            $totalAmount += $expenseItem->total_price;
        }

        // Perbarui total amount pada pengajuan biaya
        $expenseRequest->total_amount = $totalAmount;
        $expenseRequest->save();

        return redirect()->route('application.index')->with(['pesan' => 'Application created successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseRequest $expenseRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseRequest $expenseRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseRequest $expenseRequest)
    {
        //
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

        return redirect()->route('application.index')->with(['pesan' => 'Application deleted successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function approve($id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);

        // Pastikan pengajuan belum disetujui
        if ($expenseRequest->status === 'approved') {
            return redirect()->back()->with(['pesan' => 'Application approved before', 'level-alert' => 'alert-warning']);
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
            if (!$expenseRequest->approved_by_manager && $expenseRequest->total_amount > 150000) {
                return redirect()->back()->with(['pesan' => 'Application must approved by manager first', 'level-alert' => 'alert-danger']);
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

            return redirect()->route('application.index')->with(['pesan' => 'Application approved successfully by admin', 'level-alert' => 'alert-success']);
        }

        // Cek apakah approval selesai
        if ($expenseRequest->approved_by_manager && $expenseRequest->approved_by_director) {
            $expenseRequest->status = 'approved';

            // Kirim ke department finance
            $this->sendToFinance($expenseRequest);
        }

        $expenseRequest->save();

        return redirect()->route('application.index')->with(['pesan' => 'Application approved successfully', 'level-alert' => 'alert-success']);
    }

    private function sendToFinance($expenseRequest)
    {
        $expenseRequest->status = 'processing';
        $expenseRequest->save();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function reject($id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);
        $expenseRequest->status = 'rejected';
        $expenseRequest->save();

        return redirect()->route('application.index')->with(['pesan' => 'Application rejected successfully', 'level-alert' => 'alert-danger']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function process($id)
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);
        $expenseRequest->status = 'report';
        $expenseRequest->save();

        return redirect()->route('application.index')->with(['pesan' => 'Application processed successfully', 'level-alert' => 'alert-success']);
    }

    public function report(Request $request, $id)
    {
        $expenseRequest = ExpenseRequest::with('items')->findOrFail($id);
        $expenseRequest->status = 'finish';

        // Validasi input
        $request->validate([
            'actual_amounts' => 'required|array',
            'actual_amounts.*' => 'numeric|min:0',
        ]);

        // Simpan actual amount ke database
        foreach ($request->actual_amounts as $itemId => $actualAmount) {
            $item = ExpenseItem::find($itemId);
            if ($item) {
                $item->update(['actual_amount' => $actualAmount]);
            }
        }

        $expenseRequest->save();

        return redirect()->route('application.index')->with(['pesan' => 'Application reported successfully', 'level-alert' => 'alert-success']);
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
