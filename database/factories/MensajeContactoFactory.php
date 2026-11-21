<?php

namespace Database\Factories;

use App\Models\MensajeContacto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MensajeContacto>
 */
class MensajeContactoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'email' => fake()->safeEmail(),
            'asunto' => fake()->randomElement([
                'Información para registrar mi empresa',
                'Problema al subir mi CV',
                'Duda sobre estadías profesionales',
                'Solicitud de convenio de vinculación',
            ]),
            'mensaje' => fake()->paragraph(3),
            'ip' => fake()->ipv4(),
            'leido' => fake()->boolean(40),
        ];
    }
}
