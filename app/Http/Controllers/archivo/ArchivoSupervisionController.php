<?php

namespace App\Http\Controllers\archivo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\archivo\ArchivoCalendario;
use App\Models\archivo\ArchivoSerieAutorizada;
use App\Models\archivo\InventarioDocumental;
use App\Models\archivo\ArchivoSupervision;
use App\Models\Area;

class ArchivoSupervisionController extends Controller
{

    public function index(){
        // $areas = Area::get();
        $anioActual = now('Y');
        $calendarios = ArchivoCalendario::where('anio_captura','<=',$anioActual)->orderBy('anio_captura','ASC')->get()->groupBy('anio_captura');
        $anios_reporte = $calendarios->keys();
        // $contador = 0;
        // foreach ($areas as $area) {
        //     $series = ArchivoSerieAutorizada::join('inventario_documentales', 'clave_seccion', '=', 'archivo_secciones.clave')->where('area_id',$area->id)->get();
        //     $area->series = $series;
        // }
        return view('archivo.supervision', compact('calendarios', 'anios_reporte'));
    }


    public function get_meses_captura( $anio ){
        $fechaFormateada = now();
        $mesActual = date('n');
        $anioActual = date('Y');

        if( $anio == $anioActual ){
            $mesesHabilitados = ArchivoCalendario::where('anio_captura',$anio)->where('fecha_hora_limite_captura','<',$fechaFormateada )->orWhere('anio_captura',$anioActual)->where('mes_captura',$mesActual )->get()->pluck('mes_captura');
        }else{
            $mesesHabilitados = ArchivoCalendario::where('anio_captura',$anio)->where('fecha_hora_limite_captura','<',$fechaFormateada )->get()->pluck('mes_captura');
        }

        // $mesesHabilitados = ArchivoCalendario::where('anio_captura',$anio)->where('fecha_hora_limite_captura','<',$fechaFormateada )->get()->pluck('mes_captura');

        // $mesesHabilitados = ArchivoCalendario::where('anio_captura',$anio)->get()->pluck('mes_captura');
        $nombre_meses = config('app.meses');

        // $meses = array_map(function($m) use($nombre_meses) {
        //     return $nombre_meses[$m];
        // }, $mesesHabilitados->toArray());
        // array_unshift( $meses, "");
        $meses = [];
        foreach ($mesesHabilitados as $i => $mes) {
            $meses[$i]['numero'] = $mes;
            $meses[$i]['nombre'] = $nombre_meses[$mes];
            $fecha_limite = ArchivoCalendario::select('fecha_hora_limite_captura')->where('anio_captura',$anio)->where('mes_captura',$mes)->first();
            $meses[$i]['fecha_cierre'] = date( 'd/m/Y g:i:s a',strtotime($fecha_limite->fecha_hora_limite_captura) );
        }

        return $this->responseJsonSuccess( compact('meses') );
    }


    public function areas_semaforo($mes, $anio){

        if( ArchivoSupervision::where('anio_captura',$anio)->where('mes_captura',$mes)->count() > 0 ){
            $areas = ArchivoSupervision::where('anio_captura',$anio)->where('mes_captura',$mes)->get();
            foreach ($areas as $i => $area) {
                $area->nombre = $area->area->nombre;
                $area->semaforo_texto = ArchivoSupervision::semaforo_texto($area->semaforo);
            }
        }else{
            $areas = Area::where('activo',true)->get();
            foreach ($areas as $area) {
                $seriesAutorizadas = ArchivoSerieAutorizada::join('departamentos','archivo_serie_autorizadas.departamento_id','=','departamentos.id')->where('archivo_serie_autorizadas.area_id',$area->id)->where('departamentos.activo',true)->get();
//                $seriesAutorizadas = ArchivoSerieAutorizada::where('area_id',$area->id)->get();
                $totalSeriesAutorizadas = $seriesAutorizadas->count();
                $seriesRegistradas = 0;
                foreach ($seriesAutorizadas as $serie) {
                    $inventario = InventarioDocumental::where('clave_seccion',$serie->seccion->clave)->where('clave_serie',$serie->serie->clave)->where('mes_captura',$mes)->where('anio_captura',$anio)->where('departamento_id',$serie->departamento_id)->where('area_id',$area->id)->first();
                    if(!empty($inventario)){
                        $seriesRegistradas++;
                    }
                }
                if( $totalSeriesAutorizadas == $seriesRegistradas ){
                    $area->semaforo = ArchivoSupervision::COMPLETO;
                }else if( $seriesRegistradas == 0 ){
                    $area->semaforo = ArchivoSupervision::NO_CAPTURA;
                }else if( $totalSeriesAutorizadas > $seriesRegistradas ){
                    $area->semaforo = ArchivoSupervision::INCOMPLETO;
                }
                $area->semaforo_texto = ArchivoSupervision::semaforo_texto($area->semaforo);
                $area->mes = $mes;
                $area->anio = $anio;
                $area->seriesRegistradas = $seriesRegistradas;
            }
        }

        return $this->responseJsonSuccess(compact('areas'));
    }

}
