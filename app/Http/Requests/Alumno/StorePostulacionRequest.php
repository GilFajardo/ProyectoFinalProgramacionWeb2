<?php

namespace App\Http\Requests\Alumno;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

/**
 * Postulación a una vacante. La autorización aplica la regla de negocio completa
 * (VacantePolicy::postular): alumno, vacante vigente y sin postulación previa.
 */
class StorePostulacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::inspect('postular', $this->route('vacante'))->allowed();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'carta_presentacion' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * En lugar de una página 403, regresa a la vacante con el motivo (p. ej. "Ya te postulaste").
     */
    protected function failedAuthorization(): never
    {
        $respuesta = Gate::inspect('postular', $this->route('vacante'));

        throw new HttpResponseException(
            redirect()->route('vacantes.show', $this->route('vacante'))->with('error', $respuesta->message()),
        );
    }
}
