<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

class Post extends Model
{
  protected $table = 'post';
  public $timestamps  = false;

  protected $fillable = [
    'owner_id', 'group_id', 'content', 'date', 'is_public'
  ];

  public function owner() {
    return $this->belongsTo('App\Models\User');
  }

  public static function publicPosts() {
    return Post::select('post.*')
                ->join('users', 'users.id', '=', 'post.owner_id')
                ->where('users.is_public', true)
                ->where('post.is_public', true)
                ->orderBy('date', 'desc');
  }

  public function likes() {
    return count($this->hasMany('App\Models\PostLike')->get());
  }

  public function comments() {
    return $this->hasMany('App\Models\Comment')->where('previous', null)->get();
  }

  public function media() { 
    $files = glob("images/post/".$this->id.".{png,jpg,jpeg,gif,mp4}", GLOB_BRACE);
    if(sizeof($files) < 1) return null;
    return $files[0];
  }

  public function group(){
    return $this->belongsTo('App\Models\Group');
  }
}