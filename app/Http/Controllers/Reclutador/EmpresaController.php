<?php

namespace App\Http\Controllers\Reclutador;

use App\Http\Requests\Reclutador\EmpresaRequest;
use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Datos de la empresa del reclutador. Si aún no tiene empresa, el mismo
 * formulario sirve para registrarla y vincularse a ella.
 */
class EmpresaController extends ReclutadorController
{
    public function edit(Request $request): View
    {
        return view('reclutador.empresa', [
            'empresa' => $this->reclutador($request)->empresa,
        ]);
    }

    public function store(EmpresaRequest $request): RedirectResponse
    {
        Gate::authorize('create', Empresa::class);

        DB::transaction(function () use ($request): void {
            $empresa = Empresa::create($this->datos($request) + ['activa' => true, 'verificada' => false]);
            $this->reclutador($request)->update(['empresa_id' => $empresa->id]);
        });

        return redirect()->route('reclutador.panel')->with('exito', 'Empresa registrada. Ya puedes publicar vacantes.');
    }

    public function update(EmpresaRequest $request): RedirectResponse
    {
        $empresa = $this->empresa($request);

        Gate::authorize('update', $empresa);

        $datos = $this->datos($request);

        if (isset($datos['logo_path']) && $empresa->logo_path) {
            Storage::disk('public')->delete($empresa->logo_path);
        }

        $empresa->update($datos);

        return redirect()->route('reclutador.empresa.edit')->with('exito', 'Los datos de tu empresa se actualizaron.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datos(EmpresaRequest $request): array
    {
        $datos = $request->safe()->except('logo');

        if ($request->hasFile('logo')) {
            $datos['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        return $datos;
    }
}
