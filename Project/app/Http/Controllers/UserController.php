<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\TagController;

use App\Models\User;
use App\Models\Post;
use App\Models\Group;
use App\Models\Follow;
use App\Models\Blocked;
use App\Models\Configuration;
use App\Models\Notification;
use App\Models\RequestFollow;
use App\Http\Controllers\Controller;
use App\Models\UserNotification;

class UserController extends Controller
{
    public function show(int $id)
    {
        $user = User::find($id);
        if(!$user) return redirect()->back();
        $posts = $user->ownPosts()->get();
        $followers = $user->getFollowers()->get();
        $following = $user->getFollowing()->get();
        return view('pages.user', ['user' => $user, 'posts' => $posts, 'followers' => $followers, 'following' => $following]);
    }

    public function searchPage() {
        $this->authorize('searchPage', User::class);
        return view('pages.search');
    }

    public function notificationsPage() {
        $this->authorize('notificationsPage', User::class);
        return view('pages.notifications');
    }

    public function editUser()
    {   
        $this->authorize('editUser', Auth::user());
        return view('pages.editUser', ['user' => Auth::user(), 
                                       'old' => ['name' => Auth::user()->name, 
                                                 'username' => Auth::user()->username,
                                                 'email' => Auth::user()->email, 
                                                 'description' => Auth::user()->description,
                                                 'public' => Auth::user()->is_public ] ]);
    }

    public function edit(Request $request) {

        $this->authorize('edit', User::class);
        $user = Auth::user();

        $request->validate([
            'name' => 'max:255',
            'username' => 'unique:users,username,'.$user->id.'|max:255',
            'email' => 'email|unique:users,email,'.$user->id.'|max:255',
            'description' => 'max:255'
        ]);

        if($request->password) {
            $request->validate([
                'password' => 'string|min:6|confirmed',
            ]);
            $user->password = bcrypt($request->password);
        }
        
        if($request->file('image')){
            if( !in_array(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION),['jpg','jpeg','png'])) {
                return redirect('user/edit')->with('error', 'File not supported');
            }
            $request->validate([
                'image' =>  'mimes:png,jpeg,jpg',
            ]);
            ImageController::update($user->id, 'profile', $request);
        }

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->description = nl2br(TagController::parseContent($request->input('description'),'desc',-1));
        $user->is_public = null !== $request->input('public');

