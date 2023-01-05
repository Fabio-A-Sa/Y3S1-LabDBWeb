@extends('layouts.app')
@include('sidebar.bar')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    @include('partials.alert')
    <form method="POST" action="../user/deleteMedia" id="removePhoto" hidden>
        {{ csrf_field() }}
        <input name="id" value="{{$user->id}}" hidden>
    </form>
    
    <article class="edit-page-card">
        <h1 class="edit-page-card-title">Edit Profile</h1>
        <div class="edit-page-card-content">
            <form class="edit-page-form" method="post" action="{{ url('user/edit') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <section class="edit-page-photo-options">
                    <img id="edit-profile-photo" class="edit-page-image" src="{{ Auth::user()->media() }}" width=60% alt="Profile image">
                    <h4 for="image">Choose a profile picture:</h4>
                    <input type="file" name="image" id="image">
                    @if(!preg_match("/(\/default.jpg)/",$user->media()))
                    <button form="removePhoto" class="edit-page-button">Remove Photo</button>
                    @endif
                </section>

                <section class="edit-page-info-options">
                    <section class="edit-page-names-options">
                        <h3 for="name">Name</h3>
                        <input placeholder="Your name" type="text" name="name" value="{{ old('name', $old['name']) }}" required autofocus></input>
                        @if ($errors->has('name'))
                            <h5 class="error">
                                {{ $errors->first('name') }}
                            </h5>
                        @endif

                        <h3 for="username">Username</h3>
                        <input type="text" placeholder="Your username" name="username" value="{{ old('username', $old['username']) }}" required autofocus></input>
                        @if ($errors->has('username'))
                            <h5 class="error">
                                {{ $errors->first('username') }}
                            </h5>
                        @endif
                    </section>

                    <section class="edit-page-descriptions-options">
                        <h3 for="description">Description</h3>
                        @php
                            $descriptionfix = str_replace("<br />" , "", $old['description']);
                            $descriptionfix = preg_replace("/(<([^>]+)>)/", "", $descriptionfix);
                        @endphp
                        <textarea name="description" rows="8" cols="50" placeholder="Describe yourself..." autofocus>{{ old('description', $descriptionfix) }}</textarea>
                        @if ($errors->has('description'))
                            <h5 class="error">
                                {{ $errors->first('description') }}
                            </h5>
                        @endif
                    </section>

                    <section class="edit-page-config-options">   
                        <h3 for="email">Email</h3>
                        <input type="text" name="email" placeholder="Your email" value="{{ old('email', $old['email']) }}" required autofocus></input>
                        @if ($errors->has('email'))
                            <h5 class="error">
                                {{ $errors->first('email') }}
                            </h5>
                        @endif

                        <h3 for="password">Password</h3>
                        <input type="password" name="password" placeholder="Your password" autofocus></input>

                        <h3 for="password_confirmation">Confirm password</h3>
                        <input type="password" name="password_confirmation" placeholder="Confirm your password" autofocus></input>
                        @if ($errors->has('password'))
                            <h5 class="error">
                                {{ $errors->first('password') }}
                            </h5>
                        @endif

                        <h3>
                            Public profile? <input type="checkbox" name="public" {{ old('public', $old['public']) ? 'checked' : '' }}>
                        </h3>
                    </section>

                    <section class="edit-page-final-buttons">
                        <button type="submit" class="edit-page-button-2">Submit</button>
                    </section>
                </section>
            </form>
        </div>
    </article>
@endsection