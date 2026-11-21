{{-- Menú desplegable del usuario autenticado (escritorio). --}}
@props(['usuario'])

<div x-data="{ abierto: false }" class="relative" @click.outside="abierto = false">
    <button type="button" @click="abierto = ! abierto" :aria-expanded="abierto.toString()" aria-haspopup="true"
            class="flex items-center gap-2 rounded-full py-1 pr-3 pl-1 text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
        <span class="flex size-8 items-center justify-center rounded-full bg-marca-700 text-xs font-bold text-white">
            {{ mb_strtoupper(mb_substr($usuario->name, 0, 1)) }}
        </span>
        <span class="max-w-32 truncate">{{ \Illuminate\Support\Str::of($usuario->name)->explode(' ')->first() }}</span>
        <svg class="size-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
    </button>

    <div x-show="abierto" x-cloak x-transition
         class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl bg-white py-1 shadow-lg ring-1 ring-slate-200">
        <div class="border-b border-slate-100 px-4 py-2.5">
            <p class="truncate text-sm font-semibold text-slate-900">{{ $usuario->name }}</p>
            <p class="text-xs text-slate-500">{{ $usuario->role->getLabel() }}</p>
        </div>
        <x-enlaces-cuenta :usuario="$usuario" clase="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" />
    </div>
</div>
