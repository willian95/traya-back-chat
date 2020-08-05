<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="IngeniaVenezuela">

        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <title>PallMat - Sistema de logistica de traslados</title>
        <link rel="stylesheet" href="{{ URL::asset('../plugins/switchery/switchery.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('../plugins/jquery-circliful/css/jquery.circliful.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('assets/css/responsiveTable.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('assets/css/icons.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('assets/css/alertify/alertify.css') }}">

        <script src="{{ URL::asset('assets/js/modernizr.min.js') }}"></script>
<style type="text/css">
        th{
        text-align: center !important;
    }
      td{
        text-align: center !important;
    }
    .modal-lg{
        max-width: 90% !important;
    }
    textarea {
     resize: none;
    }
    .alertify-notifier .ajs-message.ajs-success{
    color: #fff !important;
    font-style: bold;
    text-shadow: -1px -1px 0 rgba(0, 0, 0, 0,5);
}
  .alertify-notifier .ajs-message.ajs-error{
    color: #fff !important;
    font-style: bold;
    text-shadow: -1px -1px 0 rgba(0, 0, 0, 0,5);
}

</style>
    </head>

    @stack('styles')

    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">
          @include('layouts.pallmat.partials.header')
          @include('layouts.pallmat.partials.LeftSidebar')

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <!-- //GRAFICAS -->
                @yield('content')
                @include('layouts.pallmat.partials.Footer')
            </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->
        </div>
        <!-- END wrapper -->



        <script>
            var resizefunc = [];
        </script>

        <!-- Plugins  -->

        <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/popper.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/detect.js') }}"></script>
        <script src="{{ URL::asset('assets/js/fastclick.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.slimscroll.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.blockUI.js') }}"></script>
        <script src="{{ URL::asset('assets/js/waves.js') }}"></script>
        <script src="{{ URL::asset('assets/js/wow.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.nicescroll.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.scrollTo.min.js') }}"></script>
        <script src="{{ URL::asset('../plugins/switchery/switchery.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/alertify.min.js') }}"></script>


        <!-- Counter Up  -->
        <script src="{{ URL::asset('../plugins/waypoints/lib/jquery.waypoints.min.js') }}"></script>
        <script src="{{ URL::asset('../plugins/counterup/jquery.counterup.min.js') }}"></script>


        <!-- circliful Chart -->
        <script src="{{ URL::asset('../plugins/counterup/jquery.counterup.min.js') }}"></script>
        <script src="{{ URL::asset('../plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
        <!-- skycons -->
        <script src="{{ URL::asset('../plugins/skyicons/skycons.min.js') }}"></script>

        <!-- Page js  -->
        <script src="{{ URL::asset('assets/pages/jquery.dashboard.js') }}"></script>

        <!-- Custom main Js -->
        <script src="{{ URL::asset('assets/js/jquery.core.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.app.js') }}"></script>
        <script src="{{ URL::asset('js/app.js') }}"></script>
        <script type="text/javascript">
        $(document).ready(function () {
                   $(".alert").fadeTo(1500, 300).slideUp(200, function () {
                       $(".alert").slideUp(300);
                   });

               });
        </script>
        <script type="text/javascript">

             alertify.set('notifier','position', 'top-right');

            // BEGIN SVG WEATHER ICON
            if (typeof Skycons !== 'undefined'){
                var icons = new Skycons(
                        {"color": "#3bafda"},
                        {"resizeClear": true}
                        ),
                        list  = [
                            "clear-day", "clear-night", "partly-cloudy-day",
                            "partly-cloudy-night", "cloudy", "rain", "sleet", "snow", "wind",
                            "fog"
                        ],
                        i;

                for(i = list.length; i--; )
                    icons.set(list[i], list[i]);
                icons.play();
            };

        </script>

        @stack('script')

    </body>
</html>
