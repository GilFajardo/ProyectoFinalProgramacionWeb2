<?php

namespace App\Http\Controllers\Reclutador;

use App\Enums\EstatusPostulacion;
use App\Enums\Modalidad;
use App\Enums\TipoContrato;
use App\Http\Requests\Reclutador\VacanteRequest;
use App\Models\Categoria;
use App\Models\Vacante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * ABC de vacantes del reclutador (alta, consulta, modificación y baja),
 * siempre limitado a las vacantes de su empresa (VacantePolicy).
 */
class VacanteController extends ReclutadorController
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Vacante::class);

        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'vigencia' => ['nullable', Rule::in(array_keys(Vacante::opcionesVigencia()))],
        ]);

        $empresa = $this->empresa($request);

        $vacantes = $empresa->vacantes()
            ->with('categoria')
            ->withCount([
                'postulaciones',
                'postulaciones as en_proceso_count' => fn ($q) => $q->where('estatus', EstatusPostulacion::EnProceso),
            ])
            ->when($filtros['q'] ?? null, fn ($q, string $texto) => $q->where('titulo', 'like', '%'.addcslashes($texto, '%_\\').'%'))
            ->conVigencia($filtros['vigencia'] ?? null)
            ->latest('fecha_inicio')
            ->paginate(10)
            ->withQueryString();

        return view('reclutador.vacantes.index', [
            'empresa' => $empresa,
            'vacantes' => $vacantes,
            'filtros' => $filtros,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $respuesta = Gate::inspect('create', Vacante::class);

        if ($respuesta->denied()) {
            return redirect()->route('reclutador.empresa.edit')->with('aviso', $respuesta->message());
        }

        return view('reclutador.vacantes.form', $this->datosFormulario(new Vacante([
            'fecha_inicio' => today(),
            'fecha_fin' => today()->addDays(30),
            'plazas' => 1,
            'modalidad' => Modalidad::Presencial,
            'activa' => true,
        ])));
    }

    public function store(VacanteRequest $request): RedirectResponse
    {
        $reclutador = $this->reclutador($request);

        $vacante = $reclutador->empresa->vacantes()->create($request->validated() + [
            'reclutador_id' => $reclutador->id,
        ]);

        return redirect()->route('reclutador.vacantes.show', $vacante)->with('exito', 'Vacante publicada correctamente.');
    }

    /**
     * Detalle de la vacante con sus candidatos postulados.
     */
    public function show(Request $request, Vacante $vacante): View
    {
        Gate::authorize('verCandidatos', $vacante);

        $filtros = $request->validate([
            'estatus' => ['nullable', Rule::enum(EstatusPostulacion::class)],
        ]);

        $vacante->load('categoria')->loadCount('postulaciones');

        return view('reclutador.vacantes.show', [
            'vacante' => $vacante,
            'candidatos' => $vacante->postulaciones()
                ->with(['alumno.user', 'alumno.habilidades'])
                ->when($filtros['estatus'] ?? null, fn ($q, string $estatus) => $q->where('estatus', $estatus))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'conteo' => $vacante->postulaciones()->selectRaw('estatus, COUNT(*) as total')->groupBy('estatus')->pluck('total', 'estatus'),
            'estatusActual' => $filtros['estatus'] ?? null,
        ]);
    }

    public function edit(Vacante $vacante): View
    {
        Gate::authorize('update', $vacante);

        return view('reclutador.vacantes.form', $this->datosFormulario($vacante));
    }

    public function update(VacanteRequest $request, Vacante $vacante): RedirectResponse
    {
        $vacante->update($request->validated());

        return redirect()->route('reclutador.vacantes.show', $vacante)->with('exito', 'Vacante actualizada.');
    }

    /**
     * Baja lógica: la vacante deja de mostrarse, pero se conserva el historial de postulaciones.
     */
    public function destroy(Vacante $vacante): RedirectResponse
    {
        Gate::authorize('delete', $vacante);

        $vacante->delete();

        return redirect()->route('reclutador.vacantes.index')->with('exito', 'La vacante «'.$vacante->titulo.'» se dio de baja.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFormulario(Vacante $vacante): array
    {
        return [
            'vacante' => $vacante,
            'categorias' => Categoria::orderBy('nombre')->pluck('nombre', 'id'),
            'modalidades' => Modalidad::opciones(),
            'tiposContrato' => TipoContrato::opciones(),
        ];
    }
}
