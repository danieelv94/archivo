<?php

namespace App\Models\archivo;

use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
/**
 * @property mixed id
 * @property mixed anio_captura
 * @property mixed mes_captura
 * @property mixed fecha_hora_limite_captura
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property false|mixed $permite_captura
 */
class ArchivoCalendario extends Model
{
    /*
    *SE USA Compoships PARA CREAR LA RELACION BASADA EN 2 COLUMNAS CON EL INVETARIO CORRSPONDIENTE A LA SERIE
    */
    use \Awobaz\Compoships\Compoships;

    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $hidden = [ 'created_at', 'updated_at', 'deleted_at', 'persona_id'];
    protected $casts = [
        'created_at'                => 'datetime',
        'updated_at'                => 'datetime',
        'deleted_at'                => 'datetime',
        'fecha_hora_limite_captura' => 'datetime:Y-m-d H:i:s',
        'anio_captura'              => 'integer',
        'mes_captura'               => 'integer',
        'permite_captura'           => 'boolean',
    ];

    public function meses_captura($anio){
        $mesActual = date('n');
        $anioActual = date('Y');
        if( $anio == $anioActual ){
            $mesesHabilitados = ArchivoCalendario::where('anio_captura',$anio)->where('mes_captura','<=',$mesActual)->get()->pluck('mes_captura');
        }else{
            $mesesHabilitados = ArchivoCalendario::where('anio_captura',$anio)->get()->pluck('mes_captura');
        }
        $nombre_meses = config('app.meses');

        // $meses = array_map(function($m) use($nombre_meses) {
        //     return $nombre_meses[$m];
        // }, $mesesHabilitados->toArray());
        // array_unshift( $meses, "");

        foreach ($mesesHabilitados as $i => $mes) {
            $meses[$i]['numero'] = $mes;
            $meses[$i]['nombre'] = $nombre_meses[$mes];
        }

        return $meses;
    }

    public function user()
    {
        return $this->belongsTo(Persona::class );
    }

    public function validar_calendario($request){
        $request->validate([
            'anio_captura' => ['required','numeric'],
            'mes_captura' => ['required','numeric', Rule::in(range(1,12))],
            'fecha_hora_limite_captura' => ['required','date'],
        ]);

        $validado = $request->only([
            'anio_captura',
            'mes_captura',
            'fecha_hora_limite_captura',
        ]);

        $validado['fecha_hora_limite_captura'] .= ':00';

        return $validado;
    }
    /**
     * $mesCaptura mes del periodo de captura seleccionado
     * $anioCaptura año del periodo de captura seleccionado
     * $fechaInicioAnterior valor de la fecha de inicio ingresada si es un registro nuevo y fecha de inicio registrada en el sistema si es una edición de expediente
     * $fechaInicioNueva valor de la nueva fecha de inicio colocada en el expediente en caso de ser un registro editado
     * $editable en caso de ser una edición de expediente retornara false en caso de no permitir edición de expediente en lugar de retornar el error
     * */
    public function fecha_valida_registro($mesCaptura, $anioCaptura, $fechaInicioAnterior, $fechaInicioNueva = null, $editable = null){
        $mesInicio = date('m',strtotime($fechaInicioAnterior));
        $anioInicio = date('Y',strtotime($fechaInicioAnterior));

        //VALIDAR FORMATO DE PERIODO DE CAPTURA
        if( !is_numeric($mesCaptura) || !is_numeric($anioCaptura) || $mesCaptura > 13  || $mesCaptura < 0  || strlen($anioCaptura) != 4 ){
            return "Formato de fecha de captura incorrecto.";
        }

        $periodo = ArchivoCalendario::where('mes_captura',$mesInicio)->where('anio_captura',$anioInicio)->get();
        if( count($periodo) != 1 ){
            return "Periodo de captura seleccionado inexistente.";
        }

        if($mesInicio > $mesCaptura || $anioInicio != $anioCaptura){
            return "La fecha de inicio debe ser igual o menor al periodo seleccionado.";
        }

        $fechaActual = strtotime(date('Y-m-d H:i:s'));
        if( $fechaActual > strtotime($periodo[0]->fecha_hora_limite_captura) && $periodo[0]->permite_captura != 1 ){
            //SI EL CAMPO NO ES EDITABLE RETORNARA FALSO PARA VALIDAR UNICAMENTE LA CAPTURA DE DATOS PERMITIDOS
            if($editable === true ){
                return false;
            }
            return "Captura en el mes ".config('app.meses')[intval($mesInicio)]." del año {$anioInicio} no autorizada.";
        }

        if( $fechaInicioNueva ){
            $mesInicioNuevo = date('m',strtotime($fechaInicioNueva));
            $anioInicioNuevo = date('Y',strtotime($fechaInicioNueva));
            $periodoNuevo = ArchivoCalendario::where('mes_captura',$mesInicioNuevo)->where('anio_captura',$anioInicioNuevo)->get();
            if( count($periodo) != 1 ){
                return "Nuevo periodo de captura seleccionado inexistente.";
            }
            if($mesInicioNuevo > $mesCaptura || $anioInicioNuevo != $anioCaptura){
                return "La fecha de inicio debe ser igual o menor al periodo seleccionado.";
            }
            if( $fechaActual > strtotime($periodoNuevo[0]->fecha_hora_limite_captura) && $periodoNuevo[0]->permite_captura != 1 ){
                //SI EL CAMPO NO ES EDITABLE RETORNARA FALSO PARA VALIDAR UNICAMENTE LA CAPTURA DE DATOS PERMITIDOS
                if($editable === true && $fechaInicioAnterior === $fechaInicioNueva ){
                    return false;
                }
                return "Captura en el mes ".config('app.meses')[intval($mesInicioNuevo)]." del año {$anioInicioNuevo} no autorizada.";
            }
        }

        return true;

    }

    public static function obtener_anios_hasta_actual(){
        $anioActual = now('Y');
        $calendarios = self::where('anio_captura','<=',$anioActual)->orderBy('anio_captura','ASC')->get()->groupBy('anio_captura');
        return $calendarios->keys();
    }

}
