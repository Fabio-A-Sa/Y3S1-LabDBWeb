{{-- Message --}}
@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible" id="alert" role="alert">
        <button type="button" class="close close-alert button-post-comment" data-dismiss="alert" onCLick="deleteMessage()" id="close-alert-button">
            <h4><i class="fa fa-times" aria-hidden="true"></i></h4>
        </button>
        <h4> <strong>Success !</strong> {{ session('success') }} </h4>
    </div>
@endif

@if (Session::has('error'))
    <div class="alert alert-danger alert-dismissible" id="alert" role="alert">
        <button type="button" class="close close-alert button-post-comment" data-dismiss="alert">
            <h4><i class="fa fa-times" aria-hidden="true"></i></h4>
        </button>
        <h4> <strong>Error !</strong> {{ session('error') }} </h4>
    </div>
@endif