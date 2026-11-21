<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Requests\Alumno\UpdatePerfilAlumnoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Edición de los datos principales del alumno.
 */
class PerfilController extends AlumnoController
{
    public function edit(Request $request): View
    {
        return view('alumno.perfil', [
            'usuario' => $request->user(),
            'alumno' => $this->alumno($request),
            'carreras' => config('upslp.carreras'),
        ]);
    }

    public function update(UpdatePerfilAlumnoRequest $request): RedirectResponse
    {
        $usuario = $request->user();
        $alumno = $this->alumno($request);
        $datos = $request->validated();

        DB::transaction(function () use ($usuario, $alumno, $datos, $request): void {
            $usuario->fill(['name' => $datos['name'], 'email' => $datos['email']]);

            if (filled($datos['password'] ?? null)) {
                $usuario->password = $datos['password'];
            }

            $usuario->save();

            $perfil = collect($datos)->only([
                'matricula', 'carrera', 'cuatrimestre', 'telefono', 'fecha_nacimiento',
                'ciudad', 'resumen', 'linkedin_url', 'portafolio_url',
            ])->all();

            if ($request->hasFile('foto')) {
                if ($alumno->foto_path) {
                    Storage::disk('public')->delete($alumno->foto_path);
                }

                $perfil['foto_path'] = $request->file('foto')->store('fotos', 'public');
            }

            $alumno->update($perfil);
        });

        return redirect()->route('alumno.perfil.edit')->with('exito', 'Tu perfil se actualizó correctamente.');
    }
}
