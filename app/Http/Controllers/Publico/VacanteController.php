<?php

namespace App\Http\Controllers\Publico;

use App\Enums\Modalidad;
use App\Enums\TipoContrato;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Vacante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Bolsa de trabajo pública: búsqueda, filtros por vigencia y área de conocimiento, y detalle.
 */
class VacanteController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'categoria' => ['nullable', 'string', 'max:120'],
            'vigencia' => ['nullable', Rule::in(['todas', ...array_keys(Vacante::opcionesVigencia())])],
            'modalidad' => ['nullable', Rule::enum(Modalidad::class)],
            'tipo' => ['nullable', Rule::enum(TipoContrato::class)],
            'orden' => ['nullable', Rule::in(['recientes', 'cierre', 'salario'])],
        ]);

        // Por defecto se muestran las vacantes vigentes; "todas" incluye vencidas y programadas activas.
        $vigencia = $filtros['vigencia'] ?? Vacante::VIGENCIA_VIGENTE;

        $vacantes = Vacante::query()
            ->with(['empresa', 'categoria'])
            ->where('activa', true)
            ->buscar($filtros['q'] ?? null)
            ->deCategoria($filtros['categoria'] ?? null)
            ->conVigencia($vigencia === 'todas' ? null : $vigencia)
            ->when($filtros['modalidad'] ?? null, fn (Builder $q, string $modalidad) => $q->where('modalidad', $modalidad))
            ->when($filtros['tipo'] ?? null, fn (Builder $q, string $tipo) => $q->where('tipo_contrato', $tipo))
            ->when(
                $filtros['orden'] ?? 'recientes',
                fn (Builder $q, string $orden) => match ($orden) {
                    'cierre' => $q->orderBy('fecha_fin'),
                    'salario' => $q->orderByDesc('salario_max'),
                    default => $q->latest('fecha_inicio')->latest('id'),
                },
            )
            ->paginate(9)
            ->withQueryString();

        return view('publico.vacantes.index', [
            'vacantes' => $vacantes,
            'filtros' => $filtros + ['vigencia' => $vigencia],
            'categorias' => Categoria::query()
                ->withCount(['vacantes' => fn (Builder $q) => $q->vigentes()])
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    public function show(Request $request, Vacante $vacante): View
    {
        // Una vacante despublicada solo la ve el reclutador de su empresa o el administrador.
        abort_if(! $vacante->activa && ! $request->user()?->can('update', $vacante), 404);

        $vacante->load(['empresa', 'categoria']);
        $alumno = $request->user()?->alumno;

        return view('publico.vacantes.show', [
            'vacante' => $vacante,
            'postulacion' => $alumno?->postulaciones()->whereBelongsTo($vacante)->first(),
            'similares' => Vacante::vigentes()
                ->with('empresa')
                ->whereBelongsTo($vacante->categoria)
                ->whereKeyNot($vacante->id)
                ->latest()
                ->take(3)
                ->get(),
        ]);
    }
}
