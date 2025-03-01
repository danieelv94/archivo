<?php

use App\Http\Controllers\archivo\ArchivoSerieAutorizadaController;
use App\Http\Controllers\archivo\ArchivoSupervisionController;
use App\Http\Controllers\archivo\exportarExcelController;
use App\Http\Controllers\ccleh\ArchivoConfigController;
use App\Http\Controllers\CadidoController;

Route::group(['middleware' => 'auth', 'prefix' => 'ccleh/admin', 'as' => 'ccleh.admin.'], function(){

    Route::group(['prefix' => 'calendario', 'as' => 'calendario.', 'middleware' => 'permission:calendario.index'], function(){

        Route::get('/', [ArchivoConfigController::class,'index'] )->name('index');
        Route::get('get_calendario', [ArchivoConfigController::class, 'get_calendarios'])->name('get_calendarios');
        Route::post('update/{archivoCalendario}', [ArchivoConfigController::class,'update'])->name('update.calenario');
        Route::post('delete/{archivoCalendario}', [ArchivoConfigController::class, 'destroy'])->name('destroy.calendario');
        Route::post('create',[ArchivoConfigController::class, 'create'])->name('calendario.create');
        Route::post('actualiza/estado_captura',[ArchivoConfigController::class, 'update_estado_captura'])->name('update.estado_captura');
        Route::post('cambiarEstado/{archivoCalendario}/{estado}', [ArchivoConfigController::class, 'cambiar_estado'])->name('cambiarEstado.calendario');
        Route::post('duplicar-calendario',[ArchivoConfigController::class, 'duplicar_calendario'])->name('duplicar.calendario');
        Route::get('listar-anios',[ArchivoConfigController::class, 'listar_anios'])->name('listar.anios');

    });


    Route::group(['prefix' => 'supervision', 'as' => 'supervision.', 'middleware' => 'permission:supervision.index'], function(){

        Route::get('/',[ ArchivoSupervisionController::class, 'index'])->name('index');
        Route::get('areas-semaforo/{mes}/{anio}',[ ArchivoSupervisionController::class, 'areas_semaforo'])->name('areas.semaforo');
        Route::get('get-meses-captura/{anio}',[ ArchivoSupervisionController::class, 'get_meses_captura'])->name('get.meses.captura');

    });


    Route::group(['prefix' => 'series-autorizadas', 'as' => 'series-autorizadas.', 'middleware' => 'permission:seriesAutorizadas.index'], function(){

        Route::get('/',[ ArchivoSerieAutorizadaController::class, 'index'])->name('index');
        Route::get('get-series-autorizadas/{area}/{departamento}/{repetidas?}/{archivoSeccion?}',[ ArchivoSerieAutorizadaController::class, 'get_series_autorizadas'])->name('get.series.autorizadas');
        Route::get('get-secciones',[ ArchivoSerieAutorizadaController::class, 'get_secciones'])->name('get.secciones');
        Route::get('get-departamentos/{area}',[ ArchivoSerieAutorizadaController::class, 'get_departamentos'])->name('get.departamentos');
        Route::get('get-series/{archivoSeccion}',[ ArchivoSerieAutorizadaController::class, 'get_series'])->name('get.series');
        Route::post('eliminar-serie-autorizada/{archivoSerieAutorizada}',[ ArchivoSerieAutorizadaController::class, 'delete'])->name('eliminar.serie.autorizada');
        Route::post('agregar-serie-autorizada',[ ArchivoSerieAutorizadaController::class, 'store'])->name('agregar.serie.autorizada');

    });


});


