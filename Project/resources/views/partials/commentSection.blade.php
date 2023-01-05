@if(Auth::check())
<article class="comment comment-make" id="temp_comment_{{$post->id}}">
    <form method="post" class="comment-form-card" action="{{ url('../comment/create') }}" enctype="multipart/form-data" id="new-comment-form{{$post->id}}">
        {{ csrf_field() }}
        <input name="post_id" value="{{$post->id}}" hidden></input>
        <textarea placeholder="Write a message..." class="comment_id" type="textbox" name="content"></textarea>
    </form>
    <button type="submit" form="new-comment-form{{$post->id}}" class="button-post-comment send-button-post-comment">
            <h4><i class="fa fa-paper-plane" aria-hidden="true"></i></h4>
    </button>
</article>
@endif

@foreach($comments as $comment)
    @include('partials.comment', ['comment' => $comment, 'previous' => $previous])
@endforeach 
