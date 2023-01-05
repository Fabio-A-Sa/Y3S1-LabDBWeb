<article class="subcomment">
    <header class="subcomment-info">
        <img class="user-profile-pic-post" src="{{$comment->owner()->media()}}" alt="subcomment owner profile picture">
        <h4 id="owner{{$comment->id}}" hidden>{{$comment->owner()->username}}</h4>
        <h4><a href="/user/{{ $comment->owner_id }}">{{ $comment->owner()->name }}</a></h4>
        <h5 class="post-info-text"> {{'@'}}{{ $comment->owner()->username }} </h5>
        <h5>{{ convert_time($comment->date) }}</h5>
    </header>
    <main class="subcomment-content">
        <div id="main-content" class="comment-content-text">
            <h3 class="content" >{!! $comment->content !!}</h3>
        </div>
        <button id="countCommentLikes{{$comment->id}}" onclick="{{(Auth::check() && Auth::user()->canLikeComment($comment)) ? 'likeComment('.$comment->id.')' : ''}}" class="{{(Auth::check() && Auth::user()->likesComment($comment->id)) ? 'button-post-comment button-outline-post-comment' : 'button-post-comment'}}">
            <h4 id="text-config" class="like-button">{{ $comment->likes() }} <i id="text-icon" class="{{(Auth::check() && Auth::user()->likesComment($comment->id)) ? 'fa fa-heart' : 'fa fa-heart-o'}}"></i></h4>
        </button> 
    </main> 
</article>