<?php

namespace App\Http\Controllers\Reclutador;

use App\Enums\EstatusPostulacion;
use App\Http\Controllers\Alumno\DocumentoController;
use App\Http\Requests\Reclutador\UpdateEstatusRequest;
use App\Models\CvDocumento;
use App\Models\Postulacion;
use App\Services\ReporteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Evaluación de candidatos: perfil y CV del alumno postulado, cambio de estatus,
 * CV en PDF y descarga de documentos probatorios.
 */
class CandidatoController extends ReclutadorController
{
    public function show(Postulacion $postulacion): View
    {
        Gate::authorize('view', $postulacion);

        $postulacion->load([
            'vacante',
            'alumno.user',
            'alumno.experiencias',
            'alumno.formaciones',
            'alumno.habilidades.categoria',
            'alumno.documentos',
        ]);

        return view('reclutador.candidatos.show', [
            'postulacion' => $postulacion,
            'alumno' => $postulacion->alumno,
            'estatus' => EstatusPostulacion::cases(),
        ]);
    }

    public function update(UpdateEstatusRequest $request, Postulacion $postulacion): RedirectResponse
    {
        $postulacion->cambiarEstatus(
            EstatusPostulacion::from($request->validated('estatus')),
            $request->validated('comentario_reclutador'),
        );

        return redirect()->route('reclutador.candidatos.show', $postulacion)
            ->with('exito', 'Estatus actualizado a '.$postulacion->estatus->getLabel().'.');
    }

    public function cv(Postulacion $postulacion, ReporteService $reportes): Response
    {
        Gate::authorize('view', $postulacion);

        return $reportes->cv($postulacion->alumno)->stream('cv-'.$postulacion->alumno->matricula.'.pdf');
    }

    public function documento(Postulacion $postulacion, CvDocumento $documento): StreamedResponse
    {
        Gate::authorize('view', $postulacion);
        abort_unless($documento->alumno_id === $postulacion->alumno_id, 404);
        Gate::authorize('view', $documento);

        return DocumentoController::descargar($documento);
    }
}
