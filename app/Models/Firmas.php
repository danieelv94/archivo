<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Firmas extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'elaboro' => 'json',
        'valido' => 'json',
        'recibio' => 'json',
        'autorizo' => 'json',
        'reviso' => 'json',
    ];

    protected $with = [

    ];

    protected  $appends = [

    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    private function obtenerFirmas($tipoDocumento, $departamentoId){
        return self::where('tipo_documento', $tipoDocumento)->where('departamento_id', $departamentoId)->first();
    }

    public static function obtenerFirmasInventarioDocumental($departamentoId){
        return self::obtenerFirmas(config('enums.tipos_documento_firmas.inventario_documental'), $departamentoId);
    }
    public static function obtenerFirmasCadido(){
        return self::where('tipo_documento', config('enums.tipos_documento_firmas.cadido'))->first();
    }
    public static function obtenerFirmasTransferenciaPrimaria($departamentoId){
        return self::obtenerFirmas(config('enums.tipos_documento_firmas.transferencia_primaria'), $departamentoId);
    }

    public static function guardarFirmas(Request $request, $tipoDocumento,$departamentoId = null){
        if ( $request->id ){
            $firma = Firmas::find($request->id);
            $firma->update($request->validated());
        }else{
            $firma = Firmas::where('departamento_id',$departamentoId ?? auth()->user()->persona->departamento_id)->where('tipo_documento',$tipoDocumento)->first();
            if( $firma ){
                $firma->update($request->validated());
            }else{
                $datosFirma = $request->validated();
                $datosFirma['tipo_documento'] = $tipoDocumento;
                $datosFirma['departamento_id'] = $departamentoId ?? auth()->user()->persona->departamento_id;
                $firma = Firmas::create($datosFirma);
            }
        }
        $firma->refresh();
        return $firma;
    }

}
