<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'deadline' => 'datetime',
        'start' => 'datetime'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function pic()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function expense()
    {
        return $this->hasMany(ExpenseRequest::class, 'project_id');
    }
    public function income()
    {
        return $this->hasOne(Income::class, 'project_id');
    }

    /**
     * Date: 22/05/2025
     */
    public function financial() {
        return $this->hasOne(ProjectFinancial::class);
    }
}
