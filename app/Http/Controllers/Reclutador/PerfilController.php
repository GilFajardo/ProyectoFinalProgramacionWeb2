<?php

namespace App\Http\Controllers\Reclutador;

use App\Http\Requests\Reclutador\UpdatePerfilReclutadorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Edición del perfil del reclutador.
 */
class PerfilController extends ReclutadorController
{
    public function edit(Request $request): View
    {
        return view('reclutador.perfil', [
            'usuario' => $request->user(),
            'reclutador' => $this->reclutador($request),
        ]);
    }

    public function update(UpdatePerfilReclutadorRequest $request): RedirectResponse
    {
        $usuario = $request->user();
        $datos = $request->validated();

        DB::transaction(function () use ($usuario, $datos, $request): void {
            $usuario->fill(['name' => $datos['name'], 'email' => $datos['email']]);

            if (filled($datos['password'] ?? null)) {
                $usuario->password = $datos['password'];
            }

            $usuario->save();

            $this->reclutador($request)->update([
                'puesto' => $datos['puesto'] ?? null,
                'telefono' => $datos['telefono'] ?? null,
            ]);
        });

        return redirect()->route('reclutador.perfil.edit')->with('exito', 'Tu perfil se actualizó correctamente.');
    }
}
