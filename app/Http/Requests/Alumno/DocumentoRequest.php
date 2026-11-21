<?php

namespace App\Http\Requests\Alumno;

use App\Enums\TipoDocumento;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * CV: documento probatorio. Se valida el tipo real del archivo (MIME) y su tamaño.
 */
class DocumentoRequest extends FormRequest
{
    protected $errorBag = 'documento';

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
            'nombre' => ['required', 'string', 'max:150'],
            'tipo' => ['required', Rule::enum(TipoDocumento::class)],
            'archivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['nombre' => 'nombre del documento'];
    }
}
