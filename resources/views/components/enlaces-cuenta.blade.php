{{-- Enlaces de la cuenta según el rol (menú de usuario y menú móvil). --}}
@props(['usuario', 'clase' => ''])

@php
    $enlaces = match ($usuario->role) {
        \App\Enums\Rol::Alumno => [
            ['alumno.panel', 'Mi panel'],
            ['alumno.cv', 'Mi CV'],
            ['alumno.postulaciones.index', 'Mis postulaciones'],
            ['alumno.perfil.edit', 'Mi perfil'],
        ],
        \App\Enums\Rol::Reclutador => [
            ['reclutador.panel', 'Mi panel'],
            ['reclutador.vacantes.index', 'Mis vacantes'],
            ['reclutador.empresa.edit', 'Mi empresa'],
            ['reclutador.perfil.edit', 'Mi perfil'],
        ],
        \App\Enums\Rol::Admin => [
            ['filament.admin.pages.dashboard', 'Panel de administración'],
        ],
    };
@endphp

@foreach ($enlaces as [$ruta, $texto])
    <a href="{{ route($ruta) }}" class="{{ $clase }}">{{ $texto }}</a>
@endforeach

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="{{ $clase }} w-full text-left text-red-600">Cerrar sesión</button>
</form>
