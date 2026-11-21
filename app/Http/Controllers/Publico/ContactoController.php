<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactoRequest;
use App\Models\MensajeContacto;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Formulario de contacto. Los mensajes se consultan en el panel de administración.
 */
class ContactoController extends Controller
{
    public function create(): View
    {
        return view('publico.contacto');
    }

    public function store(ContactoRequest $request): RedirectResponse
    {
        MensajeContacto::create($request->safe()->only(['nombre', 'email', 'asunto', 'mensaje']) + [
            'ip' => $request->ip(),
        ]);

        return redirect()->route('contacto')
            ->with('exito', 'Gracias por escribirnos. Te responderemos a la brevedad.');
    }
}
