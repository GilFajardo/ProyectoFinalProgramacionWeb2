{{--
    Espacio de marca de Vinculación en Línea. Es un isotipo genérico (no el escudo oficial):
    para usar el logotipo institucional, reemplaza este SVG por <img src="{{ asset('images/logo-upslp.png') }}">.
--}}
@props(['claro' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg viewBox="0 0 40 40" class="size-9 shrink-0" aria-hidden="true">
        <rect width="40" height="40" rx="10" class="{{ $claro ? 'fill-white' : 'fill-marca-700' }}" />
        <path d="M11 13l6 14 3-7" fill="none" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" class="{{ $claro ? 'stroke-marca-700' : 'stroke-white' }}" />
        <path d="M22 13v14h7" fill="none" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" class="stroke-acento-500" />
    </svg>
    <span class="leading-tight">
        <span class="block text-[15px] font-bold tracking-tight {{ $claro ? 'text-white' : 'text-marca-800' }}">Vinculación en Línea</span>
        <span class="block text-[11px] font-medium uppercase tracking-wider {{ $claro ? 'text-marca-200' : 'text-slate-500' }}">UPSLP · Bolsa de trabajo</span>
    </span>
</span>
