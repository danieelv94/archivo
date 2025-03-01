<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <title>{{ (!empty($title) ? $title .' - ' : '') }} {{ config('general.meta_title') }}</title>
    <meta name="description" content="{{ config('general.meta_description') }}"/>
    <meta name="keywords" content="{{ config('general.meta_keywords') }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link href="{{ asset('img/favicon.png') }}"  type="image/png" rel="shortcut icon"  sizes="16x16"/>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>

<body>
<main>
    <div class="container">

        @yield('content')
    </div>
</main>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
</a>
@yield('scripts')
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/php-email-form/validate.js') }}"></script>
<!-- Template Main JS File -->
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
