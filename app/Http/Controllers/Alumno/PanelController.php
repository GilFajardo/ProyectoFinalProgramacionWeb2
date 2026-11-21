<?php

namespace App\Http\Controllers\Alumno;

use App\Enums\EstatusPostulacion;
use App\Models\Vacante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Panel de inicio del alumno: avance del CV, resumen de postulaciones y vacantes sugeridas.
 */
class PanelController extends AlumnoController
{
    public function __invoke(Request $request): View
    {
        $alumno = $this->alumno($request)->loadCount(['experiencias', 'formaciones', 'habilidades', 'documentos']);

        $conteo = $alumno->postulaciones()
            ->selectRaw('estatus, COUNT(*) as total')
            ->groupBy('estatus')
            ->pluck('total', 'estatus');

        // Sugerencias: vacantes vigentes en las áreas de sus habilidades a las que aún no se ha postulado.
        $areas = $alumno->habilidades()->whereNotNull('categoria_id')->distinct()->pluck('categoria_id');

        $sugeridas = Vacante::vigentes()
            ->with(['empresa', 'categoria'])
            ->when($areas->isNotEmpty(), fn (Builder $q) => $q->whereIn('categoria_id', $areas))
            ->whereDoesntHave('postulaciones', fn (Builder $q) => $q->whereBelongsTo($alumno))
            ->latest()
            ->take(3)
            ->get();

        return view('alumno.panel', [
            'alumno' => $alumno,
            'avanceCv' => $this->avanceCv($alumno->toArray()),
            'conteo' => collect(EstatusPostulacion::cases())->mapWithKeys(fn (EstatusPostulacion $e): array => [$e->value => (int) ($conteo[$e->value] ?? 0)]),
            'recientes' => $alumno->postulaciones()->with('vacante.empresa')->latest()->take(5)->get(),
            'sugeridas' => $sugeridas,
        ]);
    }

    /**
     * Porcentaje de llenado del CV (5 secciones con el mismo peso).
     *
     * @param  array<string, mixed>  $datos
     */
    private function avanceCv(array $datos): int
    {
        $secciones = [
            filled($datos['resumen'] ?? null) && filled($datos['telefono'] ?? null),
            ($datos['experiencias_count'] ?? 0) > 0,
            ($datos['formaciones_count'] ?? 0) > 0,
            ($datos['habilidades_count'] ?? 0) >= 3,
            ($datos['documentos_count'] ?? 0) > 0,
        ];

        return (int) round(count(array_filter($secciones)) / count($secciones) * 100);
    }
}
