<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\archivo\ArchivoSerieAutorizada;

class ArchivoSerieAutorizadaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //*******************************************************************************
        /*                             SERIES AUTORIZADAS 2021 Y 2022                         */
        //*******************************************************************************
        //01 - 1S.1
        ArchivoSerieAutorizada::create([
            'serie_id' => '1',
            'seccion_id' => '1',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 1S.2
        ArchivoSerieAutorizada::create([
            'serie_id' => '2',
            'seccion_id' => '1',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 2S.1
        ArchivoSerieAutorizada::create([
            'serie_id' => '3',
            'seccion_id' => '2',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 2S.2
        ArchivoSerieAutorizada::create([
            'serie_id' => '4',
            'seccion_id' => '2',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 1C.10
        ArchivoSerieAutorizada::create([
            'serie_id' => '75',
            'seccion_id' => '13',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 11C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '292',
            'seccion_id' => '23',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 11C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '293',
            'seccion_id' => '23',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 11C.20
        ArchivoSerieAutorizada::create([
            'serie_id' => '298',
            'seccion_id' => '23',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //01 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //1.2 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '3',
            'area_id' => '1'
        ]);

        //1.2 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '3',
            'area_id' => '1'
        ]);

        //1.2 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '3',
            'area_id' => '1'
        ]);

        //01 - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '1',
            'area_id' => '1'
        ]);

        //02 - 2S.1
        ArchivoSerieAutorizada::create([
            'serie_id' => '3',
            'seccion_id' => '2',
            'departamento_id' => '5',
            'area_id' => '2'
        ]);

        //02 - 2S.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '5',
            'seccion_id' => '2',
            'departamento_id' => '5',
            'area_id' => '2'
        ]);

        //02 - 2S.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '6',
            'seccion_id' => '2',
            'departamento_id' => '5',
            'area_id' => '2'
        ]);

        //2.1 - 2S.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '6',
            'seccion_id' => '2',
            'departamento_id' => '6',
            'area_id' => '2'
        ]);

        //02 - 2S.5
        ArchivoSerieAutorizada::create([
            'serie_id' => '7',
            'seccion_id' => '2',
            'departamento_id' => '5',
            'area_id' => '2'
        ]);

        //02 - 2C.10
        ArchivoSerieAutorizada::create([
            'serie_id' => '93',
            'seccion_id' => '14',
            'departamento_id' => '5',
            'area_id' => '2'
        ]);

        //02 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '5',
            'area_id' => '2'
        ]);

        //2.1 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '6',
            'area_id' => '2'
        ]);

        //02 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '5',
            'area_id' => '2'
        ]);

        //2.1 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '6',
            'area_id' => '2'
        ]);

        //02 - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '5',
            'area_id' => '2'
        ]);

        //2.4 - 9C.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '247',
            'seccion_id' => '21',
            'departamento_id' => '8',
            'area_id' => '2'
        ]);

        //2.4 - 9C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '248',
            'seccion_id' => '21',
            'departamento_id' => '8',
            'area_id' => '2'
        ]);

        //2.4 - 9C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '250',
            'seccion_id' => '21',
            'departamento_id' => '8',
            'area_id' => '2'
        ]);

        //2.4 - 9C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '258',
            'seccion_id' => '21',
            'departamento_id' => '8',
            'area_id' => '2'
        ]);

        //2.4 - 9C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '260',
            'seccion_id' => '21',
            'departamento_id' => '8',
            'area_id' => '2'
        ]);

        //03 - 1S.1
        ArchivoSerieAutorizada::create([
            'serie_id' => '1',
            'seccion_id' => '1',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 1S.2
        ArchivoSerieAutorizada::create([
            'serie_id' => '2',
            'seccion_id' => '1',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 3C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '108',
            'seccion_id' => '15',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //3.3 - 3C.12
        ArchivoSerieAutorizada::create([
            'serie_id' => '113',
            'seccion_id' => '15',
            'departamento_id' => '12',
            'area_id' => '3'
        ]);

        //3.1 - 7C.12
        ArchivoSerieAutorizada::create([
            'serie_id' => '215',
            'seccion_id' => '19',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //3.1 - 8C.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '222',
            'seccion_id' => '20',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //3.1 - 8C.5
        ArchivoSerieAutorizada::create([
            'serie_id' => '224',
            'seccion_id' => '20',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //3.1 - 8C.11
        ArchivoSerieAutorizada::create([
            'serie_id' => '230',
            'seccion_id' => '20',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //3.1 - 8C.13
        ArchivoSerieAutorizada::create([
            'serie_id' => '232',
            'seccion_id' => '20',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //03 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //3.1 - 8C.22
        ArchivoSerieAutorizada::create([
            'serie_id' => '241',
            'seccion_id' => '20',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //3.1 - 8C.25
        ArchivoSerieAutorizada::create([
            'serie_id' => '244',
            'seccion_id' => '20',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //03 - 11C.9
        ArchivoSerieAutorizada::create([
            'serie_id' => '287',
            'seccion_id' => '23',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //3.2 - 11C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '292',
            'seccion_id' => '23',
            'departamento_id' => '11',
            'area_id' => '3'
        ]);

        //03 - 11C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '282',
            'seccion_id' => '23',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //3.3 - 11C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '282',
            'seccion_id' => '23',
            'departamento_id' => '12',
            'area_id' => '3'
        ]);

        //03 - 11C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '295',
            'seccion_id' => '23',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 11C.18
        ArchivoSerieAutorizada::create([
            'serie_id' => '296',
            'seccion_id' => '23',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 11C.19
        ArchivoSerieAutorizada::create([
            'serie_id' => '297',
            'seccion_id' => '23',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 10C.2
        ArchivoSerieAutorizada::create([
            'serie_id' => '264',
            'seccion_id' => '22',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 11C.10
        ArchivoSerieAutorizada::create([
            'serie_id' => '288',
            'seccion_id' => '23',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 11C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '294',
            'seccion_id' => '23',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 12C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '304',
            'seccion_id' => '24',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //03 - 12C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '305',
            'seccion_id' => '24',
            'departamento_id' => '9',
            'area_id' => '3'
        ]);

        //3.2 - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '11',
            'area_id' => '3'
        ]);

        //3.2 - 12C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '304',
            'seccion_id' => '24',
            'departamento_id' => '11',
            'area_id' => '3'
        ]);

        //3.2 - 12C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '305',
            'seccion_id' => '24',
            'departamento_id' => '11',
            'area_id' => '3'
        ]);

        //3.1 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //3.1 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //3.1 8C.10
        ArchivoSerieAutorizada::create([
            'serie_id' => '229',
            'seccion_id' => '20',
            'departamento_id' => '10',
            'area_id' => '3'
        ]);

        //3.2 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '11',
            'area_id' => '3'
        ]);

        //3.2 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '11',
            'area_id' => '3'
        ]);

        //3.2 - 11C.18
        ArchivoSerieAutorizada::create([
            'serie_id' => '296',
            'seccion_id' => '23',
            'departamento_id' => '11',
            'area_id' => '3'
        ]);

        //3.3 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '12',
            'area_id' => '3'
        ]);

        //3.3 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '12',
            'area_id' => '3'
        ]);

        //3.6 - 9C.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '247',
            'seccion_id' => '21',
            'departamento_id' => '13',
            'area_id' => '3'
        ]);

        //3.6 - 9C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '248',
            'seccion_id' => '21',
            'departamento_id' => '13',
            'area_id' => '3'
        ]);

        //3.6 - 9C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '250',
            'seccion_id' => '21',
            'departamento_id' => '13',
            'area_id' => '3'
        ]);

        //3.6 - 9C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '258',
            'seccion_id' => '21',
            'departamento_id' => '13',
            'area_id' => '3'
        ]);

        //3.6 - 9C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '260',
            'seccion_id' => '21',
            'departamento_id' => '13',
            'area_id' => '3'
        ]);


        //04 - 3C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '108',
            'seccion_id' => '15',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //04 - 3C.11
        ArchivoSerieAutorizada::create([
            'serie_id' => '112',
            'seccion_id' => '15',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //04 - 1C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '80',
            'seccion_id' => '13',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //04 - 6C.18
        ArchivoSerieAutorizada::create([
            'serie_id' => '195',
            'seccion_id' => '18',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        /*2022*/
        //4.4 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);

        //4.4 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);

        //4.4 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);
        /*2022*/


        /*2021*/
        //04 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //04 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //04 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //4.1 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.1 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.1 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.2 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.3 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.7 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '21',
            'area_id' => '4'
        ]);

        //4.7 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '21',
            'area_id' => '4'
        ]);

        //4.7 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '21',
            'area_id' => '4'
        ]);
        /*2021*/


        //04 - 10C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '268',
            'seccion_id' => '22',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //4.1 - 10C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '268',
            'seccion_id' => '22',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.1 - 4C.12
        ArchivoSerieAutorizada::create([
            'serie_id' => '133',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.1 - 4C.13
        ArchivoSerieAutorizada::create([
            'serie_id' => '134',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.1 - 4C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '135',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.1 - 4C.22
        ArchivoSerieAutorizada::create([
            'serie_id' => '143',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.2 - 10C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '268',
            'seccion_id' => '22',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.3 - 10C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '268',
            'seccion_id' => '22',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.4 - 10C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '268',
            'seccion_id' => '22',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);

        //4.7(2022) - 10C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '268',
            'seccion_id' => '22',
            'departamento_id' => '19',
            'area_id' => '4'
        ]);

        //4.7(2021) - 10C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '268',
            'seccion_id' => '22',
            'departamento_id' => '21',
            'area_id' => '4'
        ]);

        //04 - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //4.1 - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.2 - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.3 - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.4 - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);

        //4.7(2022) - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '19',
            'area_id' => '4'
        ]);

        //4.7(2021) - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '21',
            'area_id' => '4'
        ]);

        //4.1 - 10C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '276',
            'seccion_id' => '22',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //04 - 10C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '277',
            'seccion_id' => '22',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //4.1 - 10C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '277',
            'seccion_id' => '22',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.2 - 10C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '277',
            'seccion_id' => '22',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.3 - 10C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '277',
            'seccion_id' => '22',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.4 - 10C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '277',
            'seccion_id' => '22',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);

        //4.7(2022) - 10C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '277',
            'seccion_id' => '22',
            'departamento_id' => '19',
            'area_id' => '4'
        ]);

        //4.7(2021) - 10C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '277',
            'seccion_id' => '22',
            'departamento_id' => '21',
            'area_id' => '4'
        ]);

        //04 - 11C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '292',
            'seccion_id' => '23',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //04 - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //4.1 - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.2 - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.3 - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.4 - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);

        //4.4 - 4C.8
        ArchivoSerieAutorizada::create([
            'serie_id' => '129',
            'seccion_id' => '16',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);

        //4.4 - 11C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '292',
            'seccion_id' => '23',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);

        //4.4 - 11C.20
        ArchivoSerieAutorizada::create([
            'serie_id' => '298',
            'seccion_id' => '23',
            'departamento_id' => '18',
            'area_id' => '4'
        ]);

        //4.7(2022) - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '19',
            'area_id' => '4'
        ]);

        //4.7(2021) - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '21',
            'area_id' => '4'
        ]);

        //4.1 - 4C.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '124',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.1 - 4C.8
        ArchivoSerieAutorizada::create([
            'serie_id' => '129',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.1 - 4C.9
        ArchivoSerieAutorizada::create([
            'serie_id' => '130',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.1 - 4C.10
        ArchivoSerieAutorizada::create([
            'serie_id' => '131',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //04 - 4C.12
        ArchivoSerieAutorizada::create([
            'serie_id' => '133',
            'seccion_id' => '16',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //04 - 4C.13
        ArchivoSerieAutorizada::create([
            'serie_id' => '134',
            'seccion_id' => '16',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //04 - 4C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '135',
            'seccion_id' => '16',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //4.1 - 4C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '136',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //04 - 4C.22
        ArchivoSerieAutorizada::create([
            'serie_id' => '143',
            'seccion_id' => '16',
            'departamento_id' => '14',
            'area_id' => '4'
        ]);

        //4.1 - 4C.26
        ArchivoSerieAutorizada::create([
            'serie_id' => '147',
            'seccion_id' => '16',
            'departamento_id' => '15',
            'area_id' => '4'
        ]);

        //4.2 - 5C.2
        ArchivoSerieAutorizada::create([
            'serie_id' => '151',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '152',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '153',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '165',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '166',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.18
        ArchivoSerieAutorizada::create([
            'serie_id' => '167',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.19
        ArchivoSerieAutorizada::create([
            'serie_id' => '168',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.20
        ArchivoSerieAutorizada::create([
            'serie_id' => '169',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.22
        ArchivoSerieAutorizada::create([
            'serie_id' => '171',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.23
        ArchivoSerieAutorizada::create([
            'serie_id' => '172',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.24
        ArchivoSerieAutorizada::create([
            'serie_id' => '173',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.25
        ArchivoSerieAutorizada::create([
            'serie_id' => '174',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.26
        ArchivoSerieAutorizada::create([
            'serie_id' => '175',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.2 - 5C.28
        ArchivoSerieAutorizada::create([
            'serie_id' => '177',
            'seccion_id' => '17',
            'departamento_id' => '16',
            'area_id' => '4'
        ]);

        //4.7(2022) - 6C.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '180',
            'seccion_id' => '18',
            'departamento_id' => '19',
            'area_id' => '4'
        ]);

        //4.7(2021) - 6C.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '180',
            'seccion_id' => '18',
            'departamento_id' => '21',
            'area_id' => '4'
        ]);

        //4.3 - 6C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '181',
            'seccion_id' => '18',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 6C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '191',
            'seccion_id' => '18',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 6C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '192',
            'seccion_id' => '18',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 6C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '194',
            'seccion_id' => '18',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 6C.18
        ArchivoSerieAutorizada::create([
            'serie_id' => '195',
            'seccion_id' => '18',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 7C.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '206',
            'seccion_id' => '19',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 7C.5
        ArchivoSerieAutorizada::create([
            'serie_id' => '208',
            'seccion_id' => '19',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 7C.8
        ArchivoSerieAutorizada::create([
            'serie_id' => '211',
            'seccion_id' => '19',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 7C.9
        ArchivoSerieAutorizada::create([
            'serie_id' => '212',
            'seccion_id' => '19',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 7C.13
        ArchivoSerieAutorizada::create([
            'serie_id' => '216',
            'seccion_id' => '19',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 7C.14
        ArchivoSerieAutorizada::create([
            'serie_id' => '217',
            'seccion_id' => '19',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //4.3 - 7C.15
        ArchivoSerieAutorizada::create([
            'serie_id' => '218',
            'seccion_id' => '19',
            'departamento_id' => '17',
            'area_id' => '4'
        ]);

        //06 - 2S.2
        ArchivoSerieAutorizada::create([
            'serie_id' => '4',
            'seccion_id' => '2',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 1C.1
        ArchivoSerieAutorizada::create([
            'serie_id' => '66',
            'seccion_id' => '13',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 1C.3
        ArchivoSerieAutorizada::create([
            'serie_id' => '68',
            'seccion_id' => '13',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 1C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '71',
            'seccion_id' => '13',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 1C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '72',
            'seccion_id' => '13',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 1C.9
        ArchivoSerieAutorizada::create([
            'serie_id' => '74',
            'seccion_id' => '13',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 1C.10
        ArchivoSerieAutorizada::create([
            'serie_id' => '75',
            'seccion_id' => '13',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 1C.13
        ArchivoSerieAutorizada::create([
            'serie_id' => '78',
            'seccion_id' => '13',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 2C.5
        ArchivoSerieAutorizada::create([
            'serie_id' => '88',
            'seccion_id' => '14',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 2C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '89',
            'seccion_id' => '14',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 2C.8
        ArchivoSerieAutorizada::create([
            'serie_id' => '91',
            'seccion_id' => '14',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 2C.9
        ArchivoSerieAutorizada::create([
            'serie_id' => '92',
            'seccion_id' => '14',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 2C.10
        ArchivoSerieAutorizada::create([
            'serie_id' => '93',
            'seccion_id' => '14',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 6C.6
        ArchivoSerieAutorizada::create([
            'serie_id' => '183',
            'seccion_id' => '18',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //6.1 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '24',
            'area_id' => '6'
        ]);

        //6.1 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '24',
            'area_id' => '6'
        ]);

        //6.1 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '24',
            'area_id' => '6'
        ]);

        //6.4 - 8C.16
        ArchivoSerieAutorizada::create([
            'serie_id' => '235',
            'seccion_id' => '20',
            'departamento_id' => '25',
            'area_id' => '6'
        ]);

        //6.4 - 8C.17
        ArchivoSerieAutorizada::create([
            'serie_id' => '236',
            'seccion_id' => '20',
            'departamento_id' => '25',
            'area_id' => '6'
        ]);

        //6.4 - 8C.21
        ArchivoSerieAutorizada::create([
            'serie_id' => '240',
            'seccion_id' => '20',
            'departamento_id' => '25',
            'area_id' => '6'
        ]);

        //06 - 2S.2
        ArchivoSerieAutorizada::create([
            'serie_id' => '4',
            'seccion_id' => '2',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //6.4 - 2S.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '6',
            'seccion_id' => '2',
            'departamento_id' => '25',
            'area_id' => '6'
        ]);

        //6.1 - 2S.5
        ArchivoSerieAutorizada::create([
            'serie_id' => '7',
            'seccion_id' => '2',
            'departamento_id' => '24',
            'area_id' => '6'
        ]);

        //06 - 12C.4
        ArchivoSerieAutorizada::create([
            'serie_id' => '302',
            'seccion_id' => '24',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 1C.10
        ArchivoSerieAutorizada::create([
            'serie_id' => '75',
            'seccion_id' => '13',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 1C.13
        ArchivoSerieAutorizada::create([
            'serie_id' => '78',
            'seccion_id' => '13',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

        //06 - 10C.7
        ArchivoSerieAutorizada::create([
            'serie_id' => '269',
            'seccion_id' => '22',
            'departamento_id' => '23',
            'area_id' => '6'
        ]);

    }
}
