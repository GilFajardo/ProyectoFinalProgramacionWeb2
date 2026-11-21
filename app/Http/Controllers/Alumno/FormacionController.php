<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Requests\Alumno\FormacionRequest;
use App\Models\CvFormacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * CV: alta, modificación y baja de formación académica y certificaciones.
 */
class FormacionController extends AlumnoController
{
    public function store(FormacionRequest $request): RedirectResponse
    {
        $this->alumno($request)->formaciones()->create($request->datos());

        return redirect()->to(route('alumno.cv').'#formacion')->with('exito', 'Formación agregada a tu CV.');
    }

    public function update(FormacionRequest $request, CvFormacion $formacion): RedirectResponse
    {
        $this->autorizarPropietario($request, $formacion);

        $formacion->update($request->datos());

        return redirect()->to(route('alumno.cv').'#formacion')->with('exito', 'Formación actualizada.');
    }

    public function destroy(Request $request, CvFormacion $formacion): RedirectResponse
    {
        $this->autorizarPropietario($request, $formacion);

        $formacion->delete();

        return redirect()->to(route('alumno.cv').'#formacion')->with('exito', 'Formación eliminada.');
    }
}
