{{-- Plantilla base de los reportes PDF (dompdf). Usa CSS 2.1 y la fuente DejaVu Sans para acentos y ñ. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }} · {{ config('upslp.institucion.sistema') }}</title>
    <style>
        @page { margin: 95px 36px 60px 36px; }
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 9.5px; color: #1f2937; }
        header { position: fixed; top: -80px; left: 0; right: 0; height: 70px; border-bottom: 3px solid #0b3c7a; }
        header .marca { float: left; }
        header .marca .sistema { font-size: 17px; font-weight: bold; color: #0b3c7a; }
        header .marca .institucion { font-size: 9px; color: #4b5563; }
        header .meta { float: right; text-align: right; font-size: 8.5px; color: #4b5563; }
        footer { position: fixed; bottom: -45px; left: 0; right: 0; height: 30px; border-top: 1px solid #d1d5db; font-size: 7.5px; color: #6b7280; padding-top: 5px; }
        h1 { font-size: 15px; color: #0b3c7a; margin: 0 0 6px; }
        h2 { font-size: 11.5px; color: #0b3c7a; margin: 14px 0 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; }
        .filtros { margin-bottom: 8px; font-size: 8.5px; color: #374151; }
        .filtros span { background: #eef2ff; padding: 2px 6px; margin-right: 4px; }
        table.datos { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.datos th { background: #0b3c7a; color: #fff; text-align: left; padding: 5px 4px; font-size: 8.5px; }
        table.datos td { padding: 4px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        table.datos tr:nth-child(even) td { background: #f8fafc; }
        .resumen { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 0 -6px 6px; }
        .resumen td { background: #f1f5f9; border-left: 3px solid #0b3c7a; padding: 6px 8px; }
        .resumen .valor { font-size: 15px; font-weight: bold; color: #0b3c7a; }
        .resumen .etiqueta { font-size: 8px; color: #4b5563; text-transform: uppercase; }
        .badge { padding: 1px 5px; font-size: 7.5px; font-weight: bold; }
        .b-en_proceso, .b-por_vencer, .b-programada { background: #fef3c7; color: #92400e; }
        .b-aceptado, .b-vigente { background: #d1fae5; color: #065f46; }
        .b-rechazado, .b-vencida { background: #fee2e2; color: #991b1b; }
        .b-inactiva { background: #e5e7eb; color: #374151; }
        .vacio { padding: 20px; text-align: center; color: #6b7280; }
        .derecha { text-align: right; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <header>
        <div class="marca">
            <div class="sistema">{{ config('upslp.institucion.sistema') }}</div>
            <div class="institucion">{{ config('upslp.institucion.nombre') }} ({{ config('upslp.institucion.siglas') }})</div>
            <div class="institucion">Bolsa de trabajo y vinculación empresarial</div>
        </div>
        <div class="meta">
            Generado: {{ $generadoEn->translatedFormat('d \d\e F \d\e Y, H:i') }} h<br>
            @if ($generadoPor) Por: {{ $generadoPor }}<br> @endif
        </div>
    </header>

    <footer>
        {{ config('upslp.institucion.nombre') }} · {{ config('upslp.proyecto.materia') }} · {{ config('upslp.proyecto.profesora') }}
        · Desarrollado por {{ config('upslp.proyecto.autor') }} ({{ config('upslp.proyecto.matricula') }})
    </footer>

    <main>
        <h1>{{ $titulo }}</h1>

        @if (! empty($filtros))
            <div class="filtros">
                Filtros aplicados:
                @foreach ($filtros as $etiqueta => $valor)
                    <span>{{ $etiqueta }}: {{ $valor }}</span>
                @endforeach
            </div>
        @endif

        @yield('contenido')
    </main>
    {{-- La numeración "Página X de Y" la agrega ReporteService sobre el canvas de dompdf. --}}
</body>
</html>
