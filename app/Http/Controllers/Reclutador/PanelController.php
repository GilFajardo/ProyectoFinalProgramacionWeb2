<?php

namespace App\Http\Controllers\Reclutador;

use App\Enums\EstatusPostulacion;
use App\Models\Postulacion;
use App\Services\EstadisticaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Panel del reclutador: métricas de su empresa, candidatos pendientes y vacantes por vencer.
 */
class PanelController extends ReclutadorController
{
    public function __invoke(Request $request, EstadisticaService $estadisticas): View|RedirectResponse
    {
        $empresa = $this->reclutador($request)->empresa;

        if ($empresa === null) {
            return redirect()->route('reclutador.empresa.edit')
                ->with('aviso', 'Registra los datos de tu empresa para comenzar a publicar vacantes.');
        }

        return view('reclutador.panel', [
            'empresa' => $empresa,
            'resumen' => $estadisticas->resumen($empresa),
            'porEstatus' => $estadisticas->postulacionesPorEstatus($empresa),
            'pendientes' => Postulacion::query()
                ->with(['alumno.user', 'vacante'])
                ->whereRelation('vacante', 'empresa_id', $empresa->id)
                ->where('estatus', EstatusPostulacion::EnProceso)
                ->latest()
                ->take(6)
                ->get(),
            'porVencer' => $empresa->vacantes()
                ->conVigencia('por_vencer')
                ->withCount('postulaciones')
                ->orderBy('fecha_fin')
                ->get(),
        ]);
    }
}
