<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Message extends Model
{
    use HasFactory;
    public $timestamps  = false;
    protected $table = 'message';
    protected $fillable = [
        'emitter_id', 'receiver_id', 'content', 'date', 'viewed'
    ];

    public function receiver() {
        return User::find($this->receiver_id);
    }

    public function emitter() {
        return User::find($this->emitter_id);
    }

    public function mediaExist(int $user_id) {
        $path = storage_path()."/images/message/";
        $files = glob($path.$this->id.".{png,jpg,jpeg,gif,mp4,mp3}", GLOB_BRACE);
        if(sizeof($files) < 1) return null;
        $image_words = explode('.', $files[0]);
        return end($image_words);
    }
}
