<?php

namespace App\Http\Controllers\archivo;

use App\Http\Controllers\Controller;
use App\Http\Requests\archivo\StoreInventarioDocumentalDetalleRequest;
use App\Http\Requests\BuscarExpedientesTransferenciaRequest;
use App\Models\archivo\ArchivoSeccion;
use App\Models\archivo\ArchivoSerie;
use App\Models\archivo\InventarioDocumental;
use App\Models\archivo\InventarioDocumentalDetalle;
use App\Models\archivo\ArchivoCalendario;
use App\Models\Area;
use App\Models\Departamento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class InventarioDocumentalesController extends Controller
{


    public function index(){
        $areas = Area::get();
        $anios_reporte = ArchivoCalendario::obtener_anios_hasta_actual();

        return view('archivo.reporte_inventarios', compact('areas', 'anios_reporte'));
    }


    /**
     * Buscar registro maestro específico
     * @param $area
     * @param $anio_captura
     * @param $mes_captura
     * @param $seccion
     * @param $serie
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscar($anio_captura, $mes_captura, $seccion, $serie )
    {
        /** @var InventarioDocumental $inventario */
        $inventarioDocumental = InventarioDocumental::buscar($anio_captura, $mes_captura, ArchivoSeccion::find($seccion)->clave, ArchivoSerie::find($serie)->clave )->first();

       return $this->responseJsonSuccess(compact('inventarioDocumental'));
    }

    public function buscarInventarioDocumentalDetalles(Request $request){
        $request->validate([
            'inventario_id' => ['required','integer','exists:inventario_documentales,id'],
        ]);
        $inventario_detalle = InventarioDocumentalDetalle::paginarDetalles($request);
        return $this->responseJsonSuccess(compact('inventario_detalle'));
    }

    public function importar_inventario(Request $request,$anio_captura, $mes_captura, $seccion, $serie){
        if( $request->hasFile('inventarioExcel') && $request->file('inventarioExcel')->isValid()){

            /** @var InventarioDocumental $inventario */
            $inventarioDocumental = InventarioDocumental::buscar($anio_captura, $mes_captura, ArchivoSeccion::find($seccion)->clave, ArchivoSerie::find($serie)->clave )->first();
            if(empty($inventarioDocumental)){
                return $this->responseJsonError('No fue posible registrar, inventario documental no localizado.');
            }
//            InventarioDocumental::validar_captura_detalle($inventarioDocumental);
            $resultadoValidarDetalle = InventarioDocumental::validar_captura_detalle($inventarioDocumental);
            if($resultadoValidarDetalle !== true){
                return $this->responseJsonError($resultadoValidarDetalle);
            }


            $archivo = IOFactory::load( $request->file('inventarioExcel') );
            $hoja = $archivo->getSheet(0);
//            $numeroFilas = $hoja->getHighestRow();
            $numeroFilas = $hoja->getHighestDataRow();
            $datos = [];

            DB::beginTransaction();

            $verificarConsecutivo = null;
            //ALMACENARA EL NUMERO DE EXPEDIENTES QUE HAN SIDO CREADOS
            $expedientesCreados = 0;
            //ALMACENARA EL NUMERO DE EXPEDIENTES QUE HAN SIDO ACTUALIZADOS
            $expedientesActualizados = 0;
            for ($indiceFila = 12;$indiceFila <= ($numeroFilas+1);$indiceFila++){

                /*Datos recuperados de cada fila*/
                $noRegistro = $indiceFila-11;
//                $ubicacionFisica = $hoja->getCellByColumnAndRow(2,$indiceFila)->getFormattedValue();
                $ubicacionFisica = config('app.address_organization');
                $ubicacionTopografica = $hoja->getCellByColumnAndRow(3,$indiceFila)->getFormattedValue();
                $noExpediente = trim($hoja->getCellByColumnAndRow(4,$indiceFila)->getFormattedValue());
                $descripcion = $hoja->getCellByColumnAndRow(5,$indiceFila)->getFormattedValue();
                $fechaInicio = filter_var(trim($hoja->getCellByColumnAndRow(6,$indiceFila)->getValue()), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
                $fechaFinal = filter_var(trim($hoja->getCellByColumnAndRow(7,$indiceFila)->getValue()), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
                $observaciones = $hoja->getCellByColumnAndRow(8,$indiceFila)->getFormattedValue();

                //VALIDA SI ES UN REGISTRO CON VALORES VALIDOS DE LA CONTRARIO TERMINA LA IMPORTACIÓN Y RETORNO EL NUMERO DE EXPEDIENTES CREADOS Y ACTUALIZADOS
                if( InventarioDocumental::esta_vacia($fechaInicio) && InventarioDocumental::esta_vacia($noExpediente) ){
                    DB::commit();
                    return $this->responseJsonSuccess(null,$expedientesCreados." Expedientes agregados.<br>".$expedientesActualizados." Expedientes actualizados.");
                }

                //VALIDA EL FORMATO EN EL QUE VIENE LA FECHA  PARA HACER SU RESPECTIVA CONVERSIÓN SEGÚN SEA EL CASO
                if(is_numeric($fechaInicio)){
                    //EL CAMPO DE LA FECHA TIENE FORMATO TIPO FECHA
                    $fechaInicioFormateada = Date::excelToDateTimeObject($fechaInicio)->format('Y-m-d');
                }else{
                    //EL CAMPO DE LA FECHA TIENE FORMATO TIPO TEXTO
                try {
                        $fechaInicioFormateada = Carbon::createFromFormat('d/m/Y',$fechaInicio)->format('Y-m-d');
                }catch (\Carbon\Exceptions\InvalidFormatException $e){
                    return $this->responseJsonError('Error en fecha Inicio en el registro '.$noRegistro);
                }

                }

                ////VALIDA EL FORMATO EN EL QUE VIENE LA FECHA  PARA HACER SU RESPECTIVA CONVERSIÓN SEGÚN SEA EL CASO
                if(is_numeric($fechaFinal)){
                    //SI ES DE TIPO NUMERICO PUEDE SER FORMATO DE TIPO FECHA O UNICAMENTE CONTENER EL AÑO
                    if($fechaFinal<3000){
                        //UNICAMENTE CONTIENE EL AÑO POR LO QUE SE REGISTRA COMO NULO
                        $fechaFinalFormateada = null;
                    }else{
                        //ES UN VALOR DE FECHA VALIDO POR LO QUE SE REALIZA LA RESPECTIVA CONVERSIÓN
                        $fechaFinalFormateada = Date::excelToDateTimeObject($fechaFinal)->format('Y-m-d');
                    }
                }else{
                    //EL CAMPO DE LA FECHA TIENE FORMATO TIPO TEXTO
                    try {
                        $fechaFinalFormateada = Carbon::createFromFormat('d/m/Y',$fechaFinal)->format('Y-m-d');
                    }catch (\Carbon\Exceptions\InvalidFormatException $e){
                        return $this->responseJsonError('Error en fecha Final en el registro '.$noRegistro);
                    }
                }

                //OBTIENE INFORMACIÓN DEL INVENTARIO AL CUAL SE INGRESARON LOS REGISTROS PARA VERIFICAR QUE LOS NO. EXPEDIENTE COINCIDAN CON DICHO EXPEDIENTE
                $clavesInventario = InventarioDocumental::claves_inventario($inventarioDocumental);
                //VERIFICA QUE LOS NÚMEROS DE EXPEDIENTE CORRESPONDAN AL EXPEDIENTE A REGISTRAR
                if( !InventarioDocumentalDetalle::validar_expediente($noExpediente,$clavesInventario) ){
                    DB::rollBack();
                    return $this->responseJsonError('El formato del No. de Expediente es incorrecto en el registro '.$noRegistro);
                }
                //OBTIENE EL NO. CONSECUTIVO DEL REGISTRO
                $consecutivo = InventarioDocumentalDetalle::obtener_consecutivo($noExpediente);
                //VERIFICA QUE EL NUMERO CONSECUTIVO COINCIDA CON EL INGRESADO EN EL NO. DE EXPEDIENTE
//                if ( $noRegistro != $consecutivo ){
//                    DB::rollBack();
//                    return $this->responseJsonError('El No. de Expediente no es consecutivo en el registro '.$noRegistro);
//                }

                //VERIFICAR QUE NO.EXPEDIENTE SEA CONSECUTIVO CORRESPONDIENTE AL PRIMER REGISTRO AGREGADO
                $verificarConsecutivo = $verificarConsecutivo ? ($verificarConsecutivo+1) : $consecutivo;
                if( $verificarConsecutivo != $consecutivo ){
                    DB::rollBack();
                    return $this->responseJsonError('El No. de Expediente no es consecutivo en el registro '.$noRegistro);
                }

                //AGREGA CERO A LA IZQUIERDA EN EL CONSECUTIVO EN CASO DE SOLO SER 1 DIGITO
                $noExpediente = InventarioDocumentalDetalle::cero_izqueierda_expediente($noExpediente);

                //GUARDA LOS DATOS DEL REGISTRO EN UN ARREGLO
                $datos = [
                    'ubicacion_fisica' =>  $ubicacionFisica,
                    'ubicacion_topografica' => $ubicacionTopografica,
                    'no_expediente' => $noExpediente,
                    'descripcion' => $descripcion,
                    'fecha_inicio' => $fechaInicioFormateada,
                    'fecha_final' => $fechaFinalFormateada,
                    'orden' => $consecutivo,
                    'observaciones' =>  $observaciones
                ];

                //VERIFICA QUE SE ENCUENTREN LOS DATOS NECESARIOS PARA EL REGISTRO
                $valido = InventarioDocumentalDetalle::es_valido_registro_excel($datos);
                if( $valido !== true ){
                    DB::rollBack();
                    return $this->responseJsonError($valido[0].' Registro '.$noRegistro);
                }
                //INTENTA GUARDAR EL REGISTRO EN LA BD
                $resultadoRegistro = $this-> store_detalle_excel($datos, $inventarioDocumental);
                //VERIFICA EL RESULTADO E LA INSERCIÓN Y AUMENTA LOS CONTADORES DE ACTUALIZADOS Y CREADOS
                if($resultadoRegistro === 'actualizado'){
                    $expedientesActualizados++;
                }elseif($resultadoRegistro === 'creado'){
                    $expedientesCreados++;
                }else{
                    //EN CASO DE ERROR EN LA INSERCIÓN DE EXPEDIENTE CANCELA LA TRANSACCIÓN Y RETORNA EL ERROR
                    DB::rollBack();
                    return $this->responseJsonError('Error al guardar en el registro '.$noRegistro.' - '.$resultadoRegistro);
                }

            }

        }else{
            return $this->responseJsonError('Archivo no encontrado.');
        }
    }

    public function get_secciones_registradas(Area $area, $mes, $anio, Departamento $departamento = NULL){

        $preconsulta = InventarioDocumental::join('archivo_secciones', 'clave_seccion', '=', 'archivo_secciones.clave')
            ->distinct('clave_seccion')
            ->select('clave_seccion','nombre','archivo_secciones.id')
            ->where('area_id',$area->id)
            ->where('anio_captura',$anio);

        if(!empty($departamento)){
            $preconsulta = $preconsulta->where('departamento_id',$departamento->id);
        }

        if($mes == "todos"){
            $seccion = $preconsulta->get();
        }else{
            $seccion = $preconsulta->where('mes_captura',$mes)->get();

        }
        $secciones = $seccion->unique('clave_seccion');
        return $this->responseJsonSuccess(compact('secciones'));
    }


    public function get_series_registradas(Area $area, $mes, $anio, $seccion, Departamento $departamento = NULL ){
        if( $seccion != 'todos' && is_numeric($seccion) ){
            $archivoSeccion = ArchivoSeccion::find($seccion);
        }

        $preconsulta = InventarioDocumental::with(['seccion','serie','departamento'])->where('area_id',$area->id)->where('anio_captura',$anio);

        if(!empty($departamento)){
            $preconsulta = $preconsulta->where('departamento_id',$departamento->id);
        }

        if($mes == "todos"){
            if($seccion == "todos"){
                $series = $preconsulta->orderBy('mes_captura','ASC')->get();
            }else{
                $series = $preconsulta->where('clave_seccion',$archivoSeccion->clave)->orderBy('mes_captura','ASC')->get();
            }
        }else{
            if($seccion == "todos"){
                $series = $preconsulta->where('mes_captura',$mes)->orderBy('mes_captura','ASC')->get();
            }else{
                $series = $preconsulta->where('clave_seccion',$archivoSeccion->clave)->where('mes_captura',$mes)->orderBy('mes_captura','ASC')->get();
            }

        }

        /*RECORRE CADA REGISTRO EN INVENTARIO DOCUMENTAL PARA VALIDAR ESTADOS Y AGREGAR DATOS EXTRA*/
        foreach ($series as $serie) {
            $calendario = ArchivoCalendario::where('mes_captura',$serie->mes_captura)->where('anio_captura',$serie->anio_captura)->first();
            $fecha_limite = strtotime($calendario->fecha_hora_limite_captura);
            $fecha_actual = strtotime(date('Y-m-d h:i:s'));
            $serie->fecha_hora_limite_captura = date('d/m/Y g:i a',$fecha_limite);
            $serie->permite_captura           = $calendario->permite_captura;

            /*VERIFICA EL ESTADO DE LA CAPTURA DEL INVENTARIO*/
            if(  !empty($serie->fecha_cierre_captura)  ){ //si la fecha de cierre no esta vacia
                $fecha_cierre = date('d/m/Y g:i a',strtotime($serie->fecha_cierre_captura));
                if(  strtotime($fecha_cierre) == strtotime($serie->fecha_hora_limite_captura)  ){
                    $serie->estado = InventarioDocumental::CERRADO_SISTEMA;
                }else{
                    $serie->estado = InventarioDocumental::CERRADO_USUARIO;
                }
            }else if(  $fecha_actual < $fecha_limite  ){
                $serie->estado = InventarioDocumental::CAPTURA;
            }else if(  $fecha_actual > $fecha_limite && $serie->permite_captura == false  ){
                $serie->estado = InventarioDocumental::CAPTURA_EXCEDIDA_ABIERTA;
            }else if(  $fecha_actual > $fecha_limite && $serie->permite_captura == true  ){
                $serie->estado = InventarioDocumental::CAPTURA_EXTEMPORANEA;
            }

            /*VERIFICA SI EL REGISTRO DE INVENTARIO DE LA SERIE CUENTA CON TODOS LOS NO.EXPEDIENTE CONSECUTIVOS*/
            $consecutivo = InventarioDocumentalDetalle::es_expediente_consecutivo($serie);

            if( $consecutivo === true ){
                $serie->expedienteConsecutivo = true;
            }else{
                $serie->expedienteConsecutivo = false;
            }
            $serie->total_expedientes = InventarioDocumentalDetalle::contar_expedientes($serie);

        }
        return $this->responseJsonSuccess(compact('series'));
    }


    public function revisar_inventario(InventarioDocumental $inventarioDocumental){
        $inventarioDocumental->persona_revisa_id = auth()->user()->persona->id;
        $inventarioDocumental->save();
        return $this->responseJsonSuccess();
    }


    public function store(Request $request){
        $inventario = $this->create($request);
        if( is_string($inventario) ){
            return $this->responseJsonError($inventario);
        }
        return $this->responseJsonSuccess(compact('inventario'));

    }

    public function create(Request $request){
        $data = InventarioDocumental::validar_datos($request);

        if(  InventarioDocumental::captura_cerrada($data['anio_captura'],$data['mes_captura'])  ){
            return 'No es posible registrar el inventario, fecha limite para el registro excedida.';
        }

        $data = InventarioDocumental::completar_datos($data);

        if(  InventarioDocumental::existe_inventario($data['area_id'],$data['departamento_id'],$data['anio_captura'],$data['mes_captura'],$data['clave_seccion'],$data['clave_serie'])  ){
            return 'El inventario documental ya existe.'.$data['clave_serie'];
        }
        $inventario  = InventarioDocumental::create($data);

        return $inventario;
    }


    public function store_detalle(StoreInventarioDocumentalDetalleRequest $request, InventarioDocumental $inventarioDocumental){
//        InventarioDocumental::validar_captura_detalle($inventarioDocumental);
        $resultadoValidarDetalle = InventarioDocumental::validar_captura_detalle($inventarioDocumental);
        if($resultadoValidarDetalle !== true){
            return $this->responseJsonError($resultadoValidarDetalle);
        }

        $data = $request->validated();

        $clavesInventario = InventarioDocumental::claves_inventario($inventarioDocumental);

        if( !InventarioDocumentalDetalle::validar_expediente($data["no_expediente"],$clavesInventario) ){
            return $this->responseJsonError('El No. Expediente es incorrecto. Debes seguir el formato asignado para este campo.');
        }
        $data['orden'] = InventarioDocumentalDetalle::obtener_consecutivo($data['no_expediente']);

        if ( $request->has('id')  && (!empty($request->input('id'))) ) {
            $registro = InventarioDocumentalDetalle::find( $request->input('id'));
            //VALIDA LAS FECHAS EXTREMAS Y EL PERIODO A REGISTRAR
            $fechaInicioValida = ArchivoCalendario::fecha_valida_registro($inventarioDocumental->mes_captura, $inventarioDocumental->anio_captura, $registro->fecha_inicio, $data['fecha_inicio'],true);
            if( $fechaInicioValida === false ){
                $data = [
                  'ubicacion_topografica' => $data['ubicacion_topografica'],
                  'no_expediente' => $registro->no_expediente,
                  'descripcion' => $data['descripcion'],
                  'fecha_inicio' => $registro->fecha_inicio->format('d-m-Y'),
                  'fecha_final' => $registro->fecha_final,
                  'observaciones' => $data['observaciones'],
                  'id' => $data['id'],
                  'orden' => $registro->orden,
                ];
//                dd($data);
            }
            if( !is_bool($fechaInicioValida) ){
                return $this->responseJsonError($fechaInicioValida);
            }
            $resultadoAgregarDetalle = $inventarioDocumental->agregarDetalle( $data,$registro,false );
        } else {

            $fechaInicioValida = ArchivoCalendario::fecha_valida_registro($inventarioDocumental->mes_captura, $inventarioDocumental->anio_captura, $data['fecha_inicio']);
            if( $fechaInicioValida !== true ){
                return $this->responseJsonError($fechaInicioValida);
            }

            $data['ubicacion_fisica'] = config('app.address_organization');
            $data['persona_id'] = auth()->user()->persona->id;
            $data['inventario_documentales_id'] = $inventarioDocumental->id;
            $resultadoAgregarDetalle = $inventarioDocumental->agregarDetalle( $data, null, false );
        }
        if($resultadoAgregarDetalle !== 'actualizado' && $resultadoAgregarDetalle !== 'creado'){
            return $this->responseJsonError($resultadoAgregarDetalle);
        }

        return $this->responseJsonSuccess();
    }

    /**
     * @throws \Exception
     */
    public function store_detalle_excel($data, InventarioDocumental $inventarioDocumental){
//        InventarioDocumental::validar_captura_detalle($inventarioDocumental);
        $resultadoValidarDetalle = InventarioDocumental::validar_captura_detalle($inventarioDocumental);
        if($resultadoValidarDetalle !== true){
            return $resultadoValidarDetalle;
        }

        //BUSCA SI EL NO_EXPEDIENTE EXISTE REGISTRADO EN EL INVENTARIO SELECCIONADO
        $expediente_registrado = InventarioDocumentalDetalle::where('no_expediente',$data['no_expediente'])->where('inventario_documentales_id',$inventarioDocumental->id)->first();

        $fechaInicioValida = ArchivoCalendario::fecha_valida_registro($inventarioDocumental->mes_captura, $inventarioDocumental->anio_captura, $data['fecha_inicio'],null,$expediente_registrado ? true : null);

        if( !is_bool($fechaInicioValida) ){
            return $fechaInicioValida;
        }

//        $data['ubicacion_fisica'] = config('app.address_organization');
        $data['persona_id'] = auth()->user()->persona->id;
        $data['inventario_documentales_id'] = $inventarioDocumental->id;
        return $inventarioDocumental->agregarDetalle( $data );
    }

    public function eliminar_detalle(InventarioDocumentalDetalle $inventarioDocumentalDetalle){

        $inventarioDocumental = $inventarioDocumentalDetalle->inventario_documental;
        if(  InventarioDocumental::captura_cerrada($inventarioDocumental->anio_captura,$inventarioDocumental->mes_captura) || InventarioDocumental::inventario_cerrado($inventarioDocumental)  ){
            return $this->responseJsonError('No es posible eliminar el registro de una captura cerrada.');
        }

        if(  !InventarioDocumental::permite_editar($inventarioDocumental)  ){
            return $this->responseJsonError('No tienes permisos para eliminar datos de un Inventario Documental que no pertenece a tu Área Generadora.');
        }

        $fechaInicioValida = ArchivoCalendario::fecha_valida_registro($inventarioDocumental->mes_captura, $inventarioDocumental->anio_captura, $inventarioDocumentalDetalle->fecha_inicio);
        if( $fechaInicioValida !== true ){
            return $this->responseJsonError($fechaInicioValida);
        }

        $inventarioDocumentalDetalle->delete();
//        $detalles = $inventarioDocumental->inventario_documental_detalles;
        return $this->responseJsonSuccess();
    }

    public function vaciar_inventario(InventarioDocumental $inventarioDocumental){

        if(  InventarioDocumental::captura_cerrada($inventarioDocumental->anio_captura,$inventarioDocumental->mes_captura) || InventarioDocumental::inventario_cerrado($inventarioDocumental)  ){
            return $this->responseJsonError('No es posible eliminar el registro de una captura cerrada.');
        }

        if(  !InventarioDocumental::permite_editar($inventarioDocumental)  ){
            return $this->responseJsonError('No tienes permisos para eliminar datos de un Inventario Documental que no pertenece a tu Área Generadora.');
        }
        InventarioDocumentalDetalle::where('inventario_documentales_id',$inventarioDocumental->id)->delete();
        return $this->responseJsonSuccess();
    }


    public function cerrar_inventario(Request $request, InventarioDocumental $inventarioDocumental = NULL){
        $data = InventarioDocumental::validar_datos($request);
        if(  InventarioDocumental::captura_cerrada($data['anio_captura'],$data['mes_captura'])){
            return $this->responseJsonError('No es posible cerrar la captura del inventario, fecha limite excedida.');
        }

        if(!empty($inventarioDocumental)){
            if(  !InventarioDocumental::permite_editar($inventarioDocumental)  ){
                return $this->responseJsonError('No tienes permisos para cerrar un Inventario Documental que no pertenece a tu Área Generadora.');
            }

            if(InventarioDocumental::inventario_cerrado($inventarioDocumental)){
                return $this->responseJsonError('El inventario documental ya ha sido cerrado anteriormente.');
            }

            $consecutivo = InventarioDocumentalDetalle::es_expediente_consecutivo($inventarioDocumental);
            if( $consecutivo !== true ){
                return $this->responseJsonError('Error, los números de expediente no son consecutivos, No.Expediente: '.$consecutivo.' faltante.');
            }

            $inventarioDocumental->fecha_cierre_captura = date('Y-m-d H:i:s');
            $inventarioDocumental->save();
            $inventario = $inventarioDocumental;
            return $this->responseJsonSuccess(compact('inventario'));

        }

        $data = InventarioDocumental::completar_datos($data);
        $data['fecha_cierre_captura']   = date('Y-m-d H:i:s');

        if(  InventarioDocumental::existe_inventario($data['area_id'],$data['departamento_id'],$data['anio_captura'],$data['mes_captura'],$data['clave_seccion'],$data['clave_serie'])  ){
            return $this->responseJsonError('El inventario documental ya existe.');
        }

        $inventario = InventarioDocumental::create($data);
        return $this->responseJsonSuccess(compact('inventario'));

    }

    public function duplicar_inventario(Request $request){

        $mesAnt = $request->mes_captura-1;
        $anioAnt = $request->anio_captura;
        if( $mesAnt ==  0 ){
            $anioAnt --;
            $mesAnt = 12;
        }

        $inventarioDocumentalAnt = InventarioDocumental::where('mes_captura',$mesAnt)->where('anio_captura',$anioAnt)->where('area_id',auth()->user()->persona->area_id)->where('departamento_id',auth()->user()->persona->departamento->id)->where('clave_seccion',ArchivoSeccion::find($request->seccion)->clave)->where('clave_serie',ArchivoSerie::find($request->serie)->clave)->first();

        $inventarioDetallesAnt = [];
        if ( !empty($inventarioDocumentalAnt) ) {
            $inventarioDetallesAnt = $inventarioDocumentalAnt->inventario_documental_detalles;
        }else{
            return $this->responseJsonError('No se encontro inventario registrado en el mes anterior.');
        }

        if( $inventarioDetallesAnt->count() == 0 ){
            return $this->responseJsonError('Mes anterior no cuenta con registros.');
        }

        //VERIFICAR SI INVENTARIO DOCUMENTAL AL QUE SE DESEA ACCEDER EXISTE, DE LO CONTRARIO LO CREA
        if( isset($request->id) ){
            $newInventarioDocumental = InventarioDocumental::where('id',$request->id)->first();
        }else{
            $newInventarioDocumental = $this->create($request);
        }

        if( is_string($newInventarioDocumental) ){
            return $this->responseJsonError($newInventarioDocumental);
        }
        DB::beginTransaction();
        foreach ($inventarioDetallesAnt as $key => $registro) {
            $detalle = [
                'orden' => InventarioDocumentalDetalle::obtener_consecutivo($registro->no_expediente),
                'ubicacion_fisica' => config('app.address_organization'),
                'ubicacion_topografica' => $registro->ubicacion_topografica,
                'no_expediente' => $registro->no_expediente,
                'descripcion' => $registro->descripcion,
                'fecha_inicio' => $registro->fecha_inicio,
                'fecha_final' => $registro->fecha_final,
                'observaciones' => $registro->observaciones,
                'inventario_documentales_id' => $newInventarioDocumental->id,
                'persona_id' => auth()->user()->persona->id
            ];

            $resultadoAgregarDetalle = $newInventarioDocumental->agregarDetalle( $detalle );
            if($resultadoAgregarDetalle !== 'actualizado' && $resultadoAgregarDetalle !== 'creado'){
                DB::rollBack();
                return $this->responseJsonError('Error al guardar en el registro '.($key+1).' - '.$resultadoAgregarDetalle);
            }

        }
        DB::commit();
        return $this->responseJsonSuccess(compact('newInventarioDocumental'));

    }

    public function abrir_inventario(InventarioDocumental $inventarioDocumental){
        $inventarioDocumental->fecha_cierre_captura = null;
        $inventarioDocumental->persona_revisa_id = null;
        $inventarioDocumental->save();
        return $this->responseJsonSuccess();
    }


//    public function exportar_inventario(InventarioDocumental $inventarioDocumental)
//    {
//        $siglas = Str::lower($inventarioDocumental->area->siglas);
//        // dd( $inventarioDocumental->persona_responsable->toArray());
//        return (new InventarioDocumentalExport())
//            ->inventario( $inventarioDocumental )
//            ->download('ccleh-' . $siglas . '-inventario-documental.xlsx');
//    }

    public function verificar_consecutivo(InventarioDocumental $inventarioDocumental){
        $consecutivo = InventarioDocumentalDetalle::es_expediente_consecutivo($inventarioDocumental);
        if( $consecutivo !== true ){
            return $this->responseJsonError('Error, los números de expediente no son consecutivos, No.Expediente: '.$consecutivo.' faltante.');
        }else{
            return $this->responseJsonSuccess();
        }
    }

    public function buscarExpedientes(BuscarExpedientesTransferenciaRequest $request){
        $expedientes = InventarioDocumental::buscarExpedientesParaTransferencia($request);
        if(!$expedientes){
            return $this->responseJsonError('No se encontraron expedientes.');
        }
        return $this->responseJsonSuccess(compact('expedientes'));
    }


}
