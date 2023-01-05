<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;    
use App\Models\User;
use App\Models\PostLike;
use App\Models\Notification;
use App\Models\PostNotification;      
use App\Http\Controllers\ImageController; 
use App\Http\Controllers\Controller;     
use App\Http\Controllers\TagController;  

class PostController extends Controller
{
    public function list()
    { 
      if (!Auth::check()) {
        $posts = Post::publicPosts()->get();
        return view('pages.home', ['posts' => $posts]);
      }
      $this->authorize('list', Post::class);
      $posts = Auth::user()->visiblePosts()->get();
      return view('pages.home', ['posts' => $posts]);
    }

    public function create(Request $request)
    {
      $this->authorize('create', Post::class);

      $contentFound = $request->input('content');
      if (!isset($contentFound) && $_FILES["image"]["error"]) {
        return redirect()->back()->with('error', 'You can not create an empty post');
      }
      if(!isset($contentFound) && !in_array(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION),['jpg','jpeg','png','gif','mp4','mov'])) {
        return redirect()->back()->with('error', 'File format not supported');
      }

      $post = new Post();
      $post->owner_id = Auth::user()->id;
      $post->group_id = $request->group_id;
      $post->is_public = $request->public;
      $post->content = " ";
      $post->date = date('Y-m-d H:i');
      $post->is_public = null !== $request->input('public');

      $post->save();

      $post->content = nl2br(TagController::parseContent($request->content, 'post', $post->id));
      $post->save();

      ImageController::create($post->id, 'post', $request);
      return redirect()->back()->with('success', 'Post successfully created');
    }

    public function delete(Request $request)
    {
      $post = Post::find($request->input('id'));
      $this->authorize('delete', $post);

      foreach($post->comments() as $comment) $comment->delete();
      
      ImageController::delete($post->id, 'post');
      $post->delete(); 
    }

    public function edit(Request $request)
    {
      $post = Post::find($request->id);
      $this->authorize('edit', $post);
      
      $post->content = nl2br(TagController::parseContent($request->content, 'post', $post->id));
      $post->is_public = $request->is_public;

      $post->save();
    }

    public function search(Request $request) {

      $input = $request->get('search') ? $request->get('search').':*' : "*";

      /* Se não estiver logado aborta */
      if (!Auth::check()) return null;

      /* Se estiver logado como admin retorna tudo */
      
      if (Auth::user()->isAdmin()) {
        $posts = Post::select('id','owner_id','group_id', 'content', 'date')
                    ->whereRaw("tsvectors @@ to_tsquery(?)", [$input])
                    ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) ASC", [$input])->get();
      } else {

        /* Se estiver logado com outra conta retorna só aquilo que tem acesso */
        $visiblePosts = Auth::user()->visiblePosts()->union(Post::publicPosts())->get()->pluck('owner_id')->toArray();
        $posts = Post::select()
                    ->whereRaw("tsvectors @@ to_tsquery(?)", [$input])
                    ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) ASC", [$input])
                    ->whereIn('owner_id',$visiblePosts)
                    ->get();
      }

      return view('partials.searchPost', compact('posts'))->render();
    }

    public function like (Request $request) {
      
      $post = Post::find($request->id);
      $this->authorize('like', Post::class);

      if(PostLike::where([
        'user_id' => Auth::user()->id,
        'post_id' => $post->id,
      ])->exists()) return;

      PostLike::insert([
        'user_id' => Auth::user()->id,
        'post_id' => $post->id,
      ]);
      if (Auth::user()->id == $post->owner_id) return;

      DB::beginTransaction();

      Notification::insert([
        'emitter_user' => Auth::user()->id,
        'notified_user' => $post->owner_id,
        'date' => date('Y-m-d H:i'),
        'viewed' => false,
      ]);
      
      $newNotification = Notification::select('notification.id')
                            ->where('emitter_user', Auth::user()->id)
                            ->where('notified_user', $post->owner_id)->get()->last();

      PostNotification::insert([
            'id' => $newNotification->id,
            'post_id' => $post->id,
            'notification_type' => 'liked_post'
      ]);

      DB::commit();
    }

    public function dislike (Request $request) {

      $post = Post::find($request->id);
      $this->authorize('dislike', Post::class);

      DB::beginTransaction();

      PostLike::where([
        'user_id' => Auth::user()->id,
        'post_id' => $post->id,
      ])->delete();

      $oldNotification = Notification::leftJoin('post_notification', 'notification.id', '=', 'post_notification.id')
                                      ->select('notification.id')
                                      ->where('notification.emitter_user', Auth::user()->id)
                                      ->where('notification.notified_user', $post->owner_id)
                                      ->where('post_notification.post_id', $post->id)
                                      ->where('post_notification.notification_type', 'liked_post')
                                      ->get()->last();

      if ($oldNotification) {
        PostNotification::where('id', $oldNotification->id)->delete();
        Notification::where('id', $oldNotification->id)->delete();  
      }

      DB::commit();
    }
}