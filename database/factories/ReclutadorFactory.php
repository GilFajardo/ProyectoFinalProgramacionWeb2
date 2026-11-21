<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\Reclutador;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reclutador>
 */
class ReclutadorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->reclutador(),
            'empresa_id' => Empresa::factory(),
            'puesto' => fake()->randomElement([
                'Analista de Reclutamiento', 'Coordinador(a) de Recursos Humanos',
                'Gerente de Talento', 'Especialista en Atracción de Talento',
                'Jefe(a) de Capital Humano',
            ]),
            'telefono' => fake()->numerify('444 ### ####'),
        ];
    }

    /**
     * Reclutador registrado que aún no está vinculado a una empresa.
     */
    public function sinEmpresa(): static
    {
        return $this->state(fn (array $attributes) => ['empresa_id' => null]);
    }
}
