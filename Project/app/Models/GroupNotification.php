<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupNotification extends Model
{
    use HasFactory;
    protected $table = 'group_notification';
    public $timestamps  = false;

    protected $fillable = [
        'group_id', 'notification_type'
    ];
}
