<x-portal titulo="Mis vacantes" descripcion="Alta, consulta, modificación y baja de las ofertas de {{ $empresa->nombre }}.">
    <x-slot:acciones>
        <a href="{{ route('reclutador.reportes', array_filter(['tipo' => 'vacantes', 'vigencia' => $filtros['vigencia'] ?? null])) }}" target="_blank" class="btn-secundario">Reporte PDF</a>
        <a href="{{ route('reclutador.vacantes.create') }}" class="btn-primario">+ Publicar vacante</a>
    </x-slot:acciones>

    <form method="GET" class="tarjeta mb-4 flex flex-col gap-3 p-4 sm:flex-row" role="search">
        <label for="q" class="sr-only">Buscar por título</label>
        <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}" maxlength="100" placeholder="Buscar por título…" class="campo flex-1">
        <label for="vigencia" class="sr-only">Vigencia</label>
        <select id="vigencia" name="vigencia" class="campo sm:w-56" onchange="this.form.requestSubmit()">
            <option value="">Todas las vigencias</option>
            @foreach (\App\Models\Vacante::opcionesVigencia() as $valor => $texto)
                <option value="{{ $valor }}" @selected(($filtros['vigencia'] ?? null) === $valor)>{{ $texto }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secundario">Filtrar</button>
    </form>

    @if ($vacantes->isEmpty())
        <x-vacio titulo="No hay vacantes" descripcion="Publica tu primera vacante para empezar a recibir candidatos.">
            <a href="{{ route('reclutador.vacantes.create') }}" class="btn-primario">Publicar vacante</a>
        </x-vacio>
    @else
        <div class="tarjeta overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
                    <tr>
                        <th scope="col" class="px-4 py-3">Vacante</th>
                        <th scope="col" class="px-4 py-3">Vigencia</th>
                        <th scope="col" class="px-4 py-3 text-center">Candidatos</th>
                        <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($vacantes as $vacante)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('reclutador.vacantes.show', $vacante) }}" class="font-medium text-slate-900 hover:text-marca-700">{{ $vacante->titulo }}</a>
                                <p class="text-xs text-slate-500">{{ $vacante->categoria->nombre }} · {{ $vacante->tipo_contrato->getLabel() }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <x-badge-vigencia :vacante="$vacante" />
                                <p class="mt-1 text-xs text-slate-500">{{ $vacante->fecha_inicio->format('d/m/Y') }} – {{ $vacante->fecha_fin->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold text-slate-900">{{ $vacante->postulaciones_count }}</span>
                                @if ($vacante->en_proceso_count)
                                    <p class="text-xs text-amber-700">{{ $vacante->en_proceso_count }} por revisar</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('reclutador.vacantes.show', $vacante) }}" class="rounded-md px-2 py-1 text-marca-700 hover:bg-marca-50">Candidatos</a>
                                    <a href="{{ route('reclutador.vacantes.edit', $vacante) }}" class="rounded-md px-2 py-1 text-slate-600 hover:bg-slate-100">Editar</a>
                                    <form method="POST" action="{{ route('reclutador.vacantes.destroy', $vacante) }}" onsubmit="return confirm('¿Dar de baja esta vacante? Se conservará el historial de postulaciones.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-md px-2 py-1 text-red-600 hover:bg-red-50">Dar de baja</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $vacantes->links() }}</div>
    @endif
</x-portal>
