<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Comment $comment) {
        return Auth::check() && (Auth::user()->isAdmin() || Auth::user()->id == $comment->owner_id || Auth::user()->id == $comment->group()->owner_id);
    }

    public function edit(User $user, Comment $comment) {
        return Auth::check() && Auth::user()->id == $comment->owner_id;
    }

    public function create() {
        return Auth::check();
    }

    public function like() {
        return Auth::check();
    }
  
    public function dislike() {
        return Auth::check();
    }
}
