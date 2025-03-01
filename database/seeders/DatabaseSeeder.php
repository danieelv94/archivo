<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call( AreaSeeder::class );
        $this->call( DepartamentoSeeder::class );
        $this->call( PermissionsSeeder::class );
        $this->call( UsersSeeder::class );
        // $this->call( PersonaSeeder::class );
        $this->call( ArchivoCalendarioSeeder::class );
        $this->call( SeccionesSeeder::class );
        $this->call( ArchivoSerieAutorizadaSeeder::class );
    }
}
