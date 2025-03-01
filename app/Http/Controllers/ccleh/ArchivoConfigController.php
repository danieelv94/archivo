<?php

namespace App\Http\Controllers\ccleh;

use App\Http\Controllers\Controller;
use App\Models\archivo\ArchivoCalendario;
use App\Models\archivo\ArchivoConfig;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArchivoConfigController extends Controller
{
    public function index()
    {
        $estado_captura = ArchivoConfig::first()->captura_abierta;
        $calendarios = $this->get_calendarios()->getData()->calendarios;
        return view('archivo.config', compact('calendarios', 'estado_captura'));
    }

    public function get_calendarios()
    {
        $calendarios = ArchivoCalendario::orderBy('anio_captura','asc')->orderBy('mes_captura','asc')->get();

        return $this->responseJsonSuccess( compact('calendarios') );

    }

    public function destroy(ArchivoCalendario $archivoCalendario)
    {
        $archivoCalendario->delete();
        return $this->responseJsonSuccess();
    }

    public function update(Request $request, ArchivoCalendario $archivoCalendario = null)
    {
        $validado = ArchivoCalendario::validar_calendario($request);

        if ( empty($archivoCalendario )) {
            $validado['persona_id'] = auth()->user()->persona->id;
            ArchivoCalendario::create($validado);
        } else {
            $archivoCalendario->update($validado);
        }

        $calendarios = $this->get_calendarios()->getData()->calendarios;

        return $this->responseJsonSuccess(compact('calendarios'));


    }

    public function cambiar_estado(ArchivoCalendario $archivoCalendario,$estado){
        if($estado == 'true'){
            $archivoCalendario->permite_captura = false;
        }else{
            $archivoCalendario->permite_captura = true;
        }
        $archivoCalendario->save();
        return $this->responseJsonSuccess();

    }

    public function create(Request $request,ArchivoCalendario $archivoCalendario = null)
    {
        $preconsulta = archivoCalendario::where('anio_captura',$request->anio_captura)->where('mes_captura',$request->mes_captura);
        if(!empty($archivoCalendario)){
            $calendarios = $preconsulta->where('id','!=',$archivoCalendario->id)->count();
        }else{
            $calendarios = $preconsulta->count();
        }

        if( $calendarios > 0 ){
            return $this->responseJsonError('La fecha que intentas registrar ya existe.');
        }
        return $this->update($request,$archivoCalendario);
    }

    public function update_estado_captura(Request $request)
    {
        $request->validate([
            'estado' => 'required'
        ]);

        $validado = $request->only(['estado']);
        $validado['estado'] = filter_var($validado['estado'],FILTER_VALIDATE_BOOLEAN );


        $config = ArchivoConfig::first();
        $config->update([
            'captura_abierta' => $validado['estado']
        ]);

        return $this->responseJsonSuccess();

    }

    public function duplicar_calendario(Request $request){
        if(!is_numeric($request->anioOld)){
            return $this->responseJsonError('Valor del año no válido.');
        }

        $meses  = ArchivoCalendario::where('anio_captura',$request->anioOld)->get();
        $existe = ArchivoCalendario::where('anio_captura',$request->anioNew)->count();
        $aniosDiferencia = $request->anioNew - $request->anioOld;

        if($existe > 0){
            return $this->responseJsonError('Ya existen datos para el año al que desea asignar los registros.');
        }

        foreach ($meses as $key => $value) {
            $request['anio_captura'] = $request->anioNew;
            $request['mes_captura'] = $value->mes_captura;
            $request['fecha_hora_limite_captura'] = date("Y-m-d H:i",strtotime($value->fecha_hora_limite_captura.'+'.$aniosDiferencia." years"));

            $validado = ArchivoCalendario::validar_calendario($request);
            $validado['persona_id'] = auth()->user()->persona->id;
            ArchivoCalendario::create($validado);

        }

        return $this->responseJsonSuccess();
    }

    public function listar_anios(){
        $calendarios = ArchivoCalendario::orderBy('anio_captura','ASC')->get()->groupBy('anio_captura');
        $anios = $calendarios->keys();

        return $this->responseJsonSuccess(compact('anios'));
    }

}
