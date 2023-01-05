<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostNotification extends Model
{
    use HasFactory;
    protected $table = 'post_notification';
    public $timestamps  = false;

    protected $fillable = [
        'post_id', 'notification_type'
    ];
}
