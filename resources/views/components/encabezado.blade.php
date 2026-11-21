{{-- Encabezado de página con migas de pan, título, descripción y acciones opcionales. --}}
@props(['titulo', 'descripcion' => null, 'migas' => []])

<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if ($migas)
            <nav aria-label="Migas de pan" class="mb-3 text-sm text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('inicio') }}" class="hover:text-marca-700">Inicio</a></li>
                    @foreach ($migas as $texto => $url)
                        <li aria-hidden="true">/</li>
                        <li>
                            @if ($url)
                                <a href="{{ $url }}" class="hover:text-marca-700">{{ $texto }}</a>
                            @else
                                <span class="text-slate-700" aria-current="page">{{ $texto }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $titulo }}</h1>
                @if ($descripcion)
                    <p class="mt-2 max-w-3xl text-slate-600">{{ $descripcion }}</p>
                @endif
            </div>
            @isset($acciones)
                <div class="flex flex-wrap gap-2">{{ $acciones }}</div>
            @endisset
        </div>
    </div>
</section>
