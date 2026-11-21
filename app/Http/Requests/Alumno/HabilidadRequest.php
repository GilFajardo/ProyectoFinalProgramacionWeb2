<?php

namespace App\Http\Requests\Alumno;

use App\Enums\NivelHabilidad;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * CV: habilidad clasificada por área de conocimiento (sin duplicados por alumno).
 */
class HabilidadRequest extends FormRequest
{
    protected $errorBag = 'habilidad';

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
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('cv_habilidades', 'nombre')->where('alumno_id', $this->user()->alumno?->id),
            ],
            'categoria_id' => ['nullable', 'integer', 'exists:categorias,id'],
            'nivel' => ['required', Rule::enum(NivelHabilidad::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya agregaste esta habilidad a tu CV.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['nombre' => 'habilidad', 'categoria_id' => 'área de conocimiento'];
    }
}
