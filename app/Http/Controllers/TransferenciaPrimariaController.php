<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditarTransferenciaRequest;
use App\Http\Requests\GenerarTransferenciaMultipleRequest;
use App\Http\Requests\TransferenciaPrimariaRequest;
use App\Models\archivo\ArchivoCalendario;
use App\Models\archivo\ArchivoSeccion;
use App\Models\archivo\ArchivoSerie;
use App\Models\archivo\InventarioDocumental;
use App\Models\archivo\InventarioDocumentalDetalle;
use App\Models\Area;
use App\Models\Cadido;
use App\Models\Departamento;
use App\Models\Firmas;
use App\Models\TransferenciaPrimaria;
use App\Models\TransferenciaPrimariaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferenciaPrimariaController extends Controller {
    public function index() {
        $anios = ArchivoCalendario::obtener_anios_hasta_actual();
        $areas = Area::get();
        return view('transferencia-primaria.listado_transferencia', compact('anios','areas'));
    }


    public function buscarTransferencia(TransferenciaPrimaria $transferenciaPrimaria) {
        if( !auth()->user()->can('transferenciaPrimaria.editarOtros') ){
            if($transferenciaPrimaria->departamento_id != auth()->user()->persona->departamento_id){
                return $this->responseJsonError('No hay acceso a la transferencia seleccionada');
            }
        }
        $firmas = Firmas::obtenerFirmasTransferenciaPrimaria($transferenciaPrimaria->departamento_id);
        return $this->responseJsonSuccess(compact('transferenciaPrimaria','firmas'));
    }

    /*public function buscarTransferencia(TransferenciaPrimariaRequest $request) {
        if( !auth()->user()->can('transferenciaPrimaria.editarOtros') ){
            $request->departamento_id = auth()->user()->persona->departamento_id;
        }
        $transferenciaPrimaria = TransferenciaPrimaria::buscarTransferencia($request);
        $firmas = Firmas::obtenerFirmasTransferenciaPrimaria($request->departamento_id);
        return $this->responseJsonSuccess(compact('transferenciaPrimaria','firmas'));
    }*/

    public function generarTransferencia(TransferenciaPrimariaRequest $request){
        $cadido = Cadido::buscarCadidoPorAtributos($request->seccion_id,$request->serie_id,$request->anio);
        if( !$cadido ){
            return $this->responseJsonError('El CADIDO no ha sido registrado.');
        }
        if( TransferenciaPrimaria::buscarTransferencia($request) ){
            return $this->responseJsonError('La transferencia primaria ya ha sido generada.');
        }
        if( !TransferenciaPrimaria::puedeGenerarTransferencia($request->serie_id) ){
            return $this->responseJsonError('La transferencia primaria no puede ser generada.');
        }

        DB::beginTransaction();
        try {
            if( !auth()->user()->can('transferenciaPrimaria.editarOtros') ){
                $request->departamento_id = auth()->user()->persona->departamento_id;
            }

            $transferencia = TransferenciaPrimaria::create( $request->validated() );

            $inventarioDocumental = InventarioDocumental::where('mes_captura',12)
                ->where('anio_captura',$request->anio)
                ->where('clave_seccion', ArchivoSeccion::find($request->seccion_id)->clave)
                ->where('clave_serie', ArchivoSerie::find($request->serie_id)->clave)
                ->where('departamento_id', $request->departamento_id)
                ->orderBy('mes_captura','desc')->first();
            if( !$inventarioDocumental ){
                throw new \Exception('No existe inventario documental registrado en diciembre del año seleccionado.');
            }
            //$inventarioDocumental->load('inventario_documental_detalles');
            if( !count($inventarioDocumental->inventario_documental_detalles) ){
                throw new \Exception('No es posible generar una transferencia primaria vacía.');
            }

            $inventarioDocumental->inventario_documental_detalles->each( function ($expediente,$indice) use ( $transferencia ) {
                TransferenciaPrimariaDetalle::create([
                    'transferencia_primaria_id' => $transferencia->id,
                    'inventario_documental_detalle_id' => $expediente->id,
                    'no_expediente_legajo' => $expediente->no_expediente,
                ]);

                $expediente->update(['transferido' => true]);
            });
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseJsonError($e->getMessage());
        }
        DB::commit();

        $transferenciaId = $transferencia->id;

        return $this->responseJsonSuccess(compact('transferenciaId'),'Transferencia generada.');

    }

    public function editarTransferencia(EditarTransferenciaRequest $request){
        $transferencia = TransferenciaPrimaria::find($request->id);
        if( !$transferencia->puedeCompletarDatos() ){
            return $this->responseJsonError('La transferencia primaria no puede ser editada.');
        }
        $transferencia->update($request->validated());
        return $this->responseJsonSuccess(null,'Transferencia actualizada.');
    }

    public function enviarAValidar(TransferenciaPrimaria $transferenciaPrimaria){
        if( !$transferenciaPrimaria->puedeEditarTransferencia() ){
            return $this->responseJsonError('La transferencia primaria no puede ser editada.');
        }
        $expedienteSinFojas = $transferenciaPrimaria->transferencia_primaria_detalles->first(function($expediente,$indice){
            return ($expediente->no_fojas < 1);
        });
        if($expedienteSinFojas){
            return $this->responseJsonError('Aún no se han asignado número de fojas para todos los expedientes.');
        }
        $transferenciaPrimaria->update(['estado' => config('enums.estados_transferencias.pendiente_revision.valor')]);
        return $this->responseJsonSuccess();
    }

    public function validar(TransferenciaPrimaria $transferenciaPrimaria){
        $transferenciaPrimaria->update(['estado' => config('enums.estados_transferencias.cerrado.valor')]);
        return $this->responseJsonSuccess();
    }

    public function rechazar(TransferenciaPrimaria $transferenciaPrimaria){
        $transferenciaPrimaria->update(['estado' => config('enums.estados_transferencias.rechazado.valor')]);
        return $this->responseJsonSuccess();
    }

    public function buscarTransferenciasPrimaria(TransferenciaPrimariaRequest $request) {
        if( !auth()->user()->can('transferenciaPrimaria.editarOtros') ){
            $request->departamento_id = auth()->user()->persona->departamento_id;
        }
        $transferenciasPrimarias = TransferenciaPrimaria::buscarTransferenciasPrimarias($request);
        $firmas = Firmas::obtenerFirmasTransferenciaPrimaria($request->departamento_id);
        return $this->responseJsonSuccess(compact('transferenciasPrimarias','firmas'));
    }

    public function generarTransferenciaParcial(GenerarTransferenciaMultipleRequest $request){
        $cadido = Cadido::buscarCadidoPorAtributos($request->seccion_id,$request->serie_id,$request->anio);
        if( !$cadido ){
            return $this->responseJsonError('El CADIDO no ha sido registrado.');
        }
        if(!$cadido->candado){
            return $this->responseJsonError('No es posible generar transferencias parciales para esta serie.');
        }
        if( !TransferenciaPrimaria::puedeGenerarTransferencia($request->serie_id) ){
            return $this->responseJsonError('La transferencia primaria no puede ser generada.');
        }

        DB::beginTransaction();
        try {
            $transferencia = TransferenciaPrimaria::create( $request->only('anio','seccion_id','serie_id','departamento_id') );

            foreach ($request->expedientes as $expedienteId) {
                $expediente = InventarioDocumentalDetalle::find($expedienteId);
                TransferenciaPrimariaDetalle::create([
                    'transferencia_primaria_id' => $transferencia->id,
                    'inventario_documental_detalle_id' => $expedienteId,
                    'no_expediente_legajo' => $expediente->no_expediente,
                ]);
                $expediente->update(['transferido' => true]);
            };


        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseJsonError($e->getMessage());
        }
        DB::commit();

        return $this->responseJsonSuccess(null,'Transferencia generada.');
    }

    public function eliminarTransferencia(TransferenciaPrimaria $transferenciaPrimaria){
        if( !$transferenciaPrimaria->puedeEditarTransferencia() ){
            return $this->responseJsonError('La transferencia primaria no puede ser editada.');
        }
        DB::beginTransaction();
        try {
            $transferenciaPrimaria->transferencia_primaria_detalles->each(function($expedienteTransferencia,$indice){
                InventarioDocumentalDetalle::find($expedienteTransferencia->inventario_documental_detalle_id)->update(['transferido' => false]);
            });
            $transferenciaPrimaria->delete();
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseJsonError($e->getMessage());
        }
        DB::commit();
        return $this->responseJsonSuccess();
    }

}
