<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    public function run()
    {
        $personas = Persona::factory()->count(15)->create();

        /** @var Persona $persona */
        foreach ($personas as $persona) {
            $persona->create_user();
        }
    }
}
