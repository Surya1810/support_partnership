<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExtension extends Model
{
    protected $casts = [
        'dob' => 'datetime'
    ];

    // Relasi balik ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
