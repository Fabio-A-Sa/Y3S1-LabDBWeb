@section('groupBarSection')
    <section  class="groups-section">
        <h3 class="group-div-title"> Owned groups </h3>
        <div id="my_groups">
            @if(count($user->ownedGroups()->get()) == 0)
                <h6> You don't own any groups :( </h6>
            @endif
            @each('sidebar.barGroupsList', $user->ownedGroups()->get(), 'group')
        </div>
        <h3 class="group-div-title"> Favorite Groups </h3>
        <div id="fav_groups">
            @if(count($user->favoriteGroups()->get()) == 0)
                <h6> You don't have any favorite groups :( </h6>
            @endif
            @each('sidebar.barGroupsList', $user->favoriteGroups()->get(), 'group')
        </div>
        <a type=button href="{{ url('/groups') }}">
            <button class="sidebar-button-2">Groups Page</button>
        </a>
    </div>
@endsection
