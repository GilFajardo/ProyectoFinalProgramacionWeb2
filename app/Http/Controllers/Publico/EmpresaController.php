<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\View\View;

/**
 * Perfil público de una empresa activa con sus vacantes vigentes.
 */
class EmpresaController extends Controller
{
    public function __invoke(Empresa $empresa): View
    {
        abort_unless($empresa->activa, 404);

        return view('publico.empresa', [
            'empresa' => $empresa,
            'vacantes' => $empresa->vacantes()->vigentes()->with('categoria')->latest()->get(),
        ]);
    }
}
