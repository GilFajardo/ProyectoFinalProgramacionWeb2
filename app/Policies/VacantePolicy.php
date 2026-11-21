<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacante;
use Illuminate\Auth\Access\Response;

/**
 * Autorización sobre vacantes.
 * - Administrador: acceso total.
 * - Reclutador: gestiona solo las vacantes de su empresa.
 * - Alumno: puede postularse a vacantes vigentes una sola vez.
 */
class VacantePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->esAdmin() && $ability !== 'postular' ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->esReclutador();
    }

    public function view(User $user, Vacante $vacante): bool
    {
        return $this->perteneceASuEmpresa($user, $vacante);
    }

    public function create(User $user): Response
    {
        if (! $user->esReclutador()) {
            return Response::deny('Solo los reclutadores pueden publicar vacantes.');
        }

        return $user->reclutador?->puedePublicar()
            ? Response::allow()
            : Response::deny('Primero registra los datos de tu empresa para poder publicar vacantes.');
    }

    public function update(User $user, Vacante $vacante): bool
    {
        return $this->perteneceASuEmpresa($user, $vacante);
    }

    public function delete(User $user, Vacante $vacante): bool
    {
        return $this->perteneceASuEmpresa($user, $vacante);
    }

    public function restore(User $user, Vacante $vacante): bool
    {
        return $this->perteneceASuEmpresa($user, $vacante);
    }

    public function forceDelete(User $user, Vacante $vacante): bool
    {
        return false;
    }

    /**
     * Ver candidatos y generar reportes de la vacante.
     */
    public function verCandidatos(User $user, Vacante $vacante): bool
    {
        return $this->perteneceASuEmpresa($user, $vacante);
    }

    /**
     * Regla de postulación: alumno activo, vacante vigente y sin postulación previa.
     */
    public function postular(User $user, Vacante $vacante): Response
    {
        if (! $user->esAlumno() || $user->alumno === null) {
            return Response::deny('Solo los alumnos con perfil completo pueden postularse.');
        }

        if (! $vacante->estaVigente()) {
            return Response::deny('Esta vacante no se encuentra vigente.');
        }

        if ($user->alumno->yaSePostuloA($vacante)) {
            return Response::deny('Ya te postulaste a esta vacante.');
        }

        return Response::allow();
    }

    private function perteneceASuEmpresa(User $user, Vacante $vacante): bool
    {
        return $user->esReclutador()
            && $user->reclutador?->empresa_id !== null
            && $user->reclutador->empresa_id === $vacante->empresa_id;
    }
}
