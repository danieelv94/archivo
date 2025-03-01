<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

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


    <link href="{{ asset('vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/simple-datatables/style.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    @livewireStyles

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')


</head>
<body>
<!-- ======= Header ======= -->
@include('layouts.header')
<!-- ======= /Header ======= -->
<!-- ======= Sidebar ======= -->
@include('layouts.sidebar')
<!-- ======= /Sidebar ======= -->
<!-- ======= Main ======= -->
<main class="main" id="main" >
    {{ $slot }}
</main>
<!-- ======= /Main ======= -->
<!-- ======= Footer ======= -->
@include('layouts.footer')
<!-- ======= /Footer ======= -->
<a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
</a>

<script src="{{ asset('vendor/jquery/jquery-3.x.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/php-email-form/validate.js') }}"></script>

<script src="{{ asset('vendor/quill/quill.min.js') }}"></script>
<script src="{{ asset('vendor/simple-datatables/simple-datatables.js') }}"></script>
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

<script>
    window.addEventListener('load', function() {
        window.cclehConfirm = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-3 w-20',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            focusCancel: true,
        });
    });
</script>
@yield('scripts')



<!-- Template Main JS File -->
<script src="{{ asset('js/app.js') }}"></script>
@livewireScripts

</body>
</html>
