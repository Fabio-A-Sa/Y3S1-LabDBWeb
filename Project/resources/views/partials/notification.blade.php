@foreach ($notifications as $notification)
@if(Auth::user()->acceptNotification($notification->type))
<article class="notification" id="notification{{$notification->id}}">
    <h3 class="notification-message"><a href="../user/{{$notification->emitter_user}}">{{$notification->emitter_name}}</a>
        @if($notification->type == "started_following") started following you
        @elseif($notification->type == "request_follow") requested to follow you
        @elseif($notification->type == "accepted_follow") accepted your follow request

        @elseif($notification->type == "liked_post") liked your <a onclick="showContext({{$notification->post_id}}, 'post')">post</a>
        @elseif($notification->type == "post_tagging") tagged you in a <a onclick="showContext({{$notification->post_id}}, 'post')">post</a>

        @elseif($notification->type == "requested_join") requested to join your group <a href="../group/{{$notification->group_id}}">{{$notification->group_name}}</a>
        @elseif($notification->type == "joined_group") joined your group <a href="../group/{{$notification->group_id}}">{{$notification->group_name}}</a>
        @elseif($notification->type == "accepted_join") accepted you in <a href="../group/{{$notification->group_id}}">{{$notification->group_name}}</a>
        @elseif($notification->type == "leave_group") left your group <a href="../group/{{$notification->group_id}}">{{$notification->group_name}}</a>
        @elseif($notification->type == "invite") invited you to join group <a href="../group/{{$notification->group_id}}">{{$notification->group_name}}</a>
        @elseif($notification->type == "ban") banned you from group <a href="../group/{{$notification->group_id}}">{{$notification->group_name}}</a>
        @elseif($notification->type == "group_ownership") made you the owner of group <a href="../group/{{$notification->group_id}}">{{$notification->group_name}}</a>

        @elseif($notification->type == "comment_post") commented in your <a onclick="showContext({{$notification->comment_id}}, 'comment')">post</a>
        @elseif($notification->type == "liked_comment") liked your <a onclick="showContext({{$notification->comment_id}}, 'comment')">comment</a>
        @elseif($notification->type == "reply_comment") replied your <a onclick="showContext({{$notification->comment_id}}, 'subcomment')">comment</a>
        @elseif($notification->type == "comment_tagging") tagged you in a <a onclick="showContext({{$notification->comment_id}}, 'comment')">comment</a>

        @else algum erro ocorreu atrás!
        @endif
    </h3>
    @if($notification->type == "request_follow")
        <button class="request notification-button" onclick="acceptRequestFollow({{$notification->emitter_user}}, {{$notification->id}})">
            <i class="fa fa-check" aria-hidden="true"></i> Accept
        </button>
        <button class="request notification-button" onclick="rejectRequestFollow({{$notification->emitter_user}}, {{$notification->id}})">
            <i class="fa fa-times" aria-hidden="true"></i> Reject
        </button>
    @elseif($notification->type == "requested_join")
        <button class="request notification-button" onclick="acceptJoinRequest({{$notification->emitter_user}}, {{$notification->group_id}}, {{$notification->id}})">
            <i class="fa fa-check" aria-hidden="true"></i> Accept
        </button>
        <button class="request notification-button" onclick="rejectJoinRequest({{$notification->emitter_user}}, {{$notification->group_id}}, {{$notification->id}})">
            <i class="fa fa-times" aria-hidden="true"></i> Reject
        </button>
    @elseif($notification->type == "invite")
        <button class="request notification-button" onclick="acceptInviteRequest({{$notification->group_id}}, {{$notification->id}})">
            <i class="fa fa-check" aria-hidden="true"></i> Accept
        </button>
        <button class="request notification-button" onclick="rejectInviteRequest({{$notification->group_id}}, {{$notification->id}})">
            <i class="fa fa-times" aria-hidden="true"></i> Reject
        </button>
    @else
        <button class="notification-button" onclick="removeNotification({{$notification->id}})">X</button>
    @endif
</article>
@endif
@endforeach
