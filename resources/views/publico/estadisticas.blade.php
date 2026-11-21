<x-layouts.app title="Estadísticas y seguimiento">
    <x-encabezado titulo="Estadísticas y seguimiento"
                  descripcion="Indicadores agregados de la bolsa de trabajo, actualizados en tiempo real. No incluyen datos personales."
                  :migas="['Estadísticas' => null]" />

    <div class="mx-auto max-w-7xl space-y-8 px-4 py-10 sm:px-6 lg:px-8">
        <dl class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['Vacantes vigentes', number_format($resumen['vacantes_vigentes']), 'de '.number_format($resumen['vacantes_total']).' publicadas'],
                ['Empresas activas', number_format($resumen['empresas']), 'vinculadas con la UPSLP'],
                ['Postulaciones', number_format($resumen['postulaciones']), 'realizadas por alumnos'],
                ['Tasa de aceptación', $resumen['tasa_aceptacion'].'%', number_format($resumen['aceptadas']).' postulaciones aceptadas'],
            ] as [$etiqueta, $valor, $detalle])
                <div class="tarjeta p-5">
                    <dt class="text-sm text-slate-500">{{ $etiqueta }}</dt>
                    <dd class="mt-1 text-3xl font-bold text-marca-700">{{ $valor }}</dd>
                    <dd class="mt-1 text-xs text-slate-500">{{ $detalle }}</dd>
                </div>
            @endforeach
        </dl>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="tarjeta p-6 lg:col-span-2">
                <h2 class="font-semibold text-slate-900">Vacantes vigentes por área de conocimiento</h2>
                <div class="mt-4 h-96">
                    <canvas data-grafica="barras" data-titulo="Vacantes vigentes"
                            data-etiquetas="{{ json_encode(array_keys($porCategoria)) }}"
                            data-valores="{{ json_encode(array_values($porCategoria)) }}"
                            role="img" aria-label="Gráfica de barras de vacantes vigentes por área de conocimiento"></canvas>
                </div>
            </section>

            <section class="tarjeta p-6">
                <h2 class="font-semibold text-slate-900">Postulaciones por estatus</h2>
                <div class="mt-4 h-72">
                    <canvas data-grafica="dona"
                            data-etiquetas="{{ json_encode(array_keys($porEstatus)) }}"
                            data-valores="{{ json_encode(array_values($porEstatus)) }}"
                            role="img" aria-label="Gráfica de dona de postulaciones por estatus"></canvas>
                </div>
            </section>
        </div>

        <section class="tarjeta p-6">
            <h2 class="font-semibold text-slate-900">Seguimiento: postulaciones de los últimos 6 meses</h2>
            <div class="mt-4 h-72">
                <canvas data-grafica="linea" data-titulo="Postulaciones"
                        data-etiquetas="{{ json_encode(array_keys($porMes)) }}"
                        data-valores="{{ json_encode(array_values($porMes)) }}"
                        role="img" aria-label="Gráfica de línea de postulaciones por mes"></canvas>
            </div>
        </section>

        {{-- Tabla equivalente para lectores de pantalla y sin JavaScript --}}
        <details class="tarjeta p-6">
            <summary class="cursor-pointer font-medium text-slate-900">Ver los datos en tabla</summary>
            <div class="mt-4 grid gap-6 md:grid-cols-2">
                <table class="w-full text-sm">
                    <caption class="mb-2 text-left font-semibold">Vacantes vigentes por área</caption>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($porCategoria as $categoria => $total)
                            <tr><th scope="row" class="py-1.5 text-left font-normal text-slate-600">{{ $categoria }}</th><td class="py-1.5 text-right font-medium">{{ $total }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="w-full text-sm">
                    <caption class="mb-2 text-left font-semibold">Postulaciones por estatus y por mes</caption>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($porEstatus + $porMes as $etiqueta => $total)
                            <tr><th scope="row" class="py-1.5 text-left font-normal text-slate-600">{{ $etiqueta }}</th><td class="py-1.5 text-right font-medium">{{ $total }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    </div>
</x-layouts.app>
