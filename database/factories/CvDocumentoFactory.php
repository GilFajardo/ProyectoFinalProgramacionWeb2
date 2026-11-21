<?php

namespace Database\Factories;

use App\Enums\TipoDocumento;
use App\Models\Alumno;
use App\Models\CvDocumento;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CvDocumento>
 */
class CvDocumentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'alumno_id' => Alumno::factory(),
            'nombre' => fake()->randomElement(['Constancia de estudios', 'Certificado de inglés', 'Carta de recomendación', 'Constancia de estadía']),
            'tipo' => fake()->randomElement(TipoDocumento::cases()),
            'archivo_path' => 'cv-documentos/'.Str::uuid().'.pdf',
            'mime' => 'application/pdf',
            'tamano_kb' => fake()->numberBetween(80, 1500),
        ];
    }
}
