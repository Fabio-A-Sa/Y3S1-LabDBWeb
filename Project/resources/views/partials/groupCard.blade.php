<article class="groupCard card">
    <a href="/group/{{$group->id}}" class="no-hover group-card-content">
        <div class="group-card-image-holder">
            <img src="{{ asset($group->media($group->id)) }}" alt="Group photo">
        </div>
        <div class="card-body">
            <h2 class="card-title">{{ $group->name }}</h2>
            <h5 class="card-text">{{ count($group->members()->get()) }} members</h5>
            <object>
            <p class="card-text">{!! $group->description !!}</p>
            </object>
        </div>
    </a>
</article>

