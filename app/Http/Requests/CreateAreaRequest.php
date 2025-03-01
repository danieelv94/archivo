<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAreaRequest extends FormRequest
{
    public function rules(): array
    {

        $rules  = [
            'nombre' => ['required','string', 'min:5'],
            'tipo_area' => ['required', Rule::in( array_keys( config('ccleh.lista_tipo_areas') ) )],
            'siglas' => ['required','string', 'min:1', Rule::unique('areas','siglas') ],
            'titular_id' => ['required', Rule::exists('users', 'id')],
            'codigo' => ['required','string','min:1','max:10',Rule::unique('areas','codigo')],
            'activo' =>  ['required']
        ];

        if ( $this->has('id')) {
            $rules['siglas'] = ['required','string', 'min:1',
                                Rule::unique('areas','siglas')->ignore( $this->id ) ];
            $rules['codigo'] = ['required','string', 'min:1',
                                Rule::unique('areas','codigo')->ignore( $this->id ) ];
        }


        return $rules;
    }

    public function messages(){

        return [
            'codigo.unique' => 'La clave debe ser única para cada Unidad administrativa.',

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
