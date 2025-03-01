<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuscarExpedientesTransferenciaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'seccion_id' => ['required', 'integer', 'exists:archivo_secciones,id'],
            'serie_id' => ['required', 'integer', 'exists:archivo_series,id'],
            'departamento_id' => ['required', 'integer', 'exists:archivo_series,id'],
            'no_expediente' => ['required', 'string'],
            'page' => ['nullable', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
