{{-- Insignia del estado de vigencia de una vacante. --}}
@props(['vacante'])

@php
    $estado = $vacante->estado_vigencia;
    $clases = match ($estado) {
        'vigente' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'por_vencer' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'programada' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'vencida' => 'bg-red-50 text-red-700 ring-red-600/20',
        default => 'bg-slate-100 text-slate-600 ring-slate-500/20',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset $clases"]) }}>
    <span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ \App\Models\Vacante::etiquetaVigencia($estado) }}
    @if ($estado === 'por_vencer')
        · cierra {{ $vacante->fecha_fin->isToday() ? 'hoy' : $vacante->fecha_fin->diffForHumans() }}
    @endif
</span>
