<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function extension()
    {
        return $this->hasOne(UserExtension::class);
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Date: 21/05/2025
     *
     * relation to UserJob for User Job Menu
     */
    public function givenJobs() {
        return $this->hasMany(UserJob::class, 'assigner_id');
    }

    public function receivedJobs() {
        return $this->hasMany(UserJob::class, 'assignee_id');
    }
}
