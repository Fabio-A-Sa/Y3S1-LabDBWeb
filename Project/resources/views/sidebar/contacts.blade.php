@section('chatBarSection')
    <section id="my-chats" class="chats-section">
        <h3 class="chat-div-title"> Chats </h3>
        <div class="sidebar-search-chat">
            <input id="sidebar-chat-search" class="sidebar-chat-search" onkeyup="searchChats()" type="text"
                name="search" placeholder="Find chat..">
        </div>
        <div id="sidebar-current-chat-list" class="sidebar-chat-list sidebar-current-chat-list">
            @each('sidebar.contactList', Auth::user()->uniqueEmitters(), 'user')
        </div>
    </section>
@endsection