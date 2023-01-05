@section('feed')
  <section id="posts" class="feed-posts">
    @forelse ($posts as $post)
      @include('partials.post', ['post' => $post])
    @empty
      <h2 class="no_results">There are no posts here. Follow someone.</h2>
    @endforelse
  </section>
@endsection