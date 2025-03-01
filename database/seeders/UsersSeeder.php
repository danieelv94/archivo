<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Persona;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        /** @var Persona $p */
        //ADMINISTRADOR/SOPORTE
        $p = Persona::create([
            'email'               => 'daniel.lopez@hidalgo.gob.mx',
            'nombres'             => 'SysAdmin',
            'primer_apellido'     => 'sys',
            'segundo_apellido'    => 'tem',
            'curp'                => 'MASU831219HHGRLL00',
            'area_id'             => Area::firstWhere('siglas', 'DPyE')->id,
            'departamento_id' => '10',
            'puesto' => 'Subdirector de TICS',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user('1234567890.');
        $user->syncRoles('sysAdmin');


//        *******************************************************************************************************************************

        //1.2 - capturista(2021) / 4.4(2022)
        $p = Persona::create([
            'email'               => 'cclehcarchivo@gmail.com',
            'titulo'              => 'Ing.',
            'nombres'             => 'Ignacio Bernabé',
            'primer_apellido'     => 'Pérez',
            'segundo_apellido'    => 'Barrera',
            'curp'                => 'PEBI630311HHGRRG09',
            'telefono'            => '7711811693',
            'area_id'             => Area::firstWhere('codigo', '01')->id,
            'departamento_id'     => '4',
            'puesto'              => 'Titular de la Coordinación de Archivos',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('administrador de archivo');

//        ********************************************************************************************************************************


        //01 - capturista
        $p = Persona::create([
            'email'               => 'ccleh.despacho@hidalgo.gob.mx',
            'titulo'              => 'Lic.',
            'nombres'             => 'Ana María',
            'primer_apellido'     => 'Gómez',
            'segundo_apellido'    => 'Muñoz',
            'curp'                => 'XEXX010101MNEXXXB1',
            'telefono'            => '7712154939',
            'area_id'             => Area::firstWhere('codigo', '01')->id,
            'departamento_id'     => '1',
            'puesto'              => 'Directora de Conciliación con Facultades de Dirección General por Ministerio de Ley del Centro de Conciliación Laboral del Estado de Hidalgo',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

        //1.2 - capturista
        $p = Persona::create([
            'email'               => 'unidadcorrespondenciaccleh@gmail.com',
            'titulo'              => 'Lic.',
            'nombres'             => 'Ana María',
            'primer_apellido'     => 'Gómez',
            'segundo_apellido'    => 'Muñoz',
            'curp'                => 'XEXX010101MNEXXXA9',
            'telefono'            => '7712154939',
            'area_id'             => Area::firstWhere('codigo', '01')->id,
            'departamento_id'     => '3',
            'puesto'              => 'Directora de Conciliación con Facultades de Dirección General por Ministerio de Ley del Centro de Conciliación Laboral del Estado de Hidalgo',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');


//        **************************************************************************************************************


        //02 - capturista
        $p = Persona::create([
            'email'               => 'conciliacion@ccleh.com',
            'titulo'              => 'Lic.',
            'nombres'             => 'Alejandra',
            'primer_apellido'     => 'Salas',
            'segundo_apellido'    => 'Lara',
            'curp'                => 'XEXX010101MNEXXXA8',
            'telefono'            => '',
            'area_id'             => Area::firstWhere('codigo', '02')->id,
            'departamento_id'     => '5',
            'puesto'              => '',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

        //2.3 - capturista
        $p = Persona::create([
            'email'               => 'orientadores@ccleh.com',
            'titulo'              => 'Lic.',
            'nombres'             => 'Alejandra',
            'primer_apellido'     => 'Salas',
            'segundo_apellido'    => 'Lara',
            'curp'                => 'XEXX010101MNEXXXA7',
            'telefono'            => '',
            'area_id'             => Area::firstWhere('codigo', '02')->id,
            'departamento_id'     => '26',
            'puesto'              => '',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

//        //2.1 - capturista
//        $p = Persona::create([
//            'email' => 'cclehabautista@gmail.com',
//            'titulo' => 'Lic.',
//            'nombres' => 'Marilú',
//            'primer_apellido' => 'Ortiz',
//            'segundo_apellido' => 'Acosta',
//            'curp' => 'BAAB760915MHGTTB02',
//            'telefono' => '7714189342',
//            'area_id' => Area::firstWhere('codigo', '02')->id,
//            'departamento_id' => '6',
//            'puesto' => 'Auxiliar de coordinación de conciliación',
//            'unidad_presupuestal' => config('app.name_organization')
//        ]);
//        $user = $p->create_user();
//        $user->syncRoles('capturista');


//        ****************************************************************************************************************

        //03 - capturista
        $p = Persona::create([
            'email' => 'ccleh.luzlopez@gmail.com',
            'titulo' => 'M.D.P.',
            'nombres' => 'Arnold',
            'primer_apellido' => 'Padilla',
            'segundo_apellido' => 'Rodríguez',
            'curp' => 'XEXX010101HNEXXXA6',
            'telefono' => '7711844797',
            'area_id' => Area::firstWhere('codigo', '03')->id,
            'departamento_id' => '9',
            'puesto' => 'Director de Planeación y Evaluación',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

        //3.1 - capturista
        $p = Persona::create([
            'email' => 'ccleh.angelavillegas@gmail.com',
            'titulo' => 'L.S.C.',
            'nombres' => 'Ulises Dario',
            'primer_apellido' => 'Martínez',
            'segundo_apellido' => 'Salinas',
            'curp' => 'XEXX010101HNEXXXA5',
            'telefono' => '7711921261',
            'area_id' => Area::firstWhere('codigo', '03')->id,
            'departamento_id' => '10',
            'puesto' => 'Subdirector de TIC´S',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

        //3.2 - capturista
        $p = Persona::create([
            'email' => 'ccleh.marthadoroteo@gmail.com',
            'titulo' => 'L.C.P.',
            'nombres' => 'Israel Yovanni',
            'primer_apellido' => 'López',
            'segundo_apellido' => 'Sierra',
            'curp' => 'XEXX010101HNEXXXA4',
            'telefono' => '7711418599',
            'area_id' => Area::firstWhere('codigo', '03')->id,
            'departamento_id' => '11',
            'puesto' => 'Subdirector de Planeación y Transparencia',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

        //3.3 - capturista
        $p = Persona::create([
            'email' => 'ccleh.dianavega@gmail.com',
            'titulo' => 'DOC.',
            'nombres' => 'Jaime',
            'primer_apellido' => 'Tello',
            'segundo_apellido' => 'Suárez',
            'curp' => 'XEXX010101MNEXXXA6',
            'telefono' => '7751297054',
            'area_id' => Area::firstWhere('codigo', '03')->id,
            'departamento_id' => '12',
            'puesto' => 'Subdirector de Proyectos y Calidad',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

        //2.4 - 2021 / 3.6 - 2022 capturista
        $p = Persona::create([
            'email' => 'conmunicacionsocial@ccleh.com',
            'titulo' => 'M.D.P.',
            'nombres' => 'Arnold',
            'primer_apellido' => 'Padilla',
            'segundo_apellido' => 'Rodríguez',
            'curp' => 'XEXX010101HNEXXXA3',
            'telefono' => '7711844797',
            'area_id' => Area::firstWhere('codigo', '02')->id,
            'departamento_id' => '8',
            'puesto' => 'Director de Planeación y Evaluación',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');


//        ****************************************************************************************************************


        //04 - capturista
        $p = Persona::create([
            'email' => 'ccleh.archivo.da@gmail.com',
            'titulo' => 'M.M.T.',
            'nombres' => 'Leticia',
            'primer_apellido' => 'Zarco',
            'segundo_apellido' => 'Mendoza',
            'curp' => 'XEXX010101MNEXXXA5',
            'telefono' => '7712170997',
            'area_id' => Area::firstWhere('codigo', '04')->id,
            'departamento_id' => '14',
            'puesto' => 'Directora de Administración',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

        //4.1 - capturista
        $p = Persona::create([
            'email'               => 'ccleh.recursoshumanos@gmail.com',
            'titulo'              => 'Psic.',
            'nombres'             => 'Víctor Javier',
            'primer_apellido'     => 'Pérez',
            'segundo_apellido'    => 'Hernández',
            'curp'                => 'PEHV891129HHGRRC00',
            'telefono'            => '7712007349',
            'area_id'             => Area::firstWhere('codigo', '04')->id,
            'departamento_id'     => '15',
            'puesto'              => 'Subdirector de Recursos Humanos',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

        //4.2 - capturista
        $p = Persona::create([
            'email' => 'ccleh.adminsitracion@hidalgo.gob.mx',
            'titulo' => 'Lic.',
            'nombres' => 'Elizabeth',
            'primer_apellido' => 'Neri',
            'segundo_apellido' => 'Amador',
            'curp' => 'XEXX010101MNEXXXA4',
            'telefono' => '',
            'area_id' => Area::firstWhere('codigo', '04')->id,
            'departamento_id' => '16',
            'puesto' => 'Subdirector de Recursos Financieros',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

        //4.3 - capturista
        $p = Persona::create([
            'email' => 'ccleh.crisarroyo@gmail.com',
            'titulo' => 'L.C.C.',
            'nombres' => 'Cristiam Isaac',
            'primer_apellido' => 'Arroyo',
            'segundo_apellido' => 'Calderón',
            'curp' => 'XEXX010101HNEXXXA2',
            'telefono' => '',
            'area_id' => Area::firstWhere('codigo', '04')->id,
            'departamento_id' => '17',
            'puesto' => 'Subdirectora de Recursos Materiales',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');


        //4.7 - capturista
        $p = Persona::create([
            'email'               => 'ccleh.Licitaciones@outlook.com',
            'titulo'              => 'Arq.',
            'nombres'             => 'Alfredo',
            'primer_apellido'     => 'Maqueda',
            'segundo_apellido'    => 'Anaya',
            'curp'                => 'MAAA730625HHGQNL05',
            'telefono'            => '7711290143',
            'area_id'             => Area::firstWhere('codigo', '04')->id,
            'departamento_id'     => '21',
            'puesto'              => 'Encargado del Departamento de Licitaciones y Seguimiento a Auditorias',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');



        //05 - capturista
        $p = Persona::create([
            'email'               => 'ccleh.dj.archivo@gmail.com',
            'titulo'              => 'Lic.',
            'nombres'             => 'Ángeles Rocio',
            'primer_apellido'     => 'Garza',
            'segundo_apellido'    => 'Rodríguez',
            'curp'                => 'XEXX010101MNEXXXA3',
            'telefono'            => '7712357396',
            'area_id'             => Area::firstWhere('codigo', '05')->id,
            'departamento_id'     => '23',
            'puesto'              => 'Directora Jurídica',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');


        //2.3 - 2021 / 5.1 - 2022 - capturista
        $p = Persona::create([
            'email'               => 'notificacion@ccleh.com',
            'titulo'              => 'Lic.',
            'nombres'             => 'Agustin',
            'primer_apellido'     => 'Lozano',
            'segundo_apellido'    => 'Jaén',
            'curp'                => 'XEXX010101HNEXXXA1',
            'telefono'            => '',
            'area_id'             => Area::firstWhere('codigo', '02')->id,
            'departamento_id'     => '7',
            'puesto'              => 'Subdirector de Notificadores y Gestión',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');


        //2.5 - 2021 /5.3 - 2022 - capturista
        $p = Persona::create([
            'email'               => 'unidadamparos@ccleh.com',
            'titulo'              => 'Lic.',
            'nombres'             => 'Ángeles Rocio',
            'primer_apellido'     => 'Garza',
            'segundo_apellido'    => 'Rodríguez',
            'curp'                => 'XEXX010101MNEXXXA2',
            'telefono'            => '7712357396',
            'area_id'             => Area::firstWhere('codigo', '02')->id,
            'departamento_id'     => '27',
            'puesto'              => 'Directora Jurídica',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');


        //2.7 - 2021 /5.4 - 2022 - capturista
        $p = Persona::create([
            'email'               => 'asesorjuridico@ccleh.com',
            'titulo'              => 'Lic.',
            'nombres'             => 'Ángeles Rocio',
            'primer_apellido'     => 'Garza',
            'segundo_apellido'    => 'Rodríguez',
            'curp'                => 'XEXX010101MNEXXXA1',
            'telefono'            => '7712357396',
            'area_id'             => Area::firstWhere('codigo', '02')->id,
            'departamento_id'     => '29',
            'puesto'              => 'Directora Jurídica',
            'unidad_presupuestal' => config('app.name_organization')
        ]);
        $user = $p->create_user();
        $user->syncRoles('capturista');

    }
}
