@extends('layouts.app')
@include('sidebar.bar', ['group' => $group])
@include('partials.confirmation')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
@if(Auth::check())
    <button class="newpostbutton" id="newpostbutton" data-toggle="modal" data-target="#createPostModal" onclick="createPost({{$group->id}})"></button>
@endif
@include('partials.alert')
@include('partials.createPost')
<section id="group">
    <section id="presentation" class="presentation-group-card">
        <div class="group-presentation">
            <img src="{{ asset($group->media($group->id)) }}" width=20% style="border-radius: 50%; padding: 1em" alt="Group image">
            <div class="group-presentation-text">
                <section id="profile-text" class="page-group-info-text">
                    <div class="group-name-favorite">
                        <h1>{{ $group->name }}</h1>
                        @if(Auth::check() && $group->hasMember(Auth::user()))
                            <button id="fav{{$group->id}}" onclick="favorite({{$group->id}})" class="{{$group->isFavorite(Auth::user()) ? 'group-interaction-button fa fa-star' : 'group-interaction-button fa fa-star-o'}}"></button>
                        @endif
                    </div>
                <h5>Group owner: <a href='{{url("user/$group->owner_id")}}'>{{$group->owner->name}}</a></h5>
                </section>
                <section id="buttons" class="buttons">
                @if(Auth::check() && Auth::user()->id != $group->owner_id)
                    <button id="groupState{{$group->id}}" class="group-interaction-button" onclick="updateGroupState({{$group->id}},{{Auth::user()->id}},{{$group->is_public}})">
                        @if($group->hasMember(Auth::user())) <i id="text-icon" class="fa fa-minus-circle" aria-hidden="true"></i> Leave Group
                        @elseif($group->hasJoinRequest(Auth::user())) <i id="text-icon" class="fa fa-times-circle" aria-hidden="true"></i> Cancel join request
                        @else <i id="text-icon" class="fa fa-plus-circle" aria-hidden="true"></i> Join Group
                        @endif  
                    </button>
                @endif
                </section>
            </div>
        </div>
        @if($group->description)
            <h4 class="group-description">{!! $group->description !!}</h4>
        @endif
    </section>
    @if($group->is_public || (Auth::check() && $group->hasMember(Auth::user())))
        <section id="interaction-stats">
            <nav class="nav nav-pills nav-justified myNav" id="group-nav" role="tablist">
                <a class="nav-item nav-link active text-white group-nav-bar-button left-nav-button" id="groupPosts" data-toggle="pill" href="#group-posts" role="tab">{{ count($group->posts()->get()) }} posts</a>
                <a class="nav-item nav-link text-white group-nav-bar-button right-nav-button" id="groupMembers" data-toggle="pill" href="#group-members" role="tab">{{ count($group->members()->get()) }} members</a>
            </nav>
        </section>

        <div class="tab-content">
            <section class="tab-pane show active" id="group-posts" role="tabpanel">
                @each('partials.post', $group->posts()->get(), 'post')
            </section>
            <section class="tab-pane" id="group-members" role="tabpanel">
                @foreach($group->members()->get() as $member)
                <article class="member-person-card" id="group-member-{{$member->user()->id}}" class="group-member">
                    <img class="user-profile-pic" src="{{$member->user()->media()}}">
                    <a href="../user/{{$member->user()->id}}"><h2 class="user-username group-follow-card-user">{{$member->user()->name}}</h2></a>
                    <h3 class="group-follow-card-username">&#64;{{$member->user()->username}}</h3>
                    @if(Auth::check() && $group->owner_id == Auth::user()->id && $group->owner_id != $member->user()->id)
                    <div id="button_owner" class="group-member-card-button">
                        <form action="{{ url('group/makeOwner') }}" method="POST">
                            {{ csrf_field() }}
                            <input name="member_id" value="{{$member->user_id}}" hidden>
                            <input name="group_id" value="{{$group->id}}" hidden>
                            <button class="group-interaction-button submit" id="giveOnwership{{$member->user()->id}}">
                                <i class="fa fa-gavel" aria-hidden="true"></i> Make owner
                            </button>
                        </form>
                        <button class="group-interaction-button" id="removeMember{{$member->user()->id}}" onclick="confirmation(removeGroupMember, ['{{$group->id}}', '{{$member->user()->id}}'])">
                            <i class="fa fa-minus-circle" aria-hidden="true"></i> Remove Member
                        </button>
                    </div>
                    @endif
                </article>
                @endforeach
            </section>
        </div>
    @else  
        <h2>This group is private</h2>
    @endif
</section>
@endsection