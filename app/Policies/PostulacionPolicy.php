<?php

namespace App\Policies;

use App\Models\Postulacion;
use App\Models\User;

/**
 * Autorización sobre postulaciones.
 * - Administrador: acceso total (supervisión).
 * - Alumno: consulta sus propias postulaciones.
 * - Reclutador: consulta y cambia el estatus de las postulaciones a vacantes de su empresa.
 */
class PostulacionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->esAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->esAlumno() || $user->esReclutador();
    }

    public function view(User $user, Postulacion $postulacion): bool
    {
        return $this->esSuPostulacion($user, $postulacion)
            || $this->esDeSuEmpresa($user, $postulacion);
    }

    public function create(User $user): bool
    {
        return $user->esAlumno();
    }

    /**
     * Cambiar estatus (ACEPTADO / RECHAZADO / EN PROCESO) y dejar comentario.
     */
    public function update(User $user, Postulacion $postulacion): bool
    {
        return $this->esDeSuEmpresa($user, $postulacion);
    }

    public function delete(User $user, Postulacion $postulacion): bool
    {
        return false;
    }

    private function esSuPostulacion(User $user, Postulacion $postulacion): bool
    {
        return $user->esAlumno() && $user->alumno?->id === $postulacion->alumno_id;
    }

    private function esDeSuEmpresa(User $user, Postulacion $postulacion): bool
    {
        return $user->esReclutador()
            && $user->reclutador?->empresa_id !== null
            && $user->reclutador->empresa_id === $postulacion->vacante->empresa_id;
    }
}
