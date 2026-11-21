<?php

namespace App\Models;

use Database\Factories\MensajeContactoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Mensaje enviado desde el formulario público de contacto.
 */
#[Table('mensajes_contacto')]
#[Fillable(['nombre', 'email', 'asunto', 'mensaje', 'ip', 'leido'])]
class MensajeContacto extends Model
{
    /** @use HasFactory<MensajeContactoFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'leido' => 'boolean',
        ];
    }
}
