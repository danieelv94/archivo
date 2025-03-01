<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    /*GENERA*/
    Departamento::create([
        'clave' => '01',
        'nombre' => 'Dirección General',
        'iniciales' => 'DG',
        'area_id' => '1',
    ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '1.1',
            'nombre' => 'Secretario Particular',
            'iniciales' => 'SP',
            'area_id' => '1',
        ]);

        /*GENERA*/
        Departamento::create([
            'clave' => '1.2',
            'nombre' => 'Unidad de Correspondencia',
            'iniciales' => 'UC',
            'area_id' => '1',
        ]);

        /*2021*/
        Departamento::create([
            'clave' => '1.2',
            'nombre' => 'Archivo (2021)',
            'iniciales' => 'A',
            'area_id' => '1',
        ]);

    /*GENERA*/
    Departamento::create([
        'clave' => '02',
        'nombre' => 'Dirección Conciliación',
        'iniciales' => 'DC',
        'area_id' => '2',
    ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '2.1',
            'nombre' => 'Coordinación De Conciliadores',
            'iniciales' => 'CC',
            'area_id' => '2',
        ]);

        /*2021*/
        Departamento::create([
            'clave' => '2.3',
            'nombre' => 'Subdirección de Notificadores y Gestión (2021)',
            'iniciales' => 'SNyG',
            'area_id' => '2',
        ]);

        /*2021*/
        Departamento::create([
            'clave' => '2.4',
            'nombre' => 'Departamento de Comunicación Social (2021)',
            'iniciales' => 'DCS',
            'area_id' => '2',
        ]);

    /*GENERA*/
    Departamento::create([
        'clave' => '03',
        'nombre' => 'Dirección de Planeación y Evaluación',
        'iniciales' => 'DPyE',
        'area_id' => '3',
    ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '3.1',
            'nombre' => 'Subdirección de TICS',
            'iniciales' => 'STICS',
            'area_id' => '3',
        ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '3.2',
            'nombre' => 'Subdirección de Planeación y Transparencia',
            'iniciales' => 'SPyT',
            'area_id' => '3',
        ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '3.3',
            'nombre' => 'Subdirección de Proyectos y Calidad',
            'iniciales' => 'SPyC',
            'area_id' => '3',
        ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '3.6',
            'nombre' => 'Departamento De Comunicación Social',
            'iniciales' => 'DCS',
            'area_id' => '3',
        ]);

    /*GENERA*/
    Departamento::create([
        'clave' => '04',
        'nombre' => 'Dirección de Administración',
        'iniciales' => 'DA',
        'area_id' => '4',
    ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '4.1',
            'nombre' => 'Subdirección de Recursos Humanos',
            'iniciales' => 'SRH',
            'area_id' => '4',
        ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '4.2',
            'nombre' => 'Subdirección de Recursos Financieros',
            'iniciales' => 'SRF',
            'area_id' => '4',
        ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '4.3',
            'nombre' => 'Subdirección de Recursos Materiales',
            'iniciales' => 'SRM',
            'area_id' => '4',
        ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '4.4',
            'nombre' => 'Coordinación de Archivo',
            'iniciales' => 'CA',
            'area_id' => '4',
        ]);
        /*GENERA*/
        Departamento::create([
            'clave' => '4.7',
            'nombre' => 'Departamento de Licitaciones',
            'iniciales' => 'DL',
            'area_id' => '4',
        ]);

        /*2021*/
        Departamento::create([
            'clave' => '4.4',
            'nombre' => 'Subdirección de Psicología Organizacional (2021)',
            'iniciales' => 'SPO',
            'area_id' => '4',
        ]);

        /*2021*/
        Departamento::create([
            'clave' => '4.7',
            'nombre' => 'Departamento de Seguimiento a Auditorias (2021)',
            'iniciales' => 'DSA',
            'area_id' => '4',
        ]);


    Departamento::create([
        'clave' => '05',
        'nombre' => 'Órgano Interno de Control',
        'iniciales' => 'OIC',
        'area_id' => '5',
    ]);

    /*GENERA*/
    Departamento::create([
        'clave' => '05',
        'nombre' => 'Dirección Jurídica',
        'iniciales' => 'DJ',
        'area_id' => '6',
    ]);

        /*GENERA*/
        Departamento::create([
            'clave' => '5.1',
            'nombre' => 'Subdirección De Notificadores Y Gestión',
            'iniciales' => 'SNyG',
            'area_id' => '6',
        ]);

        /*GENERA*/
        Departamento::create([
            'clave' => '5.4',
            'nombre' => 'Asesor Jurídico',
            'iniciales' => 'AJ',
            'area_id' => '6',
        ]);

    /*GENERA*/
    Departamento::create([
        'clave' => '2.3',
        'nombre' => 'Orientador',
        'iniciales' => 'O',
        'area_id' => '2',
    ]);

    /*GENERA*/
    Departamento::create([
        'clave' => '2.5',
        'nombre' => 'Unidad de Amparos (2021)',
        'iniciales' => 'UA',
        'area_id' => '2',
    ]);

    /*GENERA*/
    Departamento::create([
        'clave' => '5.3',
        'nombre' => 'Unidad de Amparos',
        'iniciales' => 'UA',
        'area_id' => '6',
    ]);

    /*GENERA*/
    Departamento::create([
        'clave' => '2.7',
        'nombre' => 'Asesor Jurídico (2021)',
        'iniciales' => 'AJ',
        'area_id' => '2',
    ]);






















