<x-portal titulo="Hola, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}" descripcion="Este es el resumen de tu actividad en la bolsa de trabajo.">
    <x-slot:acciones>
        <a href="{{ route('vacantes.index') }}" class="btn-primario">Buscar vacantes</a>
    </x-slot:acciones>

    <div class="space-y-6">
        {{-- Avance del CV --}}
        <div class="tarjeta p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Avance de tu CV: {{ $avanceCv }}%</h2>
                    <p class="text-sm text-slate-600">
                        {{ $avanceCv === 100 ? '¡Excelente! Tu CV está completo.' : 'Un CV completo aumenta tus posibilidades de ser seleccionado(a).' }}
                    </p>
                </div>
                <a href="{{ route('alumno.cv') }}" class="btn-secundario shrink-0">{{ $avanceCv === 100 ? 'Ver mi CV' : 'Completar mi CV' }}</a>
            </div>
            <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-valuenow="{{ $avanceCv }}" aria-valuemin="0" aria-valuemax="100" aria-label="Avance del CV">
                <div class="h-full rounded-full bg-marca-600 transition-all" style="width: {{ $avanceCv }}%"></div>
            </div>
            <ul class="mt-4 grid gap-2 text-sm sm:grid-cols-5">
                @foreach ([
                    ['Perfil', filled($alumno->resumen) && filled($alumno->telefono)],
                    ['Experiencia', $alumno->experiencias_count > 0],
                    ['Formación', $alumno->formaciones_count > 0],
                    ['3+ habilidades', $alumno->habilidades_count >= 3],
                    ['Documentos', $alumno->documentos_count > 0],
                ] as [$seccion, $completa])
                    <li class="flex items-center gap-1.5 {{ $completa ? 'text-emerald-700' : 'text-slate-400' }}">
                        <span aria-hidden="true">{{ $completa ? '✓' : '○' }}</span> {{ $seccion }}
                        <span class="sr-only">{{ $completa ? 'completo' : 'pendiente' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Conteo por estatus --}}
        <div class="grid gap-4 sm:grid-cols-3">
            @foreach (\App\Enums\EstatusPostulacion::cases() as $estatus)
                <a href="{{ route('alumno.postulaciones.index', ['estatus' => $estatus->value]) }}" class="tarjeta p-5 transition hover:ring-marca-300">
                    <x-badge-estatus :estatus="$estatus" />
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $conteo[$estatus->value] }}</p>
                    <p class="text-sm text-slate-500">postulaciones</p>
                </a>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            {{-- Postulaciones recientes --}}
            <section class="tarjeta p-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-slate-900">Postulaciones recientes</h2>
                    <a href="{{ route('alumno.postulaciones.index') }}" class="enlace text-sm">Ver todas</a>
                </div>
                @forelse ($recientes as $postulacion)
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-0">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-900">{{ $postulacion->vacante->titulo }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $postulacion->vacante->empresa?->nombre }} · {{ $postulacion->created_at->diffForHumans() }}</p>
                        </div>
                        <x-badge-estatus :estatus="$postulacion->estatus" />
                    </div>
                @empty
                    <p class="mt-4 text-sm text-slate-500">Aún no te has postulado a ninguna vacante.</p>
                @endforelse
            </section>

            {{-- Sugerencias --}}
            <section class="tarjeta p-6">
                <h2 class="font-semibold text-slate-900">Vacantes sugeridas para ti</h2>
                <p class="text-xs text-slate-500">Según las áreas de tus habilidades.</p>
                @forelse ($sugeridas as $vacante)
                    <a href="{{ route('vacantes.show', $vacante) }}" class="block border-b border-slate-100 py-3 last:border-0 hover:bg-slate-50">
                        <p class="text-sm font-medium text-slate-900">{{ $vacante->titulo }}</p>
                        <p class="text-xs text-slate-500">{{ $vacante->empresa->nombre }} · {{ $vacante->categoria->nombre }} · hasta {{ $vacante->fecha_fin->translatedFormat('d M') }}</p>
                    </a>
                @empty
                    <p class="mt-4 text-sm text-slate-500">Agrega habilidades a tu CV para recibir sugerencias.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-portal>
