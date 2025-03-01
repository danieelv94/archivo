<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuscarCadidoRequest;
use App\Http\Requests\GuardarCadidoRequest;
use App\Http\Requests\ImportarCadidoRequest;
use App\Http\Requests\ListarCadidosRequest;
use App\Models\archivo\ArchivoCalendario;
use App\Models\archivo\ArchivoSeccion;
use App\Models\archivo\ArchivoSerie;
use App\Models\Cadido;
use App\Models\Firmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CadidoController extends Controller {
    public function index() {
        $anios = ArchivoCalendario::obtener_anios_hasta_actual();
        $firmas = Firmas::obtenerFirmasCadido();
        return view('cadido.listado_cadido_admin',compact('anios','firmas'));
    }

    public function verListado(){
        $anios = ArchivoCalendario::obtener_anios_hasta_actual();
        return view('cadido.listado_cadido',compact('anios'));
    }

    public function listarCadidos(ListarCadidosRequest $request){
        $cadidos = Cadido::where('anio',$request->anio)->where('seccion_id',$request->seccion)->get();
        return $this->responseJsonSuccess(compact('cadidos'));
    }

    public function store(GuardarCadidoRequest $request) {
        $datosCadido = $request->validated();
        $validado = Cadido::validarTiempoGuarda($datosCadido);
        if( $validado !== true ){
            return $this->responseJsonError($validado);
        }
        $error = Cadido::validarDatosVacios($datosCadido);
        if( $error ){
            return $this->responseJsonError($error);
        }

        if( $datosCadido['id'] ){
            $cadido = Cadido::find($datosCadido['id'])->update($datosCadido);
        }else{
            if( Cadido::buscarRepetido($datosCadido)->exists() ){
                return $this->responseJsonError('Ya existe registrado un CADIDO en el año y serie seleccionado.');
            }
            Cadido::create($datosCadido);
        }
        return $this->responseJsonSuccess(null,'CADIDO guardado correctamente.');
    }

    public function get(Cadido $cadido) {

        return $this->responseJsonSuccess(compact('cadido'));
    }

    public function delete(Cadido $cadido) {
        $cadido->delete();
        return $this->responseJsonSuccess();
    }

    public function importarCadido(ImportarCadidoRequest $request){
        DB::beginTransaction();
        try {
            $datosCadido = [
                'anio' => $request->anio,
                //'seccion_id' => $request->seccion_id,
            ];

            $cadido = IOFactory::load( $request->file('importarCADIDO') );

            $HojasCadidos = $cadido->getAllSheets();
            $registrosActualizados = 0;
            $registrosCreados = 0;
            foreach ($HojasCadidos as $index => $hoja) {
                $claveSeccion = trim(explode( '.',$hoja->getCell('A11')->getFormattedValue() )[0]);
                $claveSerie = trim(explode( '.',$hoja->getCell('A11')->getFormattedValue() )[1]);
                $serie = ArchivoSerie::where('clave_seccion',$claveSeccion)->where('clave',$claveSerie)->first();
                if(!$serie){
                    throw new \Exception('Serie '.$claveSerie.' no encontrada en la sección '.$claveSeccion);
                }
                $datosCadido['serie_id'] = $serie->id;
                $datosCadido['seccion_id'] = ArchivoSeccion::where('clave',$claveSeccion)->first()->id;

                $datosCadido['valor_primario_administrativa'] = intval(trim( $hoja->getCell('D11')->getValue() ));
                $datosCadido['valor_primario_fiscal'] = intval(trim( $hoja->getCell('E11')->getValue() ));
                $datosCadido['valor_primario_legal'] = intval(trim( $hoja->getCell('F11')->getValue() ));

                $datosCadido['valor_secundario_evidencial'] = strtoupper(trim( $hoja->getCell('G11')->getValue() )) == 'X' ?? false;
                $datosCadido['valor_secundario_testimonial'] = strtoupper(trim( $hoja->getCell('H11')->getValue() )) == 'X' ?? false;
                $datosCadido['valor_secundario_informativo'] = strtoupper(trim( $hoja->getCell('I11')->getValue() )) == 'X' ?? false;

                $datosCadido['tiempo_guarda_tramite'] = intval(trim( $hoja->getCell('J11')->getValue() ));
                $datosCadido['tiempo_guarda_concentracion'] = intval(trim( $hoja->getCell('K11')->getValue() ));
                if( !$datosCadido['tiempo_guarda_tramite'] || !$datosCadido['tiempo_guarda_concentracion'] ){
                    throw new \Exception("Error en la hoja ".($index+1).". Tiempo de guarda en tramite o concentración no pueden ser 0.");
                }

                $datosCadido['fundamento_legal'] = $hoja->getCell('M11')->getValue();

                $datosCadido['clasificacion_publica'] = strtoupper(trim( $hoja->getCell('N11')->getValue() )) == 'X' ?? false;
                $datosCadido['clasificacion_reservada'] = strtoupper(trim( $hoja->getCell('O11')->getValue() )) == 'X' ?? false;
                $datosCadido['clasificacion_confidencial'] = strtoupper(trim( $hoja->getCell('P11')->getValue() )) == 'X' ?? false;

                $destinoFinalBaja = strtoupper(trim( $hoja->getCell('R11')->getValue() )) == 'X' ?? false;
                $destinoFinalArchivoHistorico = strtoupper(trim( $hoja->getCell('S11')->getValue() )) == 'X' ?? false;
                $destinoFinalMuestreo = strtoupper(trim( $hoja->getCell('T11')->getValue() )) == 'X' ?? false;

                if( $destinoFinalBaja && !$destinoFinalArchivoHistorico && !$destinoFinalMuestreo ){
                    $datosCadido['destino_final'] = config('enums.validar_destino_final')[0];
                }elseif( !$destinoFinalBaja && $destinoFinalArchivoHistorico && !$destinoFinalMuestreo ){
                    $datosCadido['destino_final'] = config('enums.validar_destino_final')[1];
                }elseif( !$destinoFinalBaja && !$destinoFinalArchivoHistorico && $destinoFinalMuestreo ){
                    $datosCadido['destino_final'] = config('enums.validar_destino_final')[2];
                }else{
                    throw new \Exception("Error en la hoja ".($index+1).". Multiples opciones seleccionadas en el destino final.");
                }
                $datosCadido['particularidades'] = $hoja->getCell('U11')->getValue();

                $error = Cadido::validarDatosVacios($datosCadido);
                if( $error ){
                    throw new \Exception("Error en la hoja ".($index+1).". ".$error);
                }

                if( Cadido::buscarRepetido($datosCadido)->exists() ){
                    Cadido::where('anio',$datosCadido['anio'])->where('seccion_id',$datosCadido['seccion_id'])->where('serie_id',$datosCadido['serie_id'])->first()->update($datosCadido);
                    $registrosActualizados++;
                    //throw new \Exception("Ya existe registrado un CADIDO en el año {$datosCadido['anio']}, sección {$claveSeccion} y serie {$claveSerie}.");
                }else{
                    Cadido::create($datosCadido);
                    $registrosCreados++;
                }
            }
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseJsonError($e->getMessage());
        }

        DB::commit();
        return $this->responseJsonSuccess(null,"{$registrosCreados} registros creados y {$registrosActualizados} actualizados.");

    }

    public function buscarCadido(BuscarCadidoRequest $request){
        $cadido = Cadido::buscarCadidoPorAtributos($request->seccion_id,$request->serie_id,$request->anio);
        return $this->responseJsonSuccess(compact('cadido'));
    }

}
