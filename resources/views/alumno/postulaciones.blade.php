<x-portal titulo="Mis postulaciones" descripcion="Consulta el estatus de cada vacante a la que te postulaste.">
    <div class="mb-4 flex flex-wrap gap-2" role="tablist" aria-label="Filtrar por estatus">
        <a href="{{ route('alumno.postulaciones.index') }}" @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-marca-700 text-white' => ! $estatusActual, 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' => $estatusActual])>Todas</a>
        @foreach (\App\Enums\EstatusPostulacion::cases() as $estatus)
            <a href="{{ route('alumno.postulaciones.index', ['estatus' => $estatus->value]) }}"
               @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-marca-700 text-white' => $estatusActual === $estatus->value, 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' => $estatusActual !== $estatus->value])>
                {{ $estatus->getLabel() }}
            </a>
        @endforeach
    </div>

    @if ($postulaciones->isEmpty())
        <x-vacio titulo="No hay postulaciones" descripcion="Cuando te postules a una vacante, aquí podrás ver su estatus.">
            <a href="{{ route('vacantes.index') }}" class="btn-primario">Buscar vacantes</a>
        </x-vacio>
    @else
        <div class="space-y-4">
            @foreach ($postulaciones as $postulacion)
                <article class="tarjeta p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <h2 class="font-semibold text-slate-900">
                                @if ($postulacion->vacante->trashed())
                                    {{ $postulacion->vacante->titulo }} <span class="text-xs font-normal text-slate-500">(vacante dada de baja)</span>
                                @else
                                    <a href="{{ route('vacantes.show', $postulacion->vacante) }}" class="hover:text-marca-700">{{ $postulacion->vacante->titulo }}</a>
                                @endif
                            </h2>
                            <p class="text-sm text-slate-600">{{ $postulacion->vacante->empresa?->nombre }} · {{ $postulacion->vacante->categoria?->nombre }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                Te postulaste el {{ $postulacion->created_at->translatedFormat('d \d\e F \d\e Y') }}
                                @if ($postulacion->estatus_actualizado_en) · Actualizado {{ $postulacion->estatus_actualizado_en->diffForHumans() }} @endif
                            </p>
                        </div>
                        <x-badge-estatus :estatus="$postulacion->estatus" class="self-start text-sm" />
                    </div>
                    @if ($postulacion->comentario_reclutador)
                        <div class="mt-4 rounded-lg bg-slate-50 p-3 text-sm text-slate-700">
                            <p class="text-xs font-semibold text-slate-500 uppercase">Comentario de la empresa</p>
                            <p class="mt-1">{{ $postulacion->comentario_reclutador }}</p>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $postulaciones->links() }}</div>
    @endif
</x-portal>
