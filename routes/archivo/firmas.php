<?php

Route::group(['middleware' => 'auth', 'prefix' => 'ccleh/firmas', 'as' => 'ccleh.firmas.'], function(){
    Route::post('guardar-inventario-documental', [\App\Http\Controllers\FirmasController::class, 'guardarFirmasInventarioDocumental'])->name('guardar.inventario.documental');
    Route::post('guardar-transferencia-primaria/{departamento}', [\App\Http\Controllers\FirmasController::class, 'guardarFirmasTransferenciaPrimaria'])->name('guardar.transferencia.primaria');
    Route::post('guardar-cadido', [\App\Http\Controllers\FirmasController::class, 'guardarFirmasCadido'])->name('guardar.cadido');
});
