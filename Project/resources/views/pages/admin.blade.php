@extends('layouts.app')
@include('sidebar.bar')
@include('partials.confirmation')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    <header id="search-header" class="search-page-card">
        <h1>Admin Page</h1><br>
        <input type="search" id="search" placeholder="Search..."></input>
    </header><br>

    <nav class="nav nav-pills nav-justified myNav" id="searchpage-nav" role="tablist">
        <a class="nav-item nav-link active text-white search-nav-bar-button left-nav-button" id="postResults" data-toggle="pill" href="#results-posts" role="tab">0 Posts</a>
        <a class="nav-item nav-link text-white search-nav-bar-button" id="userResults" data-toggle="pill" href="#results-users" role="tab">0 Users</a>
        <a class="nav-item nav-link text-white search-nav-bar-button" id="commentResults" data-toggle="pill" href="#results-comments" role="tab">0 Comments</a>
        <a class="nav-item nav-link text-white search-nav-bar-button right-nav-button" id="groupResults" data-toggle="pill" href="#results-groups" role="tab">0 Groups</a>
    </nav>
    
    <div class="tab-content">
        <section class="tab-pane show active" id="results-posts" role="tabpanel">
        </section>
        <section class="tab-pane" id="results-users" role="tabpanel">
        </section>
        <section class="tab-pane" id="results-comments" role="tabpanel">
        </section>
        <section class="tab-pane" id="results-groups" role="tabpanel">
        </section>
    </div>
@endsection