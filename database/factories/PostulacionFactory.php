<?php

namespace Database\Factories;

use App\Enums\EstatusPostulacion;
use App\Models\Alumno;
use App\Models\Postulacion;
use App\Models\Vacante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Postulacion>
 */
class PostulacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vacante_id' => Vacante::factory(),
            'alumno_id' => Alumno::factory(),
            'estatus' => EstatusPostulacion::EnProceso,
            'carta_presentacion' => fake()->randomElement([
                'Me interesa mucho la vacante porque se alinea con mi formación y mis metas profesionales.',
                'Considero que mi experiencia en proyectos académicos me permitirá aportar valor a su equipo.',
                'Estoy en busca de una oportunidad para desarrollarme y aplicar lo aprendido en la UPSLP.',
                null,
            ]),
        ];
    }

    public function aceptada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => EstatusPostulacion::Aceptado,
            'comentario_reclutador' => '¡Felicidades! Nos pondremos en contacto contigo para los siguientes pasos.',
            'estatus_actualizado_en' => now()->subDays(fake()->numberBetween(0, 10)),
        ]);
    }

    public function rechazada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => EstatusPostulacion::Rechazado,
            'comentario_reclutador' => 'Gracias por tu interés. En esta ocasión buscamos un perfil con mayor experiencia.',
            'estatus_actualizado_en' => now()->subDays(fake()->numberBetween(0, 10)),
        ]);
    }
}
