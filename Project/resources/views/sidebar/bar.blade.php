@section('bar')
    <ul class="list-unstyled sidebar-itens-positioning">
        <li class="sidebar-title">
            <h1 id="page-title"><a href="{{ url('/home') }}">OnlyFEUP</a></h1>
            <button class="sidebar-button close-sidebar-button" onclick="toggleSidebar()">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </button>
        </li class="sidebar-main-content">
        @if(Auth::check())
        <li>
            <header id="bar-header" class="">
                @include('sidebar.userInfo')
            </header>
            <section id="bar-middle">
            @if(isset($user) && $_SERVER['REQUEST_URI'] == '/user/'.$user->id)
                @include('sidebar.userPage')
            @elseif($_SERVER['REQUEST_URI'] == '/home')
                @include('sidebar.homeMessages')
                @include('sidebar.groupBarSection', ['user' => Auth::user()])
                @yield('groupBarSection')
            @elseif(isset($group) && $_SERVER['REQUEST_URI'] == '/group/'.$group->id)
                @include('sidebar.groupPage')
            @elseif($_SERVER['REQUEST_URI'] == '/groups')
                @include('sidebar.groups')
            @elseif($_SERVER['REQUEST_URI'] == '/home/notifications')
                @include('sidebar.notificationPage')
            @elseif($_SERVER['REQUEST_URI'] == '/messages')
                @include('sidebar.contacts', ['user' => Auth::user()])
                @yield('chatBarSection') 
            @elseif(str_contains($_SERVER['REQUEST_URI'], "/message/"))
                @include('sidebar.messageGoBack')
                @include('sidebar.contacts', ['user' => Auth::user()])
                @yield('chatBarSection') 
            @endif
            </section>
        </li>
        @else
        <li class="sidebar-li-bar-middle">
            <header id="bar-header">
            @if(Request::is('register'))
                @include('sidebar.registerForm')
            @else
                @include('sidebar.loginForm')
            @endif
        </li>
        @endif
        <li class="sidebar-li-footer">
            @include('sidebar.staticLinks')
        </li>
    </ul>
@endsection