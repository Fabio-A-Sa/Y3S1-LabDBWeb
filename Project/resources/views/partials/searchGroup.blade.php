<div class="groupsList"> 
    @forelse($groups as $group)
        @include('partials.groupCard', ['group' => $group])
    @empty
        <h2 class="no_results">No results found</h2>
    @endforelse
</div> 
