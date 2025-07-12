<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CostCenter extends Model
{
    protected $guarded = ['id'];

    public function getMonthNameAttribute() {
        return Carbon::create()->month($this->month)->format('F');
    }

    public function category()
    {
        return $this->belongsTo(CostCenterCategory::class, 'cost_center_category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function expenses()
    {
        return $this->hasMany(ExpenseRequest::class, 'cost_center_id');
    }
}
