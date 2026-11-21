<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;

/**
 * Autorización sobre empresas.
 * - Administrador: gestión completa.
 * - Reclutador: registra su empresa (si aún no tiene) y edita la suya.
 */
class EmpresaPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->esAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Empresa $empresa): bool
    {
        return $this->esSuEmpresa($user, $empresa);
    }

    public function create(User $user): bool
    {
        return $user->esReclutador() && $user->reclutador?->empresa_id === null;
    }

    public function update(User $user, Empresa $empresa): bool
    {
        return $this->esSuEmpresa($user, $empresa);
    }

    public function delete(User $user, Empresa $empresa): bool
    {
        return false;
    }

    public function restore(User $user, Empresa $empresa): bool
    {
        return false;
    }

    public function forceDelete(User $user, Empresa $empresa): bool
    {
        return false;
    }

    private function esSuEmpresa(User $user, Empresa $empresa): bool
    {
        return $user->esReclutador() && $user->reclutador?->empresa_id === $empresa->id;
    }
}
