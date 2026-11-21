{{--
    CV dinámico del alumno. Cada sección se edita en línea (Alpine): "editando" guarda qué
    formulario está abierto. Tras un error de validación se reabre el formulario enviado (_form).
--}}
<x-portal titulo="Mi CV" descripcion="Tu currículum estructurado. Las empresas lo consultan cuando te postulas.">
    <x-slot:acciones>
        <a href="{{ route('alumno.perfil.edit') }}" class="btn-secundario">Editar datos generales</a>
        <a href="{{ route('alumno.cv.pdf') }}" class="btn-primario">Descargar CV en PDF</a>
    </x-slot:acciones>

    <div class="space-y-6">
        {{-- Datos generales --}}
        <section class="tarjeta p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                @if ($alumno->foto_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->foto_path) }}" alt="Fotografía de {{ $alumno->user->name }}" class="size-20 rounded-full object-cover ring-2 ring-marca-100">
                @else
                    <span class="flex size-20 items-center justify-center rounded-full bg-marca-100 text-2xl font-bold text-marca-700" aria-hidden="true">{{ mb_strtoupper(mb_substr($alumno->user->name, 0, 1)) }}</span>
                @endif
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-slate-900">{{ $alumno->user->name }}</h2>
                    <p class="text-slate-600">{{ $alumno->carrera }}@if ($alumno->cuatrimestre) · {{ $alumno->cuatrimestre }}.º cuatrimestre @endif</p>
                    <p class="mt-1 text-sm text-slate-500">Matrícula {{ $alumno->matricula }} · {{ $alumno->user->email }}@if ($alumno->telefono) · {{ $alumno->telefono }}@endif</p>
                    @if ($alumno->resumen)
                        <p class="mt-3 text-sm leading-relaxed text-slate-700">{{ $alumno->resumen }}</p>
                    @else
                        <p class="mt-3 rounded-lg bg-amber-50 p-3 text-sm text-amber-800">Agrega un perfil profesional breve desde <a href="{{ route('alumno.perfil.edit') }}" class="font-semibold underline">Mi perfil</a>.</p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Experiencia --}}
        <section id="experiencia" class="tarjeta scroll-mt-28 p-6"
                 x-data="{ editando: @js(str_starts_with((string) old('_form'), 'experiencia') ? old('_form') : null) }">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Experiencia laboral</h2>
                    <p class="text-sm text-slate-500">Empleos anteriores, prácticas profesionales y estadías.</p>
                </div>
                <button type="button" class="btn-secundario shrink-0" @click="editando = 'experiencia-nueva'" x-show="editando !== 'experiencia-nueva'">+ Agregar</button>
            </div>

            <div class="mt-4" x-show="editando === 'experiencia-nueva'" x-cloak>
                @include('alumno.cv.form-experiencia', ['experiencia' => null])
            </div>

            <ol class="mt-4 space-y-4">
                @forelse ($alumno->experiencias as $experiencia)
                    <li class="border-l-2 border-marca-200 pl-4">
                        <div x-show="editando !== 'experiencia-{{ $experiencia->id }}'" class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $experiencia->puesto }}</p>
                                <p class="text-sm text-slate-600">{{ $experiencia->empresa }}</p>
                                <p class="text-xs text-slate-500">{{ $experiencia->fecha_inicio->translatedFormat('M Y') }} – {{ $experiencia->actual ? 'Actual' : $experiencia->fecha_fin?->translatedFormat('M Y') }}</p>
                                @if ($experiencia->descripcion)<p class="mt-1 text-sm text-slate-700">{{ $experiencia->descripcion }}</p>@endif
                            </div>
                            <div class="flex shrink-0 gap-1">
                                <button type="button" class="rounded-md px-2 py-1 text-sm text-marca-700 hover:bg-marca-50" @click="editando = 'experiencia-{{ $experiencia->id }}'">Editar</button>
                                <form method="POST" action="{{ route('alumno.experiencias.destroy', $experiencia) }}" onsubmit="return confirm('¿Eliminar esta experiencia?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-md px-2 py-1 text-sm text-red-600 hover:bg-red-50">Eliminar</button>
                                </form>
                            </div>
                        </div>
                        <div x-show="editando === 'experiencia-{{ $experiencia->id }}'" x-cloak>
                            @include('alumno.cv.form-experiencia', ['experiencia' => $experiencia])
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500" x-show="editando !== 'experiencia-nueva'">Aún no registras experiencia. Incluye prácticas, estadías o servicio social.</li>
                @endforelse
            </ol>
        </section>

        {{-- Formación --}}
        <section id="formacion" class="tarjeta scroll-mt-28 p-6"
                 x-data="{ editando: @js(str_starts_with((string) old('_form'), 'formacion') ? old('_form') : null) }">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Formación académica</h2>
                    <p class="text-sm text-slate-500">Estudios, cursos, diplomados y certificaciones.</p>
                </div>
                <button type="button" class="btn-secundario shrink-0" @click="editando = 'formacion-nueva'" x-show="editando !== 'formacion-nueva'">+ Agregar</button>
            </div>

            <div class="mt-4" x-show="editando === 'formacion-nueva'" x-cloak>
                @include('alumno.cv.form-formacion', ['formacion' => null])
            </div>

            <ol class="mt-4 space-y-4">
                @forelse ($alumno->formaciones as $formacion)
                    <li class="border-l-2 border-marca-200 pl-4">
                        <div x-show="editando !== 'formacion-{{ $formacion->id }}'" class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $formacion->titulo }}</p>
                                <p class="text-sm text-slate-600">{{ $formacion->institucion }}</p>
                                <p class="text-xs text-slate-500">{{ $formacion->nivel->getLabel() }} · {{ $formacion->fecha_inicio?->translatedFormat('Y') }}{{ $formacion->en_curso ? ' – En curso' : ($formacion->fecha_fin ? ' – '.$formacion->fecha_fin->translatedFormat('Y') : '') }}</p>
                            </div>
                            <div class="flex shrink-0 gap-1">
                                <button type="button" class="rounded-md px-2 py-1 text-sm text-marca-700 hover:bg-marca-50" @click="editando = 'formacion-{{ $formacion->id }}'">Editar</button>
                                <form method="POST" action="{{ route('alumno.formaciones.destroy', $formacion) }}" onsubmit="return confirm('¿Eliminar esta formación?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-md px-2 py-1 text-sm text-red-600 hover:bg-red-50">Eliminar</button>
                                </form>
                            </div>
                        </div>
                        <div x-show="editando === 'formacion-{{ $formacion->id }}'" x-cloak>
                            @include('alumno.cv.form-formacion', ['formacion' => $formacion])
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500" x-show="editando !== 'formacion-nueva'">Agrega tu carrera en la UPSLP y tus cursos o certificaciones.</li>
                @endforelse
            </ol>
        </section>

        {{-- Habilidades por área --}}
        <section id="habilidades" class="tarjeta scroll-mt-28 p-6">
            <h2 class="text-lg font-semibold text-slate-900">Habilidades por área de conocimiento</h2>
            <p class="text-sm text-slate-500">Las empresas filtran candidatos por estas áreas. Agrega al menos 3 habilidades.</p>

            <form method="POST" action="{{ route('alumno.habilidades.store') }}" class="mt-4 grid gap-3 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-200 sm:grid-cols-[1fr_1fr_160px_auto] sm:items-end" data-validar>
                @csrf
                <x-campo name="nombre" label="Habilidad" required maxlength="100" placeholder="Ej. Laravel, AutoCAD, Inglés…" bolsa="habilidad" />
                <x-campo name="categoria_id" label="Área de conocimiento" tipo="select" :opciones="$categorias" vacio="General" bolsa="habilidad" />
                <x-campo name="nivel" label="Nivel" tipo="select" required :opciones="$nivelesHabilidad" value="intermedio" bolsa="habilidad" />
                <button type="submit" class="btn-primario">Agregar</button>
            </form>

            @if ($alumno->habilidades->isEmpty())
                <p class="mt-4 text-sm text-slate-500">Aún no registras habilidades.</p>
            @else
                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    @foreach ($alumno->habilidades->groupBy(fn ($h) => $h->categoria?->nombre ?? 'General')->sortKeys() as $area => $habilidades)
                        <div>
                            <h3 class="text-sm font-semibold text-marca-700">{{ $area }}</h3>
                            <ul class="mt-2 space-y-2">
                                @foreach ($habilidades as $habilidad)
                                    <li class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <div class="flex justify-between text-sm"><span class="font-medium text-slate-800">{{ $habilidad->nombre }}</span><span class="text-xs text-slate-500">{{ $habilidad->nivel->getLabel() }}</span></div>
                                            <div class="mt-1 h-1.5 rounded-full bg-slate-100"><div class="h-full rounded-full bg-marca-500" style="width: {{ $habilidad->nivel->porcentaje() }}%"></div></div>
                                        </div>
                                        <form method="POST" action="{{ route('alumno.habilidades.destroy', $habilidad) }}" onsubmit="return confirm('¿Eliminar esta habilidad?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rounded p-1 text-slate-400 hover:bg-red-50 hover:text-red-600" aria-label="Eliminar {{ $habilidad->nombre }}">✕</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Documentos --}}
        <section id="documentos" class="tarjeta scroll-mt-28 p-6">
            <h2 class="text-lg font-semibold text-slate-900">Documentos que avalan tu experiencia</h2>
            <p class="text-sm text-slate-500">Certificados, constancias, cartas de recomendación. PDF, JPG o PNG de hasta 5 MB. Son privados: solo los ven las empresas a las que te postulas.</p>

            <form method="POST" action="{{ route('alumno.documentos.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-3 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-200 sm:grid-cols-2 lg:grid-cols-[1fr_200px_1fr_auto] lg:items-end" data-validar>
                @csrf
                <x-campo name="nombre" label="Nombre del documento" required maxlength="150" placeholder="Ej. Certificado TOEFL" bolsa="documento" />
                <x-campo name="tipo" label="Tipo" tipo="select" required :opciones="$tiposDocumento" bolsa="documento" />
                <x-campo name="archivo" label="Archivo" type="file" required accept=".pdf,.jpg,.jpeg,.png" bolsa="documento" />
                <button type="submit" class="btn-primario">Subir</button>
            </form>

            @if ($alumno->documentos->isEmpty())
                <p class="mt-4 text-sm text-slate-500">Aún no subes documentos.</p>
            @else
                <ul class="mt-4 divide-y divide-slate-100">
                    @foreach ($alumno->documentos as $documento)
                        <li class="flex items-center justify-between gap-3 py-3">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-slate-900">{{ $documento->nombre }}</p>
                                <p class="text-xs text-slate-500">{{ $documento->tipo->getLabel() }} · {{ number_format($documento->tamano_kb) }} KB · {{ $documento->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="flex shrink-0 gap-1">
                                <a href="{{ route('alumno.documentos.show', $documento) }}" class="rounded-md px-2 py-1 text-sm text-marca-700 hover:bg-marca-50">Descargar</a>
                                <form method="POST" action="{{ route('alumno.documentos.destroy', $documento) }}" onsubmit="return confirm('¿Eliminar este documento?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-md px-2 py-1 text-sm text-red-600 hover:bg-red-50">Eliminar</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-portal>
