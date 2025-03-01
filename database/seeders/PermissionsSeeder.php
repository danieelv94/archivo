<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run()
    {   
        Permission::create(['name' => 'areas']);
        Permission::create(['name' => 'areas.index']);
        Permission::create(['name' => 'areas.store']);
        Permission::create(['name' => 'areas.get']);
        Permission::create(['name' => 'areas.list']);
        Permission::create(['name' => 'areas.edit']);
        Permission::create(['name' => 'areas.delete']);

        Permission::create(['name' => 'departamentos']);
        Permission::create(['name' => 'departamentos.index']);
        Permission::create(['name' => 'departamentos.store']);
        Permission::create(['name' => 'departamentos.get']);
        Permission::create(['name' => 'departamentos.list']);
        Permission::create(['name' => 'departamentos.edit']);
        Permission::create(['name' => 'departamentos.delete']);

        Permission::create(['name' => 'usuarios']);
        Permission::create(['name' => 'usuarios.index']);
        Permission::create(['name' => 'usuarios.store']);
        Permission::create(['name' => 'usuarios.get']);
        Permission::create(['name' => 'usuarios.list']);
        Permission::create(['name' => 'usuarios.edit']);
        Permission::create(['name' => 'usuarios.delete']);
        Permission::create(['name' => 'usuarios.changeRole']);
        Permission::create(['name' => 'usuarios.changePassword']);

        Permission::create(['name' => 'calendario']);
        Permission::create(['name' => 'calendario.index']);
        Permission::create(['name' => 'calendario.store']);
        Permission::create(['name' => 'calendario.get']);
        Permission::create(['name' => 'calendario.list']);
        Permission::create(['name' => 'calendario.edit']);
        Permission::create(['name' => 'calendario.delete']);
        Permission::create(['name' => 'calendario.duplicate']);
        Permission::create(['name' => 'calendario.changeCapture']);
        Permission::create(['name' => 'calendario.changeState']);

        Permission::create(['name' => 'supervision']);
        Permission::create(['name' => 'supervision.index']);
        Permission::create(['name' => 'supervision.list']);

        Permission::create(['name' => 'reporte']);
        Permission::create(['name' => 'reporte.index']);
        Permission::create(['name' => 'reporte.dowload']);
        Permission::create(['name' => 'reporte.list']);
        Permission::create(['name' => 'reporte.changeArea']);
        Permission::create(['name' => 'reporte.openCapture']);
        Permission::create(['name' => 'reporte.review']);

        Permission::create(['name' => 'seriesAutorizadas']);
        Permission::create(['name' => 'seriesAutorizadas.index']);
        Permission::create(['name' => 'seriesAutorizadas.add']);
        Permission::create(['name' => 'seriesAutorizadas.authorize']);
        Permission::create(['name' => 'seriesAutorizadas.list']);
        Permission::create(['name' => 'seriesAutorizadas.delete']);        

        Permission::create(['name' => 'inventarioDocumental']);
        Permission::create(['name' => 'inventarioDocumental.index']);
        Permission::create(['name' => 'inventarioDocumental.store']);
        Permission::create(['name' => 'inventarioDocumental.get']);
        Permission::create(['name' => 'inventarioDocumental.list']);
        Permission::create(['name' => 'inventarioDocumental.edit']);
        Permission::create(['name' => 'inventarioDocumental.delete']);
        Permission::create(['name' => 'inventarioDocumental.close']);
        Permission::create(['name' => 'inventarioDocumental.export']);

        Permission::create(['name' => 'perfil']);
        Permission::create(['name' => 'perfil.new_password']);
        Permission::create(['name' => 'perfil.index']);
        // Permission::create(['name' => 'ccleh.archivo.admin.config.index']);


        // create roles and assign existing permissions
        $capturista = Role::create(['name' => 'capturista']);
        $capturista->givePermissionTo('inventarioDocumental');
        $capturista->givePermissionTo('perfil');
        $capturista->givePermissionTo('reporte.index');
        $capturista->givePermissionTo('reporte.dowload');
        $capturista->givePermissionTo('reporte.list');


        $administradorArchivo = Role::create(['name' => 'administrador de archivo']);
        $administradorArchivo->givePermissionTo('perfil');
        $administradorArchivo->givePermissionTo('seriesAutorizadas');
        $administradorArchivo->givePermissionTo('reporte');
        $administradorArchivo->givePermissionTo('supervision');
        $administradorArchivo->givePermissionTo('calendario');
        $administradorArchivo->givePermissionTo('inventarioDocumental');


        $soporte = Role::create(['name' => 'soporte']);
        $soporte->givePermissionTo('perfil');
        $soporte->givePermissionTo('seriesAutorizadas');
        $soporte->givePermissionTo('reporte');
        $soporte->givePermissionTo('supervision');
        $soporte->givePermissionTo('calendario');
        $soporte->givePermissionTo('inventarioDocumental');
        $soporte->givePermissionTo('areas');
        $soporte->givePermissionTo('departamentos');
        $soporte->givePermissionTo('usuarios');


        $sysAdmin = Role::create(['name' => 'sysAdmin']);

    }
}
