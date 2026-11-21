<?php

namespace Tests\Feature\Filament;

use App\Models\Alumno;
use App\Models\Empresa;
use App\Models\MensajeContacto;
use App\Models\Postulacion;
use App\Models\Reclutador;
use App\Models\User;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PanelAdminTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function paginasDelPanel(): array
    {
        return [
            'dashboard' => ['/admin'],
            'empresas' => ['/admin/empresas'],
            'alta de empresa' => ['/admin/empresas/create'],
            'vacantes' => ['/admin/vacantes'],
            'alta de vacante' => ['/admin/vacantes/create'],
            'postulaciones' => ['/admin/postulaciones'],
            'categorías' => ['/admin/categorias'],
            'usuarios' => ['/admin/usuarios'],
            'alta de usuario' => ['/admin/usuarios/create'],
            'mensajes de contacto' => ['/admin/mensajes-contacto'],
            'reportes' => ['/admin/reportes'],
        ];
    }

    #[DataProvider('paginasDelPanel')]
    public function test_admin_can_open_panel_page(string $url): void
    {
        Postulacion::factory()->count(2)->create();
        MensajeContacto::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get($url)
            ->assertOk();
    }

    public function test_admin_can_open_detail_pages_of_records(): void
    {
        $postulacion = Postulacion::factory()->create();
        Reclutador::factory()->for($postulacion->vacante->empresa)->create();

        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin/empresas/'.$postulacion->vacante->empresa_id)->assertOk();
        $this->get('/admin/vacantes/'.$postulacion->vacante_id)->assertOk();
        $this->get('/admin/vacantes/'.$postulacion->vacante_id.'/edit')->assertOk();
        $this->get('/admin/postulaciones/'.$postulacion->id)->assertOk();
    }

    public function test_reclutador_and_alumno_are_forbidden_from_panel(): void
    {
        $this->actingAs(Reclutador::factory()->create()->user)->get('/admin')->assertForbidden();
        $this->actingAs(Alumno::factory()->create()->user)->get('/admin')->assertForbidden();
    }

    public function test_inactive_admin_is_forbidden_from_panel(): void
    {
        $this->actingAs(User::factory()->admin()->inactivo()->create())
            ->get('/admin/vacantes')
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_panel_login(): void
    {
        $this->get('/admin/empresas')->assertRedirect('/admin/login');
    }

    public function test_soft_deleted_empresa_detail_remains_accessible_to_admin(): void
    {
        $empresa = Empresa::factory()->create();
        Vacante::factory()->for($empresa)->create();
        $empresa->delete();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/empresas/'.$empresa->id)
            ->assertOk();
    }
}
