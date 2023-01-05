<article class="{{Auth::user()->id == $message->emitter_id ? 'msg-right' : 'msg-left'}}">
    <div class="{{Auth::user()->id == $message->emitter_id ? 'msg-page-message-ballon-me' : 'msg-page-message-ballon-other'}}">
        <h4 class="chat-msg-content">{!! $message->content !!}</h4>
        @if(null !== $message->mediaExist(Auth::user()->id))
            @if($message->mediaExist(Auth::user()->id) == "mp4")
                <video class="messagemedia" width="80%" height="auto" controls>
                    <source src="/images/message?user={{Auth::user()->id}}&message={{$message->id}}" type="video/mp4" alt="message video">
                </video><br>
            @elseif($message->mediaExist(Auth::user()->id) == "mp3")
                <audio controls>
                    <source src="/images/message?user={{Auth::user()->id}}&message={{$message->id}}" type="audio/mp3" alt="message audio">
                </audio>
            @else
            <img class="messagemedia" src="/images/message?user={{Auth::user()->id}}&message={{$message->id}}" width="35%" alt="message picture"><br>
            @endif
        @endif
        <h7 class="chat-msg-content">{{convert_time($message->date)}}</h7>
        <div class="{{Auth::user()->id == $message->emitter_id ? 'msg-bubble-right' : 'msg-bubble-left'}}"></div>
    </div>
</article>