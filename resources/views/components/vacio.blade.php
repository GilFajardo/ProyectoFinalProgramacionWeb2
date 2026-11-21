{{-- Estado vacío para listados sin resultados. --}}
@props(['titulo', 'descripcion' => null])

<div class="tarjeta flex flex-col items-center px-6 py-12 text-center">
    <div class="flex size-12 items-center justify-center rounded-full bg-marca-50 text-marca-700" aria-hidden="true">
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
    </div>
    <h3 class="mt-4 font-semibold text-slate-900">{{ $titulo }}</h3>
    @if ($descripcion)
        <p class="mt-1 max-w-md text-sm text-slate-500">{{ $descripcion }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
