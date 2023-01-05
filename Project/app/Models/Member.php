<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Member extends Model
{
    use HasFactory;
    public $timestamps  = false;
    protected $table = 'member';
    protected $fillable = [
        'user_id', 'group_id', 'is_favorite'
    ];

    public function user() {
        return User::find($this->user_id);
    }

    public function group() {
        return Group::find($this->group_id);
    }
}
