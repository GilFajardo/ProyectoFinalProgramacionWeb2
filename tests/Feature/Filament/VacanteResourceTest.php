<?php

namespace Tests\Feature\Filament;

use App\Enums\Modalidad;
use App\Enums\TipoContrato;
use App\Filament\Resources\Vacantes\Pages\CreateVacante;
use App\Filament\Resources\Vacantes\Pages\ListVacantes;
use App\Models\Categoria;
use App\Models\Reclutador;
use App\Models\User;
use App\Models\Vacante;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VacanteResourceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_creates_vacante_with_generated_slug(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $reclutador = Reclutador::factory()->create();
        $categoria = Categoria::factory()->create();

        Livewire::test(CreateVacante::class)
            ->fillForm([
                'titulo' => 'Ingeniero de Calidad Jr.',
                'categoria_id' => $categoria->id,
                'tipo_contrato' => TipoContrato::TiempoCompleto->value,
                'descripcion' => 'Control de calidad en línea de producción.',
                'empresa_id' => $reclutador->empresa_id,
                'reclutador_id' => $reclutador->id,
                'fecha_inicio' => '2026-11-21',
                'fecha_fin' => '2026-12-21',
                'modalidad' => Modalidad::Presencial->value,
                'plazas' => 2,
                'salario_min' => 12000,
                'salario_max' => 15000,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $vacante = Vacante::sole();
        $this->assertSame($reclutador->empresa_id, $vacante->empresa_id);
        $this->assertStringStartsWith('ingeniero-de-calidad-jr-', $vacante->slug);
    }

    public function test_rejects_vacante_ending_before_it_starts_or_with_inverted_salary(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $reclutador = Reclutador::factory()->create();

        Livewire::test(CreateVacante::class)
            ->fillForm([
                'titulo' => 'Analista',
                'categoria_id' => Categoria::factory()->create()->id,
                'tipo_contrato' => TipoContrato::Practicas->value,
                'descripcion' => 'Descripción.',
                'empresa_id' => $reclutador->empresa_id,
                'fecha_inicio' => '2026-12-10',
                'fecha_fin' => '2026-12-01',
                'salario_min' => 15000,
                'salario_max' => 9000,
            ])
            ->call('create')
            ->assertHasFormErrors(['fecha_fin' => 'after_or_equal', 'salario_max' => 'gte']);

        $this->assertDatabaseCount('vacantes', 0);
    }

    public function test_extender_vigencia_reactivates_vacante_with_new_end_date(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $vacante = Vacante::factory()->vencida()->inactiva()->create();

        Livewire::test(ListVacantes::class)
            ->callAction(TestAction::make('extenderVigencia')->table($vacante), ['fecha_fin' => today()->addDays(20)->toDateString()])
            ->assertHasNoFormErrors();

        $vacante->refresh();
        $this->assertTrue($vacante->activa);
        $this->assertTrue($vacante->fecha_fin->equalTo(today()->addDays(20)));
    }

    public function test_vigentes_tab_lists_only_vigente_vacantes(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $vigente = Vacante::factory()->vigente()->create();
        $vencida = Vacante::factory()->vencida()->create();

        Livewire::test(ListVacantes::class)
            ->set('activeTab', Vacante::VIGENCIA_VIGENTE)
            ->assertCanSeeTableRecords([$vigente])
            ->assertCanNotSeeTableRecords([$vencida]);
    }

    public function test_reporte_pdf_action_downloads_pdf(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        Vacante::factory()->count(2)->create();

        Livewire::test(ListVacantes::class)
            ->callAction('reportePdfVacantes', [])
            ->assertFileDownloaded(contentType: 'application/pdf');
    }
}
