<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarCadidoRequest extends FormRequest {
    public function rules(): array {
        $rules = [
            'id' => ['nullable','numeric','exists:cadidos,id'],
            'anio' => ['required','numeric'],

            'seccion_id' => ['required','numeric','exists:archivo_secciones,id'],
            'serie_id' => ['required','numeric','exists:archivo_series,id'],

            'valor_primario_administrativa' => ['nullable','numeric'],
            'valor_primario_fiscal' => ['nullable','numeric'],
            'valor_primario_legal' => ['nullable','numeric'],

            'valor_secundario_informativo' =>  ['nullable','boolean'],
            'valor_secundario_evidencial' => ['nullable','boolean'],
            'valor_secundario_testimonial' => ['nullable','boolean'],

            'tiempo_guarda_tramite' => ['required','numeric','min:1'],
            'tiempo_guarda_concentracion' => ['required','numeric','min:1'],

            'fundamento_legal' => ['required','string'],

            'clasificacion_publica' => ['nullable','boolean'],
            'clasificacion_reservada' => ['nullable','boolean'],
            'clasificacion_confidencial' => ['nullable','boolean'],

            'destino_final' => ['required','string',Rule::in(config('enums.validar_destino_final'))],

            'particularidades' => ['nullable','string'],

            'candado' => ['required','boolean'],
        ];

        return $rules;
    }

    protected function prepareForValidation() {
        $this->merge([
            'valor_secundario_informativo' => $this->toBoolean($this->valor_secundario_informativo),
            'valor_secundario_evidencial' => $this->toBoolean($this->valor_secundario_evidencial),
            'valor_secundario_testimonial' => $this->toBoolean($this->valor_secundario_testimonial),

            'clasificacion_publica' => $this->toBoolean($this->clasificacion_publica),
            'clasificacion_reservada' => $this->toBoolean($this->clasificacion_reservada),
            'clasificacion_confidencial' => $this->toBoolean($this->clasificacion_confidencial),

            'candado' => $this->toBoolean($this->candado),
        ]);
    }

    private function toBoolean($booleable)
    {
        return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    public function authorize(): bool {
        return true;
    }
}
