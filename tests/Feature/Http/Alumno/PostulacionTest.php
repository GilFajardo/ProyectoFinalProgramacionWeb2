<?php

namespace Tests\Feature\Http\Alumno;

use App\Models\Alumno;
use App\Models\Postulacion;
use App\Models\Reclutador;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PostulacionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_alumno_applies_once_and_second_attempt_is_rejected(): void
    {
        $alumno = Alumno::factory()->create();
        $vacante = Vacante::factory()->vigente()->create();

        $this->actingAs($alumno->user)
            ->post(route('alumno.postulaciones.store', $vacante), ['carta_presentacion' => 'Me interesa.'])
            ->assertRedirect(route('vacantes.show', $vacante))
            ->assertSessionHas('exito');

        $this->actingAs($alumno->user)
            ->post(route('alumno.postulaciones.store', $vacante))
            ->assertRedirect(route('vacantes.show', $vacante))
            ->assertSessionHas('error', 'Ya te postulaste a esta vacante.');

        $this->assertSame(1, Postulacion::whereBelongsTo($alumno)->whereBelongsTo($vacante)->count());
    }

    public function test_vacante_page_shows_disabled_button_after_applying(): void
    {
        $alumno = Alumno::factory()->create();
        $vacante = Vacante::factory()->vigente()->create();
        Postulacion::factory()->for($alumno)->for($vacante)->create();

        $this->actingAs($alumno->user)
            ->get(route('vacantes.show', $vacante))
            ->assertSee('Ya te postulaste')
            ->assertDontSee('>Postularme</button>', false);
    }

    public function test_rejects_application_to_expired_vacante(): void
    {
        $alumno = Alumno::factory()->create();
        $vacante = Vacante::factory()->vencida()->create();

        $this->actingAs($alumno->user)
            ->post(route('alumno.postulaciones.store', $vacante))
            ->assertSessionHas('error', 'Esta vacante no se encuentra vigente.');

        $this->assertDatabaseCount('postulaciones', 0);
    }

    public function test_reclutador_cannot_apply(): void
    {
        $vacante = Vacante::factory()->vigente()->create();

        $this->actingAs(Reclutador::factory()->create()->user)
            ->post(route('alumno.postulaciones.store', $vacante))
            ->assertForbidden();

        $this->assertDatabaseCount('postulaciones', 0);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->post(route('alumno.postulaciones.store', Vacante::factory()->vigente()->create()))
            ->assertRedirect(route('login'));
    }

    public function test_postulaciones_page_lists_only_own_applications_with_status(): void
    {
        $alumno = Alumno::factory()->create();
        $propia = Postulacion::factory()->aceptada()->for($alumno)->create();
        $ajena = Postulacion::factory()->create();

        $this->actingAs($alumno->user)
            ->get(route('alumno.postulaciones.index'))
            ->assertOk()
            ->assertSee($propia->vacante->titulo)
            ->assertSee('ACEPTADO')
            ->assertDontSee($ajena->vacante->titulo);
    }
}
