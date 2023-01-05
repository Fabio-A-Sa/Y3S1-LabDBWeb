<section id="home-chat-card" class="home-chat-section">
    <a type=button class="sidebar-chat-anchor" href="{{ url('/messages') }}">
        <div>
            <button class="sidebar-button-2 sidebar-chat-button">
                <i class="fa fa-comments-o" aria-hidden="true"></i> Chat
            </button>
        </div>
        @if(Auth::user()->haveUnseenMessages())
            <div class="sidebar-chat-list-notification">
        @endif
        </div>
    </a>
</section>

