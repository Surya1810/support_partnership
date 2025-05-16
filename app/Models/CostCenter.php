<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $guarded = ['id'];

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function subs() {
        return $this->hasMany(CostCenterSub::class);
    }
}
