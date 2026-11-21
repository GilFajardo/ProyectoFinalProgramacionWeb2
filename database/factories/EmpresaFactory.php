<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Empresa>
 */
class EmpresaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = fake()->unique()->company();

        return [
            'nombre' => $nombre,
            'razon_social' => $nombre.' S.A. de C.V.',
            'rfc' => Str::upper(fake()->unique()->bothify('???######???')),
            'sector' => fake()->randomElement([
                'Automotriz', 'Tecnología', 'Manufactura', 'Servicios', 'Construcción',
                'Salud', 'Educación', 'Comercio', 'Logística', 'Agroindustria',
            ]),
            'descripcion' => fake()->paragraph(3),
            'sitio_web' => 'https://www.'.Str::slug($nombre).'.com.mx',
            'email_contacto' => 'rh@'.Str::slug($nombre).'.com.mx',
            'telefono' => fake()->numerify('444 ### ####'),
            'direccion' => fake()->streetAddress(),
            'municipio' => fake()->randomElement(['San Luis Potosí', 'Soledad de Graciano Sánchez', 'Villa de Pozos']),
            'estado' => 'San Luis Potosí',
            'verificada' => true,
            'activa' => true,
        ];
    }

    public function inactiva(): static
    {
        return $this->state(fn (array $attributes) => ['activa' => false]);
    }
}
