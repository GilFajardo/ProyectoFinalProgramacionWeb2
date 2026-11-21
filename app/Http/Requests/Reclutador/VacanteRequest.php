<?php

namespace App\Http\Requests\Reclutador;

use App\Enums\Modalidad;
use App\Enums\TipoContrato;
use App\Models\Vacante;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Alta y modificación de vacantes por parte del reclutador.
 * La empresa no se recibe del formulario: siempre es la del reclutador autenticado.
 */
class VacanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $vacante = $this->route('vacante');

        return $vacante
            ? $this->user()->can('update', $vacante)
            : $this->user()->can('create', Vacante::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $esAlta = $this->route('vacante') === null;

        return [
            'titulo' => ['required', 'string', 'min:5', 'max:150'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'tipo_contrato' => ['required', Rule::enum(TipoContrato::class)],
            'modalidad' => ['required', Rule::enum(Modalidad::class)],
            'descripcion' => ['required', 'string', 'min:30', 'max:10000'],
            'requisitos' => ['nullable', 'string', 'max:5000'],
            'prestaciones' => ['nullable', 'string', 'max:5000'],
            'ubicacion' => ['nullable', 'string', 'max:150'],
            'plazas' => ['required', 'integer', 'between:1,999'],
            'salario_min' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'salario_max' => ['nullable', 'numeric', 'min:0', 'max:9999999', 'gte:salario_min'],
            // Al crear, la vigencia no puede iniciar en el pasado; al editar se conserva la fecha original.
            'fecha_inicio' => array_filter(['required', 'date', $esAlta ? 'after_or_equal:today' : null]),
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio', 'after_or_equal:today', 'before_or_equal:'.now()->addYear()->toDateString()],
            'activa' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha_inicio.after_or_equal' => 'La vigencia no puede iniciar antes de hoy.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de inicio y no estar en el pasado.',
            'fecha_fin.before_or_equal' => 'La vigencia máxima de una vacante es de un año.',
            'salario_max.gte' => 'El salario máximo debe ser mayor o igual que el mínimo.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activa' => $this->boolean('activa')]);
    }
}
