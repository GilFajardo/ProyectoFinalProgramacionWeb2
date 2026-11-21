<?php

namespace Tests\Feature\Http\Alumno;

use App\Models\Alumno;
use App\Models\CvExperiencia;
use App\Models\Reclutador;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PortalAlumnoTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function paginasDelPortal(): array
    {
        return [
            'panel' => ['alumno.panel'],
            'perfil' => ['alumno.perfil.edit'],
            'cv' => ['alumno.cv'],
            'postulaciones' => ['alumno.postulaciones.index'],
        ];
    }

    #[DataProvider('paginasDelPortal')]
    public function test_alumno_can_open_portal_page(string $ruta): void
    {
        $alumno = Alumno::factory()->create();
        CvExperiencia::factory()->for($alumno)->create();

        $this->actingAs($alumno->user)->get(route($ruta))->assertOk();
    }

    #[DataProvider('paginasDelPortal')]
    public function test_reclutador_is_forbidden_from_alumno_portal(string $ruta): void
    {
        $this->actingAs(Reclutador::factory()->create()->user)->get(route($ruta))->assertForbidden();
    }

    public function test_profile_update_changes_data_without_touching_password_when_blank(): void
    {
        $alumno = Alumno::factory()->create();
        $hashAnterior = $alumno->user->password;

        $this->actingAs($alumno->user)->put(route('alumno.perfil.update'), [
            'name' => 'Nombre Actualizado',
            'email' => $alumno->user->email,
            'matricula' => $alumno->matricula,
            'carrera' => $alumno->carrera,
            'telefono' => '444 123 4567',
            'resumen' => 'Perfil actualizado.',
        ])->assertRedirect(route('alumno.perfil.edit'));

        $alumno->refresh();
        $this->assertSame('Nombre Actualizado', $alumno->user->name);
        $this->assertSame('Perfil actualizado.', $alumno->resumen);
        $this->assertSame($hashAnterior, $alumno->user->password);
    }

    public function test_password_change_requires_current_password(): void
    {
        $alumno = Alumno::factory()->create();

        $this->actingAs($alumno->user)->put(route('alumno.perfil.update'), [
            'name' => $alumno->user->name,
            'email' => $alumno->user->email,
            'matricula' => $alumno->matricula,
            'carrera' => $alumno->carrera,
            'password_actual' => 'incorrecta',
            'password' => 'NuevaClave123',
            'password_confirmation' => 'NuevaClave123',
        ])->assertSessionHasErrors('password_actual');
    }
}
