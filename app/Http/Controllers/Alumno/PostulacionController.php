<?php

namespace App\Http\Controllers\Alumno;

use App\Enums\EstatusPostulacion;
use App\Exceptions\PostulacionNoPermitidaException;
use App\Http\Requests\Alumno\StorePostulacionRequest;
use App\Models\Vacante;
use App\Services\PostulacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Postulaciones del alumno: consulta de estatus y alta (sin duplicados).
 */
class PostulacionController extends AlumnoController
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'estatus' => ['nullable', Rule::enum(EstatusPostulacion::class)],
        ]);

        $postulaciones = $this->alumno($request)->postulaciones()
            ->with(['vacante.empresa', 'vacante.categoria'])
            ->when($filtros['estatus'] ?? null, fn ($q, string $estatus) => $q->where('estatus', $estatus))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('alumno.postulaciones', [
            'postulaciones' => $postulaciones,
            'estatusActual' => $filtros['estatus'] ?? null,
        ]);
    }

    public function store(StorePostulacionRequest $request, Vacante $vacante, PostulacionService $servicio): RedirectResponse
    {
        try {
            $servicio->postular($this->alumno($request), $vacante, $request->validated('carta_presentacion'));
        } catch (PostulacionNoPermitidaException $e) {
            return redirect()->route('vacantes.show', $vacante)->with('error', $e->getMessage());
        }

        return redirect()->route('vacantes.show', $vacante)
            ->with('exito', '¡Listo! Te postulaste a «'.$vacante->titulo.'». Puedes consultar el estatus en «Mis postulaciones».');
    }
}
