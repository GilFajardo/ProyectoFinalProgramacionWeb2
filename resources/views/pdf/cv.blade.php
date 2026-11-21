@extends('pdf.layout')

@section('contenido')
    <table style="width: 100%; margin-bottom: 6px;">
        <tr>
            <td>
                <div style="font-size: 16px; font-weight: bold;">{{ $alumno->user->name }}</div>
                <div>{{ $alumno->carrera }}@if ($alumno->cuatrimestre) · {{ $alumno->cuatrimestre }}.º cuatrimestre @endif</div>
                <div class="muted">Matrícula {{ $alumno->matricula }}</div>
            </td>
            <td class="derecha">
                {{ $alumno->user->email }}<br>
                @if ($alumno->telefono) {{ $alumno->telefono }}<br> @endif
                @if ($alumno->ciudad) {{ $alumno->ciudad }}<br> @endif
                @if ($alumno->linkedin_url) {{ $alumno->linkedin_url }}<br> @endif
                @if ($alumno->portafolio_url) {{ $alumno->portafolio_url }} @endif
            </td>
        </tr>
    </table>

    @if ($alumno->resumen)
        <h2>Perfil profesional</h2>
        <p>{{ $alumno->resumen }}</p>
    @endif

    <h2>Experiencia</h2>
    @forelse ($alumno->experiencias as $experiencia)
        <p>
            <strong>{{ $experiencia->puesto }}</strong> — {{ $experiencia->empresa }}<br>
            <span class="muted">{{ $experiencia->fecha_inicio->translatedFormat('M Y') }} – {{ $experiencia->actual ? 'Actual' : $experiencia->fecha_fin?->translatedFormat('M Y') }}</span>
            @if ($experiencia->descripcion)<br>{{ $experiencia->descripcion }}@endif
        </p>
    @empty
        <p class="muted">Sin experiencia registrada.</p>
    @endforelse

    <h2>Formación</h2>
    @forelse ($alumno->formaciones as $formacion)
        <p>
            <strong>{{ $formacion->titulo }}</strong> — {{ $formacion->institucion }}<br>
            <span class="muted">{{ $formacion->nivel->getLabel() }} ·
                {{ $formacion->fecha_inicio?->translatedFormat('Y') }}{{ $formacion->en_curso ? ' – En curso' : ($formacion->fecha_fin ? ' – '.$formacion->fecha_fin->translatedFormat('Y') : '') }}</span>
        </p>
    @empty
        <p class="muted">Sin formación registrada.</p>
    @endforelse

    <h2>Habilidades</h2>
    @forelse ($alumno->habilidades->groupBy(fn ($h) => $h->categoria?->nombre ?? 'General') as $area => $habilidades)
        <p><strong>{{ $area }}:</strong>
            @foreach ($habilidades as $habilidad)
                {{ $habilidad->nombre }} <span class="muted">({{ $habilidad->nivel->getLabel() }})</span>@if (! $loop->last), @endif
            @endforeach
        </p>
    @empty
        <p class="muted">Sin habilidades registradas.</p>
    @endforelse

    @if ($alumno->documentos->isNotEmpty())
        <h2>Documentos probatorios</h2>
        <ul>
            @foreach ($alumno->documentos as $documento)
                <li>{{ $documento->nombre }} <span class="muted">({{ $documento->tipo->getLabel() }})</span></li>
            @endforeach
        </ul>
    @endif
@endsection
