<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObservacionesPortadaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required','numeric','exists:transferencia_primaria_detalles,id'],
            'portada_observaciones' => ['required','string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
