<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseRequest extends Model
{

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'approved_by_manager',
        'approved_by_director',
        'processed_by_finance'
    ];

    protected $casts = [
        'use_date' => 'datetime'
    ];

    public function items()
    {
        return $this->hasMany(ExpenseItem::class, 'expense_request_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
