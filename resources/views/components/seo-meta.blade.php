{{-- resources/views/components/seo-meta.blade.php --}}
<title>{{ $title }}</title>

{{-- Meta Tags Básicos --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph Tags --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $company['name'] }}">
<meta property="og:locale" content="es_CO">

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="{{ $twitterCardType }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

{{-- Lenguaje y dirección --}}
@foreach($langAttributes as $attribute => $value)
    <meta http-equiv="content-{{ $attribute }}" content="{{ $value }}">
@endforeach

{{-- Meta Tags Adicionales --}}
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="author" content="{{ $company['name'] }}">
<meta name="copyright" content="{{ $company['name'] }} - {{ $company['nit'] }}">
<meta name="generator" content="Laravel {{ app()->version() }}">
<meta name="format-detection" content="telephone=no">

{{-- Seguridad --}}
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="referrer" content="no-referrer-when-downgrade">

{{-- PWA Meta Tags --}}
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="application-name" content="{{ $company['name'] }}">
<meta name="apple-mobile-web-app-title" content="{{ $company['name'] }}">
<meta name="theme-color" content="#ffffff">
<meta name="msapplication-TileColor" content="#ffffff">

{{-- JSON-LD Schema --}}
<script type="application/ld+json">
    {!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Preconexiones para optimización --}}
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
