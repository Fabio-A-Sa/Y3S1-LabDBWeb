<section class="sidebar-options">
    <h3 class="group-div-title"> Options </h3>
    @if($group->owner_id == Auth::user()->id)
        <button class="sidebar-button-2" onclick="window.location='{{ url("/group/".$group->id."/edit") }}'">
            <i class="fa fa-pencil" aria-hidden="true"></i> Edit Group
        </button>
    @endif
    @if($group->owner_id == Auth::user()->id || Auth::user()->isAdmin())
        <button class="sidebar-button-2 delete-group-button" onclick="confirmation(deleteGroup,[{{$group->id}}])">
            <i class="fa fa-ban" aria-hidden="true"></i> Delete Group
        </button>
    @endif
</section>