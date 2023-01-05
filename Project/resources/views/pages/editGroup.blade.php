@extends('layouts.app')
@include('sidebar.bar')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    @include('partials.alert')
    <form method="POST" action="../../../group/deleteMedia" id="removePhoto" hidden>
        {{ csrf_field() }}
        <input name="id" value="{{$group->id}}" hidden>
    </form>
    
    <article class="edit-page-card">
        <h1 class="edit-page-card-title">Edit Group</h1>
        <div class="edit-page-card-content">
            <form class="edit-page-form" method="post" action="{{ url('group/edit') }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="number" name="id" value="{{$group->id}}" required hidden></input>

                <section class="edit-page-photo-options">
                    <img id="edit-group-photo" class="edit-page-image" src="{{ asset($group->media($group->id)) }}" width=60% alt="Group image">
                    <h4 for="image">Choose a profile picture:</h4>
                    <input type="file" name="image" id="image">
                    @if(!preg_match("/(\/default.jpg)/",$group->media()))
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
                    </section>

                    <section class="edit-page-descriptions-options">
                        <h3 for="description">Description</h3>
                        @php
                            $descriptionfix = str_replace("<br />" , "", $old['description']);
                            $descriptionfix = preg_replace("/(<([^>]+)>)/", "", $descriptionfix);
                        @endphp
                        <textarea placeholder="Describe the group..." name="description" rows="8" cols="50" autofocus>{{ old('description', $descriptionfix) }}</textarea>
                        @if ($errors->has('description'))
                            <h5 class="error">
                                {{ $errors->first('description') }}
                            </h5>
                        @endif
                    </section>

                    <section class="edit-page-config-options">
                        <h3>
                            Public Group? <input type="checkbox" name="public" {{ old('public', $old['public']) ? 'checked' : '' }}>
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