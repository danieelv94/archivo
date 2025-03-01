<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePersonaRequest;
use App\Models\Departamento;
use App\Models\Persona;
use App\Models\User;
use Faker\Provider\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(Request $request)
    {

        $personas = Persona::with('user')->get();
        foreach ($personas as $person) {
            $person->rol = User::getRol($person);
            $departamento = Departamento::where('id',$person->departamento_id)->first();
            $clave = $departamento->clave ?? '';
            $nombre = $departamento->nombre ?? '';
            $person->departamento = $clave.' - '.$nombre;
        }
        $roles = User::getRoles();
        return view('personas.index', compact('personas', 'roles'));

    }

    public function store(UpdatePersonaRequest $request)
    {

        $validated = User::validar_datos($request);

        $validated['unidad_presupuestal'] = config('app.name_organization');

        /** @var Persona $persona */
        $persona = Persona::create( $validated );

        $user = $persona->create_user( $request->password );
        User::setRol($user,$request->rol);

        return $this->responseJsonSuccess();

    }

    public function update(UpdatePersonaRequest $request, Persona $persona){

        $validated = User::validar_datos($request);

        if ( ! $persona->user ) {
            return $this->responseError('La persona no tiene un usuario', '0', null, '402', $request);
        }
        $user = $persona->user;
        User::setRol($user,$request->rol);

        if ( $request->has('password') ) {
            $user->update(['password' => $request->input('password')]);
        }

        $persona->update($validated);
        $persona->refresh();

        if ( $request->expectsJson() ) {
            return $this->responseJsonSuccess(compact('persona'));
        } else {
            return $persona;
        }
    }


    public function delete(Persona $persona){
        if( Persona::count() <= 1 ){
            return $this->responseJsonError('No es posible eliminar todos los usuarios.','01x003');
        }
        if($persona->es_titular){
            return $this->responseJsonError('No es posible eliminar un usuario asignado como titular de un departamento.','01x001');
        }
        if( $persona->id == auth()->user()->persona->id ){
            return $this->responseJsonError('No es posible eliminar su propio usuario.','01x002');
        }

        $persona->user->delete();
        $persona->delete();

        return $this->responseJsonSuccess();
    }


    public function get_personas(Persona $persona = NULL){
        if ( !empty($persona)){
            $persona->rol = $persona->user->getRoleNames();
            return $this->responseJsonSuccess(compact('persona'));
        }

        $personas = Persona::with('user')->get();
        foreach ($personas as $person) {
            $person->rol = User::getRol($person);
        }
        return $this->responseJsonSuccess(compact('personas'));
    }

}
