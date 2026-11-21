<?php

namespace Tests\Feature\Http\Reclutador;

use App\Enums\EstatusPostulacion;
use App\Models\Postulacion;
use App\Models\Reclutador;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CandidatosTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function postulacionDe(Reclutador $reclutador): Postulacion
    {
        return Postulacion::factory()->for(Vacante::factory()->for($reclutador->empresa))->create();
    }

    public function test_reclutador_reviews_candidate_and_updates_status_with_comment(): void
    {
        $reclutador = Reclutador::factory()->create();
        $postulacion = $this->postulacionDe($reclutador);

        $this->actingAs($reclutador->user)
            ->get(route('reclutador.candidatos.show', $postulacion))
            ->assertOk()
            ->assertSee($postulacion->alumno->user->name);

        $this->actingAs($reclutador->user)
            ->patch(route('reclutador.candidatos.update', $postulacion), [
                'estatus' => EstatusPostulacion::Rechazado->value,
                'comentario_reclutador' => 'Buscamos más experiencia.',
            ])
            ->assertRedirect(route('reclutador.candidatos.show', $postulacion));

        $postulacion->refresh();
        $this->assertSame(EstatusPostulacion::Rechazado, $postulacion->estatus);
        $this->assertNotNull($postulacion->estatus_actualizado_en);
    }

    public function test_rejects_unknown_status(): void
    {
        $reclutador = Reclutador::factory()->create();

        $this->actingAs($reclutador->user)
            ->patch(route('reclutador.candidatos.update', $this->postulacionDe($reclutador)), ['estatus' => 'contratado'])
            ->assertSessionHasErrors('estatus');
    }

    public function test_reclutador_cannot_see_or_update_candidates_of_another_empresa(): void
    {
        $ajena = Postulacion::factory()->create();
        $intruso = Reclutador::factory()->create()->user;

        $this->actingAs($intruso)->get(route('reclutador.candidatos.show', $ajena))->assertForbidden();
        $this->actingAs($intruso)->get(route('reclutador.candidatos.cv', $ajena))->assertForbidden();
        $this->actingAs($intruso)
            ->patch(route('reclutador.candidatos.update', $ajena), ['estatus' => EstatusPostulacion::Aceptado->value])
            ->assertForbidden();

        $this->assertSame(EstatusPostulacion::EnProceso, $ajena->fresh()->estatus);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function tiposDeReporte(): array
    {
        return ['vacantes' => ['vacantes'], 'postulaciones' => ['postulaciones'], 'empresa' => ['empresas']];
    }

    #[DataProvider('tiposDeReporte')]
    public function test_reclutador_streams_pdf_report(string $tipo): void
    {
        $reclutador = Reclutador::factory()->create();
        $this->postulacionDe($reclutador);

        $this->actingAs($reclutador->user)
            ->get(route('reclutador.reportes', $tipo))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_report_rejects_vacante_from_another_empresa(): void
    {
        $reclutador = Reclutador::factory()->create();
        $ajena = Vacante::factory()->create();

        $this->actingAs($reclutador->user)
            ->get(route('reclutador.reportes', ['tipo' => 'postulaciones', 'vacante_id' => $ajena->id]))
            ->assertSessionHasErrors('vacante_id');
    }

    public function test_unknown_report_type_is_not_found(): void
    {
        $this->actingAs(Reclutador::factory()->create()->user)
            ->get('/reclutador/reportes/usuarios')
            ->assertNotFound();
    }
}
