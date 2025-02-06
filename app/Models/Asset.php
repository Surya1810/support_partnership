<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['rfid_number', 'name', 'code', 'type', 'condition', 'tgl_perawatan', 'tahun_perolehan', 'harga_perolehan', 'masa_guna', 'status', 'desc', 'gedung', 'lantai', 'ruangan', 'user_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tag()
    {
        return $this->belongsTo(tag::class, 'rfid_number');
    }

    // public function histories()
    // {
    //     return $this->hasMany(AssetHistory::class);
    // }

    // public function approvals()
    // {
    //     return $this->hasMany(AssetApproval::class);
    // }
}
