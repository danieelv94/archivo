<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\archivo\ArchivoCalendario;
use App\Models\archivo\InventarioDocumental;
use App\Models\archivo\ArchivoSerieAutorizada;
use App\Models\archivo\ArchivoSupervision;
use App\Models\Area;

class archivoLimiteCaptura extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archivo:limiteCaptura';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica las fechas limites de captura para los inventarios documentales de cada mes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*  Verifica si la feecha limite de captura de los inventarios para cada mes se ha excedido  */
        $hoy = strtotime(date('Y-m-d H:i:s'));
        $archivoCalendario = ArchivoCalendario::where('permite_captura',false)->get(); //si el mes permite captura se omitira el proceso de cierre automatico
//        $areas = Area::get();
        $areas = Area::where('activo',true)->get();
        foreach ($archivoCalendario as $calendario) {
            //CIERRA AUTOMATICAMENTE LOS INVENTARIOS DOCUMENTALES ABIERTOS DE LOS CUALES SU FECHA DE CAPTURA HA SIDO EXCEDIDA
            if( $hoy > strtotime($calendario->fecha_hora_limite_captura) ) {
                $inventarioDocumental = InventarioDocumental::where('fecha_cierre_captura',null)->where('anio_captura',$calendario->anio_captura)->where('mes_captura',$calendario->mes_captura)->get();
                foreach ($inventarioDocumental as $inventario) {
                    $inventario->fecha_cierre_captura = $calendario->fecha_hora_limite_captura;
                    $inventario->save();
                    $this->info($inventario->id.'-> fue cerrado automaticamente.  ');
                }


                /*  Crea los registros de semaforizacion correspondientes a la captura del mes cerrado  */
                // $areas = Area::get(); // las áreas no cambian repentinamente, no es necesario inicializar nuevo en cada iteración
                $mes = $calendario->mes_captura;
                $anio = $calendario->anio_captura;
                foreach ($areas as $area) {
                    $seriesAutorizadas = ArchivoSerieAutorizada::join('departamentos','archivo_serie_autorizadas.departamento_id','=','departamentos.id')->where('archivo_serie_autorizadas.area_id',$area->id)->where('departamentos.activo',true)->get();
//                    $seriesAutorizadas = ArchivoSerieAutorizada::where('area_id',$area->id)->get();
                    $totalSeriesAutorizadas = $seriesAutorizadas->count();
                    $seriesRegistradas = 0;
                    foreach ($seriesAutorizadas as $serie) {
                        $inventario = InventarioDocumental::where('clave_seccion',$serie->seccion->clave)->where('clave_serie',$serie->serie->clave)->where('mes_captura',$mes)->where('anio_captura',$anio)->where('departamento_id',$serie->departamento_id)->where('area_id',$area->id)->first();
//                        $inventario = InventarioDocumental::where('clave_seccion',$serie->seccion->clave)->where('clave_serie',$serie->serie->clave)->where('mes_captura',$mes)->where('anio_captura',$anio)->where('area_id',$area->id)->first();
                        if(!empty($inventario)){
                            $seriesRegistradas++;
                        }
                    }
                    if( $totalSeriesAutorizadas == $seriesRegistradas ){
                        $resumen = 1; //1 = Completo
                    }else if( $seriesRegistradas == 0 ){
                        $resumen = 0; //0 = No capturo datos
                    }else if( $totalSeriesAutorizadas > $seriesRegistradas ){
                        $resumen = 2;  //2 = Incompleto
                    }
                    $semaforo = [
                        'area_id'      => $area->id,
                        'anio_captura' => $anio,
                        'mes_captura'  => $mes,
                        'semaforo'     => $resumen
                    ];

                    $archivoSupervision = ArchivoSupervision::create($semaforo);
                }
            }
        }
        return 0;
    }
}
