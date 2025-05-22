<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectOtherCost extends Model
{
    protected $table = 'project_other_costs';
    protected $guarded = ['id'];

    public function financial() {
        return $this->belongsTo(ProjectFinancial::class, 'project_financial_id');
    }
}
