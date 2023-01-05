@include('partials.feed')

@section('popup_Groups')
<section class="inviteToGroup">
    <header class="popup-header">
        <h3>Owned Groups</h3>
        <button type="button" class="button-post-comment" data-dismiss="modal" id="close-button"><h4><i class="fa fa-times"></i></button>
    </header>
    @if(Auth::check())
    <section class="popup-body">
        @foreach(Auth::user()->ownedGroups()->get() as $group)
            <article class="popup-group-item">
                <img src="{{ asset($group->media($group->id)) }}" style="width:7em; border-radius: 50%; padding: 1em; aspect-ratio: 1/1;" alt="group media">
                <a href="/group/{{$group->id}}">{{$group->name}}</a>
                @if($group->hasInvite($user))
                    <button id="inviteGroup{{$group->id}}" onclick="inviteState({{$user->id}}, {{$group->id}})">Cancel invitation</button>
                @elseif($group->hasMember($user))
                    <p>Already Member</p>
                @else
                    <button id="inviteGroup{{$group->id}}" onclick="inviteState({{$user->id}}, {{$group->id}})"><i class="fa fa-user-plus" aria-hidden="true"></i> Invite</button>
                @endif
            </article>
        @endforeach
    </section>
    @endif
</section>
@endsection

@section('profile')
<section id="profile">
    <section id="presentation" class="presentation-profile-card">
        <div class="profile-presentation">
            <img src="{{ asset($user->media()) }}" class="profile-img" width=20% style="border-radius: 50%; padding: 1em" alt="profile media">
            <div class ="profile-presentation-text">
                <section id="profile-text">
                    <h1>{{ $user->name }}</h1>
                    <h3>&#64;{{ $user->username }}</h3>
                </section>
                <section id="buttons" class="buttons">
                    @if($user->name != "deleted" && Auth::check()) 
                        @if(Auth::user()->id != $user->id && !$user->isBlocked())
                            <button id="state{{$user->id}}" class="profile-interaction-button" onclick="updateState({{$user->id}}, {{Auth::user()->id}}, {{$user->is_public}})">
                            @if(Auth::user()->follows($user->id)) <i id="text-icon" class="fa fa-minus-circle" aria-hidden="true"></i> Unfollow 
                            @elseif(Auth::user()->requestFollowing($user->id)) <i id="text-icon" class="fa fa-times-circle" aria-hidden="true"></i> Cancel follow request
                            @else <i id="text-icon" class="fa fa-plus-circle" aria-hidden="true"></i> Follow
                            @endif
                            </button>
                        @endif
                        @if(!is_null(Auth::user()->ownedGroups()) && Auth::user()->id != $user->id)
                            <button id="inviteToGroup" class="profile-interaction-button" onclick="inviteToGroup()">
                            <i class="fa fa-users" aria-hidden="true"></i> Invite to Group
                            </button>
                        @endif
                        @if(Auth::user()->id != $user->id)
                        <button class="profile-interaction-button" onclick="window.location.href='../message/{{$user->id}}'">
                            <i class="fa fa-users" aria-hidden="true"></i> PM
                        </button>
                        @endif
                    @endif
                </section>
            </div>
        </div>
            @if($user->description)
                    <h4 class="profile-description">{!! $user->description !!}</h4>
            @endif
    </section>
    <section id="tabs-section">
        @if((Auth::check() && Auth::user()->isAdmin()) || (Auth::check() && Auth::user()->id == $user->id) || ($user->is_public) || (Auth::check() && Auth::user()->follows($user->id)))
            <section id="interaction-stats">
                <nav class="nav nav-pills nav-justified myNav" id="profile-nav" role="tablist">
                    <a class="nav-item nav-link active text-white profile-nav-bar-button left-nav-button" id="userPosts" data-toggle="pill" href="#posts" role="tab">{{ count($user->ownPosts()->get()) }} Posts</a>
                    <a class="nav-item nav-link text-white profile-nav-bar-button" id="userFollowers" data-toggle="pill" href="#followers" role="tab">{{ count($user->getFollowers()->get()) }} Followers</a>
                    <a class="nav-item nav-link text-white profile-nav-bar-button right-nav-button" id="userFollowing" data-toggle="pill" href="#following" role="tab">{{ count($user->getFollowing()->get()) }} Following</a>
                </nav>  
            </section>    

            <div class="tab-content">
                <section class="tab-pane show active" id="posts" role="tabpanel">
                    @each('partials.post', $posts, 'post')
                </section>
                <section class="tab-pane" id="followers" role="tabpanel">
                    @foreach($followers as $follower)
                    <article class="user-admin-result follow-person-card" id="follower{{$follower->id}}">
                        <img class="user-profile-pic" src="{{$follower->media()}}" alt="follower profile picture">
                        <a href="../user/{{$follower->id}}"><h2 class="user-username profile-follow-card-user">{{$follower->name}}</h2></a>
                        <h3 class="profile-follow-card-username">&#64;{{$follower->username}}</h3>
                        @if(Auth::check() && Auth::user()->id == $user->id)
                            <button class="profile-follow-card-button" id="removeFollower{{$follower->id}}" onclick=removeFollower({{$follower->id}})>
                                <i class="fa fa-user-times" aria-hidden="true"></i>   
                                Remove follower
                            </button>
                        @endif
                    </article>
                    @endforeach
                </section>
                <section class="tab-pane" id="following" role="tabpanel">
                    @foreach($following as $followed)
                    <article class="user-admin-result follow-person-card" id="followed{{$followed->id}}">
                        <img class="user-profile-pic" src="{{$followed->media()}}" alt="following profile picture">
                        <a href="../user/{{$followed->id}}"><h2 class="user-username profile-follow-card-user">{{$followed->name}}</h2></a>
                        <h3 class="profile-follow-card-username">&#64;{{$followed->username}}</h3>
                        @if(Auth::check() && Auth::user()->id == $user->id)
                            <button class="profile-follow-card-button" id="unfollow{{$followed->id}}" onclick=removeFollow({{$followed->id}})>
                                <i class="fa fa-user-times" aria-hidden="true"></i>
                                Unfollow
                            </button>
                        @endif
                    </article>
                    @endforeach
                </section>
            </div>

        @else 
            <section id = "private-profile">
                <h2>This profile is private.</h2>
            </section>
        @endif
    </section>
</section>
    
@endsection

