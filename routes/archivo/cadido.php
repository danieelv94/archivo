<?php


use App\Http\Controllers\archivo\exportarExcelController;
use App\Http\Controllers\CadidoController;

Route::group(['middleware' => 'auth', 'prefix' => 'ccleh/cadido', 'as' => 'ccleh.cadido.'], function(){

        Route::get('ver-listado',[ CadidoController::class, 'verListado'])->name('ver.listado')->middleware(['permission:cadido.index']);

        Route::get('/',[ CadidoController::class, 'index'])->name('index')->middleware(['permission:cadido.index']);
        Route::get('listar-cadidos',[CadidoController::class, 'listarCadidos'])->name('listar.cadidos')->middleware(['permission:cadido.get']);

        Route::post('guardar-cadido',[CadidoController::class, 'store'])->name('guardar.cadido')->middleware(['permission:cadido.store']);
        Route::post('eliminar-cadido/{cadido}',[CadidoController::class, 'delete'])->name('eliminar.cadido')->middleware(['permission:cadido.store']);

        Route::get('exportar-cadido/{cadido}',[exportarExcelController::class, 'exportar_cadido'])->name('exportar.cadido')->middleware(['permission:cadido.get']);
        Route::get('exportar-cadido-multiple/{anio}/{seccionId?}',[exportarExcelController::class, 'exportar_cadido_multiple'])->name('exportar.cadido.multiple')->middleware(['permission:cadido.get']);

        Route::post('importar-cadido',[CadidoController::class, 'importarCadido'])->name('importar.cadido')->middleware(['permission:cadido.store']);

        Route::post('buscar-cadido',[CadidoController::class, 'buscarCadido'])->name('buscar.cadido')->middleware(['permission:cadido.get']);


});


