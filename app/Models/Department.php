<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function income()
    {
        return $this->hasMany(Income::class);
    }

    public function costCenters() {
        return $this->hasMany(CostCenter::class);
    }
}
