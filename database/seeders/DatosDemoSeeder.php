<?php

namespace Database\Seeders;

use App\Enums\EstatusPostulacion;
use App\Models\Alumno;
use App\Models\Categoria;
use App\Models\CvExperiencia;
use App\Models\CvFormacion;
use App\Models\CvHabilidad;
use App\Models\Empresa;
use App\Models\MensajeContacto;
use App\Models\Postulacion;
use App\Models\Reclutador;
use App\Models\Vacante;
use Database\Factories\VacanteFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

/**
 * Datos de demostración (empresas ficticias de San Luis Potosí, vacantes,
 * alumnos con CV y postulaciones) para que filtros, reportes y estadísticas
 * tengan información. Depende de CategoriaSeeder y UsuarioPruebaSeeder.
 */
class DatosDemoSeeder extends Seeder
{
    /**
     * Empresas ficticias y las categorías en las que publican.
     *
     * @var list<array{nombre: string, rfc: string, sector: string, categorias: list<string>}>
     */
    private const array EMPRESAS = [
        ['nombre' => 'Autopartes del Altiplano', 'rfc' => 'AAL101115KJ2', 'sector' => 'Automotriz', 'categorias' => ['manufactura', 'ingenieria', 'salud']],
        ['nombre' => 'Constructora Cerro de San Pedro', 'rfc' => 'CCS090520LM3', 'sector' => 'Construcción', 'categorias' => ['construccion-y-edificacion', 'ingenieria', 'derecho-y-leyes']],
        ['nombre' => 'Grupo Logístico Potosino', 'rfc' => 'GLP120801QW4', 'sector' => 'Logística', 'categorias' => ['contabilidad-y-administracion', 'manufactura', 'mercadotecnia']],
        ['nombre' => 'Estudio Creativo Media Luna', 'rfc' => 'ECM180214ZX5', 'sector' => 'Servicios', 'categorias' => ['diseno', 'comunicaciones', 'mercadotecnia']],
        ['nombre' => 'Colegio Bilingüe Las Lomas', 'rfc' => 'CBL050830RT6', 'sector' => 'Educación', 'categorias' => ['educacion', 'comunicaciones', 'tecnologia-de-la-informacion']],
        ['nombre' => 'Clínica Integral del Centro', 'rfc' => 'CIC110407YU7', 'sector' => 'Salud', 'categorias' => ['salud', 'contabilidad-y-administracion', 'derecho-y-leyes']],
        ['nombre' => 'Centro de Conservación Fauna Potosina', 'rfc' => 'CCF160921OP8', 'sector' => 'Medio ambiente', 'categorias' => ['zoologia', 'educacion', 'diseno']],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = Categoria::all()->keyBy('slug');

        $this->crearVacantesTecnoSoft($categorias);

        foreach (self::EMPRESAS as $datos) {
            $empresa = Empresa::factory()->create([
                'nombre' => $datos['nombre'],
                'razon_social' => $datos['nombre'].' S.A. de C.V.',
                'rfc' => $datos['rfc'],
                'sector' => $datos['sector'],
            ]);

            $reclutadores = Reclutador::factory()->count(fake()->numberBetween(1, 2))->for($empresa)->create();

            foreach ($datos['categorias'] as $slug) {
                $this->crearVacantes($empresa, $reclutadores->random(), $categorias[$slug], fake()->numberBetween(1, 2));
            }
        }

        // Un reclutador recién registrado, pendiente de vincular a una empresa.
        Reclutador::factory()->sinEmpresa()->create();

        $alumnos = $this->crearAlumnosConCv($categorias);

        $this->crearPostulaciones($alumnos);

        MensajeContacto::factory()->count(8)->create();
    }

    /**
     * Vacantes de la empresa del reclutador de prueba, en distintos estados de vigencia.
     *
     * @param  Collection<string, Categoria>  $categorias
     */
    private function crearVacantesTecnoSoft(Collection $categorias): void
    {
        $empresa = Empresa::where('rfc', 'TSP150310AB1')->firstOrFail();
        $reclutador = $empresa->reclutadores()->firstOrFail();
        $ti = $categorias['tecnologia-de-la-informacion'];

        $base = ['empresa_id' => $empresa->id, 'reclutador_id' => $reclutador->id, 'categoria_id' => $ti->id];

        Vacante::factory()->vigente()->create($base + ['titulo' => 'Desarrollador(a) Web PHP/Laravel', 'ubicacion' => 'San Luis Potosí, S.L.P.']);
        Vacante::factory()->vigente()->create($base + ['titulo' => 'Analista de Datos Jr.']);
        Vacante::factory()->porVencer()->create($base + ['titulo' => 'Soporte Técnico TI']);
        Vacante::factory()->programada()->create($base + ['titulo' => 'QA Tester']);
        Vacante::factory()->vencida()->create($base + ['titulo' => 'Administrador(a) de Redes']);
        Vacante::factory()->vigente()->create($base + [
            'titulo' => 'Diseñador(a) UX/UI',
            'categoria_id' => $categorias['diseno']->id,
        ]);
    }

