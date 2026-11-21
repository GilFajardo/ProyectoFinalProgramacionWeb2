{{-- Insignia del estatus de una postulación: EN PROCESO, ACEPTADO o RECHAZADO. --}}
@props(['estatus'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold tracking-wide ring-1 ring-inset '.$estatus->clasesBadge()]) }}>
    {{ $estatus->getLabel() }}
</span>
