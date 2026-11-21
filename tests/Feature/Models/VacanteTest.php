<?php

namespace Tests\Feature\Models;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class VacanteTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-11-21 10:00:00'));
    }

    public function test_vigentes_scope_includes_boundary_dates_and_excludes_other_states(): void
    {
        $iniciaHoy = Vacante::factory()->create(['fecha_inicio' => '2026-11-21', 'fecha_fin' => '2026-12-31']);
        $terminaHoy = Vacante::factory()->create(['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-11-21']);
        Vacante::factory()->create(['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-11-20']);
        Vacante::factory()->create(['fecha_inicio' => '2026-11-22', 'fecha_fin' => '2026-12-31']);
        Vacante::factory()->inactiva()->create(['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-12-31']);

        $this->assertEqualsCanonicalizing(
            [$iniciaHoy->id, $terminaHoy->id],
            Vacante::vigentes()->pluck('id')->all(),
        );
    }

    public function test_estado_vigencia_reports_each_state(): void
    {
        $this->assertSame(Vacante::VIGENCIA_VIGENTE, Vacante::factory()->make(['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-12-31'])->estado_vigencia);
        $this->assertSame(Vacante::VIGENCIA_POR_VENCER, Vacante::factory()->make(['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-11-28'])->estado_vigencia);
        $this->assertSame(Vacante::VIGENCIA_PROGRAMADA, Vacante::factory()->make(['fecha_inicio' => '2026-11-22', 'fecha_fin' => '2026-12-31'])->estado_vigencia);
        $this->assertSame(Vacante::VIGENCIA_VENCIDA, Vacante::factory()->make(['fecha_inicio' => '2026-10-01', 'fecha_fin' => '2026-11-20'])->estado_vigencia);
        $this->assertSame(Vacante::VIGENCIA_INACTIVA, Vacante::factory()->inactiva()->make(['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-12-31'])->estado_vigencia);
    }

    public function test_con_vigencia_por_vencer_returns_only_vacantes_ending_within_seven_days(): void
    {
        $porVencer = Vacante::factory()->create(['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-11-28']);
        Vacante::factory()->create(['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-11-29']);

        $this->assertSame([$porVencer->id], Vacante::conVigencia(Vacante::VIGENCIA_POR_VENCER)->pluck('id')->all());
    }

    public function test_de_categoria_filters_by_categoria_slug(): void
    {
        $ti = Categoria::factory()->create(['nombre' => 'Tecnología de la Información', 'slug' => 'tecnologia-de-la-informacion']);
        $enTi = Vacante::factory()->for($ti)->create();
        Vacante::factory()->create();

        $this->assertSame([$enTi->id], Vacante::deCategoria('tecnologia-de-la-informacion')->pluck('id')->all());
    }

    public function test_buscar_matches_titulo_or_empresa_nombre(): void
    {
        $porTitulo = Vacante::factory()->create(['titulo' => 'Desarrollador Laravel']);
        $porEmpresa = Vacante::factory()->for(Empresa::factory()->create(['nombre' => 'Laravelistas Potosí']))->create(['titulo' => 'Contador']);
        Vacante::factory()->create(['titulo' => 'Supervisor de Producción', 'descripcion' => 'Turno matutino.']);

        $this->assertEqualsCanonicalizing(
            [$porTitulo->id, $porEmpresa->id],
            Vacante::buscar('laravel')->pluck('id')->all(),
        );
    }

    public function test_buscar_treats_like_wildcards_as_literal_text(): void
    {
        Vacante::factory()->create(['titulo' => 'Analista de Datos', 'descripcion' => 'Sin comodines.']);

        $this->assertSame(0, Vacante::buscar('%')->count());
    }

    public function test_creating_generates_unique_slug_from_titulo(): void
    {
        $primera = Vacante::factory()->create(['titulo' => 'Ingeniero de Calidad']);
        $segunda = Vacante::factory()->create(['titulo' => 'Ingeniero de Calidad']);

        $this->assertStringStartsWith('ingeniero-de-calidad-', $primera->slug);
        $this->assertNotSame($primera->slug, $segunda->slug);
    }
}
