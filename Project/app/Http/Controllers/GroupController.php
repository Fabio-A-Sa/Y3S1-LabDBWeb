<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TagController;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Models\User;
use App\Models\Post;
use App\Models\Follow;
use App\Models\Member;
use App\Models\Blocked;
use App\Models\Notification;
use App\Models\RequestFollow;
use App\Models\UserNotification;
use App\Models\GroupNotification;
use App\Models\GroupJoinRequest;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        $this->authorize('create', Group::class);

        $request->validate([
            'name' => 'unique:groups,name|min:3|max:255',
            'description' => 'max:255'
        ]);

        $group = new Group();
        $group->owner_id = Auth::user()->id;
        $group->name = $request->name;
        $group->description = nl2br(TagController::parseContent($request->description,'desc',-1));
        $group->is_public = null !== $request->public;

        $group->save();
        ImageController::create($group->id, 'groups', $request);
        
        return redirect('group/'.$group->id)->with('success', 'Group successfully created');
    }

    public function list()
    {   
        if(!Auth::check()){
            return redirect('login');
        }
        $this->authorize('list', Group::class);
        return view('pages.groupsPage', ['publicGroups' => Group::publicGroups()->get(), 'userGroups' => Auth::user()->allGroups()->get()]);
    }

    public function show(int $id)
    {
        $group2 = Group::find($id);
        if(is_null($group2)){
            return redirect()->back();
        }

        $group = Group::findOrFail($id);

        return view('pages.group', ['group' => $group]); 
    }

    public function delete(Request $request){
        $group = Group::findOrFail($request->id);
        $this->authorize('delete', Auth::user(), $group);
      
        ImageController::delete($group->id, 'groups');
        $group->delete(); 
    }

    public function editPage(int $group_id)
    {
        $group = Group::findOrFail($group_id);
        $this->authorize('editPage', $group);
        return view('pages.editGroup', ['group' => $group, 
                                        'old' => ['name' => $group->name,
                                                  'description' => $group->description,
                                                  'public' => $group->is_public ] ]);
    }

    public function edit(Request $request)
    {
        $group = Group::findOrFail($request->id);

        $this->authorize('edit', $group);
        
        $request->validate([
            'name' => 'unique:groups,name,'.$group->id.'|min:3|max:255',
            'description' => 'max:255'
        ]);
      
        if($request->file('image')){ 
            if( !in_array(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION),['jpg','jpeg','png'])) {
                return redirect('group/'.$group->id.'/edit')->with('error', 'File not supported');
            }
            $request->validate([
                'image' =>  'mimes:png,jpeg,jpg',
            ]);
            ImageController::update($group->id, 'groups', $request);
        }

        $group->name = $request->name;
        $group->description = nl2br(TagController::parseContent($request->description,'desc',-1));
        $group->is_public = null !== $request->public;

        $group->save();
        return redirect('group/'.$group->id);
    }

    public function removeMember(Request $request){

        $group = Group::findOrFail($request->group_id);
        $member = User::findOrFail($request->member_id);
        $this->authorize('removeMember', $group, $member);

        DB::beginTransaction();

        Member::where('group_id', $group->id)
            ->where('user_id', $member->id)
            ->delete();

        Notification::insert([
            'emitter_user' => $group->owner_id,
            'notified_user' => $member->id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]); 
    
        $newNotification = Notification::select('notification.id')
                                        ->where('emitter_user', $group->owner_id)
                                        ->where('notified_user', $member->id)->get()->last();
    
        GroupNotification::insert([
            'id' => $newNotification->id,
            'group_id' => $group->id,
            'notification_type' => 'ban',
        ]);
        
        DB::commit();
    }

    public function search(Request $request) {
        
        if (!Auth::check()) return null;
        $input = $request->get('search') ? $request->get('search').':*' : "*";
        $groups = Group::whereRaw("groups.tsvectors @@ to_tsquery(?)", [$input])
                    ->orderByRaw("ts_rank(groups.tsvectors, to_tsquery(?)) ASC", [$input])
                    ->get();

        return view('partials.searchGroup', compact('groups'))->render();
    }

    public function join(Request $request) {
        
        $this->authorize('join', Group::class);
        $group = Group::find($request->group_id);

        DB::beginTransaction();

        Member::insert([
            'user_id' => Auth::user()->id,
            'group_id' => $group->id,
            'is_favorite' => false
        ]);

        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $group->owner_id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]); 

        $newNotification = Notification::select('notification.id')
                                    ->where('emitter_user', Auth::user()->id)
                                    ->where('notified_user', $group->owner_id)->get()->last();

        GroupNotification::insert([
            'id' => $newNotification->id,
            'group_id' => $group->id,
            'notification_type' => 'joined_group',
        ]);

        DB::commit();
    }

    public function leave(Request $request) {

        $this->authorize('leave', Group::class);
        $group = Group::find($request->group_id);

        DB::beginTransaction();

        Member::where('group_id', $group->id)
              ->where('user_id', Auth::user()->id)->delete();

        $oldNotification = Notification::leftJoin('group_notification', 'notification.id', '=', 'group_notification.id')
              ->select('notification.id')
              ->where('notification.emitter_user', Auth::user()->id)
              ->where('notification.notified_user', $group->owner_id)
              ->where('group_notification.notification_type', 'joined_group')
              ->get()->last();

        if ($oldNotification) {
            GroupNotification::where('id', $oldNotification->id)->delete();
            Notification::where('id', $oldNotification->id)->delete();
        }

        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $group->owner_id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]); 

        $newNotification = Notification::select('notification.id')
                                    ->where('emitter_user', Auth::user()->id)
                                    ->where('notified_user', $group->owner_id)->get()->last();

        GroupNotification::insert([
            'id' => $newNotification->id,
            'group_id' => $group->id,
            'notification_type' => 'leave_group',
        ]);
        
        DB::commit();
    }

    public function doJoinRequest(Request $request) {

        $this->authorize('doJoinRequest', Group::class);
        $group = Group::find($request->group_id);

        DB::beginTransaction();

        GroupJoinRequest::insert([
            'user_id' => Auth::user()->id,
            'group_id' => $group->id
        ]);

        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $group->owner_id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]);

        $newNotification = Notification::select('notification.id')
                    ->where('emitter_user', Auth::user()->id)
                    ->where('notified_user', $group->owner_id)->get()->last();

        GroupNotification::insert([
            'id' => $newNotification->id,
            'group_id' => $group->id,
            'notification_type' => 'requested_join',
        ]);
        
        DB::commit();
    }

    public function cancelJoinRequest(Request $request) {

        $this->authorize('cancelJoinRequest', Group::class);
        $group = Group::find($request->group_id);

        DB::beginTransaction();

        GroupJoinRequest::where([
            'user_id' => Auth::user()->id,
            'group_id' => $group->id
        ])->delete();

        $oldNotification = Notification::leftJoin('group_notification', 'notification.id', '=', 'group_notification.id')
              ->select('notification.id')
              ->where('notification.emitter_user', Auth::user()->id)
              ->where('notification.notified_user', $group->owner_id)
              ->where('group_notification.notification_type', 'requested_join')
              ->get()->last();
        
        GroupNotification::where('id', $oldNotification->id)->delete();
        Notification::where('id', $oldNotification->id)->delete();
        
        DB::commit();
    }

    public function acceptJoinRequest(Request $request) {

        $this->authorize('acceptJoinRequest', Group::class);
        $group = Group::find($request->group_id);

        DB::beginTransaction();

        GroupJoinRequest::where([
            'user_id' => $request->user_id,
            'group_id' => $group->id
        ])->delete();

        $oldNotification = Notification::leftJoin('group_notification', 'notification.id', '=', 'group_notification.id')
              ->select('notification.id')
              ->where('notification.emitter_user', $request->user_id)
              ->where('notification.notified_user', Auth::user()->id)
              ->where('group_notification.notification_type', 'requested_join')
              ->get()->last();
        
        GroupNotification::where('group_notification.id', $oldNotification->id)
                ->update(['group_notification.notification_type' => 'joined_group']);

        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $request->user_id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]);

        $newNotification = Notification::select('notification.id')
                ->where('emitter_user', Auth::user()->id)
                ->where('notified_user', $request->user_id)->get()->last();

        GroupNotification::insert([
            'id' => $newNotification->id,
            'group_id' => $group->id,
            'notification_type' => 'accepted_join',
        ]);

        Member::insert([
            'group_id' => $group->id,
            'user_id' => $request->user_id,
        ]);
        
        DB::commit();
    }

    public function rejectJoinRequest(Request $request) {

        $this->authorize('rejectJoinRequest', Group::class);
        $group = Group::find($request->group_id);

        DB::beginTransaction();

        GroupJoinRequest::where([
            'user_id' => $request->user_id,
            'group_id' => $group->id
        ])->delete();

        $oldNotification = Notification::leftJoin('group_notification', 'notification.id', '=', 'group_notification.id')
            ->select('notification.id')
            ->where('notification.emitter_user', $request->user_id)
            ->where('notification.notified_user', $group->owner_id)
            ->where('group_notification.notification_type', 'requested_join')
            ->get()->last();
        
        if ($oldNotification) {
            GroupNotification::where('id', $oldNotification->id)->delete();
            Notification::where('id', $oldNotification->id)->delete();
        }
        
        DB::commit();
    }

    public function invite(Request $request) {

        $group = Group::find($request->group_id);
        $this->authorize('invite', $group);

        if ($group->owner_id == $request->user_id) return;
        DB::beginTransaction();

        Notification::insert([
            'emitter_user' => $group->owner_id,
            'notified_user' => $request->user_id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]);

        $newNotification = Notification::select('notification.id')
                    ->where('emitter_user', $group->owner_id)
                    ->where('notified_user', $request->user_id)->get()->last();

        GroupNotification::insert([
            'id' => $newNotification->id,
            'group_id' => $group->id,
            'notification_type' => 'invite',
        ]);

        DB::commit();
    }

    public function cancelInvite(Request $request) {

        $group = Group::find($request->group_id);
        $this->authorize('cancelInvite', $group);
        
        DB::beginTransaction();
        
        $oldNotification = Notification::leftJoin('group_notification', 'notification.id', '=', 'group_notification.id')
        ->select('notification.id')
        ->where('notification.emitter_user', $group->owner_id)
        ->where('notification.notified_user', $request->user_id)
        ->where('group_notification.notification_type', 'invite')
        ->get()->last();

        if ($oldNotification)  {
            GroupNotification::where('id', $oldNotification->id)->delete();
            Notification::where('id', $oldNotification->id)->delete();
        }

        DB::commit();
    }

    public function rejectInvite(Request $request) {

        $group = Group::find($request->group_id);
        $this->authorize('rejectInvite', $group);
        
        DB::beginTransaction();
        
        $oldNotification = Notification::leftJoin('group_notification', 'notification.id', '=', 'group_notification.id')
                    ->select('notification.id')
                    ->where('notification.emitter_user', $group->owner_id)
                    ->where('notification.notified_user', Auth::user()->id)
                    ->where('group_notification.notification_type', 'invite')
                    ->get()->last();

        if ($oldNotification) {
            GroupNotification::where('id', $oldNotification->id)->delete();
            Notification::where('id', $oldNotification->id)->delete();
        }

        DB::commit();
    }

    public function acceptInvite(Request $request) {

        $group = Group::find($request->group_id);
        $this->authorize('acceptInvite', $group);
        
        DB::beginTransaction();
        
        $oldNotification = Notification::leftJoin('group_notification', 'notification.id', '=', 'group_notification.id')
        ->select('notification.id')
        ->where('notification.emitter_user', $group->owner_id)
        ->where('notification.notified_user', Auth::user()->id)
        ->where('group_notification.notification_type', 'invite')
        ->get()->last();

        GroupNotification::where('id', $oldNotification->id)->delete();
        Notification::where('id', $oldNotification->id)->delete();

        Member::insert([
            'group_id' => $group->id,
            'user_id' => Auth::user()->id
        ]);

        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $group->owner_id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]);

        $newNotification = Notification::select('notification.id')
                ->where('emitter_user', Auth::user()->id)
                ->where('notified_user', $group->owner_id)->get()->last();

        GroupNotification::insert([
            'id' => $newNotification->id,
            'group_id' => $group->id,
            'notification_type' => 'joined_group',
        ]);

        DB::commit();
    }

    public function favorite(Request $request) {

        $group = Group::find($request->id);
        $this->authorize('favorite', $group);

        Member::where(['user_id' => Auth::user()->id,
                       'group_id' => $group->id])
                       ->update(['is_favorite' => true]);
    }

    public function unfavorite(Request $request) {

        $group = Group::find($request->id);
        $this->authorize('unfavorite', $group);

        Member::where(['user_id' => Auth::user()->id,
                        'group_id' => $group->id])
                        ->update(['is_favorite' => false]);
    }

    public function makeOwner(Request $request){
        
        $group = Group::findOrFail($request->input('group_id'));
        $this->authorize('makeOwner', $group);

        DB::beginTransaction();
        $group->owner_id = $request->input('member_id');
        $group->save();

        Notification::insert([
            'emitter_user' => Auth::user()->id,
            'notified_user' => $request->member_id,
            'date' => date('Y-m-d H:i'),
            'viewed' => false,
        ]);

        $newNotification = Notification::select('notification.id')
                ->where('emitter_user', Auth::user()->id)
                ->where('notified_user', $request->member_id)->get()->last();

        GroupNotification::insert([
            'id' => $newNotification->id,
            'group_id' => $group->id,
            'notification_type' => 'group_ownership',
        ]);

        DB::commit();

        return redirect()->back(); 
    }

    public function deleteMedia(Request $request) {

        $group = Group::findOrFail($request->id);
        $this->authorize('deleteMedia', $group);
        ImageController::delete($group->id, 'groups');
        return redirect()->back();
    }
}
