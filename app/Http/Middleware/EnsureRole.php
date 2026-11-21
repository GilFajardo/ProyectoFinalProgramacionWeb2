<?php

namespace App\Http\Middleware;

use App\Enums\Rol;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe una ruta a uno o más roles y expulsa a las cuentas desactivadas.
 *
 * Uso en rutas: ->middleware('role:alumno') o ->middleware('role:reclutador,admin')
 */
class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        if (! $user->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta está desactivada. Contacta a la oficina de Vinculación.']);
        }

        $permitidos = array_map(fn (string $rol): Rol => Rol::from($rol), $roles);

        abort_unless(in_array($user->role, $permitidos, true), Response::HTTP_FORBIDDEN, 'No tienes permiso para acceder a esta sección.');

        return $next($request);
    }
}
