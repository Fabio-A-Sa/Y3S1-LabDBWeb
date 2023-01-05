<section class="sidebar-user-info">
    <img src="{{ asset(Auth::user()->media()) }}" class="sidebar-profile-pic" alt="sidebar profile picture">
    <div class="sidebar-user-username">
        <h4 class="sidebar-user-name"><a id="user={{Auth::user()->id}}" href="{{ url('/user/'.Auth::user()->id) }}">{{ Auth::user()->name }}</a></h4>
        <h4 class="sidebar-user-user" id="username{{Auth::user()->id}}">&#64;{{ Auth::user()->username }}</h4>
    </div>
    <div class="dropdown sidebar-user-dropdown">
        <i class="fa fa-chevron-circle-down dropdown data-toggle sidebar-user-options" type="button" id="dropdownMenuButton" data-toggle="dropdown"></i>
        <nav class="dropdown-menu myDropdown">
            @if(Auth::user()->isAdmin())
                <a class="dropdown-item" href="{{ url('/admin') }}">Admin Page</a>
            @endif
                <a class="dropdown-item" href="{{ url('/logout') }}">Logout</a>
        </nav>
    </div>
</section>