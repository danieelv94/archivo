<?php

namespace App\Http\Requests\archivo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UbicacionesTopograficaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'id' => ['nullable', 'integer'],
            'ubicacion' => ['required', 'min:5'],
            'bien_mueble' => ['nullable', 'min:5'],
            'no_inventario' => ['nullable'],
            'departamentos_id' => ['required','exists:departamentos,id'],
        ];

        return $rules;
    }
}
