<?php

namespace App\Http\Requests\Alumno;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * CV: empleo anterior, práctica o estadía.
 */
class ExperienciaRequest extends FormRequest
{
    /**
     * Los errores se agrupan en esta bolsa para mostrarlos en su sección del CV.
     */
    protected $errorBag = 'experiencia';

    public function authorize(): bool
    {
        return $this->user()->esAlumno();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'empresa' => ['required', 'string', 'max:150'],
            'puesto' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'fecha_inicio' => ['required', 'date', 'before_or_equal:today'],
            'actual' => ['boolean'],
            'fecha_fin' => ['nullable', 'required_unless:actual,1', 'date', 'after_or_equal:fecha_inicio', 'before_or_equal:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha_fin.required_unless' => 'Indica la fecha de fin o marca «Trabajo aquí actualmente».',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['actual' => $this->boolean('actual')]);
    }

    /**
     * Datos listos para guardar: sin fecha de fin si es el empleo actual.
     *
     * @return array<string, mixed>
     */
    public function datos(): array
    {
        $datos = $this->validated();

        if ($datos['actual']) {
            $datos['fecha_fin'] = null;
        }

        return $datos;
    }
}
