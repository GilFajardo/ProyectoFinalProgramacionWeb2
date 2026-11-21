<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Vacante;
use App\Services\EstadisticaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

/**
 * Página de inicio / bolsa de trabajo: banner, buscador, categorías y vacantes recientes.
 */
class InicioController extends Controller
{
    public function __invoke(EstadisticaService $estadisticas): View
    {
        return view('publico.inicio', [
            'recientes' => Vacante::vigentes()
                ->with(['empresa', 'categoria'])
                ->latest()
                ->take(6)
                ->get(),
            'categorias' => Categoria::query()
                ->withCount(['vacantes' => fn (Builder $q) => $q->vigentes()])
                ->orderBy('nombre')
                ->get(),
            'resumen' => $estadisticas->resumen(),
        ]);
    }
}
