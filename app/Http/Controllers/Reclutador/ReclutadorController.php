<?php

namespace App\Http\Controllers\Reclutador;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Reclutador;
use Illuminate\Http\Request;

/**
 * Base de los controladores del portal del reclutador.
 */
abstract class ReclutadorController extends Controller
{
    protected function reclutador(Request $request): Reclutador
    {
        $reclutador = $request->user()->reclutador;

        abort_if($reclutador === null, 403, 'Tu cuenta no tiene un perfil de reclutador. Contacta a Vinculación.');

        return $reclutador;
    }

    /**
     * Empresa del reclutador. Si aún no tiene una, responde 403 con instrucciones.
     */
    protected function empresa(Request $request): Empresa
    {
        $empresa = $this->reclutador($request)->empresa;

        abort_if($empresa === null, 403, 'Primero registra los datos de tu empresa.');

        return $empresa;
    }
}
