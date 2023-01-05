@extends('layouts.app')
@include('sidebar.bar')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    <div class="message-page-chat">
        <section class="msg-contact-info">
            <img src="{{$user->media()}}" alt="Profile image">
            <div>
                <a href="../user/{{$user->id}}"><h3>{{$user->name}}</h3></a>
                <h4>&#64;{{$user->username}}</h4>
            </div>
        </section>

        <section id="msg-page-messages-container" class="msg-messages">
            @each('partials.message', $messages, 'message')
        </section>   

        <section id="msg-chat-box" class="msg-create">
            <article class="msg-create-article">
                <form method="post" class="message-form-card" action="{{ url('../message/create') }}" enctype="multipart/form-data" id="new-message-form">
                    {{ csrf_field() }}
                    <input name="user_id" value="{{$user->id}}" hidden>
                    <textarea id="content-textarea" placeholder="Write a message..." type="textbox" name="content" autofocus></textarea>
                    <input type="file" name="image" id="chat-media-input">
                    <div class="chat-audio-controllers">
                            <button type="button"  id="chat-audio-record" class="chat-audio-start" onclick="controlAudioRecording()">
                                <i class="fa fa-microphone" aria-hidden="true"></i> START RECODING
                            </button>
                    </div>
                </form>
                <button type="submit" form="new-message-form" class="button-message send-button-send-message">
                        <h4><i class="fa fa-paper-plane" aria-hidden="true"></i></h4>
                </button>
            </article>
        </section>
    </div> 
@endsection