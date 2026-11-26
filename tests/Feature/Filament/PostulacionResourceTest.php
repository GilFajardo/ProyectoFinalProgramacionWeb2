<?php

namespace Tests\Feature\Filament;

use App\Enums\EstatusPostulacion;
use App\Filament\Resources\Postulaciones\Pages\ListPostulaciones;
use App\Models\Postulacion;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PostulacionResourceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_cambiar_estatus_updates_estatus_comment_and_timestamp(): void
    {
        $this->freezeSecond();
        $this->actingAs(User::factory()->admin()->create());
        $postulacion = Postulacion::factory()->create();

        Livewire::test(ListPostulaciones::class)
            ->callAction(TestAction::make('cambiarEstatus')->table($postulacion), [
                'estatus' => EstatusPostulacion::Aceptado->value,
                'comentario_reclutador' => 'Te esperamos el lunes.',
            ])
            ->assertHasNoFormErrors();

        $postulacion->refresh();
        $this->assertSame(EstatusPostulacion::Aceptado, $postulacion->estatus);
        $this->assertSame('Te esperamos el lunes.', $postulacion->comentario_reclutador);
        $this->assertTrue($postulacion->estatus_actualizado_en->equalTo(now()));
    }

    public function test_descargar_cv_downloads_pdf_named_by_matricula(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $postulacion = Postulacion::factory()->create();

        Livewire::test(ListPostulaciones::class)
            ->callAction(TestAction::make('descargarCv')->table($postulacion))
            ->assertFileDownloaded('cv-'.$postulacion->alumno->matricula.'.pdf');
    }

    public function test_reporte_pdf_downloads_with_all_filters_applied(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $postulacion = Postulacion::factory()->aceptada()->create();

        Livewire::test(ListPostulaciones::class)
            ->callAction('reportePdfPostulaciones', [
                'empresa_id' => $postulacion->vacante->empresa_id,
                'vacante_id' => $postulacion->vacante_id,
                'estatus' => EstatusPostulacion::Aceptado->value,
            ])
            ->assertHasNoFormErrors()
            ->assertFileDownloaded();
    }

    public function test_estatus_tab_filters_postulaciones(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $aceptada = Postulacion::factory()->aceptada()->create();
        $enProceso = Postulacion::factory()->create();

        Livewire::test(ListPostulaciones::class)
            ->set('activeTab', EstatusPostulacion::Aceptado->value)
            ->assertCanSeeTableRecords([$aceptada])
            ->assertCanNotSeeTableRecords([$enProceso]);
    }
}
