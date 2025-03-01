<?php

namespace App\Models\archivo;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Departamento;
use App\Models\Persona;
use App\Models\archivo\ArchivoSeccion;
use App\Models\archivo\ArchivoSerie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\archivo\ArchivoCalendario;

/**
 * @property mixed id
 * @property mixed unidad_presupuestal
 * @property mixed area_id
 * @property mixed departamento
 * @property mixed persona_responsable_id
 * @property mixed anio_captura
 * @property mixed mes_captura
 * @property mixed clave_seccion
 * @property mixed clave_serie
 * @property mixed persona_revisa_id
 * @property mixed persona_autoriza_id
 * @property mixed created_at
 * @property mixed updated_at
 * @property Persona persona_responsable
 * @property Persona persona_autoriza
 * @property Persona persona_revisa
 * @property Area area
 * @property ArchivoSeccion seccion
 * @property ArchivoSerie serie*
 * @property Collection<InventarioDocumentalDetalle> inventario_documental_detalles
 * @property false|mixed|string $fecha_cierre_captura
 * @method Builder buscar( $area, $anio_captura, $mes_captura, $clave_seccion, $clave_serie)
 */
class InventarioDocumental extends Model
{
    /*
    *SE USA Compoships PARA CREAR LA RELACION BASADA EN 2 COLUMNAS CON LA SERIE CORRESPONDIENTE DEL INVENTARIO
    */
    use \Awobaz\Compoships\Compoships;

    protected $table = 'inventario_documentales';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at'];
    protected $casts = [
        'anio_captura'  => 'integer',
        'mes_captura'  => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'fecha_cierre_captura' => 'datetime:d/m/Y g:i a',
    ];

    const CERRADO_SISTEMA = 'Cerrado por el sistema.';
    const CERRADO_USUARIO = 'Cerrado por el usuario.';
    const CAPTURA = 'En captura.';
    const CAPTURA_EXCEDIDA_ABIERTA = 'Fecha de captura excedida y sin cerrar.';
    const CAPTURA_EXTEMPORANEA = 'En captura extemporánea.';

    public function persona_responsable()
    {
        return $this->belongsTo(Persona::class, 'persona_responsable_id', 'id');
    }

    public function persona_autoriza()
    {
        return $this->belongsTo(Persona::class, 'persona_autoriza_id', 'id');
    }

