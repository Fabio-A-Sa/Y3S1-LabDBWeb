@extends('layouts.app')
@include('sidebar.bar')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    <header class="main-features-title">
        <h1>Main Features<h1>
    </header>
    <section class="main-features-content">

        <article class="feature-card">
            <h3>As an visitor, you can:</h3>
            <ul>
                <li>Login/Logout</li>
                <li>Registration</li>
                <li>Recover password</li>
                <li>View public timeline</li>
                <li>View public profiles</li>
                <li>View public groups</li>
            </ul>
        </article> 

        <article class="feature-card">
            <h3>As an authenticated user, you can:</h3>
            <ul> 
                <li>View profile</li>
                <li>Edit profile</li>
                <li>Suport profile picture</li>
                <li>View personal notifications</li>
                <li>View personalized timeline</li>
                <li>Exact match search</li>
                <li>Search for public Users</li>
                <li>Search filters</li>
                <li>Full-text search</li>
                <li>Search filters</li>
                <li>Send friend requests</li>
                <li>View profiles followed</li>
                <li>Search for posts, comments, groups and users</li>
                <li>Follow someone</li>
                <li>Manage received follow requests</li>
                <li>Manage followers</li>
                <li>Create post</li>
                <li>Comment on posts</li>
                <li>Like posts</li>
                <li>Comment posts</li>
                <li>Reply to comments</li>
                <li>Create groups</li>
                <li>View users' feed</li>
                <li>Join public group</li>
                <li>Manage notifications</li>
                <li>Tag friends in posts</li>
                <li>Request to join public groups</li>
            </ul> 
            
        </article>
        <article class="feature-card">
            <h3>As the author of a post, you can:</h3>
            <ul> 
                <li>Edit post</li>
                <li>Delete post</li>
                <li>Manage post visibility</li>
            </ul>
        </article>
        <article class="feature-card">
            <h3>As the author of a comment, you can:</h3>
            <ul> 
                <li>Edit comment</li>
                <li>Delete comment</li>
            </ul>
        </article>
        <article class="feature-card">
            <h3>As a member of a group, you can:</h3>
            <ul>
                <li>View group members</li>
                <li>Post on group</li>
                <li>Leave group</li>
            </ul>
        </article>
        <article class="feature-card">
            <h3>As an owner of a group, you can:</h3>
            <ul> 
                <li>Edit group information</li>
                <li>Remove member</li>
                <li>Invite to group</li>
                <li>Remove post from group</li>
                <li>Change group privacy</li>
                <li>Manage group invitations</li>
                <li>Manage join requests</li>
            </ul>
        </article>
        <article class="feature-card">
            <h3>The admins are able to do the following actions:</h3>
            <ul>
                <li>Administer user accounts</li>
                <li>Block and unblock user accounts</li>
                <li>Delete user account</li>
            </ul>
        </article>
        <article class="feature-card">
            <h3>These are the notification categories that exist:</h3>
            <ul> 
                <li>Requests to follow you</li>
                <li>New followers</li>
                <li>Accepted follow resquests</li>
                <li>Requests to join your group</li>
                <li>Someone joined your group</li>
                <li>Group join requests accepted</li>
                <li>Members left your group</li>
                <li>Group invitations</li>
                <li>Group bans</li>
                <li>Group ownership tranfers</li>
                <li>Likes on your post</li>
                <li>Tags on other posts</li>
                <li>Likes on your comments</li>
                <li>Comments on your posts</li>
                <li>Replies to your comments</li>
                <li>Tags on other comments</li>
            </ul>
        </article>
        <article class="feature-card">
            <h3>Features included to help you:</h3>
            <ul> 
                <li>Placeholders in form inputs</li>
                <li>Contextual error messages</li>
                <li>Contextual help</li>
                <li>About us/Contacts</li>
                <li>Main features</li>
                <li>Help</li>
            </ul>
        </article>
    </section>
@endsection