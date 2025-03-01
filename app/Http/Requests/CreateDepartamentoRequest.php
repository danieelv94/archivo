<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDepartamentoRequest extends FormRequest
{
    public function rules(): array
    {

        $rules = [
            'clave' => 'required|string',
            'nombre' => 'required|string',
            'iniciales' => ['required','string', 'min:1' ],//, Rule::unique('departamentos','iniciales')
            'titular_id' => ['nullable', 'exists:personas,id'],
            'padre_id' => ['nullable'],
            'area_id' => ['required', 'exists:areas,id'],
            'activo' => ['required']
        ];

        if ( $this->has('id') ) {
            // $rules['iniciales'] = ['required','string', 'min:1', Rule::unique('departamentos','iniciales')->ignore( $this->id ) ];
            if($this->padre_id != null){
                $rules['padre_id'] = ['nullable', 'exists:departamentos,id', 'different:id'];
            }
        }else if($this->padre_id != null){
            $rules['padre_id'] = ['nullable', 'exists:departamentos,id'];
        }



        return $rules;

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'padre_id.exists' => 'No fue localizado el departamento Superior.',
            'padre_id' => 'departamento Superior',
            'id' => 'departamento',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
