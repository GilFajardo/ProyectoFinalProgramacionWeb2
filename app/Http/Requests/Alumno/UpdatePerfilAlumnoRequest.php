<?php

namespace App\Http\Requests\Alumno;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Datos principales del perfil del alumno (cuenta + datos generales del CV).
 */
class UpdatePerfilAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->esAlumno();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $alumno = $this->user()->alumno;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'matricula' => ['required', 'string', 'regex:/^\d{6,10}$/', Rule::unique('alumnos', 'matricula')->ignore($alumno?->id)],
            'carrera' => ['required', 'string', Rule::in(config('upslp.carreras'))],
            'cuatrimestre' => ['nullable', 'integer', 'between:1,10'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9 +()-]{7,20}$/'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:-15 years', 'after:1940-01-01'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'resumen' => ['nullable', 'string', 'max:1500'],
            'linkedin_url' => ['nullable', 'url:https', 'max:255'],
            'portafolio_url' => ['nullable', 'url:http,https', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=3000,max_height=3000'],
            'password_actual' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'matricula.regex' => 'La matrícula debe tener entre 6 y 10 dígitos.',
            'telefono.regex' => 'El teléfono solo debe contener números, espacios y los símbolos + ( ) -.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento no es válida.',
            'password_actual.current_password' => 'La contraseña actual es incorrecta.',
            'password_actual.required_with' => 'Escribe tu contraseña actual para cambiarla.',
        ];
    }
}
