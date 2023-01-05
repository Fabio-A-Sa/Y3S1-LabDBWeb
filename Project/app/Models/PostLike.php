<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PostLike extends Model
{
    use HasFactory;
    protected $table = 'post_likes';
    public $timestamps  = false;

    protected $fillable = [
        'user_id', 'post_id'
    ];    

    public function post() {
        $this->belongsTo('App\Models\Post');
    }

    public function user() {
        $this->belongsTo('App\Models\User');
    }
}
