@extends('layouts.blank')

@section('content')

    <main>
        <div class="container">

            <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
                <h1>404</h1>
                @if( ! empty($message))
                    <h2>{{ $message }}</h2>
                @endif
                <h2>Página no encontrada</h2>
                <a class="btn btn-primary" href="/">Regresar al inicio</a>
                <img src="{{ asset('img/not-found.svg') }}"
                     class="img-fluid py-5"
                     alt="No encontrado">
            </section>

        </div>
    </main><!-- End #main -->

@endsection
