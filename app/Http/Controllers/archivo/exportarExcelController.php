<?php

namespace App\Http\Controllers\archivo;

use App\Http\Controllers\Controller;
use App\Models\archivo\InventarioDocumental;
use App\Models\Cadido;
use App\Models\Firmas;
use App\Models\TransferenciaPrimaria;
use App\Models\TransferenciaPrimariaDetalle;
use File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\IOFactory;

class exportarExcelController extends Controller
{

    public function generar_etiquetas_transferencia(TransferenciaPrimaria $transferenciaPrimaria){
        try {
            //OBTIENE EL ARCHIVO PLANTILLA XLSX PARA CREAR LAS ETIQUETAS
            $spread = IOFactory::load( public_path("excel_plantillas/etiquetas.xlsx") );
            //ALMACENA LA PRIMERA HOJA DE LA PLANTILLA
            $sheetOrigin = $spread->getSheet(0);
            //DUPLICA LA PRIMERA HOJA DE LA PLANTILLA PARA TRABAJAR SOBRE ELLA SIN MODIFICAR EL FORMATO ORIGINAl
            $sheet = clone $sheetOrigin;
            //DEFINE EL TITULO DE LA PRIMERA HOJA
            $sheet->setTitle('Hoja 1');
            //AGREGA LA HOJA DUPLICADA DE LA PLANTILLA COMO UNA NUEVA HOJA EN EL DOCUMENTO
            $spread->addSheet($sheet);

            //        $sheet = $spread->getActiveSheet();
            //        $sheet->setCellValueByColumnAndRow(1, 1, "Valor A1");
            //        $sheet->setCellValue("B1", "Valor celda B2");

            //OBTIENE LOS EXPEDIENTES DEL INVENTARIO DOCUMENTAL CORRESPONDIENTE
            $expedientes = TransferenciaPrimariaDetalle::where('transferencia_primaria_id', $transferenciaPrimaria->id)->orderBy('no_expediente_legajo')->get();
            //SE OBTIENEN DATOS DEL INVENTARIO DOCUMENTAL
            $siglas = Str::lower($transferenciaPrimaria->departamento->iniciales);
            $anio = $transferenciaPrimaria->anio;
            $serie = $transferenciaPrimaria->serie->id;
            $seccion = $transferenciaPrimaria->seccion->id;

            $datosExcel = [
                'numHoja' => 1,//DEFINE EL NUMERO DE HOJA CON EL QUE SE ESTA TRABAJANDO
                'contadorRegistrosPagina' => 0,//CONTADOR PARA EL NUMERO DE REGISTRO POR HOJA(SE REINICIA AL CAMBIAR DE HOJA)
                'registrosPorHoja' => 18,//DEFINE CUANTOS REGISTROS SON PERMITIDOS POR CADA HOJA
                'posicionNoExpediente' => 2,//NUMERO DE FILA DONDE SE INSERTARA EL NUMERO DE EXPEDIENTE
                'posicionColumna' => 2//NUMERO DE COLUMNA DONDE SE INSERTARAN
            ];
            //RECORRE LOS EXPEDIENTES OBTENIDOS
            foreach ($expedientes as $key => $value){
                //AUMENTO EL CONTADOR DE NUMERO DE REGISTRO POR HOJA
                $datosExcel['contadorRegistrosPagina']++;

                //VALIDA EL NUMERO DE REGISTRO PARA DEFINIR SU POSICIÓN EL EL SISTEMA
                $datosExcel = $this->validar_posiciones_etiquetas($datosExcel);

                $descripcionRichText = $this->formatear_descripcion_expediente( $value['descripcion'] ?? $value->inventario_documental_detalle->descripcion );

                //AÑADE LOS VALORES DE EXPEDIENTE DESEADOS EN LAS ETIQUETAS
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumna'], $datosExcel['posicionNoExpediente'], $value['no_expediente_legajo']);
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumna'], $datosExcel['posicionNoExpediente']+1, $descripcionRichText);

                //CONDICIÓN QUE VERIFICA SI SE HA LLEGADO AL NUMERO MÁXIMO DE REGISTROS POR HOJA
                if( ($key+1) == ($datosExcel['registrosPorHoja']*$datosExcel['numHoja']) ){
                    //AUMENTA EL CONTADOR DE HOJAS
                    $datosExcel['numHoja']++;
                    //CLONA LA LA HOJA PLANTILLA PARA CREAR LA NUEVA HOJA A UTILIZAR
                    $sheet = clone $sheetOrigin;
                    //ESTABLECE EL TITULO DE LA NUEVA HOJA
                    $sheet->setTitle('Hoja '.$datosExcel['numHoja']);
                    //AGREGA LA HOJA DUPLICADA DE LA PLANTILLA COMO UNA NUEVA HOJA EN EL DOCUMENTO
                    $spread->addSheet($sheet);
                    //REINICIA EL CONTADOR DE NUMERO DE REGISTRO POR HOJA
                    $datosExcel['contadorRegistrosPagina'] = 0;
                }
            }
            //ELIMINA LA HOJA PLANTILLA DEL DOCUMENTO FINAL
            $spread->removeSheetByIndex(0);

            //ESTABLECE LA ALINEACIÓN DE TODO EL TEXTO DEL DOCUMENTO
            $spread->getDefaultStyle()->getAlignment()->setVertical('top');

            $this->descargar_exel_generado($spread,mb_strtoupper("ETIQUETAS_{$siglas}-{$anio}_{$seccion}.{$serie}","UTF-8").".xlsx");
        }catch (\PhpOffice\PhpSpreadsheet\Writer\Exception | \PhpOffice\PhpSpreadsheet\Exception $e){
            echo 'Error al generar etiquetas.';
        }
    }

