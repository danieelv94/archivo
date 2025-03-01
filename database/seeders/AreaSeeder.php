<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run()
    {
        $dg = Area::create([
            'nombre' => 'Dirección General',
            'siglas' => 'DG',
            'tipo_area' => 'direccion_general',
            'codigo' => '01',
        ]);

        Area::create([
            'nombre' => 'Dirección Conciliación',
            'siglas' => 'DC',
            'tipo_area' => 'direccion',
            'area_padre_id' => $dg->id,
            'codigo' => '02',
        ]);
        Area::create([
            'nombre' => 'Dirección de Planeación y Evaluación',
            'siglas' => 'DPyE',
            'tipo_area' => 'direccion',
            'area_padre_id' => $dg->id,
            'codigo' => '03',
        ]);
        Area::create([
            'nombre' => 'Dirección de Administración',
            'siglas' => 'DA',
            'tipo_area' => 'direccion',
            'area_padre_id' => $dg->id,
            'codigo' => '04',
        ]);
        Area::create([
            'nombre' => 'Órgano Interno de Control',
            'siglas' => 'OIC',
            'tipo_area' => 'organo_interno',
            'area_padre_id' => $dg->id,
            'codigo' => '00',
        ]);
        Area::create([
            'nombre' => 'Dirección Jurídica',
            'siglas' => 'DJ',
            'tipo_area' => 'direccion',
            'area_padre_id' => $dg->id,
            'codigo' => '05',
        ]);
    }
}
