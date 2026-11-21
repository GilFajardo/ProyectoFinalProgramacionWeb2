<?php

namespace Tests\Feature\Http\Reclutador;

use App\Models\Alumno;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Reclutador;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VacantesReclutadorTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function datosVacante(array $cambios = []): array
    {
        return $cambios + [
            'titulo' => 'Ingeniero de Procesos',
            'categoria_id' => Categoria::factory()->create()->id,
            'tipo_contrato' => 'tiempo_completo',
            'modalidad' => 'presencial',
            'descripcion' => 'Responsable de la mejora de procesos en la línea de ensamble.',
            'plazas' => 2,
            'salario_min' => 15000,
            'salario_max' => 20000,
            'fecha_inicio' => today()->toDateString(),
            'fecha_fin' => today()->addDays(30)->toDateString(),
            'activa' => '1',
        ];
    }

    public function test_reclutador_creates_vacante_for_own_empresa_even_if_payload_says_otherwise(): void
    {
        $reclutador = Reclutador::factory()->create();
        $otraEmpresa = Empresa::factory()->create();

        $this->actingAs($reclutador->user)
            ->post(route('reclutador.vacantes.store'), $this->datosVacante(['empresa_id' => $otraEmpresa->id]))
            ->assertSessionHasNoErrors();

        $vacante = Vacante::sole();
        $this->assertSame($reclutador->empresa_id, $vacante->empresa_id);
        $this->assertSame($reclutador->id, $vacante->reclutador_id);
    }

    public function test_rejects_vigencia_ending_before_start_and_inverted_salary(): void
    {
        $reclutador = Reclutador::factory()->create();

        $this->actingAs($reclutador->user)->post(route('reclutador.vacantes.store'), $this->datosVacante([
            'fecha_inicio' => today()->addDays(10)->toDateString(),
            'fecha_fin' => today()->addDays(5)->toDateString(),
            'salario_min' => 20000,
            'salario_max' => 10000,
        ]))->assertSessionHasErrors(['fecha_fin', 'salario_max']);

        $this->assertDatabaseCount('vacantes', 0);
    }

    public function test_reclutador_without_empresa_is_sent_to_register_it(): void
    {
        $this->actingAs(Reclutador::factory()->sinEmpresa()->create()->user)
            ->get(route('reclutador.vacantes.create'))
            ->assertRedirect(route('reclutador.empresa.edit'));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function accionesSobreVacanteAjena(): array
    {
        return [
            'ver candidatos' => ['get', 'reclutador.vacantes.show'],
            'editar' => ['get', 'reclutador.vacantes.edit'],
            'actualizar' => ['put', 'reclutador.vacantes.update'],
            'dar de baja' => ['delete', 'reclutador.vacantes.destroy'],
        ];
    }

    #[DataProvider('accionesSobreVacanteAjena')]
    public function test_reclutador_cannot_act_on_another_empresas_vacante(string $metodo, string $ruta): void
    {
        $ajena = Vacante::factory()->create();

        $this->actingAs(Reclutador::factory()->create()->user)
            ->{$metodo}(route($ruta, $ajena), $metodo === 'put' ? $this->datosVacante() : [])
            ->assertForbidden();

        $this->assertNotSoftDeleted($ajena);
    }

    public function test_dar_de_baja_soft_deletes_and_keeps_applications(): void
    {
        $reclutador = Reclutador::factory()->create();
        $vacante = Vacante::factory()->for($reclutador->empresa)->create();
        $vacante->postulaciones()->create(['alumno_id' => Alumno::factory()->create()->id]);

        $this->actingAs($reclutador->user)
            ->delete(route('reclutador.vacantes.destroy', $vacante))
            ->assertRedirect(route('reclutador.vacantes.index'));

        $this->assertSoftDeleted($vacante);
        $this->assertDatabaseCount('postulaciones', 1);
    }

    public function test_index_lists_only_own_empresa_vacantes(): void
    {
        $reclutador = Reclutador::factory()->create();
        $propia = Vacante::factory()->for($reclutador->empresa)->create(['titulo' => 'Vacante Propia']);
        $ajena = Vacante::factory()->create(['titulo' => 'Vacante Ajena']);

        $this->actingAs($reclutador->user)
            ->get(route('reclutador.vacantes.index'))
            ->assertOk()
            ->assertSee($propia->titulo)
            ->assertDontSee($ajena->titulo);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function paginasDelPortal(): array
    {
        return [
            'panel' => ['reclutador.panel'],
            'perfil' => ['reclutador.perfil.edit'],
            'empresa' => ['reclutador.empresa.edit'],
            'vacantes' => ['reclutador.vacantes.index'],
            'nueva vacante' => ['reclutador.vacantes.create'],
        ];
    }

    #[DataProvider('paginasDelPortal')]
    public function test_reclutador_can_open_portal_page_and_alumno_cannot(string $ruta): void
    {
        $reclutador = Reclutador::factory()->create();
        Vacante::factory()->porVencer()->for($reclutador->empresa)->create();

        $this->actingAs($reclutador->user)->get(route($ruta))->assertOk();
        $this->actingAs(Alumno::factory()->create()->user)->get(route($ruta))->assertForbidden();
    }
}
