<article id="bar-chat-item" class="bar-chat-list-item">
    <a href="/message/{{$user->id}}" class="sidebar-chat-list-anchor">
        <img src="{{ asset($user->media()) }}" class="bar-chat-image" alt="user profile picture">
        <div class="sidebar-message-user-name">
            <h3 class="chat-person-name">{{$user->name}}</h3>
            <h5 class="chat-person-username">&#64;{{$user->username}}
        </div>
        @if(Auth::user()->haveUnseenMessagesWith($user))
        <div class="sidebar-chat-list-notification"></div>
        @endif
    </a>
</article> 