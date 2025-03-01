@extends('layouts.blank')
@section('content')
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="mb-4 text-sm">
            Escribe el correo electrónico para enviarte un enlace de recuperación
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="row"
              onsubmit="document.getElementById('enviar').disabled=true;document.getElementById('email').readOnly=true;"
              id="form">
        @csrf
        <!-- Email Address -->
            <div class="mb-3 col-sm-12" >
                <label for="email" class="form-label">Correo electrónico</label>
                <input id="email" class="form-control"
                       type="email" name="email" value="{{  old('email')}} " required autofocus/>
            </div>

            <div class="mb-3 col-sm-12">

                <button class="btn btn-primary w-100" id="enviar">
                    Enviar
                </button>

            </div>

        </form>

        @if(!$errors->isEmpty())
            <div class="alert alert-danger" role="alert">
                <ul class="list-unstyled mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ ucfirst( $err)  }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>


@endsection
