<x-portal :titulo="$vacante->titulo" :descripcion="$vacante->categoria->nombre.' · '.$vacante->tipo_contrato->getLabel().' · '.$vacante->modalidad->getLabel()">
    <x-slot:acciones>
        @if ($vacante->activa)
            <a href="{{ route('vacantes.show', $vacante) }}" target="_blank" class="btn-secundario">Ver publicación</a>
        @endif
        <a href="{{ route('reclutador.reportes', ['tipo' => 'postulaciones', 'vacante_id' => $vacante->id]) }}" target="_blank" class="btn-secundario">Reporte PDF</a>
        <a href="{{ route('reclutador.vacantes.edit', $vacante) }}" class="btn-primario">Editar</a>
    </x-slot:acciones>

    <div class="mb-6 flex flex-wrap items-center gap-3 text-sm text-slate-600">
        <x-badge-vigencia :vacante="$vacante" />
        <span>Vigencia: {{ $vacante->fecha_inicio->format('d/m/Y') }} al {{ $vacante->fecha_fin->format('d/m/Y') }}</span>
        <span>· {{ $vacante->plazas }} {{ $vacante->plazas === 1 ? 'plaza' : 'plazas' }}</span>
        <span>· {{ $vacante->salario_formateado }}</span>
    </div>

    <h2 class="text-lg font-semibold text-slate-900">Candidatos postulados ({{ $vacante->postulaciones_count }})</h2>

    <div class="mt-3 mb-4 flex flex-wrap gap-2">
        <a href="{{ route('reclutador.vacantes.show', $vacante) }}" @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-marca-700 text-white' => ! $estatusActual, 'bg-white text-slate-600 ring-1 ring-slate-200' => $estatusActual])>Todos</a>
        @foreach (\App\Enums\EstatusPostulacion::cases() as $estatus)
            <a href="{{ route('reclutador.vacantes.show', [$vacante, 'estatus' => $estatus->value]) }}"
               @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-marca-700 text-white' => $estatusActual === $estatus->value, 'bg-white text-slate-600 ring-1 ring-slate-200' => $estatusActual !== $estatus->value])>
                {{ $estatus->getLabel() }} ({{ $conteo[$estatus->value] ?? 0 }})
            </a>
        @endforeach
    </div>

    @if ($candidatos->isEmpty())
        <x-vacio titulo="Sin candidatos" descripcion="Aún no hay postulaciones con este estatus." />
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($candidatos as $postulacion)
                <article class="tarjeta flex flex-col p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="truncate font-semibold text-slate-900">{{ $postulacion->alumno->user->name }}</h3>
                            <p class="truncate text-sm text-slate-600">{{ $postulacion->alumno->carrera }}</p>
                            <p class="text-xs text-slate-500">Postulado {{ $postulacion->created_at->diffForHumans() }}</p>
                        </div>
                        <x-badge-estatus :estatus="$postulacion->estatus" />
                    </div>
                    @if ($postulacion->alumno->habilidades->isNotEmpty())
                        <ul class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($postulacion->alumno->habilidades->take(5) as $habilidad)
                                <li class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $habilidad->nombre }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="mt-auto flex gap-2 pt-4">
                        <a href="{{ route('reclutador.candidatos.show', $postulacion) }}" class="btn-primario flex-1">Revisar perfil</a>
                        <a href="{{ route('reclutador.candidatos.cv', $postulacion) }}" target="_blank" class="btn-secundario">CV PDF</a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $candidatos->links() }}</div>
    @endif
</x-portal>
