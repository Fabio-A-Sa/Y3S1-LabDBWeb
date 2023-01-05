<article id="bar-group-item" class="bar-group-item">
    <img src="{{ asset($group->media($group->id)) }}" class ="bar-group-image" alt="group photo">
    <a href="/group/{{$group->id}}">{{$group->name}}</a>
</article>