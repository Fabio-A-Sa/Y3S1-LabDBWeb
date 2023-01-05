<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Metadata -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- Open Graph Metadata -->
    <meta property="og:title" content="OnlyFEUP" />
    <meta property="og:type" content="social media" />
    <meta property="og:url" content="lbaw2255.fe.up.pt" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="{{ asset('css/milligram.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link href="{{ asset('css/searchpage.css') }}" rel="stylesheet">
    <link href="{{ asset('css/postcard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/comment.css') }}" rel="stylesheet">
    <link href="{{ asset('css/group.css') }}" rel="stylesheet">
    <link href="{{ asset('css/notification.css') }}" rel="stylesheet">
    <link href="{{ asset('css/editpage.css') }}" rel="stylesheet">
    <link href="{{ asset('css/messages.css') }}" rel="stylesheet">
    <link href="{{ asset('css/static.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="{{ asset('js/app.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/audioRecorder.js') }}" defer></script>

    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

  </head>
  <body>
    <button type="button" id="sidebar-toggle-open" class="open-sidebar-button" onclick="toggleSidebar()" hidden></button>
    <nav id="sidebar">
        @section('sidebar')
        @show
    </nav>
    <section id="content">
      @section('content')
      @show
    </section>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>