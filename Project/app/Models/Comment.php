<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Group;
use App\Models\Post;

class Comment extends Model
{
    use HasFactory;
    protected $table = 'comment';
    public $timestamps  = false;

    protected $fillable = [
        'owner_id', 'post_id', 'content', 'date', 'previous'
    ];

    public function owner() {
        return User::find($this->owner_id);
    }

    public function likes() {
        return count($this->hasMany('App\Models\CommentLike')->get());
    }

    public function getNext() {
        return Comment::where('previous', $this->id)->get();
    }

    public function countReplies() {
        $total = 0;
        foreach ($this->getNext() as $next) {
            $total = $total + 1 + $next->countReplies();
        }
        return $total;
    }

    public function post() {
        return Post::find($this->post_id)->get();
    }

    public function group() {
        return Group::find($this->post()->group_id)->get();
    }
}
