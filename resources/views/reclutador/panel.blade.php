<x-portal titulo="Panel de {{ $empresa->nombre }}" descripcion="Resumen de tus vacantes y candidatos.">
    <x-slot:acciones>
        <a href="{{ route('reclutador.vacantes.create') }}" class="btn-primario">+ Publicar vacante</a>
    </x-slot:acciones>

    <div class="space-y-6">
        <dl class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['Vacantes vigentes', $resumen['vacantes_vigentes']],
                ['Vacantes publicadas', $resumen['vacantes_total']],
                ['Postulaciones recibidas', $resumen['postulaciones']],
                ['Candidatos aceptados', $resumen['aceptadas']],
            ] as [$etiqueta, $valor])
                <div class="tarjeta p-5">
                    <dt class="text-sm text-slate-500">{{ $etiqueta }}</dt>
                    <dd class="mt-1 text-3xl font-bold text-marca-700">{{ $valor }}</dd>
                </div>
            @endforeach
        </dl>

        <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
            <section class="tarjeta p-6">
                <h2 class="font-semibold text-slate-900">Candidatos por revisar (EN PROCESO)</h2>
                @forelse ($pendientes as $postulacion)
                    <a href="{{ route('reclutador.candidatos.show', $postulacion) }}" class="flex items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-0 hover:bg-slate-50">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-900">{{ $postulacion->alumno->user->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $postulacion->vacante->titulo }} · {{ $postulacion->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-sm text-marca-700">Revisar →</span>
                    </a>
                @empty
                    <p class="mt-4 text-sm text-slate-500">No tienes candidatos pendientes de revisar.</p>
                @endforelse
            </section>

            <div class="space-y-6">
                <section class="tarjeta p-6">
                    <h2 class="font-semibold text-slate-900">Postulaciones por estatus</h2>
                    <ul class="mt-4 space-y-3">
                        @foreach (\App\Enums\EstatusPostulacion::cases() as $estatus)
                            @php($total = $porEstatus[$estatus->getLabel()] ?? 0)
                            <li>
                                <div class="flex justify-between text-sm"><x-badge-estatus :estatus="$estatus" /><span class="font-semibold">{{ $total }}</span></div>
                                <div class="mt-1.5 h-1.5 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-marca-500" style="width: {{ $resumen['postulaciones'] ? round($total / $resumen['postulaciones'] * 100) : 0 }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <section class="tarjeta p-6">
                    <h2 class="font-semibold text-slate-900">Por vencer esta semana</h2>
                    @forelse ($porVencer as $vacante)
                        <a href="{{ route('reclutador.vacantes.show', $vacante) }}" class="block border-b border-slate-100 py-2.5 last:border-0">
                            <p class="text-sm font-medium text-slate-900">{{ $vacante->titulo }}</p>
                            <p class="text-xs text-amber-700">Cierra {{ $vacante->fecha_fin->translatedFormat('d M') }} · {{ $vacante->postulaciones_count }} postulaciones</p>
                        </a>
                    @empty
                        <p class="mt-3 text-sm text-slate-500">Ninguna vacante vence en los próximos 7 días.</p>
                    @endforelse
                </section>

                <section class="tarjeta p-6">
                    <h2 class="font-semibold text-slate-900">Reportes PDF</h2>
                    <div class="mt-3 grid gap-2">
                        <a href="{{ route('reclutador.reportes', 'vacantes') }}" target="_blank" class="btn-secundario justify-start">📄 Mis vacantes</a>
                        <a href="{{ route('reclutador.reportes', 'postulaciones') }}" target="_blank" class="btn-secundario justify-start">📄 Postulaciones</a>
                        <a href="{{ route('reclutador.reportes', 'empresas') }}" target="_blank" class="btn-secundario justify-start">📄 Ficha de mi empresa</a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-portal>
