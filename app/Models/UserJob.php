<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserJob extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['status'];

    public function getStatusAttribute(): string
    {
        if (in_array($this->attributes['status'], ['completed', 'cancelled'])) {
            return $this->attributes['status'];
        }

        $now = Carbon::today();
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->startOfDay();

        if ($now->lt($start)) {
            return 'planning';
        }

        if ($now->gte($start) && $now->lte($end)) {
            return 'in_progress';
        }

        if ($now->gt($end)) {
            return 'overdue';
        }

        return 'unknown';
    }


    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigner_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
