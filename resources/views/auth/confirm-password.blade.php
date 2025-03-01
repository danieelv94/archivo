@extends('layouts.blank')

@section('content')
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="form-group">
                <label for="password" class="form-label" >Contraseña: </label>
                <input id="password"
                       class="form-control"
                        type="password"
                        name="password"
                        required
                       autocomplete="current-password" />
            </div>
            <button>
            Confirmar
            </button>
        </form>

@endsection
