<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestTag extends Model
{
    public $table = 'test_tags';
    protected $guarded = ['id'];
}
