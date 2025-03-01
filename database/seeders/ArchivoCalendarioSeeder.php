<?php

namespace Database\Seeders;

use App\Models\archivo\ArchivoCalendario;
use App\Models\archivo\ArchivoConfig;
use Illuminate\Database\Seeder;

class ArchivoCalendarioSeeder extends Seeder
{
    public function run()
    {
        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 1,
            'fecha_hora_limite_captura' => '2022-02-07T15:30:00',
            'persona_id' => 1,
        ]);
        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 2,
            'fecha_hora_limite_captura' => '2022-03-07T15:30:00',
            'persona_id' => 1,
        ]);
        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 3,
            'fecha_hora_limite_captura' => '2022-04-06T15:30:00',
            'persona_id' => 1,
        ]);
        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 4,
            'fecha_hora_limite_captura' => '2022-05-06T15:30:00',
            'persona_id' => 1,
        ]);
        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 5,
            'fecha_hora_limite_captura' => '2022-06-06T15:30:00',
            'persona_id' => 1,
        ]);
        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 6,
            'fecha_hora_limite_captura' => '2022-07-06T15:30:00',
            'persona_id' => 1,
        ]);
        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 7,
            'fecha_hora_limite_captura' => '2022-08-08T15:30:00',
            'persona_id' => 1,
        ]);

        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 8,
            'fecha_hora_limite_captura' => '2022-09-06T15:30:00',
            'persona_id' => 1,
        ]);

        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 9,
            'fecha_hora_limite_captura' => '2022-10-06T15:30:00',
            'persona_id' => 1,
        ]);

        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 10,
            'fecha_hora_limite_captura' => '2022-11-07T15:30:00',
            'persona_id' => 1,
        ]);

        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 11,
            'fecha_hora_limite_captura' => '2022-12-06T15:30:00',
            'persona_id' => 1,
        ]);

        ArchivoCalendario::create([
            'anio_captura' => 2022,
            'mes_captura' => 12,
            'fecha_hora_limite_captura' => '2023-01-09T15:30:00',
            'persona_id' => 1,
        ]);

        ArchivoConfig::create([
            'captura_abierta' => true
        ]);
    }
}
