<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\CvExperiencia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CvExperiencia>
 */
class CvExperienciaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inicio = fake()->dateTimeBetween('-4 years', '-6 months');
        $actual = fake()->boolean(25);

        return [
            'alumno_id' => Alumno::factory(),
            'empresa' => fake()->company(),
            'puesto' => fake()->randomElement([
                'Practicante de Sistemas', 'Auxiliar Administrativo', 'Estadía profesional',
                'Soporte Técnico', 'Asistente de Producción', 'Becario(a) de Calidad',
                'Desarrollador(a) Freelance', 'Atención a Clientes',
            ]),
            'descripcion' => fake()->sentence(14),
            'fecha_inicio' => $inicio,
            'fecha_fin' => $actual ? null : fake()->dateTimeBetween($inicio, 'now'),
            'actual' => $actual,
        ];
    }
}
