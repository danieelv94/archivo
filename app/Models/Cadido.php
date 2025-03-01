<?php

namespace App\Models;

use App\Models\archivo\ArchivoSeccion;
use App\Models\archivo\ArchivoSerie;
use App\Models\archivo\ArchivoSerieAutorizada;
use App\Models\archivo\InventarioDocumental;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Cadido extends Model {

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'candado' => 'boolean',
    ];

    protected $with = [
        'seccion',
        'serie'
    ];

    protected  $appends = [
        'anio_transferencia_primaria',
        'anio_transferencia_secundaria',
        'anio_actual',
        'completo',
        //'lista_transferencias'
    ];

    public function seccion()
    {
        return $this->belongsTo(ArchivoSeccion::class, 'seccion_id', 'id');
    }

    public function serie()
    {
        return $this->belongsTo(ArchivoSerie::class, 'serie_id', 'id');
    }

    public function getAnioTransferenciaPrimariaAttribute(){
        return $this->anio+$this->tiempo_guarda_tramite;
    }
    public function getAnioTransferenciaSecundariaAttribute(){
        return $this->anio+$this->tiempo_guarda_tramite+$this->tiempo_guarda_concentracion;
    }

    public function getAnioActualAttribute(){
        return intval(date('Y'));
    }

    public function getCompletoAttribute(){
        $seriesAutorizadas = ArchivoSerieAutorizada::where('seccion_id',$this->seccion_id)
            ->where('serie_id',$this->serie_id)
            ->get();

        foreach ($seriesAutorizadas as $serieAutorizada) {
            $inventarioDocumental = InventarioDocumental::where('clave_serie',ArchivoSerie::find($serieAutorizada->serie_id)->clave)
                ->where('clave_seccion',ArchivoSeccion::find($serieAutorizada->seccion_id)->clave)
                ->where('departamento_id',$serieAutorizada->departamento_id)
                ->where('anio_captura',$this->anio)
                ->where('mes_captura',12)
                ->first();

            if(!$inventarioDocumental){
                return false;
            }

            $transferenciaPrimariaDetalles = TransferenciaPrimariaDetalle::
                join('transferencia_primarias','transferencia_primaria_detalles.transferencia_primaria_id','=','transferencia_primarias.id')
                ->where('transferencia_primarias.seccion_id',$serieAutorizada->seccion_id)
                ->where('transferencia_primarias.serie_id',$serieAutorizada->serie_id)
                ->where('transferencia_primarias.departamento_id',$serieAutorizada->departamento_id)
                ->where('transferencia_primarias.estado',config('enums.estados_transferencias.cerrado.valor'))->get();

            foreach ($inventarioDocumental->inventario_documental_detalles as $inventarioDocumentalDetalle) {
                $existeExpediente = null;
                $existeExpediente = $transferenciaPrimariaDetalles->first(function($transferenciaPrimariaDetalle) use ($inventarioDocumentalDetalle) {
                    return $transferenciaPrimariaDetalle->inventario_documental_detalle_id == $inventarioDocumentalDetalle->id;
                });

                if(!$existeExpediente){
                    return false;
                }
            }
        }

        /*foreach ($seriesAutorizadas as $serieAutorizada) {
            $transferenciaPrimaria = TransferenciaPrimaria::where('seccion_id',$this->seccion_id)
                ->where('serie_id',$this->serie_id)
                ->where('departamento_id',$serieAutorizada->departamento_id)
                ->where('validado',true)->exists();
            if(!$transferenciaPrimaria){
                return false;
            }
        }*/
        return true;
    }

    /*public function getListaTransferenciasAttribute(){
        $seriesAutorizadas = ArchivoSerieAutorizada::where('seccion_id',$this->seccion_id)->where('serie_id',$this->serie_id)->get();
        $listaTransferencias = [];
        foreach ($seriesAutorizadas as $serieAutorizada) {
            $transferenciaPrimaria = TransferenciaPrimaria::where('seccion_id',$this->seccion_id)
                ->where('serie_id',$this->serie_id)
                ->where('departamento_id',$serieAutorizada->departamento_id)
                ->where('validado',true)
                ->where('parcial',false)->first();
            $listaTransferencias[] = ['departamento_nombre' => $transferenciaPrimaria->departamento->nombre, 'validado' => $transferenciaPrimaria->validado];
        }
        return $listaTransferencias;
    }*/

    public function scopeBuscarRepetido(Builder $query, $datosCadido): Builder{
        return $query->where('anio',$datosCadido['anio'])->where('seccion_id',$datosCadido['seccion_id'])->where('serie_id',$datosCadido['serie_id']);
    }


    public static function validarDatosVacios($datosCadido){
        if( !$datosCadido['valor_primario_administrativa'] && !$datosCadido['valor_primario_fiscal'] && !$datosCadido['valor_primario_legal'] ){
            return 'Debes seleccionar al menos una opción en Valor Documental Primario.';
        }

        if( $datosCadido['destino_final'] != 'B' ){
            if( !$datosCadido['valor_secundario_informativo'] && !$datosCadido['valor_secundario_evidencial'] && !$datosCadido['valor_secundario_testimonial'] ){
                return 'Debes seleccionar al menos una opción en Valor Documental Secundario.';
            }
        }

        if( !$datosCadido['clasificacion_publica'] && !$datosCadido['clasificacion_reservada'] && !$datosCadido['clasificacion_confidencial'] ){
            return 'Debes seleccionar al menos una opción en Clasificación de la Información.';
        }

        return false;
    }

    public static function validarTiempoGuarda($datosCadido){
        $valorPrimarioMaximo = max($datosCadido['valor_primario_administrativa'],$datosCadido['valor_primario_fiscal'],$datosCadido['valor_primario_legal']);
        if( $valorPrimarioMaximo != ($datosCadido['tiempo_guarda_tramite'] + $datosCadido['tiempo_guarda_concentracion']) ){
            return 'El Tiempo de Guarda Total debe de ser igual al valor máximo del valor Documental Primario.';
        }
        return true;
    }

    public static function buscarCadidoPorAtributos($seccionId,$serieId,$anio){
        return Cadido::where('seccion_id',$seccionId)->where('serie_id',$serieId)->where('anio',$anio)->first();
    }

}
