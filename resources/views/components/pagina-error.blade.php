{{-- Plantilla de las páginas de error con la identidad del sitio. --}}
@props(['codigo', 'titulo', 'mensaje'])

<x-layouts.app :title="$titulo">
    <div class="mx-auto flex max-w-xl flex-col items-center px-4 py-20 text-center">
        <p class="text-6xl font-bold text-marca-700">{{ $codigo }}</p>
        <h1 class="mt-4 text-2xl font-bold text-slate-900">{{ $titulo }}</h1>
        <p class="mt-2 text-slate-600">{{ $mensaje }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('inicio') }}" class="btn-primario">Ir al inicio</a>
            <a href="{{ route('vacantes.index') }}" class="btn-secundario">Ver vacantes</a>
        </div>
    </div>
</x-layouts.app>
