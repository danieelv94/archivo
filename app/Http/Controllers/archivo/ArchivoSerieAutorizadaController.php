<?php

namespace App\Http\Controllers\archivo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\archivo\ArchivoSerieAutorizada;
use App\Models\archivo\ArchivoSeccion;
use App\Models\Area;
use App\Models\Departamento;
use App\Models\archivo\ArchivoSerie;
use App\Models\archivo\InventarioDocumental;

class ArchivoSerieAutorizadaController extends Controller
{
    public function index(){
        $unidades_administrativas = Area::get();
        return view('archivo.series_autorizadas',compact('unidades_administrativas'));
    }
    //SI VARIABLE $repetidas ES FALSE RETORNA UNICAMENTE SECCIONES DE LO CONTRARIO RETORNA SERIES
    public function get_series_autorizadas(Area $area, Departamento $departamento, $repetidas = false, ArchivoSeccion $archivoSeccion = NULL){
        if(empty($archivoSeccion)){

            if($repetidas == "false"){
                $serie = ArchivoSerieAutorizada::distinct('seccion_id')->where('area_id',$area->id)->where('departamento_id',$departamento->id)->get();
                $series = $serie->unique('seccion_id');
            }else{
                $series = ArchivoSerieAutorizada::where('area_id',$area->id)->where('departamento_id',$departamento->id)->get();
            }
            $series = $series->values()->toArray();
            return $this->responseJsonSuccess(compact('series'));

        }
        $series = ArchivoSerieAutorizada::where('area_id',$area->id)->where('departamento_id',$departamento->id)->where('seccion_id',$archivoSeccion->id)->get()->values()->toArray();
        return $this->responseJsonSuccess(compact('series'));
    }

    public function store(Request $request){
        $request->validate([
            'seccion_id' => ['required', 'exists:archivo_secciones,id'],
            'serie_id' => ['required', 'exists:archivo_series,id'],
            'area_id' => ['required', 'exists:areas,id'],
            'departamento_id' => ['required', 'exists:departamentos,id'],
        ]);

        $data = $request->only(['departamento_id', 'area_id', 'serie_id', 'seccion_id']);
        if(  $this->existe_serie_autorizada($data['departamento_id'],$data['area_id'],$data['seccion_id'],$data['serie_id'])  ){
            return $this->responseJsonError('Ya se ha autorizado la serie para esta Unidad Administrativa.');
        }

        $serie  = ArchivoSerieAutorizada::create($data);
        return $this->responseJsonSuccess(compact('serie'));

    }

    public function get_departamentos(Area $area){
        $departamentos = Departamento::where('area_id',$area->id)->get();
        return $this->responseJsonSuccess(compact('departamentos'));
    }


    public function delete(ArchivoSerieAutorizada $archivoSerieAutorizada){
        $archivoSerieAutorizada->delete();
        return $this->responseJsonSuccess();
    }


    public function tiene_inventarios($area_id,$clave_serie,$clave_seccion){
        $inventarios = InventarioDocumental::where('area_id',$area_id)->where('clave_serie',$clave_serie)->where('clave_seccion',$clave_seccion)->count();
        return $inventarios > 0;
    }

    public function existe_serie_autorizada($departamento_id,$area_id,$seccion_id,$serie_id){
        $series = ArchivoSerieAutorizada::where('departamento_id',$departamento_id)->where('area_id',$area_id)->where('seccion_id',$seccion_id)->where('serie_id',$serie_id)->count();

        return $series > 0;
    }

    public function get_secciones(){
        $secciones = ArchivoSeccion::get();
        return $this->responseJsonSuccess(compact('secciones'));
    }

    public function get_series(ArchivoSeccion $archivoSeccion){
        $series = ArchivoSerie::where('clave_seccion',$archivoSeccion->clave)->get();
        return $this->responseJsonSuccess(compact('series'));
    }
}
