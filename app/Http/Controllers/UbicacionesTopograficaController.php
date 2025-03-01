<?php

namespace App\Http\Controllers;

use App\Http\Requests\archivo\UbicacionesTopograficaRequest;
use App\Models\Departamento;
use App\Models\UbicacionesTopografica;
use Illuminate\Http\Request;

class UbicacionesTopograficaController extends Controller
{
    public function index(){

    }

    public function get_ubicaciones_topograficas(Departamento $departamento){
        $ubicaciones_topograficas = UbicacionesTopografica::where('departamentos_id',$departamento->id)->get();
        foreach($ubicaciones_topograficas as $ubicacion) {
            $ubicacion['ubicacionCompleta'] = $ubicacion->ubicacion_completa;
        }
        return $this->responseJsonSuccess(compact('ubicaciones_topograficas'));
    }

    public function delete_ubicacion(UbicacionesTopografica $ubicacionesTopografica){
        $ubicacionesTopografica->delete();
        return $this->responseJsonSuccess();

    }

    public function update_ubicacion(UbicacionesTopograficaRequest $request){
        if ( $request->has('id')  && (!empty($request->input('id'))) ) {
            $ubicacion = UbicacionesTopografica::find($request->id);
            $ubicacion->update($request->validated());
        }else{
            UbicacionesTopografica::create($request->validated());
        }
        return $this->responseJsonSuccess();
    }

    public function permite_acceso(UbicacionesTopografica $ubicacionesTopografica){
        if( $ubicacionesTopografica->departamento_id != auth()->user()->persona->departamento_id ){
            return false;
        }
        return true;
    }
}
