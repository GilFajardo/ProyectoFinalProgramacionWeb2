<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Base de los controladores del portal del alumno.
 */
abstract class AlumnoController extends Controller
{
    /**
     * Perfil de alumno del usuario autenticado.
     */
    protected function alumno(Request $request): Alumno
    {
        $alumno = $request->user()->alumno;

        abort_if($alumno === null, 403, 'Tu cuenta no tiene un perfil de alumno. Contacta a Vinculación.');

        return $alumno;
    }

    /**
     * Garantiza que un registro del CV pertenezca al alumno autenticado (evita IDOR).
     */
    protected function autorizarPropietario(Request $request, Model $registro): void
    {
        abort_unless($registro->getAttribute('alumno_id') === $this->alumno($request)->id, 403);
    }
}
