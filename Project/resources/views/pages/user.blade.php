@extends('layouts.app')
@include('partials.profile')
@include('sidebar.bar')
@include('partials.confirmation')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    @yield('popup_Groups')
    @yield('profile')
@endsection