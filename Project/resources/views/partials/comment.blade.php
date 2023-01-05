<article class="comment" id="comment{{$comment->id}}">
    <header class="comment-info">
        <img class="user-profile-pic-post" src="{{$comment->owner()->media()}}" alt="Profile picture">
        <h4 id="owner{{$comment->id}}" hidden>{{$comment->owner()->username}}</h5>
        <h4><a href="/user/{{ $comment->owner_id }}">{{ $comment->owner()->name }}</a></h4>
        <h5 class="post-info-text"> {{'@'}}{{ $comment->owner()->username }} </h5>
        <h5>{{ convert_time($comment->date) }}</h5>
    </header>
    <main class="comment-content">
        <div id="main-content" class="comment-content-text">
            <h3 class="content" >{!! $comment->content !!}</h3>
        </div>
        <button id="countCommentLikes{{$comment->id}}" onclick="{{(Auth::check() && Auth::user()->canLikeComment($comment)) ? 'likeComment('.$comment->id.')' : ''}}" class="{{(Auth::check() && Auth::user()->likesComment($comment->id)) ? 'button-post-comment button-outline-post-comment' : 'button-post-comment'}}">
            <h4 id="text-config" class="like-button">{{ $comment->likes() }} <i id="text-icon" class="{{(Auth::check() && Auth::user()->likesComment($comment->id)) ? 'fa fa-heart' : 'fa fa-heart-o'}}"></i></h4>
        </button> 
        <button id="replies{{$comment->id}}" onclick="showComments({{$comment->id}},'comment')" class="button-post-comment">
            <h4>{{$comment->countReplies()}}  <i class="fa fa-comment-o "></i></h4>
        </button>
        @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->id == $comment->owner_id))
            <button onclick="confirmation(deleteComment,[{{$comment->id}}, '{{$previous}}'])" class="button-post-comment">
                <h4><i class="fa fa-trash"></i></h4>
            </button>
        @endif
        @if(Auth::check() && Auth::user()->id == $comment->owner_id)
            <button id="editComment{{$comment->id}}" onclick="editComment({{$comment->id}})" class="button-post-comment">
                <h4 id="text-config"><i id="text-icon" class="fa fa-pencil"></i></h4>
            </button>
        @endif
    </main>
    <footer class="comment-subcomments" hidden>
        @include('partials.subcomment', ['comments' => $comment->getNext(), 'deep' => 1, 'previous' => 'replies'.$comment->id, 'comment_id' => $comment->id, 'post_id' => $comment->post_id])
    </footer>
</article>