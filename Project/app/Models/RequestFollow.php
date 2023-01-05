<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestFollow extends Model
{
    use HasFactory;
    protected $table = 'follow_request';
    public $timestamps  = false;

    protected $fillable = [
        'req_id', 'rcv_id'
    ];   
}
