<article class="main-post" id="post{{$post->id}}">
    <header class="post-info">
      <img class="user-profile-pic-post" src="{{$post->owner->media()}}" alt="post owner profile picture">
      <h4 class="post-info-text"><a href="/user/{{ $post->owner_id }}">{{ $post->owner->name }}</a></h4>
      <h5 class="post-info-text"> {{'@'}}{{ $post->owner->username }} </h5>
      @if(!is_null($post->group_id))
        <h5 class="post-info-text">From group <br> <a href="/group/{{$post->group->id}}">{{$post->group->name}}</a></h5>
      @endif
      <h5 class="post-info-text"> {{ convert_time($post->date); }} </h5>
    </header>
    <main class="post-content">
      <div id="main-content" class="post-content-text-image">
      @if($post->content)
        <h3 class="postcontent">{!! $post->content !!}</h3>
      @endif
      @if($post->media())
        @if(preg_replace('/^.*\.([^.]+)$/D', '$1', $post->media()) == "mp4")
          <video class="postmedia" width="80%" height="auto" controls>
            <source src="{{ asset($post->media()) }}" type="video/mp4">
          </video><br>
          @else
            <img class="postmedia" src="{{ asset($post->media()) }}" width="35%" alt="post media"><br>
          @endif
      @endif
      </div>
      <h3 class= "postvisibility" hidden>{{$post->is_public ? 'public' : 'private'}}</h3>
      <button id="countPostLikes{{$post->id}}" onclick="{{(Auth::check() && Auth::user()->canLikePost($post)) ? 'likePost('.$post->id.')' : ''}}" class="{{(Auth::check() && Auth::user()->likesPost($post->id)) ? 'button-post-comment button-outline-post-comment' : 'button-post-comment'}}">
        <h4 id="text-config" class="like-button"> {{ $post->likes() }}  <i id="text-icon" class="{{(Auth::check() && Auth::user()->likesPost($post->id)) ? 'fa fa-heart' : 'fa fa-heart-o'}}"></i></h4></button>
      <button id="countPostComments{{$post->id}}" class="button-post-comment" onclick="showComments({{$post->id}}, 'post')">
        <h4>{{ count($post->comments()) }}  <i class="fa fa-comment-o "></i></h4>
      </button>
      @if(Auth::check() && ($post->owner_id == Auth::user()->id || (Auth::user()->isAdmin())))
        <button onclick="confirmation(deletePost,[{{$post->id}}, '{{$post->group_id ? 'groupPosts' : 'userPosts'}}'])" class="button-post-comment">
          <h4><i class="fa fa-trash"></i></h4>
        </button>
        @if(Auth::check() && $post->owner_id == Auth::user()->id)
        <button id="editPost{{$post->id}}" onclick="editPost({{$post->id}})" class="button-post-comment">
          <h4 id="text-config"><i id="text-icon" class="fa fa-pencil"></i></h4>
        </button>
        <button id="cancelEditPost{{$post->id}}" onclick="cancelEditPost()" style="visibility:hidden;" class="button-post-comment">
          <h4><i class="fa fa-times"></i> </h4>
        </button>
        @endif
      @endif
    </main>
    <footer class="post-comments" hidden>
      @include('partials.commentSection', ['comments' => $post->comments(), 'previous' => "countPostComments".$post->id])
    </footer>
</article>