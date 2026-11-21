<?php

namespace Tests\Feature\Http\Auth;

use App\Enums\Rol;
use App\Models\Alumno;
use App\Models\Reclutador;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_each_role_is_redirected_to_its_own_panel_after_login(): void
    {
        $alumno = Alumno::factory()->create()->user;
        $reclutador = Reclutador::factory()->create()->user;
        $admin = User::factory()->admin()->create();

        $this->post('/login', ['email' => $alumno->email, 'password' => 'password'])->assertRedirect(route('alumno.panel'));
        $this->post('/logout');
        $this->post('/login', ['email' => $reclutador->email, 'password' => 'password'])->assertRedirect(route('reclutador.panel'));
        $this->post('/logout');
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $usuario = User::factory()->create();

        $this->post('/login', ['email' => $usuario->email, 'password' => 'incorrecta'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_account_cannot_log_in(): void
    {
        $usuario = User::factory()->inactivo()->create();

        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        $this->freezeTime();
        $usuario = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $usuario->email, 'password' => 'incorrecta']);
        }

        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
            ->assertSessionHasErrors(['email' => trans('auth.throttle', ['seconds' => 60])]);
        $this->assertGuest();
    }

    public function test_alumno_registration_creates_alumno_profile_and_ignores_role_from_payload(): void
    {
        $this->post('/registro/alumno', [
            'name' => 'Pedro Ramírez',
            'email' => 'pedro@alumnos.upslp.edu.mx',
            'matricula' => '201234',
            'carrera' => config('upslp.carreras')[0],
            'password' => 'Secreta123',
            'password_confirmation' => 'Secreta123',
            'acepto_aviso' => '1',
            'role' => 'admin',
        ])->assertRedirect(route('alumno.cv'));

        $usuario = User::where('email', 'pedro@alumnos.upslp.edu.mx')->sole();
        $this->assertSame(Rol::Alumno, $usuario->role);
        $this->assertSame('201234', $usuario->alumno->matricula);
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_alumno_registration_rejects_duplicate_matricula(): void
    {
        Alumno::factory()->create(['matricula' => '201234']);

        $this->post('/registro/alumno', [
            'name' => 'Otro', 'email' => 'otro@ejemplo.com', 'matricula' => '201234',
            'carrera' => config('upslp.carreras')[0], 'password' => 'Secreta123',
            'password_confirmation' => 'Secreta123', 'acepto_aviso' => '1',
        ])->assertSessionHasErrors('matricula');
    }

    public function test_reclutador_registration_creates_linked_active_empresa(): void
    {
        $this->post('/registro/reclutador', [
            'name' => 'Sofía Herrera',
            'email' => 'sofia@empresa.mx',
            'empresa_nombre' => 'Industrias del Desierto',
            'rfc' => 'ide200101ab1',
            'password' => 'Secreta123',
            'password_confirmation' => 'Secreta123',
            'acepto_aviso' => '1',
        ])->assertRedirect(route('reclutador.empresa.edit'));

        $reclutador = User::where('email', 'sofia@empresa.mx')->sole()->reclutador;
        $this->assertSame('Industrias del Desierto', $reclutador->empresa->nombre);
        $this->assertSame('IDE200101AB1', $reclutador->empresa->rfc);
        $this->assertTrue($reclutador->puedePublicar());
    }

    public function test_authenticated_user_visiting_login_is_sent_to_own_panel(): void
    {
        $this->actingAs(Alumno::factory()->create()->user)->get('/login')->assertRedirect(route('alumno.panel'));
    }

    public function test_logout_ends_session(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect(route('inicio'));
        $this->assertGuest();
    }
}
