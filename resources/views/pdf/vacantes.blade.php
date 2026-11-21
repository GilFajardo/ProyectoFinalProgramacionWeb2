@extends('pdf.layout')

@section('contenido')
    @php($etiquetasVigencia = \App\Models\Vacante::opcionesVigencia() + ['inactiva' => 'Inactivas'])

    <table class="resumen">
        <tr>
            <td><div class="valor">{{ $vacantes->count() }}</div><div class="etiqueta">Vacantes</div></td>
            @foreach (['vigente', 'por_vencer', 'programada', 'vencida', 'inactiva'] as $estado)
                <td><div class="valor">{{ $porEstado[$estado] ?? 0 }}</div><div class="etiqueta">{{ $etiquetasVigencia[$estado] }}</div></td>
            @endforeach
            <td><div class="valor">{{ $vacantes->sum('postulaciones_count') }}</div><div class="etiqueta">Postulaciones</div></td>
        </tr>
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th style="width: 20%">Vacante</th>
                <th style="width: 16%">Empresa</th>
                <th style="width: 13%">Categoría</th>
                <th>Contrato / modalidad</th>
                <th>Salario</th>
                <th>Vigencia</th>
                <th>Estado</th>
                <th class="derecha">Plazas</th>
                <th class="derecha">Postul.</th>
                <th class="derecha">Acept.</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vacantes as $vacante)
                <tr>
                    <td><strong>{{ $vacante->titulo }}</strong></td>
                    <td>{{ $vacante->empresa?->nombre }}</td>
                    <td>{{ $vacante->categoria?->nombre }}</td>
                    <td>{{ $vacante->tipo_contrato->getLabel() }}<br><span class="muted">{{ $vacante->modalidad->getLabel() }}</span></td>
                    <td>{{ $vacante->salario_formateado }}</td>
                    <td>{{ $vacante->fecha_inicio->format('d/m/Y') }}<br><span class="muted">al {{ $vacante->fecha_fin->format('d/m/Y') }}</span></td>
                    <td><span class="badge b-{{ $vacante->estado_vigencia }}">{{ mb_strtoupper(\App\Models\Vacante::etiquetaVigencia($vacante->estado_vigencia)) }}</span></td>
                    <td class="derecha">{{ $vacante->plazas }}</td>
                    <td class="derecha">{{ $vacante->postulaciones_count }}</td>
                    <td class="derecha">{{ $vacante->aceptadas_count }}</td>
                </tr>
            @empty
                <tr><td colspan="10" class="vacio">No hay vacantes que coincidan con los filtros.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
