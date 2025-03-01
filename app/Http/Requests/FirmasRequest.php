<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FirmasRequest extends FormRequest
{
    public function rules(): array
    {
        return [
             'elaboro' => ['nullable','array'],
             'elaboro.*.nombre' => ['nullable','string'],
             'elaboro.*.cargo' => ['nullable','string'],
             'valido' => ['nullable','array'],
             'valido.*.nombre' => ['nullable','string'],
             'valido.*.cargo' => ['nullable','string'],
             'recibio' => ['nullable','array'],
             'recibio.*.nombre' => ['nullable','string'],
             'recibio.*.cargo' => ['nullable','string'],
             'autorizo' => ['nullable','array'],
             'autorizo.*.nombre' => ['nullable','string'],
             'autorizo.*.cargo' => ['nullable','string'],
             'reviso' => ['nullable','array'],
             'reviso.*.nombre' => ['nullable','string'],
             'reviso.*.cargo' => ['nullable','string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
