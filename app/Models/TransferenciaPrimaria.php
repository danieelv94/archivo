<?php

namespace App\Models;

use App\Models\archivo\ArchivoSeccion;
use App\Models\archivo\ArchivoSerie;
use App\Models\archivo\ArchivoSerieAutorizada;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TransferenciaPrimaria extends Model {
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'fecha_entrega' => 'date',
    ];

    protected $with = [

    ];

    protected $appends = [
        'editable',
        'validada',
        'no_expedientes',
        //'estado_texto',
    ];

    public function seccion()
    {
        return $this->belongsTo(ArchivoSeccion::class, 'seccion_id', 'id');
    }

    public function serie()
    {
        return $this->belongsTo(ArchivoSerie::class, 'serie_id', 'id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
    }

    public function transferencia_primaria_detalles()
    {
        return $this->hasMany(TransferenciaPrimariaDetalle::class, 'transferencia_primaria_id', 'id')->orderBy('no_expediente_legajo');
    }

    public function getEditableAttribute(){
        return in_array($this->estado,[config('enums.estados_transferencias.abierto.valor'),config('enums.estados_transferencias.rechazado.valor')]);
    }

    public function getValidadaAttribute(){
        return $this->estado === config('enums.estados_transferencias.cerrado.valor');
    }

    public function getNoExpedientesAttribute(){
        return TransferenciaPrimariaDetalle::where('transferencia_primaria_id',$this->id)->count();
    }

    /*public function getEstadoTextoAttribute(){
        foreach (config('enums.estados_transferencias') as $key => $estado) {
            if( $estado['valor'] === $this->estado ){
                return $estado['texto'];
            }
        }
    }*/

    public static function buscarTransferencia(Request $request){
        return self::where('anio',$request->anio)->where('departamento_id', $request->departamento_id)->where('seccion_id', $request->seccion_id)->where('serie_id', $request->serie_id)->first();
    }

    public static function buscarTransferenciasPrimarias(Request $request){
        return self::where('anio',$request->anio)
            ->where('departamento_id', $request->departamento_id)
            ->where('seccion_id', $request->seccion_id)
            ->where('serie_id', $request->serie_id)
            ->select('id','estado', 'created_at')->get();
    }

    public function puedeEditarTransferencia(): bool
    {
        if( in_array($this->estado,[config('enums.estados_transferencias.cerrado.valor'),config('enums.estados_transferencias.pendiente_revision.valor')]) ){
            return false;
        }
        if( auth()->user()->can('transferenciaPrimaria.editarOtros') ){
            return true;
        }
        if( $this->departamento_id == auth()->user()->persona->departamento_id ){
            return true;
        }
        return false;
    }

    public function puedeCompletarDatos(): bool
    {
        if( $this->estado == config('enums.estados_transferencias.cerrado.valor') ){
            if( $this->departamento_id == auth()->user()->persona->departamento_id || auth()->user()->can('transferenciaPrimaria.editarOtros') ){
                return true;
            }
        }
        return false;
    }

    public static function puedeGenerarTransferencia($serieId): bool
    {
        if( auth()->user()->can('transferenciaPrimaria.editarOtros') ){
            return true;
        }
        if( ArchivoSerieAutorizada::where('serie_id', $serieId)->where('departamento_id', auth()->user()->persona->departamento_id)->count() > 0 ){
            return true;
        }
        return false;
    }

}
