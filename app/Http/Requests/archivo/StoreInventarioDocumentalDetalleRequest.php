<?php

namespace App\Http\Requests\archivo;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventarioDocumentalDetalleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
//            'ubicacion_fisica' => ['required', 'min:5'],
            'ubicacion_topografica' => ['required', 'min:5'],
            'no_expediente' => ['required', 'min:20','max:27'],
            'descripcion' => ['required', 'min:5'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_final' => ['nullable', 'date'],
            'observaciones' => ['nullable'],
//            'orden' => ['required'],
            'id' => 'nullable',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
