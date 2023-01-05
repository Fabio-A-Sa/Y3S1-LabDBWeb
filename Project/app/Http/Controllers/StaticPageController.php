<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public static function about() {
        $admins = UserController::getAdmins();
        return view('pages.about', ['admins' => $admins, 'userClass' => User::class]);
    }

    public static function help() {
        $admins = UserController::getAdmins();
        return view('pages.help', ['admins' => $admins]);
    }

    public static function features() {
        $admins = UserController::getAdmins();
        return view('pages.main_features');
    }
}