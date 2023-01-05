@foreach($comments as $comment)
<article class="subcomment subcomment{{$comment_id}}" id="comment{{$comment->id}}">
    <header class="subcomment-info">
        <img class="user-profile-pic-post" src="{{$comment->owner()->media()}}" alt="comment owner profile picture">
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
        @if(Auth::check())
            <button id="replies{{$comment->id}}" onclick="reply({{$comment->id}})" class="button-post-comment">
                <i class="fa fa-reply"></i>
            </button>
        @endif
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
</article>
@if($comment->countReplies() > 0)
    @include('partials.subcomment', ['comments' => $comment->getNext(), 'deep' => $deep + 1, 'previous' => 'replies'.$comment->id, 'comment_id' => $comment->id])
@endif
@endforeach 
8
@if($deep < 2 && Auth::check())
<article class="subcomment comment-make" id="temp_subcomment_{{$post_id}}">
    <form method="post" class="comment-form-card" action="{{ url('../comment/create') }}" enctype="multipart/form-data" id="new-subcomment-form-{{$comment_id}}-{{$post_id}}">
        {{ csrf_field() }}
        <input name="post_id" value="{{$post_id}}" hidden></input>
        <input name="comment_id" value="{{$comment_id}}" hidden></input>
        <textarea id="content-textarea" placeholder="Write a message..." class="comment_id" type="textbox" name="content"></textarea>
    </form>
    <button type="submit" form="new-subcomment-form-{{$comment_id}}-{{$post_id}}" class="button-post-comment send-button-post-comment">
            <h4><i class="fa fa-paper-plane" aria-hidden="true"></i></h4>
    </button>
</article>
@endif