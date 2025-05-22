<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseRequest extends Model
{

    protected $guarded = ['id'];
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
