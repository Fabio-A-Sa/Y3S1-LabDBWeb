<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

use App\Models\Post;
use App\Models\Admin;
use App\Models\Blocked;
use App\Models\Group;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\PostLike;
use App\Models\Message;
use App\Models\RequestFollowing;

class User extends Authenticatable
{
    use Notifiable;
    public $timestamps  = false;

    protected $fillable = [
        'username', 'password', 'email', 'description', 'name', 'is_public', 'tsvectors'
    ];

    protected $hidden = [
        'password'
    ];

    public function isAdmin() {
        return count($this->hasOne('App\Models\Admin', 'id')->get());
    }

    public function isBlocked() {
        return count($this->hasOne('App\Models\Blocked', 'id')->get());
    }

    public function acceptNotification(string $type) {
        return $this->hasMany('App\Models\Configuration', 'user_id')->where('notification_type', $type)
                                                                    ->where('active', true)->exists();
    }

    public function ownPosts() {
      return $this->hasMany('App\Models\Post', 'owner_id')->where('group_id', null)->orderBy('date', 'desc');
    }

    public function getFollowers() {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->orderBy('name', 'asc');
    }

    public function getFollowing() {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->orderBy('name', 'asc');
    }

    public function follows(int $id) {
        return Follow::where('follower_id', $this->id)
                    ->where('followed_id', $id)->exists();
    }

    public function following(int $id) {
        return Follow::where('followed_id', $this->id)
                    ->where('follower_id', $id)->exists();
    }

    public function likesPost(int $id) {
        return PostLike::where('user_id', $this->id)
                    ->where('post_id', $id)->exists();
    }

    public function likesComment(int $id) {
        return CommentLike::where('user_id', $this->id)
                    ->where('comment_id', $id)->exists();
    }

    public function requestFollowing(int $id) {
        return RequestFollow::where('req_id', $this->id)
                            ->where('rcv_id', $id)->exists();
    }

    public function allGroups() {
        return Group::fromRaw('groups,member')
                ->where('member.user_id', $this->id)
                ->whereColumn('groups.id', 'member.group_id');
    }

    public function ownedGroups() {
        return $this->allGroups()->where('groups.owner_id', $this->id);
    }

    public function favoriteGroups() {
        
        return $this->allGroups()->where('member.is_favorite', true)
                                 ->where('groups.owner_id', '<>', $this->id);
    }

    public function nonFavoriteGroups() {
        return $this->allGroups()->where('member.is_favorite', false);
    }   

    public function canLikePost(Post $post) {

        $group = Group::find($post->group_id);
        if ($group) {
            return $post->is_public || $this->follows($post->owner_id) || User::find($post->owner_id)->is_public 
            || $group->is_public || $group->hasMember($this);
        } else {
            return $post->is_public || $this->follows($post->owner_id) || User::find($post->owner_id)->is_public;
        }
    }

    public function canLikeComment(Comment $comment) {
        return User::find($comment->owner_id)->is_public || $this->canLikePost(Post::find($comment->post_id));
    }

    public function visiblePosts() {
        
        $own = Post::select('*')->where('post.owner_id', '=', $this->id);

        $noGroups = Post::select('post.*')
            ->fromRaw('post,follows')
            ->where('follows.follower_id', '=', $this->id)
            ->whereColumn('follows.followed_id', '=', 'post.owner_id')
            ->where('post.group_id', null);


        $fromGroups = Post::select('post.*')
            ->fromRaw('post,member')
            ->where('member.user_id', $this->id)
            ->whereColumn('post.group_id','member.group_id');
            

        return $own->union($noGroups)->union($fromGroups)
            ->orderBy('date','desc');
    }

    public function media() { 
        $files = glob("images/profile/".$this->id.".jpg", GLOB_BRACE);
        $default = "/images/profile/default.jpg";
        if(sizeof($files) < 1) return $default;
        return "/".$files[0];
    }

    public function uniqueEmitters() {
        $users = Message::select('emitter_id')
            ->where('receiver_id', $this->id)
            ->union(
                Message::select('receiver_id')
                        ->where('emitter_id', $this->id)
                )
            ->distinct()->get();
        $all = array();
        foreach ($users as $user) {
            $user_found = User::find($user->emitter_id);
            
            if(str_contains($user_found->username, "deleted")) continue;
            array_push($all, $user_found);
        }
        return $all;
    }   

    public function messagesWith(User $user) {

        $messages =  Message::where(['receiver_id' => $user->id, 'emitter_id' => $this->id])
                            ->orWhere(
                                function($q) use($user){
                                    $q->where(['emitter_id' => $user->id, 'receiver_id' => $this->id]);
                                });
        
        Message::where(['emitter_id' => $user->id, 'receiver_id' => $this->id])->update(['viewed' => true]);
        
        return $messages->orderBy('date', 'asc')->get();
    }

    public function haveUnseenMessages() {
        return Message::where(['receiver_id' => $this->id, 
                               'viewed' => false])->exists();
    }

    public function haveUnseenMessagesWith(User $user) {
        return Message::where([ 'receiver_id' => $this->id,
                                'emitter_id' => $user->id, 
                                'viewed' => false])->exists();
    }
}