    public function persona_revisa()
    {
        return $this->belongsTo(Persona::class, 'persona_revisa_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
    }

    public function seccion()
    {
        return $this->belongsTo(ArchivoSeccion::class, 'clave_seccion', 'clave');
    }
    public function serie()
    {
        /*
        *RELACIÓN CON SERIES A PARTIR DE 2 CAMPOS
        *PRIMERO SE ESPECIFICAN LAS LLAVES FORANEAS Y POSTERIORMENTE LAS LLAVES LOCALES DE LA TABLA A RELACIONAR
        */
        return $this->belongsTo(ArchivoSerie::class, ['clave_serie', 'clave_seccion'],['clave','clave_seccion']);
    }

    public function mes_captura()
    {
        /*
        *RELACIÓN CON ArchivoCalendario A PARTIR DE 2 CAMPOS
        *PRIMERO SE ESPECIFICAN LAS LLAVES FORANEAS Y POSTERIORMENTE LAS LLAVES LOCALES DE LA TABLA A RELACIONAR
        */
        return $this->belongsTo(ArchivoCalendario::class, ['anio_captura', 'mes_captura'],['anio_captura','mes_captura']);
    }

    public function inventario_documental_detalles()
    {
        return $this->hasMany(InventarioDocumentalDetalle::class,'inventario_documentales_id', 'id')->orderBy('orden');
    }

    public function inventario_documental_detalles_paginado($num_registros)
    {
        return $this->hasMany(InventarioDocumentalDetalle::class,'inventario_documentales_id', 'id')->orderBy('orden')->paginate($num_registros);
    }

    /**
     * @param Builder $query
     * @param $anio_captura
     * @param $mes_captura
     * @param $clave_seccion
     * @param $clave_serie
     * @return Builder
     */
    public function scopeBuscar($query, $anio_captura, $mes_captura, $clave_seccion, $clave_serie)
    {
        $area_id = auth()->user()->persona->area_id;
        $departamento_id = auth()->user()->persona->departamento_id;

        return $query
            ->where('area_id', $area_id)
            ->where('departamento_id', $departamento_id)
            ->where('anio_captura', $anio_captura)
            ->where('mes_captura', $mes_captura)
            ->where('clave_serie', $clave_serie)
            ->where('clave_seccion', $clave_seccion);
    }

//  Verifica si una variable dada esta vacia o conformada por espacios en blanco
    public function esta_vacia($variable){
        if( empty($variable) || ctype_space($variable) ){
            return true;
        }
        return false;
    }

//    RETORNA LAS CLAVES QUE CONTIENE EL INVENTARIO (CLAVE_SECCION,CLAVE_SERIE,CODIGO_AREA,CLAVE_DEPARTAMENTO)
    public function claves_inventario(InventarioDocumental $inventarioDocumental){
        $clavesInventario = [
            'clave_seccion' => $inventarioDocumental->clave_seccion,
            'clave_serie' => $inventarioDocumental->clave_serie,
            'codigo_area' => Area::find($inventarioDocumental->area_id)->codigo,
            'clave_departamento' => Departamento::find($inventarioDocumental->departamento_id)->clave,
            'anio_captura' => $inventarioDocumental->anio_captura
        ];
        return $clavesInventario;
    }

    /*
     * $detalle : Datos a registrar o editar del expediente
     * $registro : datos del expediente original en caso de que se trate de una edición
     * $actualizarRepetido : Define si actualizara o de lo contrario lanzara advertencia en caso de encontrar un no. expediente repetido
     * */
    public function agregarDetalle( $detalle,InventarioDocumentalDetalle $registro = null, $actualizarRepetido = true)
    {
        // buscar si el no expediente existe en el inventario, no se permite duplicar, unicamente modificar el expediente
        $existe_no_expediente = false;
        if ( empty($detalle['id']) ) {
            // es un registro nuevo, verifica la existencia del no. expediente en el mismo inventario
            $existe_no_expediente = $this->inventario_documental_detalles()->where('no_expediente', $detalle['no_expediente'] )->first();
//            $existe_no_expediente = $registro->count();
        } else {
            //es una actualización, verifica que el número de expediente no exista en otro expediente del mismo inventario
            $existe_no_expediente = $this->inventario_documental_detalles()->where('id', '!=', $detalle['id'] )->where('no_expediente', $detalle['no_expediente'] )->first();
//            $existe_no_expediente = $registro->count();
        }
//
        if( $detalle['fecha_final'] != null && $detalle['fecha_final'] < $detalle['fecha_inicio'] ){
            return 'La Fecha final debe ser mayor a la Fecha de inicio.';
        }

//      Si el numero de expedinte ya existe en el inventario y no se permite actualizar expediente repetido
        if( $existe_no_expediente && $actualizarRepetido === false ){
            return 'El número de expediente debe ser único, '.$detalle['no_expediente'].' se repite o ya se encuentra registrado.';
        }
//      Si el numero de expedinte ya existe en el inventario y si es permitido actualizar expediente repetido
        if ( $existe_no_expediente && $actualizarRepetido ){
            $existe_no_expediente->update(['ubicacion_topografica' => $detalle['ubicacion_topografica'], 'descripcion' => $detalle['descripcion'], 'observaciones' => $detalle['observaciones']]);
//            return true;
            return 'actualizado';
//            return 'El número de expediente debe ser único, '.$detalle['no_expediente'].' se repite o ya se encuentra registrado.';
//            throw new \Exception('El número de expediente debe ser único, '.$detalle['no_expediente'].' se repite o ya se encuentra registrado.');
        }
//      Si el número de expediente no existe y se trata de un registro nuevo
        if ( ! $existe_no_expediente && empty($detalle['id']) ){
            //nuevo registro
            InventarioDocumentalDetalle::create($detalle);
//            $this->inventario_documental_detalles()->save( $detalle );
        }
//      Si se trata de una edición directa de expedientes en tiempo
        if ( ! $existe_no_expediente && ! (empty($detalle['id'])) ){
            //actualizar registro
            $registro->update($detalle);
//            $detalle->save();
        }

//        return true;
        return 'creado';
    }

    public function validar_datos($request){
        $request->validate([
            'anio_captura' => ['required', 'numeric'],
            'mes_captura' => ['required', 'numeric'],
            'seccion' => ['required', 'exists:archivo_secciones,id'],
            'serie' => ['required', 'exists:archivo_series,id'],
        ]);
        return $request->only(['anio_captura', 'mes_captura', 'seccion', 'serie']);
    }

    public function completar_datos($data){

        $data['area_id']                = auth()->user()->persona->area_id;
        $data['unidad_presupuestal']    = config('app.name_organization');
        $data['departamento_id']        = auth()->user()->persona->departamento->id;
        $data['persona_responsable_id'] = auth()->user()->persona->id;
        $data['clave_seccion']          = ArchivoSeccion::find($data['seccion'])->clave;
        $data['clave_serie']            = ArchivoSerie::find($data['serie'])->clave;

        return $data;
    }

    public function existe_inventario($area_id, $departamento_id, $anio_captura, $mes_captura, $clave_seccion, $clave_serie){
        return InventarioDocumental::where('area_id',$area_id)->where('departamento_id',$departamento_id)->where('anio_captura',$anio_captura)->where('mes_captura',$mes_captura)->where('clave_seccion',$clave_seccion)->where('clave_serie',$clave_serie)->count() > 0;
    }


    public function captura_cerrada($anio,$mes){
        $archivoCalendario = ArchivoCalendario::where('anio_captura',$anio)->where('mes_captura',$mes)->first();
        $fechaActual = strtotime(date('Y-m-d H:i:s'));
        //SE CAMBIO LA CONDICIÓN PARA VERIFICAR SI LA CAPTURA DEL MES ESTE CERRADA Y BLOQUEAR REGISTROS EN MESES ANTERIORES
//        if( $fechaActual > strtotime($archivoCalendario->fecha_hora_limite_captura) && $archivoCalendario->permite_captura == false ){
        if( $fechaActual > strtotime($archivoCalendario->fecha_hora_limite_captura) ){
            return true;
        }
        return false;
    }

    public function captura_cerrada_extemporanea($anio,$mes){
        $archivoCalendario = ArchivoCalendario::where('anio_captura',$anio)->where('mes_captura',$mes)->first();
        $fechaActual = strtotime(date('Y-m-d H:i:s'));
        if( $fechaActual > strtotime($archivoCalendario->fecha_hora_limite_captura) && $archivoCalendario->permite_captura == false ){
//        if( $fechaActual > strtotime($archivoCalendario->fecha_hora_limite_captura) ){
            return true;
        }
        return false;
    }


    public function inventario_cerrado(InventarioDocumental $inventarioDocumental){
        if(!empty($inventarioDocumental->fecha_cierre_captura)){
            return true;
        }
        return false;
    }


    public function permite_editar(InventarioDocumental $inventarioDocumental){
        if( $inventarioDocumental->area_id != auth()->user()->persona->area_id || $inventarioDocumental->departamento_id != auth()->user()->persona->departamento_id ){
            return false;
        }
        return true;
    }

    /**
     *  VALIDA SI ES POSIBLE CAPTURAR REGISTROS EN EL INVENTARIO DOCUMENTAL DADO
     */
    public function validar_captura_detalle(InventarioDocumental $inventarioDocumental){
        if(  InventarioDocumental::captura_cerrada($inventarioDocumental->anio_captura,$inventarioDocumental->mes_captura) || InventarioDocumental::inventario_cerrado($inventarioDocumental)  ){
            return 'No es posible agregar registros a una captura cerrada.';
//            throw new \Exception('No es posible agregar registros a una captura cerrada.');
        }
        if(  !InventarioDocumental::permite_editar($inventarioDocumental)  ){
            return 'No tienes permisos para registrar en un Inventario Documental que no pertenece a tu Área Generadora.';
//            throw new \Exception('No tienes permisos para registrar en un Inventario Documental que no pertenece a tu Área Generadora.');
        }

        return true;
    }

    public function buscarExpedientesParaTransferencia($request){
        $inventarioDocumental = InventarioDocumental::where('anio_captura', $request->anio)
            ->where('mes_captura', 12)
            ->where('clave_seccion', ArchivoSeccion::find($request->seccion_id)->clave)
            ->where('clave_serie', ArchivoSerie::find($request->serie_id)->clave)
            ->where('departamento_id', $request->departamento_id)
            /*->orderBy('mes_captura','DESC')*/
            ->select('id')
            ->first();

        if(!$inventarioDocumental){
            return false;
        }

        return InventarioDocumentalDetalle::where('inventario_documentales_id',$inventarioDocumental->id)
            ->where('no_expediente','like', '%'.$request->no_expediente.'%')
            ->where('transferido',false)
            ->paginate(5);

        /*$expedientes = InventarioDocumental::where('anio_captura', $request->anio)
            ->where('clave_seccion', ArchivoSeccion::find($request->seccion_id)->clave)
            ->where('clave_serie', ArchivoSerie::find($request->serie_id)->clave)
            ->where('departamento_id', $request->departamento_id)
            ->orderBy('mes_captura','DESC')
            ->first()->inventario_documental_detalles;

        $filtrados = $expedientes->filter(function ($expediente, $key) use ($request){
            return stristr($expediente->no_expediente, $request->no_expediente);
        });*/
    }

}
