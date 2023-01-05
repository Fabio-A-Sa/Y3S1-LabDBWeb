<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function messages() {
        if(!Auth::check()){
            return redirect('login');
        }

        $this->authorize('messages', Message::class);
        $users = Auth::user()->uniqueEmitters();
        
        return view('pages.messages', ['users' => $users]);
    }

    public function show(int $id) {
        if(!Auth::check()){
            return redirect('login');
        }

        $user = User::find($id);
        $this->authorize('show', Message::class);

        if(is_null($user)){
            return redirect('messages/');
        }
        $messages = Auth::user()->messagesWith($user);
        return view('pages.message', ['user' => $user, 'messages' => $messages]);
    }

    public function create(Request $request) {
        $this->authorize('create', Message::class);

        $contentFound = $request->input('content');
        if (!isset($contentFound) && $_FILES["image"]["error"]) {
            return redirect()->back();
        }
        if(!isset($contentFound) && !in_array(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION),['jpg','jpeg','png','gif','mp4','mov','mp3','ogg'])) {
            return redirect()->back()->with('error', 'File format not supported');
        }
       
        $message = new Message();
        $message->emitter_id = Auth::user()->id;
        $message->receiver_id = $request->user_id;
        $message->content = nl2br(TagController::parseContent($request->content, 'message', -1));
        $message->date = date('Y-m-d H:i:us');
        $message->save();

        ImageController::create($message->id, 'message', $request);
        return redirect()->back();
    }

    public function getNewMessages(Request $request) {

        if (!Auth::check()) return null;
        $user = User::find($request->get('id'));
        if(!isset($user->id)) return null;
        $messages = Message::where([ 'emitter_id' => $user->id,
                                     'receiver_id' => Auth::user()->id,
                                     'viewed' => false ])->get();
        
        Message::where(['emitter_id' => $user->id, 'receiver_id' => Auth::user()->id])->update(['viewed' => true]);

        return view('partials.newMessages', compact('messages'))->render();
    }
}