    public function generar_etiquetas(InventarioDocumental $inventarioDocumental){
        try {
            //OBTIENE EL ARCHIVO PLANTILLA XLSX PARA CREAR LAS ETIQUETAS
            $spread = IOFactory::load( public_path("excel_plantillas/etiquetas.xlsx") );
            //ALMACENA LA PRIMERA HOJA DE LA PLANTILLA
            $sheetOrigin = $spread->getSheet(0);
            //DUPLICA LA PRIMERA HOJA DE LA PLANTILLA PARA TRABAJAR SOBRE ELLA SIN MODIFICAR EL FORMATO ORIGINAl
            $sheet = clone $sheetOrigin;
            //DEFINE EL TITULO DE LA PRIMERA HOJA
            $sheet->setTitle('Hoja 1');
            //AGREGA LA HOJA DUPLICADA DE LA PLANTILLA COMO UNA NUEVA HOJA EN EL DOCUMENTO
            $spread->addSheet($sheet);

            //        $sheet = $spread->getActiveSheet();
            //        $sheet->setCellValueByColumnAndRow(1, 1, "Valor A1");
            //        $sheet->setCellValue("B1", "Valor celda B2");

            //OBTIENE LOS EXPEDIENTES DEL INVENTARIO DOCUMENTAL CORRESPONDIENTE
            $expedientes = $inventarioDocumental->inventario_documental_detalles;
            //SE OBTIENEN DATOS DEL INVENTARIO DOCUMENTAL
            $siglas = Str::lower($inventarioDocumental->departamento->iniciales);
            $anio = $inventarioDocumental->anio_captura;
            $mes = $inventarioDocumental->mes_captura;
            $serie = $inventarioDocumental->clave_serie;
            $seccion = $inventarioDocumental->clave_seccion;

            $datosExcel = [
                'numHoja' => 1,//DEFINE EL NUMERO DE HOJA CON EL QUE SE ESTA TRABAJANDO
                'contadorRegistrosPagina' => 0,//CONTADOR PARA EL NUMERO DE REGISTRO POR HOJA(SE REINICIA AL CAMBIAR DE HOJA)
                'registrosPorHoja' => 18,//DEFINE CUANTOS REGISTROS SON PERMITIDOS POR CADA HOJA
                'posicionNoExpediente' => 2,//NUMERO DE FILA DONDE SE INSERTARA EL NUMERO DE EXPEDIENTE
                'posicionColumna' => 2//NUMERO DE COLUMNA DONDE SE INSERTARAN
            ];
            //RECORRE LOS EXPEDIENTES OBTENIDOS
            foreach ($expedientes as $key => $value){
                //AUMENTO EL CONTADOR DE NUMERO DE REGISTRO POR HOJA
                $datosExcel['contadorRegistrosPagina']++;

                //VALIDA EL NUMERO DE REGISTRO PARA DEFINIR SU POSICIÓN EL EL SISTEMA
                $datosExcel = $this->validar_posiciones_etiquetas($datosExcel);

                $descripcionRichText = $this->formatear_descripcion_expediente( $value['descripcion'] );

                //AÑADE LOS VALORES DE EXPEDIENTE DESEADOS EN LAS ETIQUETAS
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumna'], $datosExcel['posicionNoExpediente'], $value['no_expediente']);
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumna'], $datosExcel['posicionNoExpediente']+1, $descripcionRichText);


                //CONDICIÓN QUE VERIFICA SI SE HA LLEGADO AL NUMERO MÁXIMO DE REGISTROS POR HOJA
                if( ($key+1) == ($datosExcel['registrosPorHoja']*$datosExcel['numHoja']) ){
                    //AUMENTA EL CONTADOR DE HOJAS
                    $datosExcel['numHoja']++;
                    //CLONA LA LA HOJA PLANTILLA PARA CREAR LA NUEVA HOJA A UTILIZAR
                    $sheet = clone $sheetOrigin;
                    //ESTABLECE EL TITULO DE LA NUEVA HOJA
                    $sheet->setTitle('Hoja '.$datosExcel['numHoja']);
                    //AGREGA LA HOJA DUPLICADA DE LA PLANTILLA COMO UNA NUEVA HOJA EN EL DOCUMENTO
                    $spread->addSheet($sheet);
                    //REINICIA EL CONTADOR DE NUMERO DE REGISTRO POR HOJA
                    $datosExcel['contadorRegistrosPagina'] = 0;
                }
            }
            //ELIMINA LA HOJA PLANTILLA DEL DOCUMENTO FINAL
            $spread->removeSheetByIndex(0);

            //ESTABLECE LA ALINEACIÓN DE TODO EL TEXTO DEL DOCUMENTO
            $spread->getDefaultStyle()->getAlignment()->setVertical('top');

            $this->descargar_exel_generado($spread,mb_strtoupper("ETIQUETAS_{$siglas}_{$mes}-{$anio}_{$seccion}.{$serie}","UTF-8").".xlsx");
        }catch (\PhpOffice\PhpSpreadsheet\Writer\Exception | \PhpOffice\PhpSpreadsheet\Exception $e){
            echo 'Error al generar etiquetas.';
        }
    }


    private function validar_posiciones_etiquetas($datosExcel){
        //VALIDA EL NUMERO DE REGISTRO PARA DEFINIR SU POSICIÓN EL EL SISTEMA
        if( $datosExcel['contadorRegistrosPagina'] == 1 ){
            $datosExcel['posicionNoExpediente'] = 2;
            $datosExcel['posicionColumna'] = 2;
        }elseif( $datosExcel['contadorRegistrosPagina'] == 10 ){
            $datosExcel['posicionNoExpediente'] = 2;
            $datosExcel['posicionColumna'] = 6;
        }elseif( $datosExcel['contadorRegistrosPagina'] > 10 ){
            $datosExcel['posicionNoExpediente'] = $datosExcel['posicionNoExpediente']+3;
            $datosExcel['posicionColumna'] = 6;
        }else{
            $datosExcel['posicionNoExpediente'] = $datosExcel['posicionNoExpediente']+3;
            $datosExcel['posicionColumna'] = 2;
        }

        return $datosExcel;
    }


