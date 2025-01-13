<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SEO Meta Tags -->
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="keywords" content="{{ $seo['keywords'] }}">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image" content="{{ url($seo['image']) }}">
    <meta property="og:url" content="{{ $seo['url'] }}">
    <meta property="og:type" content="website">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ url($seo['image']) }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $seo['url'] }}">
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
    <!-- Estilos principales -->
    <!-- Admin and Custom CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script async src="https://www.google.com/recaptcha/api.js"></script>
</head>

<body>
    <!-- Preloder -->
    @include('layouts.preloader')

    <!-- Header Area -->
    <header class="header-area">
        @include('layouts.socialnetworks')
        @include('layouts.navbar')
    </header>

    <!-- Search Overlay -->
    @include('layouts.search')

    <!-- Main Content -->
    <main>
        @yield('content-page')
    </main>

    <!-- Footer Area -->
    @include('layouts.footer')

    <!-- Footer bottom Area -->
    <div class="footer-bottom">
        <div class="container">
            <p>Copyright @ {{ date('Y') }} Torcoroma SA. All Rights Reserved. Powered By
                    <a href="https://www.facebook.com/kronnosco.la/" target="_blank">Kronnos</a></p>
        </div>
    </div>
    <!-- End Footer bottom Area -->

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('site.whatsapp')) }}" class="whatsapp-float"
        target="_blank" rel="noopener noreferrer" aria-label="Contactar por WhatsApp">
        <img src="{{ asset('images/icons-whatsapp.png') }}" alt="WhatsApp" width="50" height="50">
    </a>
    <!-- End WhatsApp Float Button -->

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
    <!-- Custom JS -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-sweet-alert />
    @stack('scripts')
    @stack('modals')
</body>

</html>
