@extends('layouts.blank')

@section('styles')
    <style>
        .login-logo {
            line-height: 1;
        }
        @media (min-width: 1200px) {
            .login-logo {
                width: 450px;
            }
        }
        .login-logo img {
            max-height: 70px;
            margin-right: 0;
        }
    </style>
@endsection

@section('scripts')
@endsection

@section('content')
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                    <div class="d-flex justify-content-center py-4">
                        <a href="{{ route('principal') }}" class="logo login-logo d-flex align-items-center w-auto">
                            <img src="{{ asset('img/logo-escudo-armas-120.png') }}" alt="Logo">
                        </a>
                    </div><!-- End Logo -->

                    <div class="card mb-3">

                        <div class="card-body">

                            <div class="pt-4 pb-2">
                                <h5 class="card-title text-center pb-0 fs-4">Iniciar sesión</h5>
                                <p class="text-center small">Ingresa tu usuario y contraseña</p>
                            </div>

                            <form class="row g-3 needs-validation" novalidate method="post" action="{{ route('login') }}">
                                @csrf()

                                <div class="col-12">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                        <input type="email" name="email"
                                               class="form-control" id="email"
                                               value="{{ old('email') }}"
                                               required>
                                        <div class="invalid-feedback">Por favor ingresa tu correo electrónico</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" name="password" class="form-control" id="password" required>
                                    <div class="invalid-feedback">Ingresa la contraseña</div>
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

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="remember">
                                        <label class="form-check-label" for="remember">Recordar</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-primary w-100" type="submit">Iniciar sesión</button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>
@endsection
