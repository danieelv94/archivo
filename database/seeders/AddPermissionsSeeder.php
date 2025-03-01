<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AddPermissionsSeeder extends Seeder
{
    public function run()
    {
        Permission::create(['name' => 'cadido']);
        Permission::create(['name' => 'cadido.index']);
        Permission::create(['name' => 'cadido.store']);
        Permission::create(['name' => 'cadido.get']);

        Permission::create(['name' => 'transferenciaPrimaria']);
        Permission::create(['name' => 'transferenciaPrimaria.index']);
        Permission::create(['name' => 'transferenciaPrimaria.store']);
        Permission::create(['name' => 'transferenciaPrimaria.get']);
        Permission::create(['name' => 'transferenciaPrimaria.editarOtros']);


        $administradorArchivo = Role::where('name','administrador de archivo')->first();
        $administradorArchivo->givePermissionTo('cadido');
        $administradorArchivo->givePermissionTo('transferenciaPrimaria');

        $soporte = Role::where('name','soporte')->first();
        $soporte->givePermissionTo('cadido');
        $soporte->givePermissionTo('transferenciaPrimaria');

        $capturista = Role::where('name','capturista')->first();
        $capturista->givePermissionTo('cadido.index');
        $capturista->givePermissionTo('cadido.get');
        $capturista->givePermissionTo('transferenciaPrimaria.index');
        $capturista->givePermissionTo('transferenciaPrimaria.store');
        $capturista->givePermissionTo('transferenciaPrimaria.get');




    }
}
