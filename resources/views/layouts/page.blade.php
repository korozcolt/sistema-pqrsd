<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>{{ $metaTitle . ' - ' . ($info->title ?? '') ?? config('app.name', 'COOPERATIVA | TORCOROMA') }}</title>
    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Admin and Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <!-- Icofont CSS -->
    <link rel="stylesheet" href="{{ asset('css/boxicons.min.css') }}">
    <!-- Meanmenu CSS -->
    <link rel="stylesheet" href="{{ asset('css/meanmenu.min.css') }}">
    <!-- Magnific CSS -->
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">
    <!-- Odometer CSS -->
    <link rel="stylesheet" href="{{ asset('css/odometer.css') }}">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
    <!-- Stylesheet CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Stylesheet Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <!-- Scripts without good developers -->
    @if (env('APP_ENV') == 'production')
        <script defer type="text/javascript" src="https://wl.redbus.com/javascripts/widget.min.js"></script>
        <script src="https://wl.redbus.com/externaljavascript/loadwidget.js"></script>
        <script src="https://cdn.lr-in-prod.com/LogRocket.min.js" crossorigin="anonymous"></script>
        <script>
            window.LogRocket && window.LogRocket.init('f6nw7w/torocoromaweb');
        </script>
    @endif

    <script async src="https://www.google.com/recaptcha/api.js"></script>
</head>

<body>
    <!-- Preloder -->
    @include('layouts.preloader')
    <!-- End Preloder -->
    <!-- Heder Area -->
    <header class="header-area">
        @include('layouts.socialnetworks')
        @include('layouts.navbar')
    </header>
    <!-- End Heder Area -->

    <!-- Search Overlay -->
    @include('layouts.search')
    <!-- End Search Overlay -->
    @yield('content-page')
    <!-- Footer Area-->
    @include('layouts.footer')
    <!-- End Footer Area -->

    <!-- Footer bottom Area -->
    <div class="footer-bottom">
        <div class="container">
            <p>Copyright @ {{ date('Y') }} Torcoroma SA. All Rights Reserved. Powered By <a
                    href="https://www.facebook.com/kronnosco.la/" target="_blank">Kronnos</a></p>
        </div>
    </div>
    <!-- End Footer bottom Area -->

    <!-- Go Top -->
    <div class="go-top">
        <i class='bx bx-chevrons-up'></i>
    </div>
    <!-- End Go Top -->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <!-- Meanmenu JS -->
    <script src="{{ asset('js/meanmenu.min.js') }}"></script>
    <!-- Owl carousel JS -->
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <!-- Magnific JS -->
    <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Odometer JS -->
    <script src="{{ asset('js/odometer.min.js') }}"></script>
    <script src="{{ asset('js/jquery.appear.js') }}"></script>
    <!-- Form Validator JS -->
    <script src="{{ asset('js/form-validator.min.js') }}"></script>
    <!-- Contact JS -->
    <script src="{{ asset('js/contact-form-script.js') }}"></script>
    <!-- Ajaxchimp JS -->
    <script src="{{ asset('js/jquery.ajaxchimp.min.js') }}"></script>
    <!--Animate JS -->
    <script src="{{ asset('js/wow.min.js') }}"></script>
    <!-- App JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/custom.js') }}"></script>
    @include('sweetalert::alert')
    @stack('modals')
</body>

</html>
