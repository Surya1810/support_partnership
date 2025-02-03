<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    protected $fillable = ['expense_request_id', 'item_name', 'quantity', 'unit_price', 'total_price', 'actual_amount'];

    public function expenseRequest()
    {
        return $this->belongsTo(ExpenseRequest::class);
    }
}
