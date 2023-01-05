<article class="main-post post-context" id="post{{$post->id}}">
    <header class="post-info">
      <img class="user-profile-pic-post" src="{{$post->owner->media()}}" alt="post owner profile picture">
      <h4><a href="/user/{{ $post->owner_id }}">{{ $post->owner->name }}</a></h4>
      <h5 class="post-info-text"> {{'@'}}{{ $post->owner->username }} </h5>
      @if(!is_null($post->group_id))
        <h4>From group <br> <a href="/group/{{$post->group->id}}">{{$post->group->name}}</a></h5>
      @endif
      <h5 class="post-info-text"> {{ convert_time($post->date); }} </h5>
    </header>
    <main class="post-content">
      <div id="main-content" class="post-content-text-image">
        @if($post->content)
          <h3 class="postcontent">{!! $post->content !!}</h3>
        @endif
        @if($post->media($post->id))
          @if(preg_replace('/^.*\.([^.]+)$/D', '$1', $post->media()) == "mp4")
            <video class="postmedia" width="80%" height="auto" controls>
              <source src="{{ asset($post->media()) }}" type="video/mp4">
            </video><br>
            @else
              <img class="postmedia" src="{{ asset($post->media()) }}" width="35%" alt="post media"><br>
            @endif
        @endif
      </div>
      <button id="countPostLikes{{$post->id}}" onclick="{{(Auth::check() && Auth::user()->canLikePost($post)) ? 'likePost('.$post->id.')' : ''}}" class="{{(Auth::check() && Auth::user()->likesPost($post->id)) ? 'button-post-comment button-outline-post-comment' : 'button-post-comment'}}">
        <h4 id="text-config" class="like-button"> {{ $post->likes() }}  <i id="text-icon" class="{{(Auth::check() && Auth::user()->likesPost($post->id)) ? 'fa fa-heart' : 'fa fa-heart-o'}}"></i></h4></button>
      <button id="countPostComments{{$post->id}}" class="button-post-comment" onclick="showComments({{$post->id}}, 'post')">
        <h4>{{ count($post->comments()) }}  <i class="fa fa-comment-o "></i></h4>
      </button>
    </main>
    <footer class="post-comments">
      @if(isset($comment))
        @include('partials.commentContext', ['comment' => $comment])
      @endif
      @if(isset($subcomment))
        @include('partials.subcommentContext', ['subcomment' => $subcomment])
      @endif
    </footer>
</article>