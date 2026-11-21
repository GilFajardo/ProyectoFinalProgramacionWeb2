<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Catálogo obligatorio de las 12 áreas de conocimiento / categorías de vacantes.
 * Es idempotente: puede ejecutarse varias veces sin duplicar registros.
 */
class CategoriaSeeder extends Seeder
{
    /**
     * @var list<array{nombre: string, descripcion: string, icono: string}>
     */
    public const array CATEGORIAS = [
        ['nombre' => 'Contabilidad y Administración', 'descripcion' => 'Finanzas, contabilidad, nómina, recursos humanos y gestión empresarial.', 'icono' => 'heroicon-o-calculator'],
        ['nombre' => 'Comunicaciones', 'descripcion' => 'Comunicación organizacional, medios, relaciones públicas y contenidos.', 'icono' => 'heroicon-o-megaphone'],
        ['nombre' => 'Construcción y edificación', 'descripcion' => 'Obra civil, supervisión, edificación e infraestructura.', 'icono' => 'heroicon-o-building-office-2'],
        ['nombre' => 'Diseño', 'descripcion' => 'Diseño gráfico, industrial, UX/UI y multimedia.', 'icono' => 'heroicon-o-paint-brush'],
        ['nombre' => 'Derecho y leyes', 'descripcion' => 'Asesoría jurídica, cumplimiento normativo y gestión legal.', 'icono' => 'heroicon-o-scale'],
        ['nombre' => 'Educación', 'descripcion' => 'Docencia, capacitación, tutorías y gestión educativa.', 'icono' => 'heroicon-o-academic-cap'],
        ['nombre' => 'Ingeniería', 'descripcion' => 'Ingeniería de procesos, calidad, mantenimiento y proyectos.', 'icono' => 'heroicon-o-wrench-screwdriver'],
        ['nombre' => 'Manufactura', 'descripcion' => 'Producción, mejora continua, logística y cadena de suministro.', 'icono' => 'heroicon-o-cog-6-tooth'],
        ['nombre' => 'Mercadotecnia', 'descripcion' => 'Marketing digital, ventas, investigación de mercados y comercio internacional.', 'icono' => 'heroicon-o-presentation-chart-line'],
        ['nombre' => 'Salud', 'descripcion' => 'Servicios médicos, seguridad e higiene y salud ocupacional.', 'icono' => 'heroicon-o-heart'],
        ['nombre' => 'Tecnología de la Información', 'descripcion' => 'Desarrollo de software, redes, soporte, datos y ciberseguridad.', 'icono' => 'heroicon-o-computer-desktop'],
        ['nombre' => 'Zoología', 'descripcion' => 'Cuidado animal, conservación de fauna y educación ambiental.', 'icono' => 'heroicon-o-bug-ant'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::CATEGORIAS as $categoria) {
            Categoria::updateOrCreate(
                ['slug' => Str::slug($categoria['nombre'])],
                $categoria,
            );
        }
    }
}
