<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Notification;
use App\Models\CommentNotification;
use App\Models\PostNotification;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    protected static function makeNotification (string $username, string $type, int $id) {

        if($type == "message") return;

        $user = User::where('username', $username)->get()->last();
        if (!$user || $user->id == Auth::user()->id) return;

        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $user->id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]);

        $newNotification = Notification::select('notification.id')
                              ->where('emitter_user', Auth::user()->id)
                              ->where('notified_user', $user->id)->get()->last();

        if ($type == 'comment') {

            $comment = Comment::find($id);

            if (CommentNotification::where([
                'id' => $newNotification->id,
                'comment_id' => $comment->id,
                'notification_type' => 'comment_tagging'
            ])->exists()) return;

            CommentNotification::insert([
                'id' => $newNotification->id,
                'comment_id' => $comment->id,
                'notification_type' => 'comment_tagging'
            ]);
            
        } elseif ($type == 'post') {

            $post = Post::find($id);

            if (PostNotification::where([
                'id' => $newNotification->id,
                'post_id' => $post->id,
                'notification_type' => 'post_tagging'
            ])->exists()) return;

            PostNotification::insert([
                'id' => $newNotification->id,
                'post_id' => $post->id,
                'notification_type' => 'post_tagging'
            ]);
        }
    }

    public static function checkContentLength($content){

        if (gettype($content) != "string" || !isset($content)) return 0;

        // remove html from text
        $content = strip_tags($content);

        // User tags
        $content = preg_replace_callback("/@\w+/", 
                                            function ($matches) {
                                                for ($i = 0; $i < count($matches); $i++) {
                                                    $user = User::select('id','username')
                                                                ->where('username', str_replace('@', '', $matches))->get()->last();
                                                    if ($user) {
                                                        $matches[$i] = "<a href='../user/".$user->id."'>@".$user->username."</a>";
                                                    }
                                                }
                                                return implode('', $matches);
                                            }, $content);

        // Hashtags
        $content = preg_replace("/(#\w+)/", "<a href='../home/search?query=$1'>$1</a>", $content);

        return strlen($content);
        
    }


    public static function parseContent($content, string $type, int $id) {

        if (gettype($content) != "string") return null;

        // remove html from text
        $content = strip_tags($content);

        // User tags
        $content = preg_replace_callback("/@\w+/", 
                                            function ($matches) use ($type, $id) {
                                                for ($i = 0; $i < count($matches); $i++) {
                                                    $user = User::select('id','username')
                                                                ->where('username', str_replace('@', '', $matches))->get()->last();
                                                    if ($user) {
                                                        $matches[$i] = "<a href='../user/".$user->id."'>@".$user->username."</a>";
                                                        TagController::makeNotification ($user->username, $type, $id);
                                                    }
                                                }
                                                return implode('', $matches);
                                            }, $content);

        // Hashtags
        $content = preg_replace("/(#\w+)/", "<a href='../home/search?query=$1'>$1</a>", $content);

        return $content;
    }
}



