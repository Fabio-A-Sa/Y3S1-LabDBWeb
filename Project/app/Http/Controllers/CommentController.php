<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;    
use App\Models\User;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Notification;
use App\Models\PostNotification;  
use App\Models\CommentNotification;      
use App\Http\Controllers\TagController;  

class CommentController extends Controller
{
    public function delete (Request $request) {
        
        $comment = Comment::find($request->id);
        $this->authorize('delete', $comment);

        $comment->delete();
    }

    public function create(Request $request)
    {
      $this->authorize('create', Comment::class);

      $contentLength = TagController::checkContentLength($request->content);
      if (!$contentLength)
        return redirect()->back()->with('error', 'You can not create an empty comment!');

      if ($contentLength > 255)
        return redirect()->back()->with('error', 'You can not create a large comment!');

      $comment = new Comment();
      $comment->owner_id = Auth::user()->id;
      $comment->post_id = $request->post_id;
      $comment->previous = $request->comment_id;
      $comment->content = " ";
      $comment->date = date('Y-m-d H:i');
      $comment->save();

      $comment->content = nl2br(TagController::parseContent($request->content, 'comment', $comment->id));
      $comment->save();

      DB::beginTransaction();

      $post = Post::find($comment->post_id);
      if ($post->owner_id == Auth::user()->id) 
          return redirect()->back()->with('success', 'Comment successfully created');

      Notification::insert([
        'emitter_user' => Auth::user()->id,
        'notified_user' => $post->owner_id,
        'date' => date('Y-m-d H:i'),
        'viewed' => false,
      ]);
      
      $newNotification = Notification::select('notification.id')
                            ->where('emitter_user', Auth::user()->id)
                            ->where('notified_user', $post->owner_id)->get()->last();

      CommentNotification::insert([
            'id' => $newNotification->id,
            'comment_id' => $comment->id,
            'notification_type' => 'comment_post'
      ]);

      $previous = Comment::find($comment->previous);
      if (!$previous || $previous->owner_id == Auth::user()->id) 
          return redirect()->back()->with('success', 'Comment successfully created');

      Notification::insert([
        'emitter_user' => Auth::user()->id,
        'notified_user' => $previous->owner_id,
        'date' => date('Y-m-d H:i'),
        'viewed' => false,
      ]);
      
      $newNotification = Notification::select('notification.id')
                            ->where('emitter_user', Auth::user()->id)
                            ->where('notified_user', $previous->owner_id)->get()->last();

      CommentNotification::insert([
            'id' => $newNotification->id,
            'comment_id' => $comment->id,
            'notification_type' => 'reply_comment'
      ]);

      DB::commit();

      return redirect()->back()->with('success', 'Comment successfully created');
    }

    public function edit(Request $request) {

      $comment = Comment::find($request->id);
      $this->authorize('edit', $comment);

      $comment->content = nl2br(TagController::parseContent($request->content, 'comment', $comment->id));
      $comment->save();
    }

    public function search(Request $request) {

      $input = $request->get('search') ? $request->get('search').':*' : "*";

      if (!Auth::check()) return null;

      if (Auth::user()->isAdmin()) {
        $comments = Comment::whereRaw("tsvectors @@ to_tsquery(?)", [$input])
                          ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) ASC", [$input])->get();
      } else {
        $visiblePosts = Auth::user()->visiblePosts()->union(Post::publicPosts())->get()->pluck('id')->toArray();
        $comments = Comment::whereRaw("tsvectors @@ to_tsquery(?)", [$input])
                            ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) ASC", [$input])
                            ->whereIn('post_id', $visiblePosts)->get();
      }

      return view('partials.searchComment', compact('comments'))->render();
    }

    public function like (Request $request) {
      
        $comment = Comment::find($request->id);
        $this->authorize('like', Comment::class);
        
        if(CommentLike::where([
          'user_id' => Auth::user()->id,
          'comment_id' => $comment->id,
        ])->exists()) return;
        
        CommentLike::insert([
          'user_id' => Auth::user()->id,
          'comment_id' => $comment->id,
        ]);

        if (Auth::user()->id == $comment->owner_id) return;
        DB::beginTransaction();
        
        Notification::insert([
          'emitter_user' => Auth::user()->id,
          'notified_user' => $comment->owner_id,
          'date' => date('Y-m-d H:i'),
          'viewed' => false,
        ]);
        
        $newNotification = Notification::select('notification.id')
                              ->where('emitter_user', Auth::user()->id)
                              ->where('notified_user', $comment->owner_id)->get()->last();
  
        CommentNotification::insert([
              'id' => $newNotification->id,
              'comment_id' => $comment->id,
              'notification_type' => 'liked_comment'
        ]);

        DB::commit();
      }
  
      public function dislike (Request $request) {
        
        $comment = Comment::find($request->id);
        $this->authorize('dislike', Comment::class);
  
        DB::beginTransaction();
  
        CommentLike::where([
          'user_id' => Auth::user()->id,
          'comment_id' => $comment->id,
        ])->delete();
  
        $oldNotification = Notification::leftJoin('comment_notification', 'notification.id', '=', 'comment_notification.id')
                                        ->select('notification.id')
                                        ->where('notification.emitter_user', Auth::user()->id)
                                        ->where('notification.notified_user', $comment->owner_id)
                                        ->where('comment_notification.comment_id', $comment->id)
                                        ->where('comment_notification.notification_type', 'liked_comment')
                                        ->get()->last();
  
        if ($oldNotification) {
          CommentNotification::where('id', $oldNotification->id)->delete();
          Notification::where('id', $oldNotification->id)->delete();  
        }
  
        DB::commit();
      }
}
