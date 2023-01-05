@extends('layouts.app')
@include('partials.feed')
@include('sidebar.bar')

@section('sidebar')
  @yield('bar')
@endsection

@section('content')
    @yield('feed')
@endsection