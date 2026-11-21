<x-portal :titulo="$alumno->user->name" :descripcion="'Candidato(a) a «'.$postulacion->vacante->titulo.'»'">
    <x-slot:acciones>
        <a href="{{ route('reclutador.vacantes.show', $postulacion->vacante) }}" class="btn-secundario">← Volver a candidatos</a>
        <a href="{{ route('reclutador.candidatos.cv', $postulacion) }}" target="_blank" class="btn-primario">Imprimir CV (PDF)</a>
    </x-slot:acciones>

    <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
        <div class="space-y-6">
            <section class="tarjeta p-6">
                <div class="flex flex-col gap-4 sm:flex-row">
                    @if ($alumno->foto_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->foto_path) }}" alt="Fotografía de {{ $alumno->user->name }}" class="size-20 rounded-full object-cover">
                    @endif
                    <div>
                        <p class="text-slate-700">{{ $alumno->carrera }}@if ($alumno->cuatrimestre) · {{ $alumno->cuatrimestre }}.º cuatrimestre @endif</p>
                        <p class="mt-1 text-sm text-slate-500">
                            Matrícula {{ $alumno->matricula }} ·
                            <a href="mailto:{{ $alumno->user->email }}" class="enlace">{{ $alumno->user->email }}</a>
                            @if ($alumno->telefono) · {{ $alumno->telefono }} @endif
                            @if ($alumno->ciudad) · {{ $alumno->ciudad }} @endif
                        </p>
                        <div class="mt-2 flex flex-wrap gap-3 text-sm">
                            @if ($alumno->linkedin_url)<a href="{{ $alumno->linkedin_url }}" target="_blank" rel="noopener nofollow" class="enlace">LinkedIn ↗</a>@endif
                            @if ($alumno->portafolio_url)<a href="{{ $alumno->portafolio_url }}" target="_blank" rel="noopener nofollow" class="enlace">Portafolio ↗</a>@endif
                        </div>
                    </div>
                </div>
                @if ($alumno->resumen)
                    <p class="mt-4 leading-relaxed text-slate-700">{{ $alumno->resumen }}</p>
                @endif
            </section>

            @if ($postulacion->carta_presentacion)
                <section class="tarjeta p-6">
                    <h2 class="font-semibold text-slate-900">Carta de presentación</h2>
                    <p class="mt-2 leading-relaxed text-slate-700">{!! nl2br(e($postulacion->carta_presentacion)) !!}</p>
                </section>
            @endif

            <section class="tarjeta p-6">
                <h2 class="font-semibold text-slate-900">Experiencia</h2>
                @forelse ($alumno->experiencias as $experiencia)
                    <div class="mt-4 border-l-2 border-marca-200 pl-4">
                        <p class="font-medium text-slate-900">{{ $experiencia->puesto }} — {{ $experiencia->empresa }}</p>
                        <p class="text-xs text-slate-500">{{ $experiencia->fecha_inicio->translatedFormat('M Y') }} – {{ $experiencia->actual ? 'Actual' : $experiencia->fecha_fin?->translatedFormat('M Y') }}</p>
                        @if ($experiencia->descripcion)<p class="mt-1 text-sm text-slate-700">{{ $experiencia->descripcion }}</p>@endif
                    </div>
                @empty
                    <p class="mt-2 text-sm text-slate-500">Sin experiencia registrada.</p>
                @endforelse
            </section>

            <section class="tarjeta p-6">
                <h2 class="font-semibold text-slate-900">Formación</h2>
                @forelse ($alumno->formaciones as $formacion)
                    <div class="mt-4 border-l-2 border-marca-200 pl-4">
                        <p class="font-medium text-slate-900">{{ $formacion->titulo }}</p>
                        <p class="text-sm text-slate-600">{{ $formacion->institucion }} · {{ $formacion->nivel->getLabel() }}{{ $formacion->en_curso ? ' · En curso' : '' }}</p>
                    </div>
                @empty
                    <p class="mt-2 text-sm text-slate-500">Sin formación registrada.</p>
                @endforelse
            </section>

            <section class="tarjeta p-6">
                <h2 class="font-semibold text-slate-900">Habilidades por área</h2>
                @forelse ($alumno->habilidades->groupBy(fn ($h) => $h->categoria?->nombre ?? 'General') as $area => $habilidades)
                    <div class="mt-3">
                        <h3 class="text-sm font-semibold text-marca-700">{{ $area }}</h3>
                        <ul class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach ($habilidades as $habilidad)
                                <li class="rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-700">{{ $habilidad->nombre }} · <span class="text-slate-500">{{ $habilidad->nivel->getLabel() }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="mt-2 text-sm text-slate-500">Sin habilidades registradas.</p>
                @endforelse
            </section>

            <section class="tarjeta p-6">
                <h2 class="font-semibold text-slate-900">Documentos probatorios</h2>
                @forelse ($alumno->documentos as $documento)
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $documento->nombre }}</p>
                            <p class="text-xs text-slate-500">{{ $documento->tipo->getLabel() }} · {{ number_format($documento->tamano_kb) }} KB</p>
                        </div>
                        <a href="{{ route('reclutador.candidatos.documento', [$postulacion, $documento]) }}" class="btn-secundario py-1.5">Descargar</a>
                    </div>
                @empty
                    <p class="mt-2 text-sm text-slate-500">El candidato no ha subido documentos.</p>
                @endforelse
            </section>
        </div>

        {{-- Evaluación --}}
        <aside>
            <form method="POST" action="{{ route('reclutador.candidatos.update', $postulacion) }}" class="tarjeta space-y-4 p-6 xl:sticky xl:top-24" data-validar
                  x-data="{ estatus: @js(old('estatus', $postulacion->estatus->value)) }">
                @csrf
                @method('PATCH')
                <div>
                    <h2 class="font-semibold text-slate-900">Evaluación</h2>
                    <p class="text-sm text-slate-500">Postulado el {{ $postulacion->created_at->translatedFormat('d \d\e F \d\e Y') }}</p>
                </div>

                <fieldset>
                    <legend class="campo-label">Estatus de la postulación</legend>
                    <div class="mt-2 grid gap-2">
                        @foreach ($estatus as $opcion)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg p-3 ring-1 transition"
                                   :class="estatus === '{{ $opcion->value }}' ? 'ring-2 ring-marca-600 bg-marca-50' : 'ring-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="estatus" value="{{ $opcion->value }}" x-model="estatus" class="size-4 text-marca-700 focus:ring-marca-600">
                                <x-badge-estatus :estatus="$opcion" />
                            </label>
                        @endforeach
                    </div>
                    @error('estatus') <p class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p> @enderror
                </fieldset>

                <x-campo name="comentario_reclutador" label="Comentario para el candidato" tipo="textarea" rows="4" maxlength="1000"
                         :value="$postulacion->comentario_reclutador" placeholder="Opcional. El alumno verá este mensaje." />

                <button type="submit" class="btn-primario w-full">Guardar evaluación</button>

                @if ($postulacion->estatus_actualizado_en)
                    <p class="text-center text-xs text-slate-500">Último cambio: {{ $postulacion->estatus_actualizado_en->diffForHumans() }}</p>
                @endif
            </form>
        </aside>
    </div>
</x-portal>
