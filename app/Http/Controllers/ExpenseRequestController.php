<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExpenseItem;
use App\Models\ExpenseRequest;
use App\Models\User;
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
        $my_expenses = ExpenseRequest::where('user_id', Auth::id())->get();

        //query seluruh data
        $all_expenses = ExpenseRequest::all();

        //query manager
        $user_by_department = User::where('department_id', Auth::user()->department_id)->pluck('id')->toArray();
        // dd($user_by_department);
        $managerRequests = ExpenseRequest::where('status', 'pending')
            ->whereIn('user_id', $user_by_department)
            ->where(function ($query) {
                $query->where('total_amount', '<=', 150000)
                    ->orWhere('approved_by_manager', false);
            })
            ->where('user_id', '!=', Auth::user()->id)
            ->get();

        //query direktur  
        $directorRequests = ExpenseRequest::where('status', 'pending')
            ->where(function ($query) {
                $query->where('total_amount', '>', 150000)
                    ->orWhereHas('user', function ($q) {
                        $q->where('role_id', 3);
                    });
            })
            ->get();

        return view('finance.application', compact('departments', 'my_expenses', 'managerRequests', 'directorRequests', 'all_expenses'));
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

        $expenseRequest->approved_by_manager = false;
        $expenseRequest->approved_by_director = false;
        $expenseRequest->status = 'reject';
        $expenseRequest->update();

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
}
