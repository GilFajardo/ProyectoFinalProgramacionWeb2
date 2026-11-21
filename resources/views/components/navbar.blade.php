{{-- Barra de navegación principal: menú de escritorio y menú desplegable en móvil (Alpine.js). --}}
@php
    $enlaces = [
        ['ruta' => 'inicio', 'texto' => 'Inicio', 'activo' => 'inicio'],
        ['ruta' => 'vacantes.index', 'texto' => 'Bolsa de trabajo', 'activo' => 'vacantes.*'],
        ['ruta' => 'quienes-somos', 'texto' => 'Quiénes somos', 'activo' => 'quienes-somos'],
        ['ruta' => 'estadisticas', 'texto' => 'Estadísticas', 'activo' => 'estadisticas'],
        ['ruta' => 'faq', 'texto' => 'Preguntas frecuentes', 'activo' => 'faq'],
        ['ruta' => 'contacto', 'texto' => 'Contacto', 'activo' => 'contacto'],
    ];
    $usuario = auth()->user();
@endphp

<header x-data="{ abierto: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur" @keydown.escape.window="abierto = false">
    <div class="border-b border-marca-800 bg-marca-900 text-xs text-marca-100">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-1.5 sm:px-6 lg:px-8">
            <span class="truncate">{{ config('upslp.institucion.nombre') }}</span>
            <a href="{{ config('upslp.institucion.sitio_web') }}" class="hidden shrink-0 hover:text-white sm:inline" target="_blank" rel="noopener">upslp.edu.mx ↗</a>
        </div>
    </div>

    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8" aria-label="Navegación principal">
        <a href="{{ route('inicio') }}" class="shrink-0" aria-label="Ir al inicio">
            <x-logo />
        </a>

        <ul class="hidden items-center gap-1 lg:flex">
            @foreach ($enlaces as $enlace)
                <li>
                    <a href="{{ route($enlace['ruta']) }}"
                       @class([
                           'rounded-lg px-3 py-2 text-sm font-medium transition',
                           'bg-marca-50 text-marca-700' => request()->routeIs($enlace['activo']),
                           'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs($enlace['activo']),
                       ])
                       @if (request()->routeIs($enlace['activo'])) aria-current="page" @endif>
                        {{ $enlace['texto'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="hidden items-center gap-2 lg:flex">
            @auth
                <x-menu-usuario :usuario="$usuario" />
            @else
                <a href="{{ route('login') }}" class="btn-secundario">Iniciar sesión</a>
                <a href="{{ route('registro.alumno') }}" class="btn-primario">Registrarme</a>
            @endauth
        </div>

        <button type="button" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                @click="abierto = ! abierto" :aria-expanded="abierto.toString()" aria-controls="menu-movil" aria-label="Abrir menú">
            <svg x-show="! abierto" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            <svg x-show="abierto" x-cloak class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
        </button>
    </nav>

    {{-- Menú móvil --}}
    <div id="menu-movil" x-show="abierto" x-cloak x-transition.origin.top class="border-t border-slate-200 bg-white lg:hidden">
        <ul class="space-y-1 px-4 py-3">
            @foreach ($enlaces as $enlace)
                <li>
                    <a href="{{ route($enlace['ruta']) }}" @class([
                        'block rounded-lg px-3 py-2 text-base font-medium',
                        'bg-marca-50 text-marca-700' => request()->routeIs($enlace['activo']),
                        'text-slate-700 hover:bg-slate-100' => ! request()->routeIs($enlace['activo']),
                    ])>{{ $enlace['texto'] }}</a>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-slate-200 px-4 py-3">
            @auth
                <p class="px-3 pb-2 text-sm text-slate-500">{{ $usuario->name }} · {{ $usuario->role->getLabel() }}</p>
                <x-enlaces-cuenta :usuario="$usuario" clase="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-100" />
            @else
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="btn-secundario">Iniciar sesión</a>
                    <a href="{{ route('registro.alumno') }}" class="btn-primario">Registrarme</a>
                </div>
                <a href="{{ route('registro.reclutador') }}" class="mt-3 block text-center text-sm enlace">¿Eres empresa? Publica vacantes</a>
            @endauth
        </div>
    </div>
</header>
