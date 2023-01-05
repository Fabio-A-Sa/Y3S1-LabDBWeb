@forelse ($comments as $comment)
    @if(!isset($comment->previous))
        @include('partials.comment', ['comment' => $comment, 'previous' => "commentResults"])
    @endif
@empty
    <h2 class="no_results">No results found</h2>
@endforelse
