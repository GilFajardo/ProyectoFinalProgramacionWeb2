<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Requests\Alumno\DocumentoRequest;
use App\Models\CvDocumento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CV: documentos probatorios. Se guardan en el disco privado "local" con nombre
 * aleatorio y solo se descargan a través de este controlador (CvDocumentoPolicy).
 */
class DocumentoController extends AlumnoController
{
    public function store(DocumentoRequest $request): RedirectResponse
    {
        Gate::authorize('create', CvDocumento::class);

        $alumno = $this->alumno($request);
        $archivo = $request->file('archivo');

        $alumno->documentos()->create([
            'nombre' => $request->validated('nombre'),
            'tipo' => $request->validated('tipo'),
            'archivo_path' => $archivo->store('cv-documentos/'.$alumno->id, 'local'),
            'mime' => $archivo->getMimeType(),
            'tamano_kb' => (int) ceil($archivo->getSize() / 1024),
        ]);

        return redirect()->to(route('alumno.cv').'#documentos')->with('exito', 'Documento agregado a tu CV.');
    }

    public function show(CvDocumento $documento): StreamedResponse
    {
        Gate::authorize('view', $documento);

        return self::descargar($documento);
    }

    public function destroy(Request $request, CvDocumento $documento): RedirectResponse
    {
        Gate::authorize('delete', $documento);

        $documento->delete();

        return redirect()->to(route('alumno.cv').'#documentos')->with('exito', 'Documento eliminado.');
    }

    /**
     * Respuesta de descarga con un nombre legible y la extensión original.
     */
    public static function descargar(CvDocumento $documento): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($documento->archivo_path), 404, 'El archivo ya no está disponible.');

        $extension = pathinfo($documento->archivo_path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download(
            $documento->archivo_path,
            str($documento->nombre)->slug()->append('.'.$extension)->toString(),
        );
    }
}
