@extends('layouts.blank')
@section('content')
    <form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label" >Nombre</label>
            <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus />
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label" >Correo</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required />
        </div>

        <!-- Password -->
        <div class="form-group" >
            <label for="password" class="form-label" >Contraseña</label>

            <input id="password" class="form-control"
                     type="password"
                     name="password"
                     required autocomplete="new-password">
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
            <input id="password_confirmation" class="form-control"
                     type="password"
                     name="password_confirmation" required />
        </div>

        <div class="d-flex justify-content-center align-items-center mt-4 flex-column">
            <a class="text-sm" href="{{ route('login') }}">
                Iniciar sesión
            </a>

            <button class="btn btn-primary">
                Registrar
            </button>
        </div>

        @if(!$errors->isEmpty())
            <div class="alert alert-danger" role="alert">
                <ul class="list-unstyled mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ ucfirst( $err)  }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>
@endsection
