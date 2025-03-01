<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAreaRequest;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreasController extends Controller
{

    public function index(Request $request)
    {
        $areas = Area::with('titular')->get();
        $lista_tipo_area = config('ccleh.lista_tipo_areas');

        $usuarios = (new UserController())->get_personas();
        $usuarios = ($usuarios->getData())->personas;

        return view('areas.index', compact('areas', 'lista_tipo_area', 'usuarios'));
    }

    public function get_areas_activas()
    {
        $areas = Area::where('activo',true)->with('titular')->get();
//        $areas = Area::with('titular')->get();
        return $this->responseJsonSuccess(compact('areas'));
    }

    public function get_areas()
    {
//        $areas = Area::where('activo',true)->with('titular')->get();
        $areas = Area::with('titular')->get();
        return $this->responseJsonSuccess(compact('areas'));
    }

    public function store(CreateAreaRequest $request)
    {
        $validated = $request->safe()->only([
            'nombre',
            'siglas',
            'tipo_area',
            'titular_id',
            'codigo',
            'activo'
        ]);
//        $validated['codigo'] = $request->codigo;

        //CONVIERTE EL VALOR DEL CAMPO ACTIVO EN UNO QUE LA TABLA DE LA BD ACEPTE
        if( $validated['activo'] == 'true' ){
            $validated['activo'] = true;
        }else{
            $validated['activo'] = false;
        }
        $area = Area::create($validated);

        return $this->responseJsonSuccess(compact('area'));

    }

    public function update(CreateAreaRequest $request, Area $area)
    {
        $validated = $request->safe()->only([
            'nombre',
            'siglas',
            'tipo_area',
            'titular_id',
            'codigo',
            'activo'
        ]);
//        $validated['codigo'] = $request->codigo;

        if( $validated['activo'] == 'true' ){
            $validated['activo'] = true;
        }else{
            $validated['activo'] = false;
        }
        $area->update($validated);

        return $this->responseJsonSuccess(compact('area'));

    }

    public function delete(Area $area){
        if($area->tiene_hijos){
            return $this->responseJsonError('No es posible eliminar un area con departamentos asociados.','00x002');
        }
        $area->delete();
        return $this->responseJsonSuccess();
    }


}
