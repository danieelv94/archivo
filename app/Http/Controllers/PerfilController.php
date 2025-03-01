<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Persona;
use Illuminate\Http\Request;

class PerfilController extends Controller
{

    public function index(Request $request)
    {
        $usuario = $request->user()->persona;

        return view('auth.perfil', compact('usuario'));
    }

    public function store_new_password(Request $request)
    {

        $request->validate([
            'current_password' => ['required', 'min:4', 'current_password:web'],
            'password' => ['required', 'min:4', 'confirmed'],
            'password_confirmation' => ['required', 'min:4'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $user->update(['password' => $request->input('password')] );

        return $this->responseJsonSuccess();
    }
}
