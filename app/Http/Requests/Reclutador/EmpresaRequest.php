<?php

namespace App\Http\Requests\Reclutador;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Datos de la empresa capturados por el reclutador (alta o edición).
 * "activa" y "verificada" no se aceptan aquí: solo los gestiona el administrador.
 */
class EmpresaRequest extends FormRequest
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
        $empresaId = $this->user()->reclutador?->empresa_id;

        return [
            'nombre' => ['required', 'string', 'max:150'],
            'razon_social' => ['nullable', 'string', 'max:200'],
            'rfc' => ['nullable', 'string', 'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/i', Rule::unique('empresas', 'rfc')->ignore($empresaId)],
            'sector' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'sitio_web' => ['nullable', 'url:http,https', 'max:255'],
            'email_contacto' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9 +()-]{7,20}$/'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'municipio' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rfc.regex' => 'El RFC no tiene un formato válido.',
            'telefono.regex' => 'El teléfono solo debe contener números, espacios y los símbolos + ( ) -.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('rfc')) {
            $this->merge(['rfc' => mb_strtoupper(trim((string) $this->input('rfc')))]);
        }
    }
}