//        Departamento::create([
//            'clave' => '1.1',
//            'nombre' => 'Secretario Particular',
//            'iniciales' => 'SP',
//            'area_id' => '1',
//        ]);
//
//        Departamento::create([
//            'clave' => '1.2',
//            'nombre' => 'Archivo',
//            'iniciales' => 'A',
//            'area_id' => '1',
//        ]);
//
//        Departamento::create([
//            'clave' => '2.1',
//            'nombre' => 'Conciliador de Asuntos Individuales',
//            'iniciales' => 'CAI',
//            'area_id' => '2',
//        ]);
//
//        Departamento::create([
//            'clave' => '2.2',
//            'nombre' => 'Conciliador de Asuntos Colectivos',
//            'iniciales' => 'CAC',
//            'area_id' => '2',
//        ]);
//
//
//        Departamento::create([
//            'clave' => '2.3',
//            'nombre' => 'Subdirección de Notificadores y Gestión',
//            'iniciales' => 'SNyG',
//            'area_id' => '2',
//        ]);
//
//        Departamento::create([
//            'clave' => '2.4',
//            'nombre' => 'Departamento de Comunicación Social',
//            'iniciales' => 'DCS',
//            'area_id' => '2',
//        ]);
//
//        Departamento::create([
//            'clave' => '2.5',
//            'nombre' => 'Unidad de Amparos',
//            'iniciales' => 'UA',
//            'area_id' => '2',
//        ]);
//
//        Departamento::create([
//            'clave' => '2.6',
//            'nombre' => 'Notificadores',
//            'iniciales' => 'N',
//            'area_id' => '2',
//        ]);
//
//        Departamento::create([
//            'clave' => '2.7',
//            'nombre' => 'Asesor Jurídico',
//            'iniciales' => 'AJ',
//            'area_id' => '2',
//        ]);
//
//
//
//        Departamento::create([
//            'clave' => '3.1',
//            'nombre' => 'Subdirección de TICS',
//            'iniciales' => 'STICS',
//            'area_id' => '3',
//        ]);
//
//        Departamento::create([
//            'clave' => '3.2',
//            'nombre' => 'Subdirección de Planeación',
//            'iniciales' => 'SP',
//            'area_id' => '3',
//        ]);
//
//        Departamento::create([
//            'clave' => '3.3',
//            'nombre' => 'Subdirección de Proyectos y Calidad',
//            'iniciales' => 'SPyC',
//            'area_id' => '3',
//        ]);
//
//        Departamento::create([
//            'clave' => '3.4',
//            'nombre' => 'Departamento de Sistemas de Información',
//            'iniciales' => 'DSI',
//            'area_id' => '3',
//        ]);
//
//        Departamento::create([
//            'clave' => '3.5',
//            'nombre' => 'Departamento de Planeación',
//            'iniciales' => 'DP',
//            'area_id' => '3',
//        ]);
//
//        Departamento::create([
//            'clave' => '3.6',
//            'nombre' => 'Departamento de Proyectos',
//            'iniciales' => 'DP',
//            'area_id' => '3',
//        ]);
//
//        Departamento::create([
//            'clave' => '3.7',
//            'nombre' => 'Departamento de Calidad',
//            'iniciales' => 'CD',
//            'area_id' => '3',
//        ]);
//
//        Departamento::create([
//            'clave' => '3.8',
//            'nombre' => 'Unidad de Transparencia',
//            'iniciales' => 'UT',
//            'area_id' => '3',
//        ]);
//
//        Departamento::create([
//            'clave' => '3.9',
//            'nombre' => 'Secretaria Técnica de la Junta de Gobierno del CCLEH',
//            'iniciales' => 'STJGCCLEH',
//            'area_id' => '3',
//        ]);
//
//        Departamento::create([
//            'clave' => '4.1',
//            'nombre' => 'Subdirección de Recursos Humanos',
//            'iniciales' => 'SRH',
//            'area_id' => '4',
//        ]);
//
//        Departamento::create([
//            'clave' => '4.2',
//            'nombre' => 'Subdirección de Recursos Financieros',
//            'iniciales' => 'SRF',
//            'area_id' => '4',
//        ]);
//
//        Departamento::create([
//            'clave' => '4.3',
//            'nombre' => 'Subdirección de Recursos Materiales',
//            'iniciales' => 'SRM',
//            'area_id' => '4',
//        ]);
//
//        Departamento::create([
//            'clave' => '4.4',
//            'nombre' => 'Subdirección de Psicología Organizacional',
//            'iniciales' => 'SPO',
//            'area_id' => '4',
//        ]);
//
//        Departamento::create([
//            'clave' => '4.5',
//            'nombre' => 'Departamento de Evaluación y Profesionalización',
//            'iniciales' => 'DEyP',
//            'area_id' => '4',
//        ]);
//
//        Departamento::create([
//            'clave' => '4.6',
//            'nombre' => 'Departamento de Contabilidad',
//            'iniciales' => 'DC',
//            'area_id' => '4',
//        ]);
//
//        Departamento::create([
//            'clave' => '4.7',
//            'nombre' => 'Departamento de Seguimiento a Auditorías',
//            'iniciales' => 'DSA',
//            'area_id' => '4',
//        ]);
//
//        Departamento::create([
//            'clave' => '05',
//            'nombre' => 'Dirección Jurídica',
//            'iniciales' => 'DJ',
//            'area_id' => '5',
//        ]);
//
//        Departamento::create([
//            'clave' => '06',
//            'nombre' => 'Órgano interno de control',
//            'iniciales' => 'OIC',
//            'area_id' => '6',
//        ]);

    }
}
