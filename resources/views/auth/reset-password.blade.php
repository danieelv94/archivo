@extends('layouts.blank')

@section('content')

    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">

        <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label"> Correo electrónico  </label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                       class="form-control" required autofocus>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" class="form-control" type="password" name="password" required />
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label" >Confirmar contraseña</label>
                <input id="password_confirmation" class="form-control"
                         type="password"
                         name="password_confirmation"
                       required />
            </div>

            <div class="flex items-center justify-end mb-3">
                <button class="btn btn-primary">
                    Guardar contraseña
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
