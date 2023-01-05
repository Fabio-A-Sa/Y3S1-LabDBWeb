<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;

class ImageController extends Controller
{
    public static function create(int $id, string $type, Request $request) {
        if($type == "post"){
            if ($request->file('image')) {
                $file= $request->file('image');

                $filename = $id.".".pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION);

                //$filename = $id.pathinfo($request->content, PATHINFO_EXTENSION);
                $file->move(public_path('images/'. $type. '/'), $filename);
            }
        }
        elseif($type == "message") {
            $file = $request->file('image');
            if(isset($file)){
                $filename = $id.".".pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION);
                $file->move(storage_path()."/images/{$type}/", $filename);
            }
        }
        else{
            if ($request->file('image')) {
                $file= $request->file('image');
                $filename= $id.".jpg";
                $file->move(public_path('images/'. $type. '/'), $filename);
            }
        }
    }

    public static function delete(int $id, string $type) {
        foreach ( glob(public_path().'/images/'.$type.'/'.$id.'.*',GLOB_BRACE) as $image){
            if (file_exists($image)) unlink($image);
        }
    }

    public static function update(int $id, string $type, Request $request) {
        if ($request->file('image')) {
            ImageController::delete($id, $type);
            ImageController::create($id, $type, $request);
        }
    }

    /* chat messages images */

    public function viewMessageMedia(string $type, Request $request){

        if (!Auth::check()) return null;
        $user = User::find($request->get('user'));
        $message = Message::find($request->get('message'));

        if($user->id == $message->emitter_id || $user->id == $message->receiver_id) {
            $path = storage_path()."/images/{$type}/";
            $files = glob($path.$message->id.".{png,jpg,jpeg,gif,mp4,mp3}", GLOB_BRACE);
            if(sizeof($files) < 1) return null;
            return response()->file($files[0]);
        }

        return null;
    }

}