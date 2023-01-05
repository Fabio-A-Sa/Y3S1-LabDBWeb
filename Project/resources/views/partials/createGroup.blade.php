
<section id="createPostModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content new-post-card">
        <div class="modal-body">
            <form id="newgroup" method="POST" action="{{ url('group/create') }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <label for="name">Name</label>
                <input placeholder="Group name" type="text" name="name" value="{{old('name')}}" required autofocus>
                    @if ($errors->has('name'))
                        <h5 class="error">
                            {{ $errors->first('name') }}
                        </h5>
                    @endif
                <label for="description">Description</label>
                <textarea id="newgroup-description" placeholder="Describe your group" name="description" value="{{old('description')}}" rows="8" cols="25" maxlength="300"></textarea>
                @if ($errors->has('description'))
                    <h5 class="error">
                        {{ $errors->first('description') }}
                    </h5>
                @endif
                <label>
                    Image: <input type="file" id="image" name="image">
                </label>
                <label>
                    Public Group? <input type="checkbox" name="public" checked>
                </label>
            </form>
        </div>
        <div class="modal-footer border-0">
            <!--<button type="button" class="btn btn-secondary" data-dismiss="modal"><h4><i class="fa fa-times" aria-hidden="true"></i></button>-->
            <!--<button type="submit" form="newPostForm" onclick="" class="btn btn-primary"><h4><i class="fa fa-paper-plane" aria-hidden="true"></i></h4></button>-->
            <button type="button" class="button-post-comment" data-dismiss="modal"><h4><i class="fa fa-times"></i></button>
            <button type="submit" class="button-post-comment" form="newgroup"><h4><i class="fa fa-paper-plane"></i></h4></button>
        </div>
    </div>
  </div>
</section>
