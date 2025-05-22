<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    protected $guarded = ['id'];

    public function expenseRequest()
    {
        return $this->belongsTo(ExpenseRequest::class);
    }
}
