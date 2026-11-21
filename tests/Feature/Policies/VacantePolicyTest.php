<?php

namespace Tests\Feature\Policies;

use App\Models\Alumno;
use App\Models\Postulacion;
use App\Models\Reclutador;
use App\Models\User;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class VacantePolicyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_reclutador_can_update_vacantes_of_own_empresa_only(): void
    {
        $reclutador = Reclutador::factory()->create();
        $propia = Vacante::factory()->for($reclutador->empresa)->create();
        $ajena = Vacante::factory()->create();

        $this->assertTrue($reclutador->user->can('update', $propia));
        $this->assertFalse($reclutador->user->can('update', $ajena));
        $this->assertFalse($reclutador->user->can('verCandidatos', $ajena));
    }

    public function test_reclutador_without_empresa_cannot_create_vacantes(): void
    {
        $reclutador = Reclutador::factory()->sinEmpresa()->create();

        $this->assertFalse($reclutador->user->can('create', Vacante::class));
    }

    public function test_admin_can_manage_any_vacante(): void
    {
        $admin = User::factory()->admin()->create();
        $vacante = Vacante::factory()->create();

        $this->assertTrue($admin->can('update', $vacante));
        $this->assertTrue($admin->can('delete', $vacante));
    }

    public function test_alumno_can_postular_once_to_vigente_vacante(): void
    {
        $alumno = Alumno::factory()->create();
        $vacante = Vacante::factory()->vigente()->create();

        $this->assertTrue($alumno->user->can('postular', $vacante));

        Postulacion::factory()->for($alumno)->for($vacante)->create();

        $this->assertFalse($alumno->user->fresh()->can('postular', $vacante));
    }

    public function test_non_alumno_roles_cannot_postular(): void
    {
        $vacante = Vacante::factory()->vigente()->create();

        $this->assertFalse(User::factory()->admin()->create()->can('postular', $vacante));
        $this->assertFalse(Reclutador::factory()->create()->user->can('postular', $vacante));
    }
}
