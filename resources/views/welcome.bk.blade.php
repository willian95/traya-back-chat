<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Laravel</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

  <!-- Styles -->
  <style>
  html, body {
    background-color: #fff;
    color: #636b6f;
    font-family: 'Raleway', sans-serif;
    font-weight: 100;
    height: 100vh;
    margin: 0;
  }

  .full-height {
    height: 100vh;
  }

  .flex-center {
    align-items: center;
    display: flex;
    justify-content: center;
  }

  .position-ref {
    position: relative;
  }

  .top-right {
    position: absolute;
    right: 10px;
    top: 18px;
  }

  .content {
    text-align: center;
  }

  .title {
    font-size: 84px;
  }

  .links > a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
  }

  .m-b-md {
    margin-bottom: 30px;
  }
  </style>
</head>
<body>
  <div class="flex-center position-ref full-height">
    @if (Route::has('login'))
    <div class="top-right links">
      @auth
      <a href="{{ url('/home') }}">Home</a>
      @else
      <a href="{{ route('login') }}">Login</a>
      <a href="{{ route('register') }}">Register</a>
      @endauth
    </div>
    @endif

    <div class="content">
      <div class="title m-b-md">
        {{trans('messages.welcome')}}
        <a href="{{ route('login.provider', 'google') }}"
        class="btn btn-secondary">{{ __('Google Sign in') }}</a>
        <a href="{{ route('login.provider', 'facebook') }}"
        class="btn btn-secondary">{{ __('Facebook Sign in') }}</a>
      </div>

      <div class="links">
        <a href="https://laravel.com/docs">Documentation</a>
        <a href="https://laracasts.com">Laracasts</a>
        <a href="https://laravel-news.com">News</a>
        <a href="https://forge.laravel.com">Forge</a>
        <a href="https://github.com/laravel/laravel">GitHub</a>
      </div>
    </div>
  </div>

   <script src="https://js.pusher.com/4.1/pusher.min.js"></script>
  <script type="text/javascript">

  // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;
    var pusher = new Pusher('2a629b96ad637980d3db', {
      cluster: 'us2',
      encrypted: true
    });
    // Subscribe to the channel we specified in our Laravel Event
      var channel = pusher.subscribe('notification-9');
      // var channel = pusher.subscribe('vehicles-list');
    // Bind a function to a Event (the full Laravel class)
    // channel.bind('Modules\\Icda\\Events\\RecordListVehicles', function(data) {

      channel.bind('notificationUser', function(data) {
        console.log(data);
        console.log('Msg: '+data.message);
        console.log('user id'+data.user_id);
      });

  </script>

</body>
</html>
