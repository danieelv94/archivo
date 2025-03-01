<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonaRequest extends FormRequest
{
    public function rules(): array
    {

        $rules = [
            'titulo' => 'nullable|string',
            'nombres' => 'required|string',
            'primer_apellido' => 'required|string',
            'segundo_apellido' => 'required|string',
            'telefono' => 'nullable|string|min:10|max:10',
            'sexo' => ['nullable', Rule::in(['M', 'H', 'm', 'h'])],
            'fecha_nacimiento' => 'nullable|date|date_format:Y-m-d',
            'area_id' => ['required', 'exists:areas,id'],
            'departamento_id' => ['required', 'exists:departamentos,id'],
            'puesto' => ['nullable'],
            'unidad_presupuestal' => ['nullable'],
        ];
        if ( ! $this->has('id') ) {
            $rules['password'] = 'required|min:6';
            $rules['rfc'] = ['nullable', 'string','min:10', Rule::unique('personas', 'rfc') ];
            $rules['curp'] = ['required', 'string','min:18', Rule::unique('personas', 'curp') ];
            $rules['email'] = ['required', 'email', Rule::unique('personas', 'email') ];
        } else {
            $rules['rfc'] = ['nullable', 'string','min:10', Rule::unique('personas', 'rfc')->ignore( $this->id ) ];
            $rules['curp'] = ['required', 'string','min:18', Rule::unique('personas', 'curp')->ignore( $this->id ) ];
            $rules['email'] = ['required', 'email', Rule::unique('personas', 'email')->ignore( $this->id ) ];
        }

        return $rules;

    }

    public function messages(){

        return [
            'departamento_id' => 'Departamento',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
