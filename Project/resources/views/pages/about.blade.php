@extends('layouts.app')
@include('sidebar.bar')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    <header id="admin-header" class="aboutus-title">
        <h1>About Us<h1>
    </header>
    <section class="aboutus-content">
        <h2>OnlyFEUP</h2>
        <p>
            The main goal of the OnlyFEUP project is the development of a web-based social network with the purpose of creating 
            connections between students and staff, sharing resources about courses and subjects. This is a tool that can be used by anyone from FEUP.
            After signing up and verifying the user is related to the university (students/teachers), they can start using it for a better experience at FEUP.
        </p>
    </section>
    <section id="admins" class="aboutus-admin-list">
        <h2>Admins</h2>
        @foreach ($admins as $admin)
            <article class="aboutus-admin-card" id="user{{$admin->id}}">
                <img class="aboutus-profile-pic" src="{{$admin->media()}}" alt="Profile picture">
                <div class="aboutus-admin-info">
                    <a href="../user/{{$admin->id}}"><h2 class="aboutus-username search-page-card-user"> {{ $admin->name }}</h2></a>
                    <h3 class="aboutus-user-card-username">&#64;{{$admin->username}}</h3>
                </div>
                
            </article>
        @endforeach
    </section>
@endsection