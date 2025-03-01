<?php

use App\Http\Controllers\AreasController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UserController;

Route::group(['middleware' => 'auth', 'prefix' => 'ccleh', 'as' => 'ccleh.'], function () {

    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::group(['prefix' => 'areas', 'as' => 'areas.', 'middleware' => 'permission:areas.index'], function(){
        Route::get('/', [AreasController::class, 'index'])->name('index');
        Route::get('get/{area?}', [AreasController::class, 'get_areas'])->name('get');
        Route::get('get-activas/{area?}', [AreasController::class, 'get_areas_activas'])->name('get.activas');
        Route::post('nueva', [AreasController::class, 'store'])->name('store');
        Route::post('actualizar/{area}', [AreasController::class, 'update'])->name('update');
        Route::post('eliminar/{area}', [AreasController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'usuarios', 'as' => 'usuarios.', 'middleware' => 'permission:usuarios.index'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('get/{persona?}', [UserController::class, 'get_personas'])->name('get');
        Route::post('nuevo', [UserController::class, 'store'])->name('store');
        Route::post('actualizar/{persona}', [UserController::class, 'update'])->name('update');
        Route::post('eliminar/{persona}', [UserController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'perfil', 'as' => 'perfil.', 'middleware' => 'permission:perfil.index'], function () {
        Route::get('/', [PerfilController::class, 'index'])->name('index');
        Route::post('nueva-clave',[PerfilController::class, 'store_new_password'])->name('new_password');
    });

    Route::group(['prefix' => 'departamentos', 'as' => 'departamentos.', 'middleware' => 'permission:departamentos.index'], function () {
        Route::get('/',[DepartamentoController::class, 'index'])->name('index');
        Route::get('get/{departamento?}', [DepartamentoController::class, 'get_departamentos'])->name('get');
        Route::get('getPadres/{departamento?}', [DepartamentoController::class, 'get_padres'])->name('getPadres');
        Route::get('getDepartamentosArea/{area}', [DepartamentoController::class, 'get_departamentos_area'])->name('getDepartamentosArea');
        Route::post('eliminar/{departamento}', [DepartamentoController::class, 'delete'])->name('delete');
        Route::post('nuevo', [DepartamentoController::class, 'store'])->name('store');
        Route::post('actualizar/{departamento}', [DepartamentoController::class, 'update'])->name('update');
    });

});


