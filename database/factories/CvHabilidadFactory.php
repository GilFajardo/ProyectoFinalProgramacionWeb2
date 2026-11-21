<?php

namespace Database\Factories;

use App\Enums\NivelHabilidad;
use App\Models\Alumno;
use App\Models\CvHabilidad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CvHabilidad>
 */
class CvHabilidadFactory extends Factory
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
            'categoria_id' => null,
            'nombre' => fake()->randomElement([
                'PHP', 'Laravel', 'JavaScript', 'SQL', 'Python', 'Excel', 'AutoCAD',
                'Trabajo en equipo', 'Comunicación efectiva', 'Inglés', 'Power BI',
                'SolidWorks', 'Lean Manufacturing', 'Photoshop', 'Liderazgo', 'Redes Cisco',
            ]),
            'nivel' => fake()->randomElement(NivelHabilidad::cases()),
        ];
    }
}
