<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenterCategory extends Model
{
    protected $guarded = ['id'];

    public function costCenters()
    {
        return $this->hasMany(CostCenter::class);
    }
}
