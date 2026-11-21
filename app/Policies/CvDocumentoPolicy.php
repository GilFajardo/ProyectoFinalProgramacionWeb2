<?php

namespace App\Policies;

use App\Models\CvDocumento;
use App\Models\User;

/**
 * Autorización sobre los documentos probatorios del CV (archivos privados).
 * - Alumno: sube, descarga y elimina sus propios documentos.
 * - Reclutador: descarga documentos solo de alumnos postulados a vacantes de su empresa.
 */
class CvDocumentoPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->esAdmin() ? true : null;
    }

    public function view(User $user, CvDocumento $documento): bool
    {
        if ($this->esSuDocumento($user, $documento)) {
            return true;
        }

        $empresaId = $user->esReclutador() ? $user->reclutador?->empresa_id : null;

        return $empresaId !== null
            && $documento->alumno->postulaciones()
                ->whereRelation('vacante', 'empresa_id', $empresaId)
                ->exists();
    }

    public function create(User $user): bool
    {
        return $user->esAlumno() && $user->alumno !== null;
    }

    public function delete(User $user, CvDocumento $documento): bool
    {
        return $this->esSuDocumento($user, $documento);
    }

    private function esSuDocumento(User $user, CvDocumento $documento): bool
    {
        return $user->esAlumno() && $user->alumno?->id === $documento->alumno_id;
    }
}
