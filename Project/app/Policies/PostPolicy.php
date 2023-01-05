<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class PostPolicy
{
    use HandlesAuthorization;

    public function list(User $user)
    {
      return Auth::check();
    }

    public function create(User $user)
    {
      return $user->id == Auth::user()->id;
    }

    public function delete(User $user, Post $post)
    {
      return ($user->id == Auth::user()->id) && ($user->id == $post->owner_id || $user->isAdmin() || $post->group()->owner_id == Auth::user()->id);
    }

    public function deletemedia(User $user, Post $post)
    {
      return ($user->id == Auth::user()->id) && ($user->id == $post->owner_id || $user->isAdmin());
    }

    public function edit(User $user, Post $post)
    {
      return $user->id == Auth::user()->id && $user->id == $post->owner_id;
    }

    public function search()
    {
      return Auth::check();
    }

    public function like() {
      return Auth::check();
    }

    public function dislike() {
      return Auth::check();
    }

}
