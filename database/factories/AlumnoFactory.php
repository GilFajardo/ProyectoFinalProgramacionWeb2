<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alumno>
 */
class AlumnoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->alumno(),
            'matricula' => (string) fake()->unique()->numberBetween(170000, 259999),
            'carrera' => fake()->randomElement(config('upslp.carreras')),
            'cuatrimestre' => fake()->numberBetween(4, 10),
            'telefono' => fake()->numerify('444 ### ####'),
            'fecha_nacimiento' => fake()->dateTimeBetween('-27 years', '-19 years'),
            'ciudad' => fake()->randomElement(['San Luis Potosí', 'Soledad de Graciano Sánchez', 'Villa de Pozos', 'Mexquitic de Carmona']),
            'resumen' => fake()->randomElement([
                'Estudiante proactivo con interés en el desarrollo de soluciones tecnológicas y trabajo en equipo.',
                'Alumno con experiencia en proyectos académicos y facilidad para aprender nuevas herramientas.',
                'Busco mi primera oportunidad profesional para aplicar mis conocimientos y seguir creciendo.',
                'Responsable, organizado y orientado a resultados, con interés en la mejora continua.',
            ]),
            'linkedin_url' => null,
            'portafolio_url' => null,
        ];
    }
}
