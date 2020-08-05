<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="Traya Api backend, realizado por Ingenia Venezuela, Ingenia Web" content="Ingenia Venezuela - Ingenia Web">
  <meta name="keywords" content="ingenia,ingenia web,ingenia venezuela, api, backend,desarrollo,luis hurtado">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>TRAYA - API</title>

  <!-- Styles -->
  <link href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" media="all">

  <link rel="stylesheet" href="{{ URL::asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/css/flaticon.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/css/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/css/animate.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}">
</head>
<body>
  <!-- Hero section -->
  <section class="hero-section set-bg" data-setbg="assets/images/bg.jpg">
    <div class="container h-100">
      <div class="hero-content text-white">
        <div class="row">
          <div class="col-lg-6 pr-0">
            <h2>Traya  Backend</h2>
            <h3>Desarrollado por Ingenia</h3>
            <p>Crea,innova,visualiza, únete a nosotros y posiciónate, somos la mejor opción para hacer de tus ideas una realidad de otro planeta </p>
            <a href="#" class="site-btn">Visitanos</a>
          </div>
        </div>
        <div class="hero-rocket">
          <img src="./assets/images/rocket.png" alt="">
        </div>
      </div>
    </div>
  </section>
  <!-- Hero section end -->
        <script src="{{ URL::asset('js/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('js/owl.carousel.min.js') }}"></script>
        <script src="{{ URL::asset('js/circle-progress.min.js') }}"></script>
        <script src="{{ URL::asset('js/main.js') }}"></script>
</body>

</html>
