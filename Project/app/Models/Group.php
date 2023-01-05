<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupNotification;
use App\Models\Notification;
use App\Models\Member;

class Group extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'groups';
    protected $fillable = [
        'name', 'description'
    ];

    public function owner() {
        return $this->belongsTo('App\Models\User');
    }

    public function members() {
        return $this->hasMany('App\Models\Member');
    }

    public function posts(){
        return $this->hasMany('App\Models\Post')->orderBy('date', 'desc'); 
    }

    public function joinRequests(){
        return $this->hasMany('App\Models\GroupJoinRequest');
    }

    public function media() { 
        $file = "/images/groups/$this->id.jpg";
        $default = "/images/groups/default.jpg";
        return (file_exists(public_path() . $file)) ? $file : $default;
    }

    public static function publicGroups(){
        return Group::where('is_public',true);
    }

    public function hasMember(User $user){
        return $this->members()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    public function hasJoinRequest(User $user) {
        return $this->joinRequests()
                    ->where('user_id', $user->id)
                    ->exists();  
    }

    public function hasInvite(User $user) {
        return Notification::join('group_notification', 'notification.id', '=', 'group_notification.id')
            ->where('notification.emitter_user', $this->owner_id)
            ->where('notification.notified_user', $user->id)
            ->where('group_notification.notification_type', 'invite')   
            ->exists();
    }

    public function isFavorite(User $user) {
        return Member::where([  'user_id' => $user->id,
                                'group_id' => $this->id,
                                'is_favorite' => true ])->exists();
    }
}
