<?php

namespace Database\Factories;

use App\Enums\Modalidad;
use App\Enums\TipoContrato;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Vacante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vacante>
 */
class VacanteFactory extends Factory
{
    /**
     * Títulos realistas por categoría (clave = slug de la categoría).
     *
     * @var array<string, list<string>>
     */
    public const array TITULOS_POR_CATEGORIA = [
        'contabilidad-y-administracion' => ['Auxiliar Contable', 'Analista de Nómina', 'Asistente Administrativo', 'Practicante de Finanzas'],
        'comunicaciones' => ['Community Manager', 'Redactor(a) de Contenidos', 'Asistente de Relaciones Públicas'],
        'construccion-y-edificacion' => ['Residente de Obra', 'Auxiliar de Supervisión de Obra', 'Dibujante AutoCAD'],
        'diseno' => ['Diseñador(a) Gráfico Jr.', 'Diseñador(a) UX/UI', 'Practicante de Diseño Industrial'],
        'derecho-y-leyes' => ['Auxiliar Jurídico', 'Asistente de Cumplimiento Legal', 'Pasante de Derecho Corporativo'],
        'educacion' => ['Instructor(a) de Computación', 'Tutor(a) de Matemáticas', 'Asistente de Capacitación'],
        'ingenieria' => ['Ingeniero(a) de Procesos', 'Ingeniero(a) de Calidad Jr.', 'Ingeniero(a) de Mantenimiento'],
        'manufactura' => ['Supervisor(a) de Producción', 'Técnico(a) en Mejora Continua', 'Planeador(a) de Materiales'],
        'mercadotecnia' => ['Analista de Marketing Digital', 'Ejecutivo(a) de Ventas', 'Practicante de Investigación de Mercados'],
        'salud' => ['Auxiliar de Seguridad e Higiene', 'Asistente de Servicios Médicos', 'Coordinador(a) de Salud Ocupacional'],
        'tecnologia-de-la-informacion' => ['Desarrollador(a) Web PHP/Laravel', 'Soporte Técnico TI', 'Analista de Datos Jr.', 'Administrador(a) de Redes', 'QA Tester'],
        'zoologia' => ['Auxiliar de Cuidado Animal', 'Técnico(a) en Bienestar Animal', 'Asistente de Educación Ambiental'],
    ];

    /**
     * Define the model's default state: una vacante vigente.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $salarioMin = fake()->numberBetween(8, 25) * 1000;

        return [
            'empresa_id' => Empresa::factory(),
            'categoria_id' => Categoria::factory(),
            'titulo' => fake()->randomElement(array_merge(...array_values(self::TITULOS_POR_CATEGORIA))),
            'descripcion' => implode("\n\n", [
                'Buscamos talento comprometido para integrarse a nuestro equipo en San Luis Potosí.',
                fake()->paragraph(4),
            ]),
            'requisitos' => implode("\n", [
                '- Estudiante o egresado(a) de carrera afín',
                '- Disponibilidad de horario',
                '- Trabajo en equipo y buena comunicación',
                '- '.fake()->randomElement(['Inglés intermedio', 'Manejo de paquetería Office', 'Licencia de manejo vigente', 'Conocimientos básicos del área']),
            ]),
            'prestaciones' => implode("\n", [
                '- Prestaciones de ley',
                '- '.fake()->randomElement(['Vales de despensa', 'Seguro de gastos médicos menores', 'Comedor subsidiado', 'Transporte de personal']),
                '- Capacitación continua',
            ]),
            'modalidad' => fake()->randomElement(Modalidad::cases()),
            'tipo_contrato' => fake()->randomElement(TipoContrato::cases()),
            'salario_min' => $salarioMin,
            'salario_max' => $salarioMin + fake()->numberBetween(2, 8) * 1000,
            'ubicacion' => fake()->randomElement(['San Luis Potosí, S.L.P.', 'Zona Industrial, S.L.P.', 'Soledad de G. S., S.L.P.', 'Remoto']),
            'plazas' => fake()->numberBetween(1, 4),
            'fecha_inicio' => today()->subDays(fake()->numberBetween(1, 20)),
            'fecha_fin' => today()->addDays(fake()->numberBetween(10, 60)),
            'activa' => true,
        ];
    }

    public function vigente(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_inicio' => today()->subDays(fake()->numberBetween(1, 20)),
            'fecha_fin' => today()->addDays(fake()->numberBetween(10, 60)),
            'activa' => true,
        ]);
    }

    public function porVencer(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_inicio' => today()->subDays(fake()->numberBetween(15, 40)),
            'fecha_fin' => today()->addDays(fake()->numberBetween(0, 6)),
            'activa' => true,
        ]);
    }

    public function vencida(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_inicio' => today()->subDays(fake()->numberBetween(60, 120)),
            'fecha_fin' => today()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    public function programada(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_inicio' => today()->addDays(fake()->numberBetween(3, 15)),
            'fecha_fin' => today()->addDays(fake()->numberBetween(30, 60)),
            'activa' => true,
        ]);
    }

    public function inactiva(): static
    {
        return $this->state(fn (array $attributes) => ['activa' => false]);
    }
}
