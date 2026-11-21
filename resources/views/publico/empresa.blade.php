<x-layouts.app :title="$empresa->nombre">
    <x-encabezado :titulo="$empresa->nombre" :descripcion="$empresa->sector" :migas="['Bolsa de trabajo' => route('vacantes.index'), $empresa->nombre => null]" />

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[320px_1fr] lg:px-8">
        <aside class="tarjeta h-fit space-y-4 p-6">
            @if ($empresa->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($empresa->logo_path) }}" alt="Logotipo de {{ $empresa->nombre }}" class="h-20 w-auto rounded-lg object-contain">
            @endif
            @if ($empresa->verificada)
                <p class="text-sm font-medium text-emerald-700">✓ Empresa verificada por Vinculación</p>
            @endif
            @if ($empresa->descripcion)
                <p class="text-sm leading-relaxed text-slate-600">{!! nl2br(e($empresa->descripcion)) !!}</p>
            @endif
            <dl class="space-y-2 text-sm">
                <div><dt class="text-slate-500">Ubicación</dt><dd class="text-slate-900">{{ $empresa->municipio }}, {{ $empresa->estado }}</dd></div>
                @if ($empresa->sitio_web)
                    <div><dt class="text-slate-500">Sitio web</dt><dd><a href="{{ $empresa->sitio_web }}" class="enlace break-all" target="_blank" rel="noopener nofollow">{{ $empresa->sitio_web }}</a></dd></div>
                @endif
            </dl>
        </aside>

        <section>
            <h2 class="text-xl font-bold text-slate-900">Vacantes vigentes ({{ $vacantes->count() }})</h2>
            @if ($vacantes->isEmpty())
                <div class="mt-5"><x-vacio titulo="Sin vacantes vigentes" descripcion="Esta empresa no tiene vacantes abiertas en este momento." /></div>
            @else
                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    @foreach ($vacantes as $vacante)
                        <x-vacante-card :vacante="$vacante->setRelation('empresa', $empresa)" />
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
