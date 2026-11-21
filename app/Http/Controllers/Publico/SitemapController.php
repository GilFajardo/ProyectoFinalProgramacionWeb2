<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Vacante;
use Illuminate\Http\Response;

/**
 * sitemap.xml para buscadores: páginas públicas y vacantes vigentes.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $paginas = collect(['inicio', 'vacantes.index', 'quienes-somos', 'faq', 'contacto', 'estadisticas', 'mapa-sitio', 'registro.alumno', 'registro.reclutador'])
            ->map(fn (string $ruta): array => ['url' => route($ruta), 'actualizado' => null]);

        $vacantes = Vacante::vigentes()
            ->latest('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(fn (Vacante $vacante): array => [
                'url' => route('vacantes.show', $vacante),
                'actualizado' => $vacante->updated_at,
            ]);

        return response()
            ->view('publico.sitemap', ['urls' => $paginas->concat($vacantes)])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
