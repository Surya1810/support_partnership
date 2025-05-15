<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenterSub extends Model
{
    protected $guarded = ['id'];

    public function costCenter() {
        return $this->belongsTo(CostCenter::class);
    }
}
