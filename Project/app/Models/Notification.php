<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notification';
    public $timestamps  = false;

    protected $fillable = [
        'date', 'notified_user', 'emitter_user', 'viewed'
    ];
}
