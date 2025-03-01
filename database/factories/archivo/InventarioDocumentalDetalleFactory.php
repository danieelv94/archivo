<?php

namespace Database\Factories\archivo;

use App\Models\archivo\InventarioDocumentalDetalle;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventarioDocumentalDetalleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = InventarioDocumentalDetalle::class;
    public function definition()
    {
        return [
            'ubicacion_fisica' => $this->faker->word(),
            'ubicacion_topografica' => $this->faker->word(),
            'no_expediente' => 'CCLEH-03*1S.2/'.$this->faker->numberBetween(1,99999).'-2022',
            'descripcion' => $this->faker->word(),
            'fecha_inicio' => $this->faker->date('Y-m-d'),
            'fecha_final' => $this->faker->date('Y-m-d'),
            'observaciones' => $this->faker->word(),
            'inventario_documentales_id' => 1,
            'persona_id' => 1,
        ];
    }
}
