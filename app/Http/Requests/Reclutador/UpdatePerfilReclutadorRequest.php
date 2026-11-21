<?php

namespace App\Http\Requests\Reclutador;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Perfil del reclutador (cuenta + puesto y teléfono).
 */
class UpdatePerfilReclutadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->esReclutador();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'puesto' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9 +()-]{7,20}$/'],
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
            'telefono.regex' => 'El teléfono solo debe contener números, espacios y los símbolos + ( ) -.',
            'password_actual.current_password' => 'La contraseña actual es incorrecta.',
            'password_actual.required_with' => 'Escribe tu contraseña actual para cambiarla.',
        ];
    }
}
