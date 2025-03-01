<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListarCadidosRequest extends FormRequest {
    public function rules(): array {
        return [
            'anio' => ['required','numeric'],
            'seccion' => ['required','numeric','exists:archivo_secciones,id'],
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
