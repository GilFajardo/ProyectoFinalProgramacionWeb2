<?php

namespace App\Services;

use App\Enums\EstatusPostulacion;
use App\Exceptions\PostulacionNoPermitidaException;
use App\Models\Alumno;
use App\Models\Postulacion;
use App\Models\Vacante;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Lógica de negocio de las postulaciones.
 *
 * La regla "un alumno solo se postula una vez por vacante" se valida aquí
 * y, como última defensa, la garantiza el índice UNIQUE (vacante_id, alumno_id).
 */
class PostulacionService
{
    /**
     * Registra la postulación de un alumno a una vacante vigente.
     *
     * @throws PostulacionNoPermitidaException
     */
    public function postular(Alumno $alumno, Vacante $vacante, ?string $cartaPresentacion = null): Postulacion
    {
        if (! $vacante->estaVigente()) {
            throw PostulacionNoPermitidaException::noVigente();
        }

        if ($alumno->yaSePostuloA($vacante)) {
            throw PostulacionNoPermitidaException::duplicada();
        }

        try {
            return DB::transaction(fn (): Postulacion => Postulacion::create([
                'vacante_id' => $vacante->id,
                'alumno_id' => $alumno->id,
                'estatus' => EstatusPostulacion::EnProceso,
                'carta_presentacion' => $cartaPresentacion,
            ]));
        } catch (UniqueConstraintViolationException) {
            // Dos solicitudes simultáneas (p. ej. doble clic) pasaron la validación previa.
            throw PostulacionNoPermitidaException::duplicada();
        }
    }
}
