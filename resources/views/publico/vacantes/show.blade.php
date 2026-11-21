<x-layouts.app :title="$vacante->titulo" :description="\Illuminate\Support\Str::limit($vacante->descripcion, 155)">
    <x-encabezado :titulo="$vacante->titulo" :migas="['Bolsa de trabajo' => route('vacantes.index'), $vacante->titulo => null]">
        <x-slot:acciones>
            <x-badge-vigencia :vacante="$vacante" class="text-sm" />
        </x-slot:acciones>
    </x-encabezado>

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[1fr_340px] lg:px-8">
        <article class="space-y-6">
            <div class="tarjeta p-6">
                <dl class="grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div><dt class="text-slate-500">Área</dt><dd class="font-medium text-slate-900">{{ $vacante->categoria->nombre }}</dd></div>
                    <div><dt class="text-slate-500">Contratación</dt><dd class="font-medium text-slate-900">{{ $vacante->tipo_contrato->getLabel() }}</dd></div>
                    <div><dt class="text-slate-500">Modalidad</dt><dd class="font-medium text-slate-900">{{ $vacante->modalidad->getLabel() }}</dd></div>
                    <div><dt class="text-slate-500">Plazas</dt><dd class="font-medium text-slate-900">{{ $vacante->plazas }}</dd></div>
                    <div><dt class="text-slate-500">Salario mensual</dt><dd class="font-medium text-slate-900">{{ $vacante->salario_formateado }}</dd></div>
                    <div><dt class="text-slate-500">Ubicación</dt><dd class="font-medium text-slate-900">{{ $vacante->ubicacion ?? 'No especificada' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Vigencia</dt><dd class="font-medium text-slate-900">Del {{ $vacante->fecha_inicio->translatedFormat('d \d\e F') }} al {{ $vacante->fecha_fin->translatedFormat('d \d\e F \d\e Y') }}</dd></div>
                </dl>
            </div>

            <section class="tarjeta p-6">
                <h2 class="text-lg font-semibold text-slate-900">Descripción del puesto</h2>
                {{-- Texto escapado con e() y saltos de línea convertidos: seguro ante XSS. --}}
                <div class="mt-3 leading-relaxed text-slate-700">{!! nl2br(e($vacante->descripcion)) !!}</div>
            </section>

            @if ($vacante->requisitos)
                <section class="tarjeta p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Requisitos</h2>
                    <div class="mt-3 leading-relaxed text-slate-700">{!! nl2br(e($vacante->requisitos)) !!}</div>
                </section>
            @endif

            @if ($vacante->prestaciones)
                <section class="tarjeta p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Prestaciones</h2>
                    <div class="mt-3 leading-relaxed text-slate-700">{!! nl2br(e($vacante->prestaciones)) !!}</div>
                </section>
            @endif
        </article>

        <aside class="space-y-6">
            {{-- Postulación --}}
            <div class="tarjeta p-6 lg:sticky lg:top-24">
                @if ($postulacion)
                    <h2 class="font-semibold text-slate-900">Ya te postulaste</h2>
                    <p class="mt-1 text-sm text-slate-600">El {{ $postulacion->created_at->translatedFormat('d \d\e F \d\e Y') }}.</p>
                    <div class="mt-4 flex items-center gap-2 text-sm">Estatus: <x-badge-estatus :estatus="$postulacion->estatus" /></div>
                    @if ($postulacion->comentario_reclutador)
                        <p class="mt-3 rounded-lg bg-slate-50 p-3 text-sm text-slate-700">«{{ $postulacion->comentario_reclutador }}»</p>
                    @endif
                    <button type="button" class="btn-secundario mt-5 w-full" disabled aria-disabled="true">✓ Postulación enviada</button>
                    <a href="{{ route('alumno.postulaciones.index') }}" class="enlace mt-3 block text-center text-sm">Ver mis postulaciones</a>
                @elseif (! $vacante->estaVigente())
                    <h2 class="font-semibold text-slate-900">Postulaciones cerradas</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $vacante->estado_vigencia === 'programada' ? 'Esta vacante abre el '.$vacante->fecha_inicio->translatedFormat('d \d\e F').'.' : 'Esta vacante ya no recibe postulaciones.' }}
                    </p>
                    <a href="{{ route('vacantes.index', ['categoria' => $vacante->categoria->slug]) }}" class="btn-secundario mt-5 w-full">Ver vacantes similares</a>
                @else
                    @auth
                        @can('postular', $vacante)
                            <h2 class="font-semibold text-slate-900">Postúlate a esta vacante</h2>
                            <form method="POST" action="{{ route('alumno.postulaciones.store', $vacante) }}" class="mt-4 space-y-4" data-validar x-data="contador(2000, @js(old('carta_presentacion', '')))">
                                @csrf
                                <div>
                                    <label for="carta_presentacion" class="campo-label">Carta de presentación <span class="font-normal text-slate-400">(opcional)</span></label>
                                    <textarea id="carta_presentacion" name="carta_presentacion" rows="5" maxlength="2000" x-model="texto" class="campo"
                                              placeholder="Cuéntale a la empresa por qué eres un buen candidato…">{{ old('carta_presentacion') }}</textarea>
                                    <p class="mt-1 text-right text-xs text-slate-400"><span x-text="restantes">2000</span> caracteres restantes</p>
                                </div>
                                <button type="submit" class="btn-primario w-full">Postularme</button>
                                <p class="text-xs text-slate-500">La empresa podrá consultar tu CV y documentos.</p>
                            </form>
                        @else
                            <h2 class="font-semibold text-slate-900">Postulación</h2>
                            <p class="mt-1 text-sm text-slate-600">Solo las cuentas de alumno pueden postularse a las vacantes.</p>
                        @endcan
                    @else
                        <h2 class="font-semibold text-slate-900">¿Te interesa esta vacante?</h2>
                        <p class="mt-1 text-sm text-slate-600">Inicia sesión con tu cuenta de alumno para postularte.</p>
                        <a href="{{ route('login') }}" class="btn-primario mt-5 w-full">Iniciar sesión para postularme</a>
                        <a href="{{ route('registro.alumno') }}" class="enlace mt-3 block text-center text-sm">Crear cuenta de alumno</a>
                    @endauth
                @endif
            </div>

            {{-- Empresa --}}
            <div class="tarjeta p-6">
                <h2 class="text-sm font-semibold tracking-wide text-slate-500 uppercase">Empresa</h2>
                <div class="mt-3 flex items-center gap-3">
                    @if ($vacante->empresa->logo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($vacante->empresa->logo_path) }}" alt="Logotipo de {{ $vacante->empresa->nombre }}" class="size-12 rounded-lg object-contain ring-1 ring-slate-200" loading="lazy">
                    @endif
                    <div>
                        <p class="font-semibold text-slate-900">{{ $vacante->empresa->nombre }}</p>
                        @if ($vacante->empresa->verificada)
                            <p class="text-xs font-medium text-emerald-700">✓ Empresa verificada</p>
                        @endif
                    </div>
                </div>
                @if ($vacante->empresa->descripcion)
                    <p class="mt-3 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($vacante->empresa->descripcion, 180) }}</p>
                @endif
                <a href="{{ route('empresas.show', $vacante->empresa) }}" class="enlace mt-4 inline-block text-sm">Ver perfil y vacantes →</a>
            </div>
        </aside>
    </div>

    @if ($similares->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-slate-900">Vacantes similares en {{ $vacante->categoria->nombre }}</h2>
            <div class="mt-5 grid gap-5 md:grid-cols-3">
                @foreach ($similares as $similar)
                    <x-vacante-card :vacante="$similar->setRelation('categoria', $vacante->categoria)" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
