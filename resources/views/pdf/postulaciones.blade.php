@extends('pdf.layout')

@section('contenido')
    <table class="resumen">
        <tr>
            <td><div class="valor">{{ $postulaciones->count() }}</div><div class="etiqueta">Postulaciones</div></td>
            @foreach (\App\Enums\EstatusPostulacion::cases() as $estatus)
                <td><div class="valor">{{ $porEstatus[$estatus->getLabel()] ?? 0 }}</div><div class="etiqueta">{{ $estatus->getLabel() }}</div></td>
            @endforeach
        </tr>
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th style="width: 18%">Alumno</th>
                <th>Matrícula</th>
                <th style="width: 18%">Carrera</th>
                <th style="width: 18%">Vacante</th>
                <th style="width: 14%">Empresa</th>
                <th>Fecha</th>
                <th>Estatus</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($postulaciones as $postulacion)
                <tr>
                    <td><strong>{{ $postulacion->alumno?->user?->name }}</strong><br><span class="muted">{{ $postulacion->alumno?->user?->email }}</span></td>
                    <td>{{ $postulacion->alumno?->matricula }}</td>
                    <td>{{ $postulacion->alumno?->carrera }}</td>
                    <td>{{ $postulacion->vacante?->titulo }}</td>
                    <td>{{ $postulacion->vacante?->empresa?->nombre }}</td>
                    <td>{{ $postulacion->created_at->format('d/m/Y') }}</td>
                    <td><span class="badge b-{{ $postulacion->estatus->value }}">{{ $postulacion->estatus->getLabel() }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" class="vacio">No hay postulaciones que coincidan con los filtros.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
