<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarTransferenciaDetalleRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'eliminar_expedientes' => ['nullable','array'],
            'expedientes' => ['required','array'],
            'expedientes.*.id' => ['nullable','integer','exists:transferencia_primaria_detalles,id'],
            'expedientes.*.inventario_documental_detalle_id' => ['required','integer','exists:inventario_documental_detalles,id'],
            'expedientes.*.transferencia_primaria_id' => ['required','integer','exists:transferencia_primarias,id'],
            'expedientes.*.no_fojas' => ['required','integer'],
            'expedientes.*.descripcion' => ['nullable','string'],
            'expedientes.*.observaciones' => ['nullable','string'],
        ];
        if( $this->has('expedientes') && count($this->expedientes) > 1 ){
            $rules['expedientes.*.no_legajo'] = ['required','integer'];
            $rules['expedientes.*.fecha_inicio'] = ['required','date'];
            $rules['expedientes.*.fecha_final'] = ['required','date'];
        }else{
            $rules['expedientes.*.no_legajo'] = ['nullable','integer'];
            $rules['expedientes.*.fecha_inicio'] = ['nullable','date'];
            $rules['expedientes.*.fecha_final'] = ['nullable','date'];
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
