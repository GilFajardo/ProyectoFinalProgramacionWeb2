<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Alta de un alumno (solicitante) con su perfil básico.
 */
class RegistroAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'matricula' => ['required', 'string', 'regex:/^\d{6,10}$/', 'unique:alumnos,matricula'],
            'carrera' => ['required', 'string', Rule::in(config('upslp.carreras'))],
            'cuatrimestre' => ['nullable', 'integer', 'between:1,10'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'acepto_aviso' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'matricula.regex' => 'La matrícula debe tener entre 6 y 10 dígitos.',
            'acepto_aviso.accepted' => 'Debes aceptar el aviso de privacidad.',
        ];
    }
}