    public function exportar_inventario_sabana(InventarioDocumental $inventarioDocumental){
        $siglas = Str::lower($inventarioDocumental->departamento->iniciales);
        $anio = $inventarioDocumental->anio_captura;
        $mes = $inventarioDocumental->mes_captura;

        $inventario_detalle = $inventarioDocumental->inventario_documental_detalles;
        $serie = $inventarioDocumental->clave_serie;
        $seccion = $inventarioDocumental->clave_seccion;


        try{
            //HELPER HTML PARA AGREGAR TEXTO ENRIQUECIDO A LAS CELDAS
            $html = new Html();

            //OBTIENE EL ARCHIVO PLANTILLA XLSX PARA CREAR LAS ETIQUETAS
            $spread = IOFactory::load( public_path("excel_plantillas/plantilla_sabana.xlsx") );
            //ALMACENA LA PRIMERA HOJA DE LA PLANTILLA
            $sheet = $spread->getSheet(0);
            //DEFINE EL TITULO DE LA PRIMERA HOJA
            $sheet->setTitle("{$seccion}.{$serie}");

            $datosExcel = [
                'contadorRegistrosPagina' => 0,//CONTADOR PARA EL NUMERO DE REGISTRO POR HOJA(SE REINICIA AL CAMBIAR DE HOJA)
                'posicionFilaExpediente' => 11,//NUMERO DE FILA ANTERIOR A DONDE INICIARA A INSERTAR LOS EXPEDIENTES
                'posicionColumnaExpediente' => 1//NUMERO DE COLUMNA DONDE INICIARA A INSERTAR LOS EXPEDIENTES
            ];

            $sheet = $this->encabezados_inventario_documental($inventarioDocumental, $sheet, 1, 1);

            //RECORRE LOS EXPEDIENTES OBTENIDOS
            foreach ($inventario_detalle as $key => $value){
                //AUMENTO EL CONTADOR DE NUMERO DE REGISTRO POR HOJA
                $datosExcel['contadorRegistrosPagina']++;
                //DEFINE EL ANCHO DE LA FILA CON LA QUE SE ESTA TRABAJANDO
                $sheet->getRowDimension($datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'])->setRowHeight(46.5);

                //DA FORMATO A LA DESCRIPCIÓN DEL EXPEDIENTE
                $descripcionRichText = $this->formatear_descripcion_expediente( $value['descripcion'] );

                //AÑADE LOS VALORES DE EXPEDIENTE DESEADOS
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente'], $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $datosExcel['contadorRegistrosPagina'] );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+1, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['ubicacion_fisica'] );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+2, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['ubicacion_topografica'] );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+3, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['no_expediente'] );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+4, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $descripcionRichText );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+5, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['fecha_inicio']->format('d/m/Y') );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+6, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], empty($value['fecha_final']) ? $value['fecha_inicio']->format('Y') :  $value['fecha_final']->format('d/m/Y') );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+7, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['observaciones'] );

                //AÑADE EL TOTAL DE REGISTROS GENERADOS
                if($key == count($inventario_detalle) - 1) {
                    $sheet->setCellValueByColumnAndRow(2, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina']+1, "TOTAL" );
                    $sheet->setCellValueByColumnAndRow(3, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina']+1, count($inventario_detalle)." EXPEDIENTES" );
                }
            }

            //ESTABLECE QUE TODO EL TEXTO DEL DOCUMENTO SEA ALINEADO AL TAMAÑO DE LA CELDA
            $spread->getDefaultStyle()->getAlignment()->setWrapText(true);
            //ESTABLECE LA ALINEACIÓN DE TODO EL TEXTO DEL DOCUMENTO
            $spread->getDefaultStyle()->getAlignment()->setVertical('top');

            $this->descargar_exel_generado($spread, mb_strtoupper("SABANA_{$siglas}_{$mes}-{$anio}_{$seccion}.{$serie}","UTF-8").".xlsx" );
        }catch (\PhpOffice\PhpSpreadsheet\Writer\Exception | \PhpOffice\PhpSpreadsheet\Exception $e){
            echo 'Error al generar sabana de inventario.';
        }


    }


    public function exportar_inventario(InventarioDocumental $inventarioDocumental){
        $siglas = Str::lower($inventarioDocumental->departamento->iniciales);
        $anio = $inventarioDocumental->anio_captura;
        $mes = $inventarioDocumental->mes_captura;

        $inventario_detalle = $inventarioDocumental->inventario_documental_detalles;
        $serie = $inventarioDocumental->clave_serie;
        $seccion = $inventarioDocumental->clave_seccion;


        try{
            //HELPER HTML PARA AGREGAR TEXTO ENRIQUECIDO A LAS CELDAS
            $html = new Html();

            $datosExcel = [
                'numHoja' => 1,//DEFINE EL NUMERO DE HOJA CON EL QUE SE ESTA TRABAJANDO
                'registrosPorHoja' => 11,//DEFINE CUANTOS REGISTROS SON PERMITIDOS POR CADA HOJA
                'contadorRegistrosPagina' => 0,//CONTADOR PARA EL NUMERO DE REGISTRO POR HOJA(SE REINICIA AL CAMBIAR DE HOJA)
                'posicionFilaExpediente' => 11,//NUMERO DE FILA ANTERIOR A DONDE INICIARA A INSERTAR LOS EXPEDIENTES
                'posicionColumnaExpediente' => 1,//NUMERO DE COLUMNA DONDE INICIARA A INSERTAR LOS EXPEDIENTES
                'totalHojas' =>  count($inventario_detalle)%11 != 0 ? intval(count($inventario_detalle)/11)+1 : intval(count($inventario_detalle)/11),
            ];

            //OBTIENE EL ARCHIVO PLANTILLA XLSX PARA CREAR LAS ETIQUETAS
            $spread = IOFactory::load( public_path("excel_plantillas/plantilla_inv-doc-v2.xlsx") );

            //ALMACENA LA PRIMERA HOJA DE LA PLANTILLA
            $sheetOrigin1 = $spread->getSheet(0);
            //ALMACENA LA SEGUNDA HOJA DE LA PLANTILLA
            $sheetOrigin2 = $spread->getSheet(1);
            //DUPLICA LA PRIMERA HOJA DE LA PLANTILLA PARA TRABAJAR SOBRE ELLA SIN MODIFICAR EL FORMATO ORIGINAl
            $sheet = clone $sheetOrigin1;
            //DEFINE EL TITULO DE LA PRIMERA HOJA
            $sheet->setTitle("{$seccion}.{$serie}-{$datosExcel['numHoja']}");
            //AGREGA LA HOJA DUPLICADA DE LA PLANTILLA COMO UNA NUEVA HOJA EN EL DOCUMENTO
            $spread->addSheet($sheet);

            //DEFINE LOS ENCABEZADOS DE LA PRIMERA HOJA
            $sheet = $this->encabezados_inventario_documental($inventarioDocumental, $sheet, $datosExcel['totalHojas'], $datosExcel['numHoja']);
            $sheet = $this->firmas_inventario_documental($inventarioDocumental, $sheet);

            //RECORRE LOS EXPEDIENTES OBTENIDOS
            foreach ($inventario_detalle as $key => $value){
                //AUMENTO EL CONTADOR DE NUMERO DE REGISTRO POR HOJA
                $datosExcel['contadorRegistrosPagina']++;
                //DEFINE EL ANCHO DE LA FILA CON LA QUE SE ESTA TRABAJANDO
//                $sheet->getRowDimension($datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'])->setRowHeight(46.5);
                //DA FORMATO A LA DESCRIPCIÓN DEL EXPEDIENTE
                $descripcionRichText = $this->formatear_descripcion_expediente( $value['descripcion'] );

                //AÑADE LOS VALORES DE EXPEDIENTE DESEADOS
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente'], $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['ubicacion_fisica'] );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+1, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['ubicacion_topografica'] );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+2, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['no_expediente'] );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+3, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $descripcionRichText );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+4, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['fecha_inicio']->format('d/m/Y') );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+5, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], empty($value['fecha_final']) ? $value['fecha_inicio']->format('Y') :  $value['fecha_final']->format('d/m/Y') );
                $sheet->setCellValueByColumnAndRow($datosExcel['posicionColumnaExpediente']+6, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $value['observaciones'] );

                //AÑADE EL TOTAL DE REGISTROS POR HOJA
                if( $datosExcel['contadorRegistrosPagina'] == 11 || $datosExcel['numHoja'] == $datosExcel['totalHojas'] ) {
                    $sheet->setCellValueByColumnAndRow(3, 23, $datosExcel['contadorRegistrosPagina']." EXPEDIENTES" );
                }

                //CONDICIÓN QUE VERIFICA SI SE HA LLEGADO AL NUMERO MÁXIMO DE REGISTROS POR HOJA
                if( ($key+1) == ($datosExcel['registrosPorHoja']*$datosExcel['numHoja']) ){
                    //AUMENTA EL CONTADOR DE HOJAS
                    $datosExcel['numHoja']++;
                    //CLONA LA LA HOJA PLANTILLA PARA CREAR LA NUEVA HOJA A UTILIZAR
                    $sheet = clone $sheetOrigin2;
                    //ESTABLECE EL TITULO DE LA NUEVA HOJA
                    $sheet->setTitle("{$seccion}.{$serie}-{$datosExcel['numHoja']}");
                    //AGREGA LA HOJA DUPLICADA DE LA PLANTILLA COMO UNA NUEVA HOJA EN EL DOCUMENTO
                    $spread->addSheet($sheet);
                    //DEFINE LOS ENCABEZADOS DE LA NUEVA HOJA
                    $sheet = $this->encabezados_inventario_documental($inventarioDocumental, $sheet, $datosExcel['totalHojas'], $datosExcel['numHoja']);
                    $sheet = $this->firmas_inventario_documental($inventarioDocumental, $sheet);
                    //REINICIA EL CONTADOR DE NUMERO DE REGISTRO POR HOJA
                    $datosExcel['contadorRegistrosPagina'] = 0;
                }

            }
            //ELIMINA LAS HOJAS PLANTILLA DEL DOCUMENTO FINAL
            $spread->removeSheetByIndex(0);
            $spread->removeSheetByIndex(0);

            //ESTABLECE QUE TODO EL TEXTO DEL DOCUMENTO SEA ALINEADO AL TAMAÑO DE LA CELDA
            $spread->getDefaultStyle()->getAlignment()->setWrapText(true);
            //ESTABLECE LA ALINEACIÓN DE TODO EL TEXTO DEL DOCUMENTO
            $spread->getDefaultStyle()->getAlignment()->setVertical('top');

            $this->descargar_exel_generado($spread, mb_strtoupper("{$siglas}_{$mes}-{$anio}_{$seccion}.{$serie}","UTF-8").".xlsx" );
        }catch (\PhpOffice\PhpSpreadsheet\Writer\Exception | \PhpOffice\PhpSpreadsheet\Exception $e){
            echo 'Error al generar sabana de inventario.';
        }


    }

    public function exportar_transferencia_primaria(TransferenciaPrimaria $transferenciaPrimaria){
        $transferenciaPrimaria->load('transferencia_primaria_detalles');
        $transferenciaPrimaria->transferencia_primaria_detalles->load('inventario_documental_detalle');
        $anio = $transferenciaPrimaria->anio;
        $serie = $transferenciaPrimaria->serie->clave;
        $seccion = $transferenciaPrimaria->seccion->clave;

        try {
            $datosExcel = [
                'numHoja' => 1,//DEFINE EL NUMERO DE HOJA CON EL QUE SE ESTA TRABAJANDO
                'registrosPorHoja' => 11,//DEFINE CUANTOS REGISTROS SON PERMITIDOS POR CADA HOJA
                'contadorRegistrosPagina' => 0,//CONTADOR PARA EL NUMERO DE REGISTRO POR HOJA(SE REINICIA AL CAMBIAR DE HOJA)
                'posicionFilaExpediente' => 12,//NUMERO DE FILA DONDE INICIARA A INSERTAR LOS EXPEDIENTES
                'totalHojas' => count($transferenciaPrimaria->transferencia_primaria_detalles)%11 != 0 ? intval(count($transferenciaPrimaria->transferencia_primaria_detalles)/11)+1 : intval(count($transferenciaPrimaria->transferencia_primaria_detalles)/11),
            ];

            //HELPER HTML PARA AGREGAR TEXTO ENRIQUECIDO A LAS CELDAS
            $html = new Html();
            //OBTIENE EL ARCHIVO PLANTILLA XLSX
            $spread = IOFactory::load( public_path("excel_plantillas/plantilla_inv-transferencia.xlsx") );

            //ALMACENA LA PRIMERA HOJA DE LA PLANTILLA
            $sheetOrigin1 = $spread->getSheet(0);
            //ALMACENA LA SEGUNDA HOJA DE LA PLANTILLA
            $sheetOrigin2 = $spread->getSheet(1);
            //DUPLICA LA PRIMERA HOJA DE LA PLANTILLA PARA TRABAJAR SOBRE ELLA SIN MODIFICAR EL FORMATO ORIGINAl
            $sheet = clone $sheetOrigin1;
            //DEFINE EL TITULO DE LA PRIMERA HOJA
            $sheet->setTitle("{$seccion}.{$serie}-{$datosExcel['numHoja']}");
            //AGREGA LA HOJA DUPLICADA DE LA PLANTILLA COMO UNA NUEVA HOJA EN EL DOCUMENTO
            $spread->addSheet($sheet);

            //DEFINE LOS ENCABEZADOS DE LA PRIMERA HOJA
            $sheet = $this->encabezados_transferencia_primaria($transferenciaPrimaria, $sheet, $datosExcel['totalHojas'], $datosExcel['numHoja']);
            $sheet = $this->firmas_transferencia_primaria($transferenciaPrimaria, $sheet);

            foreach ($transferenciaPrimaria->transferencia_primaria_detalles as $index => $transferenciaPrimariaDetalle){
                $sheet->setCellValueByColumnAndRow(1, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $transferenciaPrimariaDetalle['no_expediente_legajo'] );
                $sheet->setCellValueByColumnAndRow(2, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $transferenciaPrimariaDetalle['no_fojas'] );
                $sheet->setCellValueByColumnAndRow(3, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $this->formatear_descripcion_expediente( $transferenciaPrimariaDetalle['descripcion'] ?? $transferenciaPrimariaDetalle['inventario_documental_detalle']['descripcion'] ) );
                $sheet->setCellValueByColumnAndRow(6, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $transferenciaPrimariaDetalle['fecha_inicio'] ? $transferenciaPrimariaDetalle['fecha_inicio']->format('d/m/Y') : $transferenciaPrimariaDetalle['inventario_documental_detalle']['fecha_inicio']->format('d/m/Y') );
                $sheet->setCellValueByColumnAndRow(7, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'],
                    $transferenciaPrimariaDetalle['fecha_final'] ? $transferenciaPrimariaDetalle['fecha_final']->format('d/m/Y') : ($transferenciaPrimariaDetalle['inventario_documental_detalle']['fecha_final'] ? $transferenciaPrimariaDetalle['inventario_documental_detalle']['fecha_final']->format('d/m/Y') : $transferenciaPrimariaDetalle['inventario_documental_detalle']['fecha_inicio']->format('d/m/Y')) );
                $sheet->setCellValueByColumnAndRow(8, $datosExcel['posicionFilaExpediente']+$datosExcel['contadorRegistrosPagina'], $transferenciaPrimariaDetalle['observaciones'] ?? $transferenciaPrimariaDetalle['inventario_documental_detalle']['observaciones'] );

                $datosExcel['contadorRegistrosPagina']++;
                //CONDICIÓN QUE VERIFICA SI SE HA LLEGADO AL NUMERO MÁXIMO DE REGISTROS POR HOJA
                if( ($index+1) == ($datosExcel['registrosPorHoja']*$datosExcel['numHoja']) ){
                    //AUMENTA EL CONTADOR DE HOJAS
                    $datosExcel['numHoja']++;
                    //CLONA LA LA HOJA PLANTILLA PARA CREAR LA NUEVA HOJA A UTILIZAR
                    $sheet = clone $sheetOrigin2;
                    //ESTABLECE EL TITULO DE LA NUEVA HOJA
                    $sheet->setTitle("{$seccion}.{$serie}-{$datosExcel['numHoja']}");
                    //AGREGA LA HOJA DUPLICADA DE LA PLANTILLA COMO UNA NUEVA HOJA EN EL DOCUMENTO
                    $spread->addSheet($sheet);
                    //DEFINE LOS ENCABEZADOS DE LA NUEVA HOJA
                    $sheet = $this->encabezados_transferencia_primaria($transferenciaPrimaria, $sheet, $datosExcel['totalHojas'], $datosExcel['numHoja']);
                    $sheet = $this->firmas_transferencia_primaria($transferenciaPrimaria, $sheet);
                    //REINICIA EL CONTADOR DE NUMERO DE REGISTRO POR HOJA
                    $datosExcel['contadorRegistrosPagina'] = 0;
                }
            }
            //ELIMINA LAS HOJAS PLANTILLA DEL DOCUMENTO FINAL
            $spread->removeSheetByIndex(0);
            $spread->removeSheetByIndex(0);

            $this->descargar_exel_generado($spread, mb_strtoupper("Transferencia-Primaria-{$anio}_{$seccion}.{$serie}","UTF-8").".xlsx" );

        }catch (\Exception $e){
//            echo 'Error al generar Inventario de Transferencia Primaria';
            echo $e->getMessage();
        }
    }

    public function exportar_cadido(Cadido $cadido){
        $anio = $cadido->anio;
        $serie = $cadido->serie->clave;
        $seccion = $cadido->seccion->clave;

        try{
            //HELPER HTML PARA AGREGAR TEXTO ENRIQUECIDO A LAS CELDAS
            $html = new Html();
            //OBTIENE EL ARCHIVO PLANTILLA XLSX PARA CREAR CADIDO
            $spread = IOFactory::load( public_path("excel_plantillas/plantilla_cadido.xlsx") );

            //ALMACENA LA PRIMERA HOJA DE LA PLANTILLA
            $sheet = $spread->getSheet(0);
            //DEFINE EL TITULO DE LA PRIMERA HOJA
            $sheet->setTitle("{$seccion}.{$serie}");

            //DEFINE LOS ENCABEZADOS DE LA PRIMERA HOJA
            $sheet = $this->encabezados_cadido($cadido, $sheet, 1, 1);
            $sheet = $this->firmas_cadido($sheet);

            $sheet = $this->definirDatosCadido($sheet, $cadido);

            $this->descargar_exel_generado($spread, mb_strtoupper("CADIDO-{$anio}_{$seccion}.{$serie}","UTF-8").".xlsx" );
        }catch (\PhpOffice\PhpSpreadsheet\Writer\Exception | \PhpOffice\PhpSpreadsheet\Exception $e){
            echo 'Error al generar CADIDO.';
        }
    }

    public function exportar_cadido_multiple($anio, $seccionId = null){
        try {
            $cadidos = Cadido::where('anio',$anio);
            if( $seccionId ){
                $cadidos = $cadidos->where('seccion_id',$seccionId);
            }
            $cadidos = $cadidos->orderBy('serie_id')->get();

            //OBTIENE EL ARCHIVO PLANTILLA XLSX PARA CREAR CADIDO
            $spread = IOFactory::load( public_path("excel_plantillas/plantilla_cadido.xlsx") );
            //ALMACENA LA PRIMERA HOJA DE LA PLANTILLA
            $sheetOrigin = $spread->getSheet(0);
            $numHojas = $cadidos->count();
            foreach ($cadidos as $index => $cadido) {
                $anio = $cadido->anio;
                $serie = $cadido->serie->clave;
                $seccion = $cadido->seccion->clave;

                //DUPLICA LA PRIMERA HOJA DE LA PLANTILLA PARA TRABAJAR SOBRE ELLA SIN MODIFICAR EL FORMATO ORIGINAl
                $sheet = clone $sheetOrigin;
                //DEFINE EL TITULO DE LA PRIMERA HOJA
                $sheet->setTitle("{$seccion}.{$serie}");

                $sheet = $this->encabezados_cadido( $cadido, $sheet, $numHojas, ($index+1) );
                $sheet = $this->firmas_cadido($sheet);

                $sheet = $this->definirDatosCadido($sheet, $cadido);

                $spread->addSheet($sheet);
            }
            $spread->removeSheetByIndex(0);
            $cadidoNombre = mb_strtoupper($seccionId ? "CADIDO-{$anio}_{$seccion}" : "CADIDO-{$anio}","UTF-8");
            $this->descargar_exel_generado($spread, $cadidoNombre.".xlsx" );
        }catch (\Exception $e){
            //echo "Error al generar CADIDO.";
            echo $e->getMessage();
        }
    }

    private function definirDatosCadido($sheet, $cadido){
        $sheet->setCellValueByColumnAndRow(1, 11, $cadido->seccion->clave.".".$cadido->serie->clave);//CÓDIGO
        $sheet->setCellValueByColumnAndRow(2, 11, $cadido->serie->nombre);//SERIE

        $sheet->setCellValueByColumnAndRow(4, 11, $cadido->valor_primario_administrativa ?? "-");//VALOR PRIMARIO ADMINISTRATIVO
        $sheet->setCellValueByColumnAndRow(5, 11, $cadido->valor_primario_fiscal ?? "-");//VALOR PRIMARIO FISCAL
        $sheet->setCellValueByColumnAndRow(6, 11, $cadido->valor_primario_legal ?? "-");//VALOR PRIMARIO LEGAL

        $sheet->setCellValueByColumnAndRow(7, 11, $cadido->valor_secundario_evidencial ? "X" : "-");//VALOR SECUNDARIO EVIDENCIAL
        $sheet->setCellValueByColumnAndRow(8, 11, $cadido->valor_secundario_testimonial ? "X" : "-");//VALOR SECUNDARIO TESTIMONIAL
        $sheet->setCellValueByColumnAndRow(9, 11, $cadido->valor_secundario_informativo ? "X" : "-");//VALOR SECUNDARIO INFORMATIVO

        $sheet->setCellValueByColumnAndRow(10, 11, $cadido->tiempo_guarda_tramite);//TIEMPO DE GUARDA TRAMITE
        $sheet->setCellValueByColumnAndRow(11, 11, $cadido->tiempo_guarda_concentracion);//TIEMPO DE GUARDA CONCENTRACIÓN

        $sheet->setCellValueByColumnAndRow(13, 11, $cadido->fundamento_legal);//FUNDAMENTO LEGAL

        $sheet->setCellValueByColumnAndRow(14, 11, $cadido->clasificacion_publica ? "X" : "-");//CLASIFICACIÓN PUBLICA
        $sheet->setCellValueByColumnAndRow(15, 11, $cadido->clasificacion_reservada ? "X" : "-");//CLASIFICACIÓN RESERVADA
        $sheet->setCellValueByColumnAndRow(16, 11, $cadido->clasificacion_confidencial ? "X" : "-");//CLASIFICACIÓN CONFIDENCIAL

        if( $cadido->destino_final == config('enums.validar_destino_final')[0] ){
            $sheet->setCellValueByColumnAndRow(18, 11, "X");//DESTINO BAJA
            $sheet->setCellValueByColumnAndRow(19, 11, "-");//DESTINO ARCHIVO HISTÓRICO
            $sheet->setCellValueByColumnAndRow(20, 11, "-");//DESTINO MUESTREO
        }elseif( $cadido->destino_final == config('enums.validar_destino_final')[1] ){
            $sheet->setCellValueByColumnAndRow(18, 11, "-");//DESTINO BAJA
            $sheet->setCellValueByColumnAndRow(19, 11, "X");//DESTINO ARCHIVO HISTÓRICO
            $sheet->setCellValueByColumnAndRow(20, 11, "-");//DESTINO MUESTREO
        }else{
            $sheet->setCellValueByColumnAndRow(18, 11, "-");//DESTINO BAJA
            $sheet->setCellValueByColumnAndRow(19, 11, "-");//DESTINO ARCHIVO HISTÓRICO
            $sheet->setCellValueByColumnAndRow(20, 11, "X");//DESTINO MUESTREO
        }

        $sheet->setCellValueByColumnAndRow(21, 11, $cadido->particularidades);//PARTICULARIDADES

        return $sheet;
    }

    public function exportar_portada(TransferenciaPrimariaDetalle $transferenciaPrimariaDetalle){
        $transferenciaPrimariaDetalle->load('inventario_documental_detalle');
        $transferenciaPrimariaDetalle->load('transferencia_primaria');
        $cadido = Cadido::where('anio',$transferenciaPrimariaDetalle->transferencia_primaria->anio)->where('seccion_id',$transferenciaPrimariaDetalle->transferencia_primaria->seccion_id)->where('serie_id',$transferenciaPrimariaDetalle->transferencia_primaria->serie_id)->first();
        if( !$cadido ){
             echo 'Se requiere registrar el CADIDO correspondiente antes de generar la portada.';
             return;
        }
        $seccion = $transferenciaPrimariaDetalle->transferencia_primaria->seccion->clave;
        $serie = $transferenciaPrimariaDetalle->transferencia_primaria->serie->clave;
        $anio = $transferenciaPrimariaDetalle->transferencia_primaria->anio;

        try{
            //HELPER HTML PARA AGREGAR TEXTO ENRIQUECIDO A LAS CELDAS
            $html = new Html();
            //OBTIENE EL ARCHIVO PLANTILLA XLSX
            $spread = IOFactory::load( public_path("excel_plantillas/plantilla_portada.xlsx") );

            //ALMACENA LA PRIMERA HOJA DE LA PLANTILLA
            $sheet = $spread->getSheet(0);

            //DEFINE EL TITULO DE LA PRIMERA HOJA
            $numExp = explode('/',$transferenciaPrimariaDetalle->no_expediente_legajo)[1];
            $sheet->setTitle("{$seccion}.{$serie}-{$numExp}");

            $sheet = $this->definirDatosPortada($sheet, $transferenciaPrimariaDetalle, $cadido, $seccion, $serie);

            $this->descargar_exel_generado($spread, mb_strtoupper("PORTADA-{$seccion}.{$serie}-{$numExp}","UTF-8").".xlsx" );
        }catch (\PhpOffice\PhpSpreadsheet\Writer\Exception | \PhpOffice\PhpSpreadsheet\Exception $e){
            echo 'Error al generar PORTADA.';
            return;
        }


    }

    public function exportar_multiples_portadas(TransferenciaPrimaria $transferenciaPrimaria){
        $rutaZip = null;
        $dirTemporal = null;
        try{
            $this->crearCarpetaTemporal();

            //OBTIENE EL ARCHIVO PLANTILLA XLSX
            $spread = IOFactory::load( public_path("excel_plantillas/plantilla_portada.xlsx") );
            //ALMACENA LA PRIMERA HOJA DE LA PLANTILLA
            $sheetOrigen = $spread->getSheet(0);

            $seccion = $transferenciaPrimaria->seccion->clave;
            $serie = $transferenciaPrimaria->serie->clave;
            $anio = $transferenciaPrimaria->anio;
            $numPortadasDoc = 0;//NUMERO DE PORTADAS POR DOCUMENTO
            $numDoc = 1;//NUMERO DE DOCUMENTOS GENERADOS
            $nombreCarpetaTemporal = auth()->user()->id.$anio.$seccion.$serie;//NOMBRE DE LA CARPETA TEMPORAL DONDE SE GUARDARAN LOS DOCUMENTOS XLSX
            $dirTemporal = public_path(config('app.carpeta_docs_temporal')).$nombreCarpetaTemporal;//RUTA DE LA CARPETA TEMPORAL
            //GENERA LA CARPETA TEMPORAL DONDE ALMACENARA LOS ARCHIVOS
            if( !file_exists($dirTemporal) ){
                mkdir($dirTemporal, 0777, true);
            }
            // $detalles = $transferenciaPrimaria->transferencia_primaria_detalles;

            $countDetalles = $transferenciaPrimaria->transferencia_primaria_detalles()->count();

            $transferenciaPrimaria->transferencia_primaria_detalles()
                ->with(['transferencia_primaria', 'inventario_documental_detalle'])
                ->chunk(500,
                    function ($group)
                    use (
                        $numPortadasDoc,
                        $sheetOrigen,
                        $seccion,
                        $serie,
                        $spread,
                        $dirTemporal,
                        $numDoc,
                        $countDetalles
                    ) {
                        foreach ($group as $index => $transferenciaPrimariaDetalle) {
                            $numPortadasDoc++;
                            // $transferenciaPrimariaDetalle->load('inventario_documental_detalle');
                            // $transferenciaPrimariaDetalle->load('transferencia_primaria');
                            $cadido = Cadido::where('anio',
                                $transferenciaPrimariaDetalle->transferencia_primaria->anio)->where('seccion_id',
                                $transferenciaPrimariaDetalle->transferencia_primaria->seccion_id)->where('serie_id',
                                $transferenciaPrimariaDetalle->transferencia_primaria->serie_id)->first();
                            if (!$cadido) {
                                throw new \Exception('Se requiere registrar el CADIDO correspondiente antes de generar la portada.');
                            }
                            $sheet = clone $sheetOrigen;
                            //DEFINE EL TITULO DE LA HOJA
                            $numExp = explode('/', $transferenciaPrimariaDetalle->no_expediente_legajo)[1];
                            $sheet->setTitle("{$seccion}.{$serie}-{$numExp}");

                            $sheet = $this->definirDatosPortada($sheet, $transferenciaPrimariaDetalle, $cadido,
                                $seccion, $serie);

                            $spread->addSheet($sheet);
                            if ($numPortadasDoc === 300 || ($index + 1) === $countDetalles) {
                                $spread->removeSheetByIndex(0);
                                $writer = IOFactory::createWriter($spread, 'Xlsx');
                                $writer->save($dirTemporal.'/'."PORTADAS-{$seccion}.{$serie}-{$numDoc}.xlsx");
                                $spread = IOFactory::load(public_path('excel_plantillas/plantilla_portada.xlsx'));;
                                $numDoc++;
                                $numPortadasDoc = 0;
                            }
                        }

                    });

            /*foreach ( $detalles as $index => $transferenciaPrimariaDetalle) {
                $numPortadasDoc++;
                $transferenciaPrimariaDetalle->load('inventario_documental_detalle');
                $transferenciaPrimariaDetalle->load('transferencia_primaria');
                $cadido = Cadido::where('anio',$transferenciaPrimariaDetalle->transferencia_primaria->anio)->where('seccion_id',$transferenciaPrimariaDetalle->transferencia_primaria->seccion_id)->where('serie_id',$transferenciaPrimariaDetalle->transferencia_primaria->serie_id)->first();
                if( !$cadido ){
                    throw new \Exception('Se requiere registrar el CADIDO correspondiente antes de generar la portada.');
                }
                $sheet = clone $sheetOrigen;
                //DEFINE EL TITULO DE LA HOJA
                $numExp = explode('/',$transferenciaPrimariaDetalle->no_expediente_legajo)[1];
                $sheet->setTitle("{$seccion}.{$serie}-{$numExp}");

                $sheet = $this->definirDatosPortada($sheet, $transferenciaPrimariaDetalle, $cadido, $seccion, $serie);

                $spread->addSheet($sheet);
                if( $numPortadasDoc === 300 || ($index+1) === $transferenciaPrimaria->transferencia_primaria_detalles->count() ){
                    $spread->removeSheetByIndex(0);
                    $writer = IOFactory::createWriter($spread, "Xlsx");
                    $writer->save($dirTemporal."/"."PORTADAS-{$seccion}.{$serie}-{$numDoc}.xlsx");
                    $spread = IOFactory::load( public_path("excel_plantillas/plantilla_portada.xlsx") );;
                    $numDoc++;
                    $numPortadasDoc = 0;
                }
            }*/

            $zip = new \ZipArchive();//CREA EL OBJETO ZIP
            $nombreZip = "PORTADAS {$anio}-{$seccion}.{$serie}.zip";//NOMBRE DEL ARCHIVO ZIP
            $rutaZip = public_path(config('app.carpeta_docs_temporal')."/".$nombreZip);//RUTA DEL ARCHIVO ZIP

            //GENERA EL ARCHIVO ZIP
            if ( $zip->open($rutaZip, \ZipArchive::CREATE) === true ) {
                $files = File::files($dirTemporal);//OBTIENE LOS ARCHIVOS XLSX DE LA CARPETA TEMPORAL
                //RECORRE LOS ARCHIVOS Y LOS AGREGA AL ARCHIVO ZIP
                foreach ($files as $key => $value){
                    $relativeName = basename($value);
                    $zip->addFile($value, $relativeName);
                }
                $zip->close();
            }else{
                throw new \Exception("No se pudo crear el archivo zip");
            }
            $files = glob($dirTemporal.'/*'); //OBTIENE LOS NOMBRES DE LOS ARCHIVOS DE LA CARPETA TEMPORAL
            foreach($files as $file){
                if(is_file($file))
                    unlink($file);
            }
            rmdir($dirTemporal);

            header("Content-disposition: attachment; filename={$nombreZip}");
            header("Content-type: application/zip");
            readfile($rutaZip);
            unlink($rutaZip);
            return;
        }catch (\Exception $e){
            if(file_exists($dirTemporal)){
                $files = glob($dirTemporal.'/*'); //OBTIENE LOS NOMBRES DE LOS ARCHIVOS DE LA CARPETA TEMPORAL
                foreach($files as $file){
                    if(is_file($file))
                        unlink($file);
                }
                rmdir($dirTemporal);
            }
            if(file_exists($rutaZip)){
                unlink($rutaZip);
            }
            dd($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
        }
    }

    //CREA CARPETA PARA ALMACENAR ARCHIVOS TEMPORALES EN CASO DE NO EXISTIR
    private function crearCarpetaTemporal(){
        if( !file_exists(public_path(config('app.carpeta_docs_temporal'))) ){
            mkdir(public_path(config('app.carpeta_docs_temporal')), 0777, true);
        }
    }

    private function definirDatosPortada($sheet, $transferenciaPrimariaDetalle, $cadido, $seccion, $serie){
        $sheet->setCellValue('M5',$transferenciaPrimariaDetalle->transferencia_primaria->departamento->area->nombre);//UNIDAD ADMINISTRATIVA
        $sheet->setCellValue('C10',$seccion.' '.$transferenciaPrimariaDetalle->transferencia_primaria->seccion->nombre);//SECCIÓN
        $sheet->setCellValue('C13',$seccion.'.'.$serie.' '.$transferenciaPrimariaDetalle->transferencia_primaria->serie->nombre);//SERIE
        $sheet->setCellValue('F15',mb_strtoupper( config('app.acronyms_organization').'-'.$transferenciaPrimariaDetalle->transferencia_primaria->departamento->clave.'*'.$transferenciaPrimariaDetalle->transferencia_primaria->seccion->clave.'.'.$transferenciaPrimariaDetalle->transferencia_primaria->serie->clave,'UTF-8' ) );//CÓDIGO
        $sheet->setCellValue('R10',$transferenciaPrimariaDetalle->descripcion ?? $transferenciaPrimariaDetalle->inventario_documental_detalle->descripcion);//DESCRIPCIÓN (ASUNTO)

        $sheet->setCellValue('I19',$transferenciaPrimariaDetalle->fecha_inicio ? $transferenciaPrimariaDetalle->fecha_inicio->format('d/m/Y') : $transferenciaPrimariaDetalle->inventario_documental_detalle->fecha_inicio->format('d/m/Y') );//FECHA DE APERTURA
        $sheet->setCellValue('I21', $transferenciaPrimariaDetalle->fecha_final ? $transferenciaPrimariaDetalle->fecha_final->format('d/m/Y') : ($transferenciaPrimariaDetalle->inventario_documental_detalle->fecha_final ? $transferenciaPrimariaDetalle->inventario_documental_detalle->fecha_final->format('d/m/Y') : $transferenciaPrimariaDetalle->inventario_documental_detalle->fecha_inicio->format('d/m/Y')) );//FECHA DE CIERRE

        $sheet->setCellValue('X19',$transferenciaPrimariaDetalle->no_expediente_legajo);//NO. EXPEDIENTE
        $sheet->setCellValue('X21',$transferenciaPrimariaDetalle->no_fojas);//NO. HOJAS

        $sheet->setCellValue('C26',$cadido->fundamento_legal);//FUNDAMENTO LEGAL

        $sheet->setCellValue('H34', $cadido->valor_primario_administrativa ?? "-");//VALOR PRIMARIO ADMINISTRATIVO
        $sheet->setCellValue('H36', $cadido->valor_primario_fiscal ?? "-");//VALOR PRIMARIO FISCAL
        $sheet->setCellValue('H38', $cadido->valor_primario_legal ?? "-");//VALOR PRIMARIO LEGAL

        $sheet->setCellValue('R34', $cadido->valor_secundario_evidencial ? "X" : "-");//VALOR SECUNDARIO EVIDENCIAL
        $sheet->setCellValue('R36', $cadido->valor_secundario_testimonial ? "X" : "-");//VALOR SECUNDARIO TESTIMONIAL
        $sheet->setCellValue('R38', $cadido->valor_secundario_informativo ? "X" : "-");//VALOR SECUNDARIO INFORMATIVO

        $sheet->setCellValue('AA34', $cadido->clasificacion_publica ? "X" : "-");//CLASIFICACIÓN PUBLICA
        $sheet->setCellValue('AA36', $cadido->clasificacion_reservada ? "X" : "-");//CLASIFICACIÓN RESERVADA
        $sheet->setCellValue('AA38', $cadido->clasificacion_confidencial ? "X" : "-");//CLASIFICACIÓN CONFIDENCIAL

        $sheet->setCellValue('F44', $cadido->tiempo_guarda_tramite.' años');//VIGENCIA DOCUMENTAL TRÁMITE
        $sheet->setCellValue('P44', $cadido->tiempo_guarda_concentracion.' años');//VIGENCIA DOCUMENTAL CONCENTRACIÓN

        $sheet->setCellValue('W44', $transferenciaPrimariaDetalle->portada_observaciones);//OBSERVACIONES

        $sheet->setCellValue('C49', $transferenciaPrimariaDetalle->inventario_documental_detalle->ubicacion_fisica);//UBICACIÓN FÍSICA

        if( $cadido->destino_final == config('enums.validar_destino_final')[0] ){
            $sheet->setCellValue('I56', "X");//DESTINO BAJA
            $sheet->setCellValue('I58', "-");//DESTINO ARCHIVO HISTÓRICO
            $sheet->setCellValue('I60', "-");//DESTINO MUESTREO
        }elseif( $cadido->destino_final == config('enums.validar_destino_final')[1] ){
            $sheet->setCellValue('I56', "-");//DESTINO BAJA
            $sheet->setCellValue('I58', "X");//DESTINO ARCHIVO HISTÓRICO
            $sheet->setCellValue('I60', "-");//DESTINO MUESTREO
        }else{
            $sheet->setCellValue('I56', "-");//DESTINO BAJA
            $sheet->setCellValue('I58', "-");//DESTINO ARCHIVO HISTÓRICO
            $sheet->setCellValue('I60', "X");//DESTINO MUESTREO
        }

        if( $transferenciaPrimariaDetalle->portada_fechas_consulta ){
            $fechasConsulta = json_decode($transferenciaPrimariaDetalle->portada_fechas_consulta);
            $numFila = 56;
            foreach ($fechasConsulta as $fecha) {
                $sheet->setCellValue('U'.$numFila, $fecha);//FECHA DE CONSULTA
                $numFila = $numFila+2;
            }
        }

        return $sheet;
    }


    private function encabezados_cadido($cadido, $sheet, $totalHojas  = null, $numHoja = null){
        //AGREGA LOS DATOS EN EL ENCABEZADOS DEL DOCUMENTO
        $sheet->setCellValueByColumnAndRow( 1, 6, $sheet->getCellByColumnAndRow(1,6)." ".mb_strtoupper( $cadido->seccion->clave." ".$cadido->seccion->nombre,'UTF-8' ) );

        $sheet->setCellValueByColumnAndRow( 18, 3, $sheet->getCellByColumnAndRow(18,3)." ".$cadido->anio );//FECHA DE EMISIÓN
        //AGREGA EL NUMERO DE HOJA
        if( $numHoja && $totalHojas ){
            $sheet->setCellValueByColumnAndRow( 18, 5, "HOJA {$numHoja} DE {$totalHojas}" );//NUMERO DE HOJA
        }
        return $sheet;
    }

    private function firmas_cadido($sheet){
        $firmas = Firmas::obtenerFirmasCadido();
        if( $firmas ){
            $sheet->setCellValueByColumnAndRow(1,15,$firmas->elaboro[0]['nombre']);
            $sheet->setCellValueByColumnAndRow(1,16,$firmas->elaboro[0]['cargo']);

            $sheet->setCellValueByColumnAndRow(12,15,$firmas->autorizo[0]['nombre']);
            $sheet->setCellValueByColumnAndRow(12,16,$firmas->autorizo[0]['cargo']);

            $sheet->setCellValueByColumnAndRow(19,15,$firmas->valido[0]['nombre']);
            $sheet->setCellValueByColumnAndRow(19,16,$firmas->valido[0]['cargo']);
        }

        return $sheet;
    }

    private function encabezados_transferencia_primaria($transferencia, $sheet, $totalHojas  = null, $numHoja = null){
        //AGREGA LOS DATOS EN EL ENCABEZADOS DEL DOCUMENTO
        if( $numHoja == 1 ){
            $sheet->setCellValueByColumnAndRow( 1, 4, $sheet->getCellByColumnAndRow(1,4)." ".mb_strtoupper( $transferencia->departamento->area->nombre,'UTF-8' ) );//UNIDAD ADMINISTRATIVA
            $sheet->setCellValueByColumnAndRow( 1, 5, $sheet->getCellByColumnAndRow(1,5)." ".mb_strtoupper( $transferencia->departamento->clave.'* '.$transferencia->departamento->nombre,'UTF-8' ) );//ÁREA GENERADORA
            $sheet->setCellValueByColumnAndRow( 1, 6, $sheet->getCellByColumnAndRow(1,6)." ".mb_strtoupper( $transferencia->seccion->clave.' '.$transferencia->seccion->nombre,'UTF-8' ) );//SECCIÓN
            $sheet->setCellValueByColumnAndRow( 1, 7, $sheet->getCellByColumnAndRow(1,7)." ".mb_strtoupper( $transferencia->seccion->clave.'.'.$transferencia->serie->clave.' '.$transferencia->serie->nombre,'UTF-8' ) );//SERIE

            $sheet->setCellValueByColumnAndRow( 6, 3, $sheet->getCellByColumnAndRow(6,3)." ".mb_strtoupper( $transferencia->fecha_entrega ? $transferencia->fecha_entrega->format('d/m/Y') : '','UTF-8' ) );//FECHA DE ENTREGA
            $sheet->setCellValueByColumnAndRow( 6, 4, $sheet->getCellByColumnAndRow(6,4)." ".mb_strtoupper( $transferencia->no_oficio_transferencia,'UTF-8' ) );//NO. DE OFICIO
            $sheet->setCellValueByColumnAndRow( 6, 5, $sheet->getCellByColumnAndRow(6,5)." ".mb_strtoupper( $transferencia->no_caja,'UTF-8' ) );//NO. CAJA
            $sheet->setCellValueByColumnAndRow( 6, 6, $sheet->getCellByColumnAndRow(6,6)." ".mb_strtoupper( $transferencia->transferencia_primaria_detalles->count(),'UTF-8' ) );//TOTAL DE EXPEDIENTES
            $sheet->setCellValueByColumnAndRow( 6, 8, $sheet->getCellByColumnAndRow(6,8)." ".mb_strtoupper( config('app.acronyms_organization').'-'.$transferencia->departamento->clave.'*'.$transferencia->seccion->clave.'.'.$transferencia->serie->clave,'UTF-8' ) );//CÓDIGO
        }else{
            $sheet->setCellValueByColumnAndRow( 6, 3, $sheet->getCellByColumnAndRow(6,3)." ".mb_strtoupper( $transferencia->no_oficio_transferencia,'UTF-8' ) );//NO. DE OFICIO
            $sheet->setCellValueByColumnAndRow( 6, 4, $sheet->getCellByColumnAndRow(6,4)." ".mb_strtoupper( $transferencia->no_caja,'UTF-8' ) );//NO. CAJA
            $sheet->setCellValueByColumnAndRow( 6, 5, $sheet->getCellByColumnAndRow(6,5)." ".mb_strtoupper( $transferencia->transferencia_primaria_detalles->count(),'UTF-8' ) );//TOTAL DE EXPEDIENTES
        }


        //AGREGA EL NUMERO DE HOJA
        if( $numHoja && $totalHojas ){
            if( $numHoja == 1 ){
                $sheet->setCellValueByColumnAndRow( 6, 7, "HOJA {$numHoja} DE {$totalHojas}" );//NUMERO DE HOJA
            }else{
                $sheet->setCellValueByColumnAndRow( 6, 6, "HOJA {$numHoja} DE {$totalHojas}" );//NUMERO DE HOJA
            }

        }
        return $sheet;
    }

    private function firmas_transferencia_primaria($transferencia, $sheet){
        $firmas = Firmas::obtenerFirmasTransferenciaPrimaria($transferencia->departamento_id);
        if( $firmas ){
            $sheet->setCellValueByColumnAndRow(1,27,$firmas->elaboro[0]['nombre']);
            $sheet->setCellValueByColumnAndRow(1,28,$firmas->elaboro[0]['cargo']);

            $sheet->setCellValueByColumnAndRow(3,27,$firmas->reviso[0]['nombre']);
            $sheet->setCellValueByColumnAndRow(3,28,$firmas->reviso[0]['cargo']);
            $sheet->setCellValueByColumnAndRow(5,27,$firmas->reviso[1]['nombre']);
            $sheet->setCellValueByColumnAndRow(5,28,$firmas->reviso[1]['cargo']);
            $sheet->setCellValueByColumnAndRow(6,27,$firmas->reviso[2]['nombre']);
            $sheet->setCellValueByColumnAndRow(6,28,$firmas->reviso[2]['cargo']);

            $sheet->setCellValueByColumnAndRow(9,27,$firmas->recibio[0]['nombre']);
            $sheet->setCellValueByColumnAndRow(9,28,$firmas->recibio[0]['cargo']);
        }

        return $sheet;
    }

    private function encabezados_inventario_documental($inventarioDocumental, $sheet, $totalHojas  = null, $numHoja = null){
        //AGREGA LOS DATOS EN EL ENCABEZADOS DEL DOCUMENTO
        if( $numHoja < 2 ){
            $sheet->setCellValueByColumnAndRow( 1, 4, $sheet->getCellByColumnAndRow(1,4)." ".mb_strtoupper( $inventarioDocumental->area->codigo.'* '.$inventarioDocumental->area->nombre,'UTF-8' ) );//UNIDAD ADMINISTRATIVA
            $sheet->setCellValueByColumnAndRow( 1, 5, $sheet->getCellByColumnAndRow(1,5)." ".mb_strtoupper( $inventarioDocumental->departamento->clave.'* '.$inventarioDocumental->departamento->nombre,'UTF-8' ) );//ÁREA GENERADORA
            $sheet->setCellValueByColumnAndRow( 1, 6, $sheet->getCellByColumnAndRow(1,6)." ".mb_strtoupper( $inventarioDocumental->persona_responsable->nombre_completo_profesion,'UTF-8' ) );//RESPONSABLE
            $sheet->setCellValueByColumnAndRow( 1, 7, $sheet->getCellByColumnAndRow(1,7)." ".mb_strtoupper( $inventarioDocumental->seccion->clave.'. '.$inventarioDocumental->seccion->nombre,'UTF-8' ) );//SECCIÓN
            $sheet->setCellValueByColumnAndRow( 1, 8, $sheet->getCellByColumnAndRow(1,8)." ".mb_strtoupper( $inventarioDocumental->seccion->clave.'.'.$inventarioDocumental->serie->clave.'. '.$inventarioDocumental->serie->nombre,'UTF-8' ) );//SERIE
        }


        $sheet->setCellValueByColumnAndRow( 5, 3, $inventarioDocumental->fecha_cierre_captura ? $sheet->getCellByColumnAndRow(5,3)." ".mb_strtoupper( config('app.meses')[$inventarioDocumental->fecha_cierre_captura->format('n')].' '.$inventarioDocumental->fecha_cierre_captura->format('Y'),'UTF-8' ) : $sheet->getCellByColumnAndRow(5,3) );//FECHA DE EMISIÓN
        $sheet->setCellValueByColumnAndRow( 5, 7, $sheet->getCellByColumnAndRow(5,7)." ".mb_strtoupper( config('app.acronyms_organization').'-'.$inventarioDocumental->departamento->clave.'*'.$inventarioDocumental->clave_seccion.'.'.$inventarioDocumental->clave_serie,'UTF-8' ) );//CÓDIGO
//        $sheet->setCellValueByColumnAndRow( 5, 8, $sheet->getCellByColumnAndRow(5,8)." ".mb_strtoupper( config('app.meses')[$inventarioDocumental->mes_captura],'UTF-8' ) );//MES QUE REPORTA
        //AGREGA EL NUMERO DE HOJA
        if( $numHoja && $totalHojas ){
            $sheet->setCellValueByColumnAndRow( 5, 4, "HOJA {$numHoja} DE {$totalHojas}" );//NUMERO DE HOJA
        }
        return $sheet;
    }

    private function firmas_inventario_documental($inventarioDocumental, $sheet){
        $firmas = Firmas::obtenerFirmasInventarioDocumental($inventarioDocumental->departamento_id);
        if( $firmas ){
            $sheet->setCellValueByColumnAndRow(1,27,$firmas->elaboro[0]['nombre']);
            $sheet->setCellValueByColumnAndRow(1,28,$firmas->elaboro[0]['cargo']);

            $sheet->setCellValueByColumnAndRow(4,27,$firmas->reviso[0]['nombre']);
            $sheet->setCellValueByColumnAndRow(4,28,$firmas->reviso[0]['cargo']);

            $sheet->setCellValueByColumnAndRow(7,27,$firmas->valido[0]['nombre']);
            $sheet->setCellValueByColumnAndRow(7,28,$firmas->valido[0]['cargo']);
        }

        return $sheet;
    }


    private function formatear_descripcion_expediente( $descripcionExpediente ){
        //HELPER HTML PARA AGREGAR TEXTO ENRIQUECIDO A LAS CELDAS
        $html = new Html();

        //SEPARA LA DESCRIPCIÓN PARA OBTENER EL NOMBRE DEL EXPEDIENTE Y DESCRIPCIÓN POR SEPARADO
        $partesDescripcion = explode('.-', $descripcionExpediente);
        //SI EL ARREGLO DE LA DESCRIPCIÓN TIENE MAS DE 1 ELEMENTO MARCARA EL NOMBRE DE EXPEDIENTE EN NEGRITAS Y POSTERIORMENTE UNIRA CON EL RESTO DE DESCRIPCIÓN
        if( count($partesDescripcion) > 1 ){
            //GENERA EL TITULO EN NEGRITAS USANDO ETIQUETAS HTML
            $descripcion = "<b>{$partesDescripcion[0]}.-</b>";
            //RECORRE EL RESTO DE ELEMENTO DE LA DESCRIPCIÓN Y LOS UNE CON EL TITULO
            foreach ($partesDescripcion as $num => $val) {
                if($num > 0){
                    $descripcion.= " {$val}";
                }
            }
        }else{
            $descripcion = $descripcionExpediente;
        }
        //CONVIERTE EL TEXTO CON ETIQUETAS HTML EN TEXTO ENRIQUECIDO
        return $html->toRichTextObject($descripcion);
    }


    private function descargar_exel_generado($spread, $fileName = 'excel.xlsx'){
        //Crear un "escritor"
        $writer = IOFactory::createWriter($spread, 'Xlsx');
        //        $writer = new Xlsx($spread);
        //DEFINE LOS ENCABEZADOS
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        //Le pasamos la ruta de guardado
        $writer->save('php://output');
        exit();
    }


    public function descargar_plantilla($doc){
        $file = public_path("excel_plantillas/".$doc);
        return response()->download($file);
    }


}
