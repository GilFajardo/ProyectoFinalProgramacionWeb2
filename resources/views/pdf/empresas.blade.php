@extends('pdf.layout')

@section('contenido')
    <table class="resumen">
        <tr>
            <td><div class="valor">{{ $empresas->count() }}</div><div class="etiqueta">Empresas</div></td>
            <td><div class="valor">{{ $empresas->where('activa', true)->count() }}</div><div class="etiqueta">Activas</div></td>
            <td><div class="valor">{{ $empresas->sum('vacantes_count') }}</div><div class="etiqueta">Vacantes</div></td>
            <td><div class="valor">{{ $empresas->sum('vigentes_count') }}</div><div class="etiqueta">Vigentes</div></td>
            <td><div class="valor">{{ $empresas->sum('postulaciones_count') }}</div><div class="etiqueta">Postulaciones</div></td>
        </tr>
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th style="width: 20%">Empresa</th>
                <th>RFC</th>
                <th>Sector</th>
                <th style="width: 18%">Contacto</th>
                <th style="width: 20%">Reclutadores</th>
                <th class="derecha">Vacantes</th>
                <th class="derecha">Vigentes</th>
                <th class="derecha">Postul.</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($empresas as $empresa)
                <tr>
                    <td><strong>{{ $empresa->nombre }}</strong><br><span class="muted">{{ $empresa->municipio }}, {{ $empresa->estado }}</span></td>
                    <td>{{ $empresa->rfc }}</td>
                    <td>{{ $empresa->sector }}</td>
                    <td>{{ $empresa->email_contacto }}<br><span class="muted">{{ $empresa->telefono }}</span></td>
                    <td>
                        @forelse ($empresa->reclutadores as $reclutador)
                            {{ $reclutador->user?->name }}@if ($reclutador->puesto) <span class="muted">({{ $reclutador->puesto }})</span>@endif<br>
                        @empty
                            <span class="muted">Sin reclutadores</span>
                        @endforelse
                    </td>
                    <td class="derecha">{{ $empresa->vacantes_count }}</td>
                    <td class="derecha">{{ $empresa->vigentes_count }}</td>
                    <td class="derecha">{{ $empresa->postulaciones_count }}</td>
                    <td><span class="badge {{ $empresa->activa ? 'b-vigente' : 'b-inactiva' }}">{{ $empresa->activa ? 'ACTIVA' : 'INACTIVA' }}</span></td>
                </tr>
            @empty
                <tr><td colspan="9" class="vacio">No hay empresas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
