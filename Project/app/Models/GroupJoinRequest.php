<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupJoinRequest extends Model
{
    use HasFactory;
    protected $table = 'group_join_request';
    public $timestamps  = false;

    protected $fillable = [
        'user_id', 'group_id'
    ];  

    public function group(){
        $this->hasOne('App\Models\Group');
    }
}
