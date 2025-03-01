<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartamentoRequest;
use App\Models\UbicacionesTopografica;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Area;

class DepartamentoController extends Controller
{
    //
    public function index(){

        $departamentos = Departamento::get();

        return view('departamentos.index',compact('departamentos'));
    }


    public function get_departamentos(Departamento $departamento = NULL){

        if ( !empty($departamento)){
            return $this->responseJsonSuccess(compact('departamento'));
        }

        $departamentos = Departamento::get();

        return $this->responseJsonSuccess(compact('departamentos'));
    }


    public function get_departamentos_area(Area $area){

        $departamentos = Departamento::where('activo',true)->where('area_id',$area->id)->get();
//        $departamentos = Departamento::where('area_id',$area->id)->get();
        return $this->responseJsonSuccess(compact('departamentos'));
    }


    public function get_padres(Departamento $departamento = NULL){

        if ( empty($departamento)){
            $departamentos = Departamento::get();
            return $this->responseJsonSuccess(compact('departamentos'));
        }
        $departamentos = Departamento::whereRaw('id != ? AND (padre_id != ? OR padre_id is null)',[$departamento->id,$departamento->id])->get();

        return $this->responseJsonSuccess(compact('departamentos'));
    }


    public function store(CreateDepartamentoRequest $request){

        $validated = Departamento::validar_datos($request);
        //CONVIERTE EL VALOR DEL CAMPO ACTIVO EN UNO QUE LA TABLA DE LA BD ACEPTE
        if( $validated['activo'] == 'true' ){
            $validated['activo'] = true;
        }else{
            $validated['activo'] = false;
        }
        /** @var Departamento $departamento */
        $departamento = Departamento::create( $validated );

        return $this->responseJsonSuccess();
    }


    public function update(CreateDepartamentoRequest $request, Departamento $departamento){
        if($request->padre_id === $departamento->id){
            $this->responseJsonError('Departamento superior invalido.');
        }

        $validated = Departamento::validar_datos($request);
        //CONVIERTE EL VALOR DEL CAMPO ACTIVO EN UNO QUE LA TABLA DE LA BD ACEPTE
        if( $validated['activo'] == 'true' ){
            $validated['activo'] = true;
        }else{
            $validated['activo'] = false;
        }
        $departamento->update($validated);

        $departamento->refresh();

        if ( $request->expectsJson() ) {
            return $this->responseJsonSuccess(compact('departamento'));
        } else {
            return $departamento;
        }
    }


    public function delete(Departamento $departamento){
        if($departamento->tiene_hijos){
            return $this->responseJsonError('No es posible eliminar un departamento padre.','00x001');
        }
        $departamento->delete();
        return $this->responseJsonSuccess();
    }

}
