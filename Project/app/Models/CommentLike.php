<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CommentLike extends Model
{
    use HasFactory;
    protected $table = 'comment_likes';
    public $timestamps  = false;

    protected $fillable = [
        'user_id', 'comment_id'
    ];    

    public function comment() {
        $this->belongsTo('App\Models\Comment');
    }

    public function user() {
        $this->belongsTo('App\Models\User');
    }
}
