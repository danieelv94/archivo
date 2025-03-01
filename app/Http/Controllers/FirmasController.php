<?php

namespace App\Http\Controllers;

use App\Http\Requests\FirmasRequest;
use App\Models\Departamento;
use App\Models\Firmas;
use Illuminate\Http\Request;


class FirmasController extends Controller
{
    public function guardarFirmasInventarioDocumental(FirmasRequest $request){
        $firma = Firmas::guardarFirmas($request, config('enums.tipos_documento_firmas.inventario_documental'));

        return $this->responseJsonSuccess(compact('firma'));
    }
    public function obtenerFirmasInventarioDocumental(Departamento $departamento = null){

    }
    public function guardarFirmasCadido(FirmasRequest $request){
        $firma = Firmas::guardarFirmas($request, config('enums.tipos_documento_firmas.cadido'));

        return $this->responseJsonSuccess(compact('firma'));
    }
    public function obtenerFirmasCadido(Departamento $departamento = null){

    }
    public function guardarFirmasTransferenciaPrimaria(FirmasRequest $request, Departamento $departamento){
        $firma = Firmas::guardarFirmas($request, config('enums.tipos_documento_firmas.transferencia_primaria'), $departamento->id);

        return $this->responseJsonSuccess(compact('firma'));
    }
    public function obtenerFirmasTransferenciaPrimaria(Departamento $departamento = null){

    }


}
