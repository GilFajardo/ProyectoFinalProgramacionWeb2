<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Formulario público de contacto. Incluye un campo trampa (honeypot)
 * oculto que los bots suelen llenar.
 */
class ContactoRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'asunto' => ['required', 'string', 'min:5', 'max:150'],
            'mensaje' => ['required', 'string', 'min:20', 'max:3000'],
            'sitio_web' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sitio_web.prohibited' => 'No se pudo enviar el mensaje.',
        ];
    }
}
