<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Alta de un reclutador junto con los datos básicos de su empresa.
 */
class RegistroReclutadorRequest extends FormRequest
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
            'puesto' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9 +()-]{7,20}$/'],
            'empresa_nombre' => ['required', 'string', 'max:150'],
            'rfc' => ['nullable', 'string', 'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/i', 'unique:empresas,rfc'],
            'sector' => ['nullable', 'string', 'max:100'],
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
            'rfc.regex' => 'El RFC no tiene un formato válido.',
            'rfc.unique' => 'Esta empresa ya está registrada. Pide a Vinculación que te vincule a ella.',
            'telefono.regex' => 'El teléfono solo debe contener números, espacios y los símbolos + ( ) -.',
            'acepto_aviso.accepted' => 'Debes aceptar el aviso de privacidad.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('rfc')) {
            $this->merge(['rfc' => mb_strtoupper(trim((string) $this->input('rfc')))]);
        }
    }
}
