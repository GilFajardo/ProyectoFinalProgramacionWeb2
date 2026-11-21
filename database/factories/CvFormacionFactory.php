<?php

namespace Database\Factories;

use App\Enums\NivelFormacion;
use App\Models\Alumno;
use App\Models\CvFormacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CvFormacion>
 */
class CvFormacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nivel = fake()->randomElement([NivelFormacion::Bachillerato, NivelFormacion::Curso, NivelFormacion::Certificacion, NivelFormacion::Diplomado]);
        $inicio = fake()->dateTimeBetween('-6 years', '-1 year');

        return [
            'alumno_id' => Alumno::factory(),
            'institucion' => match ($nivel) {
                NivelFormacion::Bachillerato => fake()->randomElement(['COBACH Plantel 01', 'CECyTE SLP', 'Preparatoria UASLP', 'CBTis 131']),
                default => fake()->randomElement(['Cisco Networking Academy', 'Google Actívate', 'Coursera', 'Platzi', 'CANACINTRA SLP']),
            },
            'titulo' => match ($nivel) {
                NivelFormacion::Bachillerato => 'Bachillerato General',
                NivelFormacion::Certificacion => fake()->randomElement(['CCNA Introduction to Networks', 'Scrum Fundamentals', 'Lean Six Sigma White Belt']),
                NivelFormacion::Diplomado => fake()->randomElement(['Diplomado en Ciencia de Datos', 'Diplomado en Gestión de Proyectos']),
                default => fake()->randomElement(['Curso de Excel Avanzado', 'Curso de Python', 'Curso de Atención al Cliente', 'Curso de Inglés B1']),
            },
            'nivel' => $nivel,
            'fecha_inicio' => $inicio,
            'fecha_fin' => fake()->dateTimeBetween($inicio, 'now'),
            'en_curso' => false,
        ];
    }

    /**
     * Formación actual del alumno en la UPSLP.
     */
    public function upslp(string $carrera): static
    {
        return $this->state(fn (array $attributes) => [
            'institucion' => 'Universidad Politécnica de San Luis Potosí',
            'titulo' => $carrera,
            'nivel' => NivelFormacion::Licenciatura,
            'fecha_inicio' => today()->subYears(fake()->numberBetween(1, 3))->startOfYear(),
            'fecha_fin' => null,
            'en_curso' => true,
        ]);
    }
}
