@extends('layouts.app')
@include('sidebar.bar')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    <header id="search-header" class="search-page-card">
        <h1>Search Users</h1><br>
        <input type="search" id="search-contacts" placeholder="Search..."></input>
    </header><br>

    <section id="results-users">
    </section>
@endsection