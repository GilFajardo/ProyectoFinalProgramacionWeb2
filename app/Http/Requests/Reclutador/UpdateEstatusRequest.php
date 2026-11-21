<?php

namespace App\Http\Requests\Reclutador;

use App\Enums\EstatusPostulacion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Cambio de estatus de una postulación (ACEPTADO / RECHAZADO / EN PROCESO).
 */
class UpdateEstatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('postulacion'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'estatus' => ['required', Rule::enum(EstatusPostulacion::class)],
            'comentario_reclutador' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
