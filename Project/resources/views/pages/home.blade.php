@extends('layouts.app')
@include('sidebar.bar')
@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    @include('partials.feed')
    @include('partials.confirmation')
    @if(Auth::check())
        <button type="button" id="newpostbutton" class="newpostbutton" data-toggle="modal" data-target="#createPostModal" onclick="createPost(null)"></button>
        <button class="searchpagebutton" onclick="window.location.href= '{{ url('/home/search') }}' "></button>
        <button class="notificationspagebutton" onclick="window.location.href= '{{ url('/home/notifications') }}' "></button>
    @endif
    @include('partials.alert')
    @include('partials.createPost')
    @yield('feed')
@endsection