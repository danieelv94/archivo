<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuscarCadidoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'anio' => ['required','integer'],
            'seccion_id' => ['required','integer','exists:archivo_secciones,id'],
            'serie_id' => ['required','integer','exists:archivo_series,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
