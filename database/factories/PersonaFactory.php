<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PersonaFactory extends Factory
{

    protected $model = Persona::class;

    public function definition(): array
    {
        return [
            'titulo' => $this->faker->randomElement(['Lic', 'Dr', 'Mtr', 'CC', 'HDP', 'C', 'LAE', 'PSQ']),
            'nombres' => $this->faker->firstName(),
            'primer_apellido' => $this->faker->lastName(),
            'segundo_apellido' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->isbn10(),
            'curp' => $this->faker->regexify('[A-Z]{1}[AEIOU]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[HM]{1}(AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{1}[0-9]{1}'),
            'sexo' => $this->faker->randomElement(['M', 'H']),
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '31 december 2000' ),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'area_id' => rand(1, 6),
            'departamento_id' => NULL,
            'puesto' => $this->faker->jobTitle(),
            'unidad_presupuestal' => config('app.name_organization')
        ];
    }
}
