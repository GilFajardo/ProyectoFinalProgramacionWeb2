<?php

namespace App\Http\Requests\Alumno;

use App\Enums\NivelFormacion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * CV: formación académica, curso o certificación.
 */
class FormacionRequest extends FormRequest
{
    protected $errorBag = 'formacion';

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
            'institucion' => ['required', 'string', 'max:150'],
            'titulo' => ['required', 'string', 'max:150'],
            'nivel' => ['required', Rule::enum(NivelFormacion::class)],
            'fecha_inicio' => ['nullable', 'date', 'before_or_equal:today'],
            'en_curso' => ['boolean'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['en_curso' => $this->boolean('en_curso')]);
    }

    /**
     * @return array<string, mixed>
     */
    public function datos(): array
    {
        $datos = $this->validated();

        if ($datos['en_curso']) {
            $datos['fecha_fin'] = null;
        }

        return $datos;
    }
}
