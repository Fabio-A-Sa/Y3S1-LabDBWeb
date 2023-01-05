<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blocked extends Model
{
    use HasFactory;

    protected $table = 'blocked';
    public $timestamps  = false;

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
