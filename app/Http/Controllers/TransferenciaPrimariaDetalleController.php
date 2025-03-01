<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarTransferenciaDetalleRequest;
use App\Http\Requests\ObservacionesPortadaRequest;
use App\Models\TransferenciaPrimaria;
use App\Models\TransferenciaPrimariaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferenciaPrimariaDetalleController extends Controller {

    public function buscarTransferenciaDetalles(Request $request){
        $request->validate([
            'transferencia_id' => ['required','integer','exists:transferencia_primarias,id'],
        ]);
        $transferenciaPrimariaDetalles = TransferenciaPrimariaDetalle::paginarTransferenciaPrimariaDetalles($request);

        return $this->responseJsonSuccess(compact('transferenciaPrimariaDetalles'));
    }

    public function buscarLegajos(TransferenciaPrimariaDetalle $transferenciaPrimariaDetalle){
        $legajos = TransferenciaPrimariaDetalle::where('inventario_documental_detalle_id',$transferenciaPrimariaDetalle->inventario_documental_detalle_id)->orderBy('no_legajo')->with('inventario_documental_detalle')->get();
        return $this->responseJsonSuccess(compact('legajos'));
    }

    public function guardarTransferenciaDetalle(GuardarTransferenciaDetalleRequest $request){
        DB::beginTransaction();
        try {
            if(isset($request->eliminar_expedientes)){
                TransferenciaPrimariaDetalle::whereIn('id',$request->eliminar_expedientes)->delete();
            }
            foreach ($request->expedientes as $expediente) {
                $transferenciaPrimaria = TransferenciaPrimaria::find($expediente['transferencia_primaria_id']);
                if( !$transferenciaPrimaria->puedeEditarTransferencia() ){
                    throw new \Exception('La transferencia primaria no puede ser editada.');
                }
                $expediente['no_expediente_legajo'] = TransferenciaPrimariaDetalle::generarNoExpedienteLegajo($expediente['inventario_documental_detalle_id'],$expediente['no_legajo']);
                if( isset($expediente['id']) ){
                    TransferenciaPrimariaDetalle::find($expediente['id'])->update($expediente);
                }else{
                    TransferenciaPrimariaDetalle::create($expediente);
                }
            }
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseJsonError($e->getMessage());
        }
        DB::commit();
        return $this->responseJsonSuccess(null,'Datos guardados correctamente.');
    }

    public function guardarObservacionesPortada(ObservacionesPortadaRequest $request){
        $transferenciaPrimaria = TransferenciaPrimaria::find(TransferenciaPrimariaDetalle::find($request->id)->transferencia_primaria_id);
        if( !$transferenciaPrimaria->puedeCompletarDatos() ){
            return $this->responseJsonError('La transferencia primaria no puede ser editada.');
        }
        TransferenciaPrimariaDetalle::find($request->id)->update(['portada_observaciones' => $request->portada_observaciones]);
        return $this->responseJsonSuccess(null,'Observaciones guardados correctamente.');
    }

}
