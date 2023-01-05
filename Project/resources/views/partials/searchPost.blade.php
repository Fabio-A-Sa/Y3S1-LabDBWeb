@forelse($posts as $post)
    @include('partials.post', ['post' => $post])
@empty
    <h2 class="no_results">No results found</h2>
@endforelse