<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Configuration;
use App\Models\PostNotification;
use App\Models\UserNotification;
use App\Models\CommentNotification;
use App\Models\GroupNotification;

class NotificationController extends Controller
{
    public function delete (Request $request) {

        $this->authorize('delete', Notification::class);

        DB::beginTransaction();

        CommentNotification::where('id', $request->id)->delete();
        UserNotification::where('id', $request->id)->delete();
        GroupNotification::where('id', $request->id)->delete();
        PostNotification::where('id', $request->id)->delete();
        Notification::where('id', $request->id)->delete();

        DB::commit();
    }

    protected function seen ($notifications) {
        foreach ($notifications as $notification) {
            Notification::where('id', $notification->id)
                        ->update(['viewed' => true]);
        }
    }


    protected function getUserNotifications() {
        return Notification::join('user_notification', 'notification.id', '=', 'user_notification.id')
            ->join('users', 'users.id', '=', 'notification.emitter_user')
            ->select('notification.id', 'users.name AS emitter_name', 'notification.emitter_user', 'user_notification.notification_type AS type')
            ->where('notification.notified_user', Auth::user()->id)
            ->orderBy('user_notification.notification_type', 'asc')
            ->orderBy('notification.date', 'desc')
            ->get();
    }

    protected function getPostNotifications () {
        return Notification::join('post_notification', 'notification.id', '=', 'post_notification.id')
            ->join('users', 'users.id', '=', 'notification.emitter_user')
            ->select('notification.id', 'users.name AS emitter_name', 'notification.emitter_user', 'post_notification.notification_type AS type', 'post_notification.post_id')
            ->where('notification.notified_user', Auth::user()->id)
            ->orderBy('post_notification.notification_type', 'asc')
            ->orderBy('notification.date', 'desc')
            ->get();
    }

    protected function getCommentNotifications () {
        return Notification::join('comment_notification', 'notification.id', '=', 'comment_notification.id')
            ->join('users', 'users.id', '=', 'notification.emitter_user')
            ->select('notification.id', 'users.name AS emitter_name', 'notification.emitter_user', 'comment_notification.notification_type AS type', 'comment_notification.comment_id AS comment_id')
            ->where('notification.notified_user', Auth::user()->id)
            ->orderBy('comment_notification.notification_type', 'asc')
            ->orderBy('notification.date', 'desc')
            ->get();
    }

    protected function getGroupNotifications () {
        return Notification::join('group_notification', 'notification.id', '=', 'group_notification.id')
            ->join('users', 'users.id', '=', 'notification.emitter_user')
            ->join('groups', 'groups.id', '=', 'group_notification.group_id')
            ->select('notification.id', 'users.name AS emitter_name', 'notification.emitter_user', 'group_notification.notification_type AS type', 'groups.name AS group_name', 'groups.id AS group_id')
            ->where('notification.notified_user', Auth::user()->id)
            ->orderBy('group_notification.notification_type', 'asc')
            ->orderBy('notification.date', 'desc')
            ->get();
    }

    protected function getAllNotifications () {
        return count(Notification::select('notification.id')
            ->where('notification.notified_user', Auth::user()->id)
            ->where('viewed', false)
            ->get());
    }

    public function getNotifications(Request $request) {
        
        $this->authorize('getNotifications', Notification::class);

        switch($request->type) {
            case 'user':
                $notifications = $this->getUserNotifications();
                break;
            case 'post':
                $notifications = $this->getPostNotifications();
                break;
            case 'comment':
                $notifications = $this->getCommentNotifications();
                break;
            case 'group':
                $notifications = $this->getGroupNotifications();
                break;
            case 'all':
                return $this->getAllNotifications();
                break;
            default:
                $notifications = array();
        }

        $this->seen($notifications);
        return view('partials.notification', compact('notifications'))->render();
    }

    public function context(Request $request) {

        $this->authorize('context', Notification::class);
        switch($request->get('type')) {
            case 'post':
                $post = Post::find($request->get('id'));
                return view('partials.postContext', ['post' => $post])->render();
                break;
            case 'comment':
                $comment = Comment::find($request->get('id'));
                $post = Post::find($comment->post_id);
                return view('partials.postContext', ['post' => $post, 'comment' => $comment])->render();
                break;
            case 'subcomment':
                $subcomment = Comment::find($request->get('id'));
                $comment = Comment::find($subcomment->previous);
                $post = Post::find($subcomment->post_id);
                return view('partials.postContext', ['post' => $post, 'comment' => $comment, 'subcomment' => $subcomment])->render();
                break;
            default:
                break;
        }
    }

    public function update(Request $request) {

        $this->authorize('update', Notification::class);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'request_follow'])
                     ->update(['active' => isset($request->request_follow)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'started_following'])
                     ->update(['active' => isset($request->started_following)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'accepted_follow'])
                     ->update(['active' => isset($request->accepted_follow)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'requested_join'])
                     ->update(['active' => isset($request->requested_join)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'joined_group'])
                     ->update(['active' => isset($request->joined_group)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'accepted_join'])
                     ->update(['active' => isset($request->accepted_join)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'leave_group'])
                     ->update(['active' => isset($request->leave_group)]);
                     
        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'invite'])
                     ->update(['active' => isset($request->invite)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'ban'])
                     ->update(['active' => isset($request->ban)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'group_ownership'])
                     ->update(['active' => isset($request->group_ownership)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'liked_post'])
                     ->update(['active' => isset($request->liked_post)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'post_tagging'])
                     ->update(['active' => isset($request->post_tagging)]);
                     
        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'liked_comment'])
                     ->update(['active' => isset($request->liked_comment)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'comment_post'])
                     ->update(['active' => isset($request->comment_post)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'reply_comment'])
                     ->update(['active' => isset($request->reply_comment)]);

        Configuration::where(['user_id' => Auth::user()->id, 'notification_type' => 'comment_tagging'])
                     ->update(['active' => isset($request->comment_tagging)]);
                     

        return redirect()->back()->with('success', 'Update successfully your configurations');
    }
}
