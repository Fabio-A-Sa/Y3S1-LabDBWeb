<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;
    
    public function show(User $me, User $user)
    {
      return (Auth::user()->id == $user->id) && !($user->isBlocked());
    }

    public function searchPage() 
    {
      return Auth::check();
    }

    public function notificationsPage() {
      return Auth::check();
    }

    public function editUser(User $user)
    {
      return $user->id == Auth::user()->id;
    }

    public function edit() 
    {
      return Auth::check();
    }

    public function search() 
    {
      return Auth::check();
    }

    public function delete(User $user) {
      return Auth::check() && (Auth::user()->id == $user->id || Auth::user()->isAdmin());
    }

    public function removeFollower() {
      return Auth::check();
    }

    public function follow() {
      return Auth::check();
    }

    public function unfollow() {
      return Auth::check();
    }

    public function doFollowRequest() {
      return Auth::check();
    }

    public function cancelFollowRequest() {
      return Auth::check();
    }

    public function acceptFollowRequest() {
      return Auth::check();
    }

    public function rejectFollowRequest() {
      return Auth::check();
    }

    public function deleteMedia(User $user) {
      return Auth::check() && Auth::user()->id == $user->id;
    }
}