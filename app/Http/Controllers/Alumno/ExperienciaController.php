<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Requests\Alumno\ExperienciaRequest;
use App\Models\CvExperiencia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * CV: alta, modificación y baja de empleos anteriores.
 */
class ExperienciaController extends AlumnoController
{
    public function store(ExperienciaRequest $request): RedirectResponse
    {
        $this->alumno($request)->experiencias()->create($request->datos());

        return redirect()->to(route('alumno.cv').'#experiencia')->with('exito', 'Experiencia agregada a tu CV.');
    }

    public function update(ExperienciaRequest $request, CvExperiencia $experiencia): RedirectResponse
    {
        $this->autorizarPropietario($request, $experiencia);

        $experiencia->update($request->datos());

        return redirect()->to(route('alumno.cv').'#experiencia')->with('exito', 'Experiencia actualizada.');
    }

    public function destroy(Request $request, CvExperiencia $experiencia): RedirectResponse
    {
        $this->autorizarPropietario($request, $experiencia);

        $experiencia->delete();

        return redirect()->to(route('alumno.cv').'#experiencia')->with('exito', 'Experiencia eliminada.');
    }
}
