<?php

namespace App\Http\Controllers\ccleh;

use App\Http\Controllers\Controller;
use App\Models\archivo\ArchivoCalendario;
use App\Models\archivo\ArchivoSeccion;
use App\Models\Firmas;
use App\Models\UbicacionesTopografica;
use Illuminate\Http\Request;

class ArchivoTramiteController extends Controller
{

    public function view_inventario()
    {
        $anios_captura = ArchivoCalendario::obtener_anios_hasta_actual();
        $firmas = Firmas::obtenerFirmasInventarioDocumental(auth()->user()->persona->departamento_id);
        return view('archivo.inventario', compact('anios_captura','firmas'));
    }

    public function get_meses_captura( $anio )
    {
        $meses = ArchivoCalendario::meses_captura($anio);

        return $this->responseJsonSuccess( compact('meses') );
    }

    public function get_fecha_limite( $anio ,$mes ){

        $archivoCalendario = ArchivoCalendario::where('anio_captura',$anio)->where('mes_captura',$mes)->first();
        $fecha_limite = strtotime($archivoCalendario->fecha_hora_limite_captura);
        $cierre['fecha_limite'] = date('d/m/Y g:i a', $fecha_limite);
        $cierre['cerrado'] = false;
        $fecha_actual = strtotime(date('Y-m-d H:i:s'));
        if( $fecha_actual > $fecha_limite && $archivoCalendario->permite_captura == false ){
            $cierre['cerrado'] = true;
        }else if( $fecha_actual > $fecha_limite && $archivoCalendario->permite_captura == true ){
            $cierre['cerrado'] = 'extemporaneo';
        }

        return $this->responseJsonSuccess( compact('cierre') );

    }

}
