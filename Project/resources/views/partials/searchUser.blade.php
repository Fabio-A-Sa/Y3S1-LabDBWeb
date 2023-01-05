@forelse ($users as $user)
    <article class="search-page-card" id="user{{$user->id}}">
        <img class="user-profile-pic" src="{{$user->media()}}" alt="user profile picture">
        <a href="../user/{{$user->id}}"><h2 class="user-username search-page-card-user"> {{ $user->name }}</h2></a>
        <h3 class="search-user-card-username">&#64;{{$user->username}}</h3>
        <div id="button_owner" class="search-page-card-buttons">
            <button class="search-page-button" onclick="window.location='../message/{{$user->id}}'">Send Message</button>
            @if(Auth::check() && Auth::user()->isAdmin())
            <button class="search-page-button" id="block{{$user->id}}" onclick="blockUser({{$user->id}})">{{$user->isBlocked() ? 'Unblock' : 'Block'}}</button>
            <button class="search-page-button" onclick="confirmation(deleteUser, [{{$user->id}}, 'userResults'])">BAN!</button>
            @endif
        </div>
    </article>
@empty
<h2 class="no_results">No results found</h2>
@endforelse
