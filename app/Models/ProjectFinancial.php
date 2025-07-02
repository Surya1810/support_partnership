<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFinancial extends Model
{
    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
