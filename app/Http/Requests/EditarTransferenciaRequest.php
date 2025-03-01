<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarTransferenciaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required','numeric','exists:transferencia_primarias,id'],
            'no_oficio_transferencia' => ['nullable','string'],
            'no_caja' => ['nullable','numeric'],
            'fecha_entrega' => ['nullable','date'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