        $user->save();
        return redirect('user/'.$user->id);
    }
    
    public function search(Request $request) {
        
        if (!Auth::check()) return null;
        $input = $request->get('search') ? $request->get('search').':*' : "*";
        $users = User::select('users.id', 'users.name', 'users.username', 'blocked.id AS blocked')
                    ->leftJoin('blocked', 'users.id', '=', 'blocked.id')
                    ->whereRaw("users.tsvectors @@ to_tsquery(?)", [$input])
                    ->where('users.name', '<>', 'deleted')
                    ->orderByRaw("ts_rank(users.tsvectors, to_tsquery(?)) ASC", [$input])
                    ->get();

        return view('partials.searchUser', compact('users'))->render();
    }

    public function userVerify(Request $request) {
        if (!Auth::check()) return null;
        $input = $request->get('search');
        $user = User::where('username', $input)
                      ->get()
                      ->last();
        return $user->id ?? -1;
    }

    public static function getAdmins() {
        return User::join('admin', 'users.id', '=', 'admin.id')
                    ->orderBy('name', 'asc')
                    ->get();
    }

    public function profileDelete(Request $request) {

        $this->authorize('delete', User::class);
        $this->delete($request);
        if(Auth::user()->id == $request->id) {
            auth()->logout();
        }
        return redirect()->back();
    }

    public function delete(Request $request) {

        $this->authorize('delete', User::class);
        $id = $request->id;
        $user = User::find($id);
        DB::beginTransaction();

        // Update user information
        $user->name = "deleted";
        $user->username = "deleted" . $id;
        $user->password = "deleted" . $id;
        $user->email = "deleted" . $id;
        $user->description = "";
        $user->is_public = false;

        // Block user
        Blocked::insert(['id' => $id]);

        // Delete user photo
        ImageController::delete($id, 'profile');

        // Delete follows
        Follow::where('followed_id', $id)->orWhere('follower_id', $id)->delete();
        RequestFollow::where('rcv_id', $id)->delete();

        // Delete Configurations
        Configuration::where('user_id', $id)->delete();

        // Delete groups (and members, photos, posts, comments, subcomments, invites, notifications, ..., with triggers cascade)
        $groups = Group::where('owner_id', $id)->get();
        foreach ($groups as $group) {
            $group->delete();
            ImageController::delete($group->id, 'groups');
        }

        $user->save();
        DB::commit();
    }

    public function removeFollower(Request $request) {
        $this->authorize('removeFollower', User::class);
        Follow::where('followed_id', Auth::user()->id)
                            ->where('follower_id', $request->id)->delete();
    }

    public function follow(Request $request) {

        $this->authorize('follow', User::class);
        
        DB::beginTransaction();

        Follow::insert([
            'follower_id' => Auth::user()->id,
            'followed_id' => $request->id,
        ]);

        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $request->id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]);

        $newNotification = Notification::select('notification.id')
                                    ->where('emitter_user', Auth::user()->id)
                                    ->where('notified_user', $request->id)->get()->last();

        UserNotification::insert([
            'id' => $newNotification->id,
            'notification_type' => 'started_following',
        ]);
    
        DB::commit();
    }

    public function unfollow(Request $request) {

        $this->authorize('unfollow', User::class);

        DB::beginTransaction();

        Follow::where('followed_id', $request->id)
              ->where('follower_id', Auth::user()->id)->delete();

        $oldNotification = Notification::join('user_notification', 'notification.id', '=', 'user_notification.id')
                            ->select('notification.id')
                            ->where('notification.emitter_user', Auth::user()->id)
                            ->where('notification.notified_user', $request->id)
                            ->where('user_notification.notification_type', 'started_following')
                             ->get()->last();
        
        if ($oldNotification) {
            UserNotification::where('id', $oldNotification->id)->delete();
            Notification::where('id', $oldNotification->id)->delete();
        }

        DB::commit();
    }

    public function doFollowRequest(Request $request) {

        $this->authorize('doFollowRequest', User::class);

        DB::beginTransaction();

        RequestFollow::insert([
            'req_id' => Auth::user()->id,
            'rcv_id' => $request->id,
        ]);

        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $request->id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]);
        
        $newNotification = Notification::select('notification.id')
                                    ->where('emitter_user', Auth::user()->id)
                                    ->where('notified_user', $request->id)->get()->last();

        UserNotification::insert([
            'id' => $newNotification->id,
            'notification_type' => 'request_follow',
        ]);

        DB::commit();
    }

    public function cancelFollowRequest(Request $request) {

        $this->authorize('cancelFollowRequest', User::class);
        
        DB::beginTransaction();

        RequestFollow::where('req_id', Auth::user()->id)
                        ->where('rcv_id', $request->id)->delete();
        
        $oldNotification = Notification::join('user_notification', 'notification.id', '=', 'user_notification.id')
                                ->select('notification.id')
                                ->where('notification.emitter_user', Auth::user()->id)
                                ->where('notification.notified_user', $request->id)
                                ->where('user_notification.notification_type', 'request_follow')
                                ->get()->last();
        
        UserNotification::where('id', $oldNotification->id)->delete();
        
        DB::commit();
    }

    public function acceptFollowRequest(Request $request) {

        $this->authorize('acceptFollowRequest', User::class);
        
        DB::beginTransaction();

        RequestFollow::where('req_id', Auth::user()->id)
                        ->where('rcv_id', $request->id)->delete();

        Follow::insert([
            'follower_id' => $request->id,
            'followed_id' => Auth::user()->id,
        ]);
                       
        $oldNotification = Notification::join('user_notification', 'notification.id', '=', 'user_notification.id')
                                ->select('notification.id')
                                ->where('notification.emitter_user', $request->id)
                                ->where('notification.notified_user', Auth::user()->id)
                                ->where('user_notification.notification_type', 'request_follow')
                                ->get()->last();
        
        UserNotification::where('user_notification.id', $oldNotification->id)
                        ->update(['user_notification.notification_type' => 'started_following']);
        
        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $request->id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]);
        
        $newNotification = Notification::select('notification.id')
                            ->where('emitter_user', Auth::user()->id)
                            ->where('notified_user', $request->id)->get()->last();

        UserNotification::insert([
            'id' => $newNotification->id,
            'notification_type' => 'accepted_follow',
        ]);
        
        DB::commit();
    }

    public function rejectFollowRequest(Request $request) {

        $this->authorize('rejectFollowRequest', User::class);
        
        DB::beginTransaction();

        RequestFollow::where('req_id', $request->id)
                        ->where('rcv_id', Auth::user()->id)->delete();
        
        $oldNotification = Notification::join('user_notification', 'notification.id', '=', 'user_notification.id')
                                ->select('notification.id')
                                ->where('notification.emitter_user', $request->id)
                                ->where('notification.notified_user', Auth::user()->id)
                                ->where('user_notification.notification_type', 'request_follow')
                                ->get()->last();
        
        UserNotification::where('id', $oldNotification->id)->delete();
        
        DB::commit();
    }

    public function deleteMedia(Request $request) {

        $user = User::findOrFail($request->id);
        $this->authorize('deleteMedia', $user);
        ImageController::delete($user->id, 'profile');
        return redirect()->back();
    }

}