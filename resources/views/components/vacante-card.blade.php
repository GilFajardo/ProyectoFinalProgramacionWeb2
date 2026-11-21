{{-- Tarjeta de vacante para listados (requiere empresa y categoria cargadas). --}}
@props(['vacante'])

<article class="tarjeta group relative flex flex-col p-5 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-marca-200">
    <div class="flex items-start justify-between gap-3">
        <span class="inline-flex rounded-md bg-marca-50 px-2 py-1 text-xs font-medium text-marca-700">{{ $vacante->categoria?->nombre }}</span>
        <x-badge-vigencia :vacante="$vacante" />
    </div>

    <h3 class="mt-3 text-lg font-semibold leading-snug text-slate-900">
        <a href="{{ route('vacantes.show', $vacante) }}" class="after:absolute after:inset-0 focus:outline-none group-hover:text-marca-700">
            {{ $vacante->titulo }}
        </a>
    </h3>
    <p class="mt-1 text-sm text-slate-600">{{ $vacante->empresa?->nombre }}</p>

    <dl class="mt-4 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
        <div class="flex items-center gap-1"><dt class="sr-only">Modalidad</dt><dd>📍 {{ $vacante->modalidad->getLabel() }}@if ($vacante->ubicacion) · {{ $vacante->ubicacion }}@endif</dd></div>
        <div class="flex items-center gap-1"><dt class="sr-only">Contratación</dt><dd>🗂️ {{ $vacante->tipo_contrato->getLabel() }}</dd></div>
    </dl>

    <div class="mt-auto flex items-end justify-between gap-2 pt-5">
        <p class="text-sm font-semibold text-slate-900">{{ $vacante->salario_formateado }}</p>
        <p class="text-xs text-slate-500">Hasta {{ $vacante->fecha_fin->translatedFormat('d M Y') }}</p>
    </div>
</article>
