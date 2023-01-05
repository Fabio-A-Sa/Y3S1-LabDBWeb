<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class GroupPolicy
{
    use HandlesAuthorization;

    public function list()
    {   
        return Auth::check();
    }

    public function editPage(User $user, Group $group)
    {   
        return Auth::check() && $user->id == $group->owner_id;
    }

    public function edit(User $user, Group $group){
        return Auth::user()->id == $group->owner_id;
    }

    public function removeMember(User $user, Group $group){
        return Auth::user()->id == $group->owner_id;
    }

    public function create(){
        return Auth::check();
    }

    public function join(){
        return Auth::check();
    }

    public function leave(){
        return Auth::check();
    }

    public function doJoinRequest(){
        return Auth::check();
    }

    public function cancelJoinRequest(){
        return Auth::check();
    }

    public function acceptJoinRequest(){
        return Auth::check();
    }

    public function rejectJoinRequest(){
        return Auth::check();
    }

    public function invite() {
        return Auth::check();
    }

    public function cancelInvite() {
        return Auth::check();
    }

    public function delete(User $user, Group $group){
        return Auth::user()->id == $group->owner_id || Auth::user()->isAdmin();
    }

    public function rejectInvite() {
        return Auth::check();
    }

    public function acceptInvite() {
        return Auth::check();
    }

    public function favorite(User $user, Group $group){
        return Auth::check() && $group->hasMember($user) && !$group->isFavorite($user);
    }

    public function unfavorite(User $user, Group $group){
        return Auth::check() && $group->hasMember($user) && $group->isFavorite($user);
    }

    public function makeOwner(User $user, Group $group){
        return Auth::check() && Auth::user()->id == $group->owner_id && $group->hasMember($user);
    }

    public function deleteMedia(User $user, Group $group) {
        return Auth::check() && Auth::user()->id == $group->owner_id;
    }
}
