<form action="../user/profileDelete" method="post" id="delete-user-form" hidden>
    {{ csrf_field() }}
    <input name="id" value="{{$user->id}}"></input>
    <button type="submit" id="delete-user-form-button" form="delete-user-form"></button>
</form>

<section class="sidebar-options">
    <h3 class="group-div-title"> Options </h3>
    @if($_SERVER['REQUEST_URI'] == '/user/'.Auth::user()->id)
        <button class="sidebar-button-2" onclick="window.location='{{ url("/user/edit") }}'">
            <i class="fa fa-pencil" aria-hidden="true"></i> Edit Profile
        </button>
    @endif
    @if(Auth::user()->id == $user->id || Auth::user()->isAdmin())
        <button id="delete{{$user->id}}"  class="sidebar-button-2 delete-account-button" onclick="confirmation(deleteUser,[{{$user->id}}, 'profile'])">
            <i class="fa fa-ban" aria-hidden="true"></i> Delete Account
        </button>
    @endif
    @if(Auth::user()->isAdmin() && Auth::user()->id != $user->id)
        <button id="block{{$user->id}}" class="sidebar-button-2" onclick="blockUser({{$user->id}})">{{!$user->isBlocked() ? 'Block' : 'Unblock'}}</button>
    @endif
</section>