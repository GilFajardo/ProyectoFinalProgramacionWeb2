<?php

namespace App\Http\Controllers\Alumno;

use App\Enums\NivelFormacion;
use App\Enums\NivelHabilidad;
use App\Enums\TipoDocumento;
use App\Models\Categoria;
use App\Services\ReporteService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * CV estructurado del alumno: experiencia, formación, habilidades por área y documentos.
 * Cada sección se edita con su propio controlador (ExperienciaController, etc.).
 */
class CvController extends AlumnoController
{
    public function show(Request $request): View
    {
        $alumno = $this->alumno($request)->load(['user', 'experiencias', 'formaciones', 'habilidades.categoria', 'documentos']);

        return view('alumno.cv', [
            'alumno' => $alumno,
            'categorias' => Categoria::orderBy('nombre')->pluck('nombre', 'id'),
            'nivelesFormacion' => NivelFormacion::opciones(),
            'nivelesHabilidad' => NivelHabilidad::opciones(),
            'tiposDocumento' => TipoDocumento::opciones(),
        ]);
    }

    /**
     * Descarga el CV en PDF para imprimirlo o compartirlo.
     */
    public function pdf(Request $request, ReporteService $reportes): Response
    {
        $alumno = $this->alumno($request);

        return $reportes->cv($alumno)->download('cv-'.$alumno->matricula.'.pdf');
    }
}
