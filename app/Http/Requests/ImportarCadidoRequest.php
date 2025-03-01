<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportarCadidoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'anio' => ['required','numeric'],
            //'seccion_id' => ['required','numeric','exists:archivo_secciones,id'],
            'importarCADIDO' => ['required','file'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
