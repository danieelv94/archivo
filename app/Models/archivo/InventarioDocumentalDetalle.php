<?php

namespace App\Models\archivo;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * @property mixed id
 * @property mixed orden
 * @property mixed ubicacion_fisica
 * @property mixed ubicacion_topografica
 * @property mixed no_expediente
 * @property mixed descripcion
 * @property Carbon fecha_inicio
 * @property Carbon fecha_final
 * @property mixed observaciones
 * @property mixed inventario_documentales_id
 * @property mixed persona_id
 * @property Carbon create_at
 * @property Carbon updated_at
 * @property Persona persona
 * @property InventarioDocumental inventarioDocumental
 * @property InventarioDocumental $inventario_documental
 */
class InventarioDocumentalDetalle extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'create_at', 'updated_at'];
    protected $casts = [
        'create_at' => 'date',
        'updated_at' => 'date',
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_final' => 'date:Y-m-d',
    ];
    protected $appends = [
        'editable'
    ];

//    protected static function booted()
//    {
//        static::creating(function ($item) {
//            // si el registro no tiene orden o el valor es cero, mándalo al último
//            // el valor de orden debe asignarse
//
//            if ( empty( $item->orden) || ( isset($item->orden) && $item->orden == 0 ) ) {
//                $orden = (InventarioDocumentalDetalle::count()  * 2);// nos saltamos dos números
//                $item->orden = $orden;
//            }
//
//        });
//    }

    public static function paginarDetalles(Request $request, $porPagina = 10){
        return self::where('inventario_documentales_id',$request->inventario_id)
            ->orderBy('orden')
            ->paginate($porPagina)->withQueryString();
    }

    public function obtener_consecutivo($noExpediente){
        $noExpediente = explode('/',$noExpediente);
        $noExpediente = explode('-',$noExpediente[1]);
        return $noExpediente[0];
    }

    public function getEditableAttribute(){
        $fechaInicioValida = ArchivoCalendario::fecha_valida_registro($this->inventario_documental->mes_captura, $this->inventario_documental->anio_captura, $this->fecha_inicio);
        return $fechaInicioValida === true ?? false;
    }

    public function es_valido_registro_excel($datos){
        $rules = [
            'ubicacion_fisica' => ['required', 'min:5'],
            'ubicacion_topografica' => ['required', 'min:5'],
            'no_expediente' => ['required', 'min:20','max:27'],
            'descripcion' => ['required', 'min:5'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_final' => ['nullable', 'date'],
            'orden' => ['required'],
            'observaciones' => ['nullable']
        ];
        $validator = Validator::make($datos, $rules, $messages = [
            'required' => 'El campo de :attribute es requerido.',
            'min' => 'La longitud del campo de :attribute es muy corta.',
            'max' => 'La longitud del campo de :attribute es muy larga.',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        return true;
    }

    public function es_expediente_consecutivo(InventarioDocumental $inventarioDocumental){
        $detalles = $inventarioDocumental->inventario_documental_detalles;
        foreach ($detalles as $key => $registro){
            $consecutivo = explode('/',$registro->no_expediente);
            $consecutivo = explode('-', $consecutivo[1]);
            if( $consecutivo[0] != ($key+1) ){
                return ($key+1);
            }
        }
        return true;
    }

    public function contar_expedientes(InventarioDocumental $inventarioDocumental){
        $expedientes = $inventarioDocumental->inventario_documental_detalles;
        return count($expedientes);
    }


    public function cero_izqueierda_expediente($noExpediente){
        $expedienteSeparado = explode('/', $noExpediente);
        $consecutivo = explode('-', $expedienteSeparado[1]);

        $format = "%02d";
        $nuevoCosecutivo = sprintf($format, $consecutivo[0]);

        $expedienteFormateado = "{$expedienteSeparado[0]}/{$nuevoCosecutivo}-{$consecutivo[1]}";

        return $expedienteFormateado;

    }


    public function validar_expediente($expediente,$clavesInventario){
        try {
        if( preg_match("/CCLEH-{1}[0-9]{2}\*[0-9]{1,2}[S,C]\.[0-9]{1,2}\/[0-9]{1,5}\|?\-[0-9]{4}|CCLEH-{1}[0-9]{1}\.?[0-9]{1,2}\*[0-9]{1,2}[S,C]\.[0-9]{1,2}\/[0-9]{1,5}\|?\-[0-9]{4}/i",$expediente) ){

            //LA CLAVE DE AREA/DEPARTAMENTO SE ENCUENTRA EN EL PRIMER ELEMENTO DEL ARREGLO $clave_departamento[0]
            $clave_departamento = explode('-',$expediente);
            $clave_departamento = explode('*',$clave_departamento[1]);

            //LA CLAVE DE SECCION SE ENCUENTRA EN EL PRIMER ELEMENTO DEL ARREGLO $clave_seccion[0]
            $clave_seccion = explode('*',$expediente);
            $clave_seccion = explode('.',$clave_seccion[1]);

            //LA CLAVE DE SERIE SE ENCUENTRA EN EL PRIMER ELEMENTO DEL ARREGLO $clave_serie[0]
            $clave_serie = explode('.',$expediente);
            //SI LA CLAVE DEL DEPARTAMENTO CUENTA CON UN PUNTO(.) VARIA EL ELEMENTO UTILIZADO DEL ARREGLO
            $clave_serie = strpos($clave_departamento[0],'.') ? explode('/',$clave_serie[2]) : explode('/',$clave_serie[1]);

            //EL AÑO DE CAPTURA SE ENCUENTRA EN EL ULTIMO ELEMENTO DEL ARREGLO $anio_captura[2]
            $anio_captura = explode('-',$expediente);

            //EL NUMERO CONSECUTIVO SE ENCUENTRA EN EL PRIMER ELEMENTO DEL ARREGLO $consecutivo[0]
            $consecutivo = explode('/',$expediente);
            $consecutivo = explode('-', $consecutivo[1]);
            if( $consecutivo[0] == 0 ){
                return false;
            }


            if(  $clave_departamento[0] == $clavesInventario['clave_departamento'] && $clave_seccion[0] == $clavesInventario['clave_seccion'] && $clave_serie[0] == $clavesInventario['clave_serie'] && $anio_captura[2] == $clavesInventario['anio_captura'] ){
                return true;
            }
        }
        }catch (Exception $e){
            return false;
        }
        return false;
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function inventario_documental()
    {
        return $this->belongsTo(InventarioDocumental::class, 'inventario_documentales_id', 'id');
    }
}
