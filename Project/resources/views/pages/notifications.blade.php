@extends('layouts.app')
@include('sidebar.bar')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    <header id="search-header" class="notification-page-card">
        <h1>My Notifications</h1>
    </header>
    <section id="notification_content">
        <section id="notification_buttons">

            <nav class="nav nav-pills nav-justified myNav" id="searchpage-nav" role="tablist">
                <a class="nav-item nav-link active text-white notif-nav-bar-button left-nav-button" id="button_post_notifications" data-toggle="pill" href="#post_notifications" role="tab">0 Posts</a>
                <a class="nav-item nav-link text-white notif-nav-bar-button" id="button_user_notifications" data-toggle="pill" href="#user_notifications" role="tab">0 Interactions</a>
                <a class="nav-item nav-link text-white notif-nav-bar-button" id="button_comment_notifications" data-toggle="pill" href="#comment_notifications" role="tab">0 Comments</a>
                <a class="nav-item nav-link text-white notif-nav-bar-button right-nav-button" id="button_group_notifications" data-toggle="pill" href="#group_notifications" role="tab">0 Groups</a>
            </nav>
        </section>

        <div class="tab-content">
            <section class="tab-pane show active" id="post_notifications" role="tabpanel">
            </section>
            <section class="tab-pane" id="user_notifications" role="tabpanel">
            </section>
            <section class="tab-pane" id="comment_notifications" role="tabpanel">
            </section>
            <section class="tab-pane" id="group_notifications" role="tabpanel">
            </section>
        </div>
    </section>
    
    <section id="notification_popup" hidden>

    </section>
    
    <section id="configuration_popup" hidden>

        <h2>Configurations</h2>
        <h5>Select your notification preferences</h5>

        <form method="POST" action="{{ url('notification/update') }}" id="configurationForm">
            {{ csrf_field() }}
            <div class="row-notif">
                <fieldset class="column-notif">
                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="request_follow" {{Auth::user()->acceptNotification('request_follow') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Requests to follow you</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="started_following" {{Auth::user()->acceptNotification('started_following') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">New followers</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="accepted_follow" {{Auth::user()->acceptNotification('accepted_follow') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Accepted follow resquests</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="requested_join" {{Auth::user()->acceptNotification('requested_join') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Requests to join your group</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="joined_group" {{Auth::user()->acceptNotification('joined_group') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Someone joined your group</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="accepted_join" {{Auth::user()->acceptNotification('accepted_join') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Group join requests accepted</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="leave_group" {{Auth::user()->acceptNotification('leave_group') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Members left your group</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="invite" {{Auth::user()->acceptNotification('invite') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Group invitations</span>
                    </label>
                </fieldset>
                <fieldset class="column-notif">
                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="ban" {{Auth::user()->acceptNotification('ban') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Group bans</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="group_ownership" {{Auth::user()->acceptNotification('group_ownership') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Group ownership tranfers</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="liked_post" {{Auth::user()->acceptNotification('liked_post') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Likes on your post</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="post_tagging" {{Auth::user()->acceptNotification('post_tagging') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Tags on other posts</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="liked_comment" {{Auth::user()->acceptNotification('liked_comment') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Likes on your comments</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="comment_post" {{Auth::user()->acceptNotification('comment_post') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Comments on your posts</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="reply_comment" {{Auth::user()->acceptNotification('reply_comment') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Replies to your comments</span>
                    </label>

                    <label class="toggle">
                        <input class="toggle-checkbox" type="checkbox" name="comment_tagging" {{Auth::user()->acceptNotification('comment_tagging') ? 'checked' : ''}}>
                        <div class="toggle-switch"></div>
                        <span class="toggle-label">Tags on other comments</span>
                    </label>
                </fieldset>
            </div>
        </form>
        <button class="notification-button" onclick="cancelPopup()">Cancel</button>
        <button class="notification-button" form="configurationForm" type="submit">Update</button>
    </section>
  
@endsection