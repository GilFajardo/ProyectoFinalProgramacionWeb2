{{-- Mensajes de sesión: exito, error y aviso. Se pueden cerrar y desaparecen solos. --}}
@php
    $mensajes = array_filter([
        'exito' => session('exito'),
        'error' => session('error'),
        'aviso' => session('aviso'),
    ]);
    $estilos = [
        'exito' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        'error' => 'bg-red-50 text-red-800 ring-red-200',
        'aviso' => 'bg-amber-50 text-amber-800 ring-amber-200',
    ];
@endphp

@if ($mensajes)
    <div class="mx-auto max-w-7xl space-y-2 px-4 pt-6 sm:px-6 lg:px-8">
        @foreach ($mensajes as $tipo => $mensaje)
            <div x-data="{ visible: true }" x-show="visible" x-init="setTimeout(() => visible = false, 8000)" x-transition.opacity
                 role="{{ $tipo === 'error' ? 'alert' : 'status' }}"
                 class="flex items-start justify-between gap-4 rounded-xl px-4 py-3 text-sm ring-1 {{ $estilos[$tipo] }}">
                <p>{{ $mensaje }}</p>
                <button type="button" @click="visible = false" class="shrink-0 opacity-60 hover:opacity-100" aria-label="Cerrar mensaje">✕</button>
            </div>
        @endforeach
    </div>
@endif
