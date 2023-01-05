@extends('layouts.app')
@include('sidebar.bar')
@include('partials.createGroup')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
<section id="groups">
    @include('partials.alert')
    @include('partials.createGroup')

    <header id="search-header" class="groups-page-card">
        <h1>Groups</h1>
    </header>

    <nav class="nav nav-pills nav-justified myNav" id="groups-page-nav" role="tablist">
        <a class="nav-item nav-link active text-white group-nav-bar-button left-nav-button" id="my-groups-tab" data-toggle="pill" href="#my-groups" role="tab">My groups</a>
        <a class="nav-item nav-link text-white group-nav-bar-button right-nav-button" id="public-groups-tab" data-toggle="pill" href="#public-groups" role="tab">Public Groups</a>
    </nav>

    <div class="tab-content group-page-content-tab">
        <section class="tab-pane show active" id="my-groups" role="tabpanel">
            <div class="groupsList">                               
                @forelse($userGroups as $group)
                    @include('partials.groupCard', ['group' => $group])
                @empty
                    <h2 class="no_results">You don't belong to any group</h2>
                @endforelse
            </div>
        </section>
        <section class="tab-pane" id="public-groups" role="tabpanel">
            <div class="groupsList">              
                @forelse($publicGroups as $group)
                    @include('partials.groupCard', ['group' => $group])
                @empty
                    <h2 class="no_results">No results found</h2>
                @endforelse          
            </div>                                                 
        </section>
    </div>
</section>
@endsection