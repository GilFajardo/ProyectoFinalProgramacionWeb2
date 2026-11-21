{{--
    Contenedor de los portales de alumno y reclutador: navegación lateral (pestañas en móvil) + contenido.
--}}
@props(['titulo', 'descripcion' => null])

@php
    $usuario = auth()->user();
    $secciones = $usuario->esAlumno()
        ? [
            ['alumno.panel', 'alumno.panel', 'Resumen', 'M2.25 12l8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
            ['alumno.cv', 'alumno.cv', 'Mi CV', 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
            ['alumno.postulaciones.index', 'alumno.postulaciones.*', 'Mis postulaciones', 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
            ['vacantes.index', 'vacantes.*', 'Buscar vacantes', 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z'],
            ['alumno.perfil.edit', 'alumno.perfil.*', 'Mi perfil', 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z'],
        ]
        : [
            ['reclutador.panel', 'reclutador.panel', 'Resumen', 'M2.25 12l8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
            ['reclutador.vacantes.index', 'reclutador.vacantes.*', 'Mis vacantes', 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0'],
            ['reclutador.empresa.edit', 'reclutador.empresa.*', 'Mi empresa', 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'],
            ['reclutador.perfil.edit', 'reclutador.perfil.*', 'Mi perfil', 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z'],
        ];
@endphp

<x-layouts.app :title="$titulo">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-[230px_1fr] lg:gap-8">
            <aside class="mb-6 lg:mb-0">
                <div class="tarjeta hidden p-4 lg:block">
                    <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">{{ $usuario->role->getLabel() }}</p>
                    <p class="mt-1 truncate font-semibold text-slate-900">{{ $usuario->name }}</p>
                    @if ($usuario->esReclutador() && $usuario->reclutador?->empresa)
                        <p class="truncate text-sm text-slate-500">{{ $usuario->reclutador->empresa->nombre }}</p>
                    @endif
                </div>

                <nav aria-label="Secciones del portal" class="-mx-4 overflow-x-auto px-4 lg:mx-0 lg:mt-4 lg:overflow-visible lg:px-0">
                    <ul class="flex gap-2 lg:flex-col lg:gap-1">
                        @foreach ($secciones as [$ruta, $patron, $texto, $icono])
                            <li class="shrink-0">
                                <a href="{{ route($ruta) }}" @class([
                                    'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium whitespace-nowrap transition',
                                    'bg-marca-700 text-white shadow-sm' => request()->routeIs($patron),
                                    'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 lg:bg-transparent lg:ring-0' => ! request()->routeIs($patron),
                                ]) @if (request()->routeIs($patron)) aria-current="page" @endif>
                                    <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icono }}" /></svg>
                                    {{ $texto }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </aside>

            <section>
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $titulo }}</h1>
                        @if ($descripcion)
                            <p class="mt-1 text-slate-600">{{ $descripcion }}</p>
                        @endif
                    </div>
                    @isset($acciones)
                        <div class="flex flex-wrap gap-2">{{ $acciones }}</div>
                    @endisset
                </div>

                {{ $slot }}
            </section>
        </div>
    </div>
</x-layouts.app>
