<?php

use App\Http\Controllers\archivo\ArchivoSerieAutorizadaController;
use App\Http\Controllers\archivo\exportarExcelController;
use App\Http\Controllers\archivo\InventarioDocumentalesController;
use App\Http\Controllers\ccleh\ArchivoConfigController;
use App\Http\Controllers\ccleh\ArchivoTramiteController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\UbicacionesTopograficaController;

Route::group(['middleware' => 'auth', 'prefix' => 'ccleh/inventario', 'as' => 'ccleh.inventario.'], function () {

    Route::group(['prefix' => 'captura', 'as' => 'captura.', 'middleware' => ['permission:inventarioDocumental.index', 'archivo.puedeCapturar']], function(){

        Route::get('index', [ArchivoTramiteController::class, 'view_inventario'])->name('index');
        Route::get('buscar-meses-disponibles/{anio_captura}',[ArchivoTramiteController::class, 'get_meses_captura'])->name('buscar_meses_disponibles');
        Route::get('buscar-fecha-limite/{anio_captura}/{mes_captura}',[ArchivoTramiteController::class, 'get_fecha_limite'])->name('buscar_fecha_limite');
        Route::get('buscar-inventario/{anio_captura}/{mes_captura}/{clave_seccion}/{clave_serie}',[InventarioDocumentalesController::class, 'buscar'])->name('buscar.inventario');

        Route::post('buscar-inventario-detalles',[InventarioDocumentalesController::class, 'buscarInventarioDocumentalDetalles'])->name('buscar.inventario.detalles');

        Route::post('importar-inventario/{anio_captura}/{mes_captura}/{seccion}/{serie}',[InventarioDocumentalesController::class, 'importar_inventario'])->name('importar.inventario');

        Route::post('guardar-inventario',[InventarioDocumentalesController::class, 'store'])->name('store.inventario');
        Route::post('duplicar-inventario',[InventarioDocumentalesController::class, 'duplicar_inventario'])->name('duplicate.inventario');
        Route::post('agregar-detalle/{inventarioDocumental}',[InventarioDocumentalesController::class, 'store_detalle'])->name('store.detalle');
        Route::post('ordenar-detalle/{inventarioDocumental}/{inventarioDocumentalDetalle}/{sentido}',[InventarioDocumentalesController::class, 'ordenar_detalle'])->name('ordenar.detalle');
        Route::post('eliminar-detalle/{inventarioDocumentalDetalle}',[InventarioDocumentalesController::class, 'eliminar_detalle'])->name('delete.detalle');
        Route::post('vaciar-inventario/{inventarioDocumental}',[InventarioDocumentalesController::class, 'vaciar_inventario'])->name('vaciar.inventario');
        Route::get('verificar-consecutivo/{inventarioDocumental}',[InventarioDocumentalesController::class, 'verificar_consecutivo'])->name('verificar.consecutivo');
        Route::post('cerrar-inventario/{inventarioDocumental?}', [InventarioDocumentalesController::class, 'cerrar_inventario'])->name('cerrar_inventario');

        Route::get('validar-fecha-inicio/{mesCaptura}/{anioCaptura}/{fechaInicio}',[ InventarioDocumentalesController::class, 'validar_fecha_inicio'])->name('validar_fecha_inicio');

        Route::get('get-series-autorizadas/{area}/{departamento}/{repetidas?}/{archivoSeccion?}',[ ArchivoSerieAutorizadaController::class, 'get_series_autorizadas'])->name('get.series.autorizadas');

        Route::get('get-ubicaciones/{departamento}',[ UbicacionesTopograficaController::class, 'get_ubicaciones_topograficas'])->name('get.ubicaciones.topograficas');
        Route::post('delete-ubicacion/{ubicacionesTopografica}',[ UbicacionesTopograficaController::class, 'delete_ubicacion'])->name('delete.ubicaciones');
        Route::post('update-ubicacion',[ UbicacionesTopograficaController::class, 'update_ubicacion'])->name('update.ubicaciones');

    });

    Route::get('descargar-plantilla/{tipo}',[ exportarExcelController::class, 'descargar_plantilla'])->name('descargar.plantilla');

    Route::group(['prefix' => 'reporte', 'as' => 'reporte.', 'middleware' => 'permission:reporte.index'], function(){

        Route::get('index',[ InventarioDocumentalesController::class, 'index'])->name('index');
        Route::get('get-secciones-registradas/{area}/{mes}/{anio}/{departamento?}',
            [InventarioDocumentalesController::class, 'get_secciones_registradas'])->name('get.secciones.registradas');
        Route::get('get-series-registradas/{area}/{mes}/{anio}/{seccion}/{departamento?}',
            [InventarioDocumentalesController::class, 'get_series_registradas'])->name('get.series.registradas');
        Route::post('revisar-inventario/{inventarioDocumental}',[ InventarioDocumentalesController::class, 'revisar_inventario'])->name('revisar.inventario');
        Route::post('abrir-inventario/{inventarioDocumental}', [InventarioDocumentalesController::class, 'abrir_inventario'])->name('abrir.inventario');

        Route::get('generar-etiquetas/{inventarioDocumental}',[ exportarExcelController::class, 'generar_etiquetas'])->name('generar.etiquetas');

        Route::get('exportar-inventario-doc/{inventarioDocumental}',[ exportarExcelController::class, 'exportar_inventario'])->name('exportar.inventario.documental');
        Route::get('exportar-sabana-inventario-doc/{inventarioDocumental}',[ exportarExcelController::class, 'exportar_inventario_sabana'])->name('exportar.inventario.documental.sabana');

        Route::get('getDepartamentosArea/{area}', [DepartamentoController::class, 'get_departamentos_area'])->name('getDepartamentosArea');
    });

});
