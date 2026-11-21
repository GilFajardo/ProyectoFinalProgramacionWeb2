<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistroAlumnoRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Registro público de alumnos (solicitantes).
 */
class RegistroAlumnoController extends Controller
{
    public function create(): View
    {
        return view('auth.registro-alumno', [
            'carreras' => config('upslp.carreras'),
        ]);
    }

    public function store(RegistroAlumnoRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        // El rol se asigna aquí, nunca desde los datos del formulario.
        $usuario = DB::transaction(function () use ($datos): User {
            $usuario = User::create([
                'name' => $datos['name'],
                'email' => $datos['email'],
                'password' => $datos['password'],
                'role' => Rol::Alumno,
            ]);

            $usuario->alumno()->create([
                'matricula' => $datos['matricula'],
                'carrera' => $datos['carrera'],
                'cuatrimestre' => $datos['cuatrimestre'] ?? null,
            ]);

            return $usuario;
        });

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('alumno.cv')
            ->with('exito', '¡Bienvenido(a)! Completa tu CV para postularte a las vacantes.');
    }
}
