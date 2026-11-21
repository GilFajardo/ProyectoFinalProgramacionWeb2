<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Services\EstadisticaService;
use Illuminate\View\View;

/**
 * Estadísticas y seguimiento públicos de la bolsa de trabajo (datos agregados, sin datos personales).
 */
class EstadisticaController extends Controller
{
    public function __invoke(EstadisticaService $estadisticas): View
    {
        return view('publico.estadisticas', [
            'resumen' => $estadisticas->resumen(),
            'porCategoria' => $estadisticas->vacantesPorCategoria(soloVigentes: true),
            'porEstatus' => $estadisticas->postulacionesPorEstatus(),
            'porMes' => $estadisticas->postulacionesPorMes(6),
        ]);
    }
}
