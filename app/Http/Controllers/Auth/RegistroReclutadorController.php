<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistroReclutadorRequest;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Registro público de reclutadores. Se crea también su empresa, de modo que
 * puede publicar vacantes de inmediato (sin verificación previa del administrador).
 */
class RegistroReclutadorController extends Controller
{
    public function create(): View
    {
        return view('auth.registro-reclutador');
    }

    public function store(RegistroReclutadorRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $usuario = DB::transaction(function () use ($datos): User {
            $empresa = Empresa::create([
                'nombre' => $datos['empresa_nombre'],
                'rfc' => $datos['rfc'] ?? null,
                'sector' => $datos['sector'] ?? null,
                'activa' => true,
                'verificada' => false,
            ]);

            $usuario = User::create([
                'name' => $datos['name'],
                'email' => $datos['email'],
                'password' => $datos['password'],
                'role' => Rol::Reclutador,
            ]);

            $usuario->reclutador()->create([
                'empresa_id' => $empresa->id,
                'puesto' => $datos['puesto'] ?? null,
                'telefono' => $datos['telefono'] ?? null,
            ]);

            return $usuario;
        });

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('reclutador.empresa.edit')
            ->with('exito', 'Cuenta creada. Completa el perfil de tu empresa y publica tu primera vacante.');
    }
}
