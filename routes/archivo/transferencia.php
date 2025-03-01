<?php

use App\Http\Controllers\archivo\exportarExcelController;
use App\Http\Controllers\archivo\InventarioDocumentalesController;
use App\Http\Controllers\TransferenciaPrimariaController;
use App\Http\Controllers\TransferenciaPrimariaDetalleController;

Route::group(['middleware' => 'auth', 'prefix' => 'transferencia', 'as' => 'transferencia.'], function () {

    Route::group(['prefix' => 'primaria', 'as' => 'primaria.'], function(){

        Route::get('/', [TransferenciaPrimariaController::class, 'index'])->name('index')->middleware(['permission:transferenciaPrimaria.index']);

        Route::post('buscar-transferencia/{transferenciaPrimaria}',[TransferenciaPrimariaController::class, 'buscarTransferencia'])->name('buscar.transferencia')->middleware(['permission:transferenciaPrimaria.get']);

        Route::post('buscar-transferencia-detalles',[TransferenciaPrimariaDetalleController::class, 'buscarTransferenciaDetalles'])->name('buscar.transferencia.detalles')->middleware(['permission:transferenciaPrimaria.get']);

        Route::post('generar-transferencia',[TransferenciaPrimariaController::class, 'generarTransferencia'])->name('generar.transferencia')->middleware(['permission:transferenciaPrimaria.store']);

        Route::get('exportar-transferencia/{transferenciaPrimaria}',[ exportarExcelController::class, 'exportar_transferencia_primaria'])->name('exportar.transferencia')->middleware(['permission:transferenciaPrimaria.get']);

        Route::post('buscar-legajos/{transferenciaPrimariaDetalle}',[TransferenciaPrimariaDetalleController::class, 'buscarLegajos'])->name('buscar.legajos')->middleware(['permission:transferenciaPrimaria.get']);

        Route::post('guardar-transferencia-detalle',[TransferenciaPrimariaDetalleController::class, 'guardarTransferenciaDetalle'])->name('guardar.transferencia.detalle')->middleware(['permission:transferenciaPrimaria.store']);

        Route::post('editar-transferencia',[TransferenciaPrimariaController::class, 'editarTransferencia'])->name('editar.transferencia')->middleware(['permission:transferenciaPrimaria.store']);

        Route::post('enviar-validar/{transferenciaPrimaria}',[ TransferenciaPrimariaController::class, 'enviarAValidar'])->name('enviar.validar')->middleware(['permission:transferenciaPrimaria.store']);

        Route::post('validar/{transferenciaPrimaria}',[ TransferenciaPrimariaController::class, 'validar'])->name('validar')->middleware(['permission:transferenciaPrimaria.editarOtros']);

        Route::post('rechazar/{transferenciaPrimaria}',[ TransferenciaPrimariaController::class, 'rechazar'])->name('rechazar')->middleware(['permission:transferenciaPrimaria.editarOtros']);

        Route::post('cancelar-validacion/{transferenciaPrimaria}',[ TransferenciaPrimariaController::class, 'rechazar'])->name('cancelar.validacion')->middleware(['permission:transferenciaPrimaria.editarOtros']);

        Route::post('buscar-transferencias-primarias',[TransferenciaPrimariaController::class, 'buscarTransferenciasPrimaria'])->name('buscar.transferencias.primarias')->middleware(['permission:transferenciaPrimaria.get']);

        Route::post('buscar-expedientes',[InventarioDocumentalesController::class, 'buscarExpedientes'])->name('buscar.expedientes')->middleware(['permission:transferenciaPrimaria.get']);

        Route::post('generar-transferencia-parcial',[TransferenciaPrimariaController::class, 'generarTransferenciaParcial'])->name('generar.transferencia.parcial')->middleware(['permission:transferenciaPrimaria.store']);

        Route::post('eliminar-transferencia/{transferenciaPrimaria}',[TransferenciaPrimariaController::class, 'eliminarTransferencia'])->name('eliminar.transferencia')->middleware(['permission:transferenciaPrimaria.store']);


        Route::get('generar-etiquetas/{transferenciaPrimaria}',[ exportarExcelController::class, 'generar_etiquetas_transferencia'])->name('generar.etiquetas')->middleware(['permission:transferenciaPrimaria.get']);
    });

});

Route::group(['middleware' => 'auth', 'prefix' => 'portada', 'as' => 'portada.'], function () {
    Route::get('exportar-portada/{transferenciaPrimariaDetalle}',[ exportarExcelController::class, 'exportar_portada'])->name('exportar.portada')->middleware(['permission:transferenciaPrimaria.get']);

    Route::get('exportar-multiples-portadas/{transferenciaPrimaria}',[ exportarExcelController::class, 'exportar_multiples_portadas'])->name('exportar.multiples.portadas')->middleware(['permission:transferenciaPrimaria.get']);

    Route::post('guardar-observaciones',[ TransferenciaPrimariaDetalleController::class, 'guardarObservacionesPortada'])->name('guardar.observaciones')->middleware(['permission:transferenciaPrimaria.store']);
});

