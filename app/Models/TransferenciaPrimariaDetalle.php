<?php

namespace App\Models;

use App\Models\archivo\InventarioDocumentalDetalle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TransferenciaPrimariaDetalle extends Model {

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_final' => 'date:Y-m-d',
        'portada_fechas_consulta' => 'array'
    ];

    protected $with = [
//        'inventario_documental_detalle'
    ];

    protected $appends = [
    ];

    public function transferencia_primaria()
    {
        return $this->belongsTo(TransferenciaPrimaria::class);
    }

    public function inventario_documental_detalle()
    {
        return $this->belongsTo(InventarioDocumentalDetalle::class);
    }


    public static function generarNoExpedienteLegajo($inventarioDocumentalDetalleId, $noLegajo = null){
        $noExpedienteOriginal = InventarioDocumentalDetalle::find($inventarioDocumentalDetalleId)->no_expediente;
        if($noLegajo){
            $explodeNoExpediente = explode('-',$noExpedienteOriginal);
            $legajoRomano = self::decimalARomano($noLegajo);
            $noExpedienteLegajo = $explodeNoExpediente[0].'-'.$explodeNoExpediente[1].$legajoRomano.'-'.$explodeNoExpediente[2];
            return $noExpedienteLegajo;
        }
        return $noExpedienteOriginal;
    }

    public static function paginarTransferenciaPrimariaDetalles(Request $request, $porPagina = 10){
        return self::with('inventario_documental_detalle')
            ->where('transferencia_primaria_id', $request->transferencia_id)
            ->orderBy('no_expediente_legajo')
            ->paginate($porPagina)->withQueryString();
    }

    public static function decimalARomano($numero) {
        $valores = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        );
        $resultado = '';
        foreach ($valores as $simbolo => $valor) {
            $repeticiones = intval($numero / $valor);
            $resultado .= str_repeat($simbolo, $repeticiones);
            $numero -= $repeticiones * $valor;
        }
        return $resultado;
    }





}
