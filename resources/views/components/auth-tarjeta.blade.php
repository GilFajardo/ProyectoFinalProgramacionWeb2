{{-- Contenedor centrado para los formularios de acceso y registro. --}}
@props(['titulo', 'descripcion' => null, 'ancho' => 'max-w-md'])

<x-layouts.app :title="$titulo">
    <div class="mx-auto {{ $ancho }} px-4 py-12 sm:py-16">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $titulo }}</h1>
            @if ($descripcion)
                <p class="mt-2 text-slate-600">{{ $descripcion }}</p>
            @endif
        </div>
        <div class="tarjeta p-6 sm:p-8">
            {{ $slot }}
        </div>
        @isset($pie)
            <div class="mt-6 text-center text-sm text-slate-600">{{ $pie }}</div>
        @endisset
    </div>
</x-layouts.app>
