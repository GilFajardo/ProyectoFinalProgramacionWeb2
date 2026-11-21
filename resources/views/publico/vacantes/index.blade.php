<x-layouts.app title="Bolsa de trabajo" description="Busca vacantes de empleo, prácticas y estadías por área de conocimiento y vigencia.">
    <x-encabezado titulo="Bolsa de trabajo"
                  descripcion="Busca entre las ofertas de empleo publicadas y filtra por área de conocimiento, vigencia, modalidad y tipo de contratación."
                  :migas="['Bolsa de trabajo' => null]" />

    <div x-data="{ filtrosAbiertos: false }" class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('vacantes.index') }}" class="lg:grid lg:grid-cols-[280px_1fr] lg:gap-8" role="search">
            {{-- Filtros --}}
            <aside>
                <button type="button" class="btn-secundario mb-4 w-full lg:hidden" @click="filtrosAbiertos = ! filtrosAbiertos" :aria-expanded="filtrosAbiertos.toString()">
                    <span x-text="filtrosAbiertos ? 'Ocultar filtros' : 'Mostrar filtros'">Mostrar filtros</span>
                </button>

                <div class="tarjeta space-y-5 p-5 lg:sticky lg:top-24 lg:block" :class="filtrosAbiertos ? 'block' : 'hidden'">
                    <div>
                        <label for="q" class="campo-label">Buscar</label>
                        <input id="q" name="q" type="search" maxlength="100" value="{{ $filtros['q'] ?? '' }}" placeholder="Puesto o empresa…" class="campo">
                    </div>

                    <fieldset>
                        <legend class="campo-label">Vigencia</legend>
                        <div class="mt-2 space-y-1.5">
                            @foreach (['vigente' => 'Vigentes', 'por_vencer' => 'Por vencer (7 días)', 'programada' => 'Próximas a publicarse', 'vencida' => 'Vencidas', 'todas' => 'Todas'] as $valor => $texto)
                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                    <input type="radio" name="vigencia" value="{{ $valor }}" @checked($filtros['vigencia'] === $valor) class="size-4 border-slate-300 text-marca-700 focus:ring-marca-600" onchange="this.form.requestSubmit()">
                                    {{ $texto }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div>
                        <label for="categoria" class="campo-label">Área de conocimiento</label>
                        <select id="categoria" name="categoria" class="campo" onchange="this.form.requestSubmit()">
                            <option value="">Todas las áreas</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->slug }}" @selected(($filtros['categoria'] ?? null) === $categoria->slug)>
                                    {{ $categoria->nombre }} ({{ $categoria->vacantes_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="modalidad" class="campo-label">Modalidad</label>
                        <select id="modalidad" name="modalidad" class="campo" onchange="this.form.requestSubmit()">
                            <option value="">Cualquiera</option>
                            @foreach (\App\Enums\Modalidad::opciones() as $valor => $texto)
                                <option value="{{ $valor }}" @selected(($filtros['modalidad'] ?? null) === $valor)>{{ $texto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="tipo" class="campo-label">Tipo de contratación</label>
                        <select id="tipo" name="tipo" class="campo" onchange="this.form.requestSubmit()">
                            <option value="">Cualquiera</option>
                            @foreach (\App\Enums\TipoContrato::opciones() as $valor => $texto)
                                <option value="{{ $valor }}" @selected(($filtros['tipo'] ?? null) === $valor)>{{ $texto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn-primario flex-1">Aplicar</button>
                        <a href="{{ route('vacantes.index') }}" class="btn-secundario">Limpiar</a>
                    </div>
                </div>
            </aside>

            {{-- Resultados --}}
            <section aria-live="polite">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">{{ $vacantes->total() }}</span>
                        {{ $vacantes->total() === 1 ? 'vacante encontrada' : 'vacantes encontradas' }}
                        @if (filled($filtros['q'] ?? null)) para «<span class="font-medium">{{ $filtros['q'] }}</span>» @endif
                    </p>
                    <div class="flex items-center gap-2">
                        <label for="orden" class="text-sm text-slate-600">Ordenar por</label>
                        <select id="orden" name="orden" class="campo w-auto py-1.5" onchange="this.form.requestSubmit()">
                            <option value="recientes" @selected(($filtros['orden'] ?? 'recientes') === 'recientes')>Más recientes</option>
                            <option value="cierre" @selected(($filtros['orden'] ?? null) === 'cierre')>Próximas a cerrar</option>
                            <option value="salario" @selected(($filtros['orden'] ?? null) === 'salario')>Mayor salario</option>
                        </select>
                    </div>
                </div>

                @if ($vacantes->isEmpty())
                    <x-vacio titulo="No encontramos vacantes con esos filtros" descripcion="Prueba con otra palabra clave, cambia el área de conocimiento o selecciona «Todas» en vigencia.">
                        <a href="{{ route('vacantes.index') }}" class="btn-secundario">Quitar filtros</a>
                    </x-vacio>
                @else
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($vacantes as $vacante)
                            <x-vacante-card :vacante="$vacante" />
                        @endforeach
                    </div>

                    <div class="mt-8">{{ $vacantes->links() }}</div>
                @endif
            </section>
        </form>
    </div>
</x-layouts.app>
