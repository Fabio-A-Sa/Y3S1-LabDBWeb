let log = console.log.bind(console),
id = val => document.getElementById(val),
startRecording = id('chat-audio-record'),
stream, recorder, counter=1, chunks, media;

var blob;

startRecording.onclick = e => {

    let typeOpt = document.getElementById('chat-audio-record');
    if(typeOpt.className == "chat-audio-stop") {
        typeOpt.className = "chat-audio-start";
        typeOpt.innerHTML = '<i class="fa fa-microphone" aria-hidden="true"></i> START RECORDING';
        recorder.stop();
        return;
    }

    media = {tag: 'audio',
            type: 'audio/mp3',
            ext: '.mp3',
            gUM: {audio: true}
    };

    navigator.mediaDevices.getUserMedia(media.gUM).then(_stream => {
        stream = _stream;
        recorder = new MediaRecorder(stream);
        recorder.ondataavailable = e => {
        chunks.push(e.data);
        if(recorder.state == 'inactive')  appendAudio();
        };
        typeOpt.className = "chat-audio-stop";
        typeOpt.innerHTML = '<i class="fa fa-microphone-slash" aria-hidden="true"></i> STOP RECORDING';
        chunks=[];
        recorder.start();
        log('got media successfully');
    }).catch(log);
}

function appendAudio(){
    let blob = new Blob(chunks, {type: media.type });
    url = URL.createObjectURL(blob);
    mt = document.getElementById("chat-audio-recording");
    if(mt == null) mt = document.createElement(media.tag);
    mt.id = "chat-audio-recording";
    mt.controls = true;
    mt.src = url;
    let append_place = document.querySelector(".chat-audio-controllers");
    append_place.appendChild(mt);

    //append audio to file send
    let chatInputMedia = document.getElementById("chat-media-input");
    console.log(chatInputMedia);
    let file = new File([blob], "audio.mp3", {type:"audio/mp3"});
    let container = new DataTransfer();
    container.items.add(file);
    chatInputMedia.files = container.files;
    console.log(chatInputMedia.files);
}