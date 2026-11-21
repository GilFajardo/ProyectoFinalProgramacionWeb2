<?php

namespace App\Http\Controllers\Reclutador;

use App\Enums\EstatusPostulacion;
use App\Models\Vacante;
use App\Services\ReporteService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * Reportes PDF del reclutador (vacantes, postulaciones y ficha de su empresa).
 * El alcance siempre se limita a la empresa del reclutador.
 */
class ReporteController extends ReclutadorController
{
    public function __invoke(Request $request, string $tipo, ReporteService $reportes): Response
    {
        $empresa = $this->empresa($request);

        $filtros = $request->validate([
            'vigencia' => ['nullable', Rule::in(array_keys(Vacante::opcionesVigencia()))],
            'categoria_id' => ['nullable', 'integer', 'exists:categorias,id'],
            'estatus' => ['nullable', Rule::enum(EstatusPostulacion::class)],
            // Solo se acepta una vacante de su propia empresa.
            'vacante_id' => ['nullable', 'integer', Rule::exists('vacantes', 'id')->where('empresa_id', $empresa->id)],
        ]);

        return $reportes->generar($tipo, array_filter($filtros, 'filled'), $empresa)
            ->stream($reportes->nombreArchivo($tipo));
    }
}
