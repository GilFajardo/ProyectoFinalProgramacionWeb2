<?php

namespace Tests\Feature\Http\Publico;

use App\Models\MensajeContacto;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ContactoTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function mensajeValido(): array
    {
        return [
            'nombre' => 'Laura Gómez',
            'email' => 'laura@ejemplo.com',
            'asunto' => 'Registro de mi empresa',
            'mensaje' => 'Hola, quisiera saber cómo registrar mi empresa en la bolsa de trabajo.',
        ];
    }

    public function test_valid_message_is_stored_and_redirects_with_success(): void
    {
        $this->post('/contacto', $this->mensajeValido())
            ->assertRedirect('/contacto')
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('mensajes_contacto', ['email' => 'laura@ejemplo.com', 'leido' => false]);
    }

    public function test_rejects_short_message_and_invalid_email(): void
    {
        $this->post('/contacto', ['email' => 'no-es-correo', 'mensaje' => 'corto'] + $this->mensajeValido())
            ->assertSessionHasErrors(['email', 'mensaje']);

        $this->assertDatabaseCount('mensajes_contacto', 0);
    }

    public function test_rejects_submission_with_honeypot_filled(): void
    {
        $this->post('/contacto', $this->mensajeValido() + ['sitio_web' => 'http://spam.example'])
            ->assertSessionHasErrors('sitio_web');

        $this->assertSame(0, MensajeContacto::count());
    }
}
