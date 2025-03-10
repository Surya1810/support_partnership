<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'deadline' => 'datetime',
        'start' => 'datetime'
    ];

    protected $dates = ['deleted_at'];

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
}
