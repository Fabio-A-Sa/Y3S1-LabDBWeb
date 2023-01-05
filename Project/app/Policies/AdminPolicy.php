<?php

namespace App\Policies;

use Illuminate\Support\Facades\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function show() {
        return Auth::check() && Auth::user()->isAdmin();
    }   

    public function block_user() {
        return Auth::check() && Auth::user()->isAdmin();
    } 

    public function unblock_user() {
        return Auth::check() && Auth::user()->isAdmin();
    }

    public function delete_post() {
        return Auth::check() && Auth::user()->isAdmin();
    }
}