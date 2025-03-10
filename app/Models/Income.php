<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'department_id',
        'project_id',
        'desc',
        'amount',
        'category'
    ];
}
