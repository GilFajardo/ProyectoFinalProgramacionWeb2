<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Requests\Alumno\HabilidadRequest;
use App\Models\CvHabilidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * CV: habilidades por área de conocimiento.
 */
class HabilidadController extends AlumnoController
{
    public function store(HabilidadRequest $request): RedirectResponse
    {
        $this->alumno($request)->habilidades()->create($request->validated());

        return redirect()->to(route('alumno.cv').'#habilidades')->with('exito', 'Habilidad agregada a tu CV.');
    }

    public function destroy(Request $request, CvHabilidad $habilidad): RedirectResponse
    {
        $this->autorizarPropietario($request, $habilidad);

        $habilidad->delete();

        return redirect()->to(route('alumno.cv').'#habilidades')->with('exito', 'Habilidad eliminada.');
    }
}