    /**
     * Crea vacantes con títulos propios de la categoría y una mezcla de vigencias.
     */
    private function crearVacantes(Empresa $empresa, Reclutador $reclutador, Categoria $categoria, int $cantidad): void
    {
        $titulos = VacanteFactory::TITULOS_POR_CATEGORIA[$categoria->slug];

        for ($i = 0; $i < $cantidad; $i++) {
            $factory = Vacante::factory();

            $factory = match (fake()->randomElement(['vigente', 'vigente', 'vigente', 'por_vencer', 'vencida', 'programada'])) {
                'por_vencer' => $factory->porVencer(),
                'vencida' => $factory->vencida(),
                'programada' => $factory->programada(),
                default => $factory->vigente(),
            };

            $factory->create([
                'empresa_id' => $empresa->id,
                'reclutador_id' => $reclutador->id,
                'categoria_id' => $categoria->id,
                'titulo' => fake()->randomElement($titulos),
            ]);
        }
    }

    /**
     * Alumnos con CV estructurado (formación, experiencia y habilidades por área).
     *
     * @param  Collection<string, Categoria>  $categorias
     * @return Collection<int, Alumno>
     */
    private function crearAlumnosConCv(Collection $categorias): Collection
    {
        $habilidadesPorArea = [
            'tecnologia-de-la-informacion' => ['PHP', 'Laravel', 'JavaScript', 'SQL', 'Python', 'Redes Cisco', 'Power BI'],
            'manufactura' => ['Lean Manufacturing', 'SolidWorks', 'Control de calidad', '5S'],
            'contabilidad-y-administracion' => ['Excel avanzado', 'CONTPAQi', 'Nómina'],
            'mercadotecnia' => ['Marketing digital', 'Google Ads', 'Ventas consultivas'],
            'comunicaciones' => ['Comunicación efectiva', 'Redacción', 'Inglés'],
            'diseno' => ['Photoshop', 'Figma', 'Illustrator'],
        ];

        $alumnos = Alumno::factory()->count(24)->create();

        foreach ($alumnos as $alumno) {
            CvFormacion::factory()->upslp($alumno->carrera)->for($alumno)->create();
            CvFormacion::factory()->count(fake()->numberBetween(1, 2))->for($alumno)->create();
            CvExperiencia::factory()->count(fake()->numberBetween(0, 2))->for($alumno)->create();

            foreach (fake()->randomElements(array_keys($habilidadesPorArea), 2) as $slug) {
                foreach (fake()->randomElements($habilidadesPorArea[$slug], 2) as $nombre) {
                    CvHabilidad::factory()->for($alumno)->create([
                        'categoria_id' => $categorias[$slug]->id,
                        'nombre' => $nombre,
                    ]);
                }
            }
        }

        return $alumnos->push(Alumno::whereRelation('user', 'email', UsuarioPruebaSeeder::EMAIL_ALUMNO)->firstOrFail());
    }

    /**
     * Postulaciones a vacantes ya publicadas, sin repetir alumno en la misma vacante.
     *
     * @param  Collection<int, Alumno>  $alumnos
     */
    private function crearPostulaciones(Collection $alumnos): void
    {
        $vacantes = Vacante::whereDate('fecha_inicio', '<=', today())->get();

        foreach ($vacantes as $vacante) {
            $postulantes = $alumnos->random(min($alumnos->count(), fake()->numberBetween(0, 4)));

            foreach ($postulantes as $alumno) {
                $estatus = fake()->randomElement([
                    EstatusPostulacion::EnProceso, EstatusPostulacion::EnProceso,
                    EstatusPostulacion::Aceptado, EstatusPostulacion::Rechazado,
                ]);

                $factory = Postulacion::factory();

                $factory = match ($estatus) {
                    EstatusPostulacion::Aceptado => $factory->aceptada(),
                    EstatusPostulacion::Rechazado => $factory->rechazada(),
                    default => $factory,
                };

                $factory->for($vacante)->for($alumno)->create([
                    'created_at' => fake()->dateTimeBetween($vacante->fecha_inicio, min($vacante->fecha_fin, today())),
                ]);
            }
        }

        // Garantiza que el alumno de prueba tenga postulaciones en los tres estatus.
        $alumnoPrueba = $alumnos->last();
        $vacantesTecnoSoft = Vacante::vigentes()
            ->whereRelation('empresa', 'rfc', 'TSP150310AB1')
            ->whereDoesntHave('postulaciones', fn ($q) => $q->whereBelongsTo($alumnoPrueba))
            ->get();

        foreach ($vacantesTecnoSoft->take(1) as $vacante) {
            Postulacion::factory()->for($vacante)->for($alumnoPrueba)->create();
        }
    }
}
