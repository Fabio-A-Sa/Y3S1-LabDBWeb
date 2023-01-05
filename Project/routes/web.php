<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\User;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ImageController;
// Home
Route::group(['middleware' => ['auth']], function() {
    Route::get('/', [LoginController::class, 'home']);
    Route::get('home', [PostController::class, 'list'])->name('home');
    Route::get('home/search', [UserController::class, 'searchPage'])->name('search');
    Route::get('home/notifications', [UserController::class, 'notificationsPage'])->name('notifications');
    Route::get('messages', [MessageController::class, 'messages'])->name('messages');
});

// Static pages
Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/help', [StaticPageController::class, 'help'])->name('help');
Route::get('/features', [StaticPageController::class, 'features'])->name('features');

// Posts
Route::post('post/create', [PostController::class, 'create']);
Route::post('post/delete', [PostController::class, 'delete']);
Route::post('post/edit', [PostController::class, 'edit']);
Route::post('post/like', [PostController::class, 'like']);
Route::post('post/dislike', [PostController::class, 'dislike']);

// Authentication
Route::get('login', [LoginController::class, 'show'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('register', [RegisterController::class, 'show'])->name('register');
Route::post('register', [RegisterController::class, 'register']);
Route::post('sendEmail', [LoginController::class, 'sendEmail'])->name('sendEmail');
Route::post('recoverPassword', [LoginController::class, 'recoverPassword'])->name('recoverPassword');

// User
Route::get('user/{id}', [UserController::class, 'show'])->where('id', '[0-9]+')->name('profile');
Route::get('user/edit', [UserController::class, 'editUser']);
Route::post('user/edit', [UserController::class, 'edit']);
Route::post('user/profileDelete', [UserController::class, 'profileDelete']);
Route::post('user/delete', [UserController::class, 'delete']);
Route::post('user/removeFollower', [UserController::class, 'removeFollower']);
Route::post('user/follow', [UserController::class, 'follow']);
Route::post('user/unfollow', [UserController::class, 'unfollow']);
Route::post('user/doFollowRequest', [UserController::class, 'doFollowRequest']);
Route::post('user/cancelFollowRequest', [UserController::class, 'cancelFollowRequest']);
Route::post('user/acceptFollowRequest', [UserController::class, 'acceptFollowRequest']);
Route::post('user/rejectFollowRequest', [UserController::class, 'rejectFollowRequest']);

// Admin
Route::get('admin', [AdminController::class, 'show']);
Route::post('admin/user/block', [AdminController::class, 'block_user']);
Route::post('admin/user/unblock', [AdminController::class, 'unblock_user']);

// API
Route::get('api/user', [UserController::class, 'search']);
Route::get('api/userVerify', [UserController::class, 'userVerify']);
Route::get('api/post', [PostController::class, 'search']);
Route::get('api/group', [GroupController::class, 'search']);
Route::get('api/comment', [CommentController::class, 'search']);
Route::get('api/notifications', [NotificationController::class, 'getNotifications']);
Route::get('api/context', [NotificationController::class, 'context']);
Route::get('api/messages', [MessageController::class, 'getNewMessages']);

// Groups
Route::get('group/{id}', [GroupController::class, 'show']);
Route::get('groups', [GroupController::class, 'list']);
Route::get('group/{id}/edit', [GroupController::class, 'editPage']);
Route::post('group/edit', [GroupController::class, 'edit']);
Route::post('group/create', [GroupController::class, 'create']);
Route::post('group/join', [GroupController::class, 'join']);
Route::post('group/leave', [GroupController::class, 'leave']);
Route::post('group/delete', [GroupController::class, 'delete']);
Route::post('group/makeOwner', [GroupController::class, 'makeOwner']);
Route::post('group/doJoinRequest', [GroupController::class, 'doJoinRequest']);
Route::post('group/cancelJoinRequest', [GroupController::class, 'cancelJoinRequest']);
Route::post('group/acceptJoinRequest', [GroupController::class, 'acceptJoinRequest']);
Route::post('group/rejectJoinRequest', [GroupController::class, 'rejectJoinRequest']);
Route::post('group/removeMember', [GroupController::class, 'removeMember']);
Route::post('group/invite', [GroupController::class, 'invite']);
Route::post('group/cancelInvite', [GroupController::class, 'cancelInvite']);
Route::post('group/rejectInvite', [GroupController::class, 'rejectInvite']);
Route::post('group/acceptInvite', [GroupController::class, 'acceptInvite']);
Route::post('group/favorite', [GroupController::class, 'favorite']);
Route::post('group/unfavorite', [GroupController::class, 'unfavorite']);
Route::post('group/deleteMedia', [GroupController::class, 'deleteMedia']);

// Comments
Route::post('comment/like', [CommentController::class, 'like']);
Route::post('comment/dislike', [CommentController::class, 'dislike']);
Route::post('comment/create', [CommentController::class, 'create']);
Route::post('comment/delete', [CommentController::class, 'delete']);
Route::post('comment/edit', [CommentController::class, 'edit']);

// Messages
Route::get('message/{id}', [MessageController::class, 'show']);
Route::post('message/create', [MessageController::class, 'create']);

// Notifications
Route::post('notification/delete', [NotificationController::class, 'delete']);
Route::post('notification/update', [NotificationController::class, 'update']);

// Media
Route::get('/images/{type}', [ImageController::class, 'viewMessageMedia']);