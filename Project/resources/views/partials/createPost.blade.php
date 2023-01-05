<section id="createPostModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content new-post-card">
      <div class="modal-body">
        <form method="POST" action="{{ url('post/create') }}" enctype="multipart/form-data" id="newPostForm">
          {{ csrf_field() }}
          <h3 class="new-post-title">New post</h3>
          <textarea placeholder="Write you post..." id="newpost-content" name="content" rows="8" cols="25" maxlength="256" autofocus></textarea>
          <label>
              Image: <input type="file" id="image" name="image">
          </label>
          <label>
              Public Post? <input type="checkbox" name="public" checked>
          </label>
      </form>
      </div>
      <div class="modal-footer border-0">
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal"><h4><i class="fa fa-times" aria-hidden="true"></i></button>-->
        <!--<button type="submit" form="newPostForm" onclick="" class="btn btn-primary"><h4><i class="fa fa-paper-plane" aria-hidden="true"></i></h4></button>-->
        <button type="button" class="button-post-comment" data-dismiss="modal"><h4><i class="fa fa-times"></i></button>
        <button type="submit" class="button-post-comment" form="newPostForm"><h4><i class="fa fa-paper-plane"></i></h4></button>
      </div>
    </div>
  </div>
</section>
