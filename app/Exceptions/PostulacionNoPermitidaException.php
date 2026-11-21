<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Se lanza cuando una postulación viola una regla de negocio
 * (vacante no vigente, postulación duplicada o usuario sin perfil de alumno).
 */
class PostulacionNoPermitidaException extends RuntimeException
{
    public static function duplicada(): self
    {
        return new self('Ya te postulaste a esta vacante.');
    }

    public static function noVigente(): self
    {
        return new self('Esta vacante no se encuentra vigente.');
    }
}
