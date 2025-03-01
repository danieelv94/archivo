<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DepartamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'clave' => $this->faker->randomElement(['1.1', '2.1', '3.1', '4.1']),
            'nombre' => $this->faker->word(),
            'iniciales' => $this->faker->lexify('????'),
            'titular_id' => NULL,
            'padre_id' => NULL,
            'area_id' => rand(1,6),
        ];
    }
}
