<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExtension extends Model
{
    // Relasi balik ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
