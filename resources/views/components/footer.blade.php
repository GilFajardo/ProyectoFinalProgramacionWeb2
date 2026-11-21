{{-- Pie de página institucional con navegación y créditos obligatorios del proyecto. --}}
@php
    $institucion = config('upslp.institucion');
    $proyecto = config('upslp.proyecto');
@endphp

<footer class="mt-16 bg-marca-950 text-marca-100">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
        <div class="space-y-4">
            <x-logo claro />
            <p class="text-sm leading-relaxed text-marca-200">
                Plataforma de la {{ $institucion['nombre'] }} que conecta a alumnos y egresados con empresas de la región.
            </p>
        </div>

        <nav aria-label="Navegación del pie de página">
            <h2 class="text-sm font-semibold tracking-wide text-white uppercase">Navegación</h2>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="{{ route('inicio') }}" class="hover:text-white">Inicio</a></li>
                <li><a href="{{ route('vacantes.index') }}" class="hover:text-white">Bolsa de trabajo</a></li>
                <li><a href="{{ route('quienes-somos') }}" class="hover:text-white">Quiénes somos</a></li>
                <li><a href="{{ route('estadisticas') }}" class="hover:text-white">Estadísticas</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-white">Preguntas frecuentes</a></li>
                <li><a href="{{ route('contacto') }}" class="hover:text-white">Contacto</a></li>
                <li><a href="{{ route('mapa-sitio') }}" class="hover:text-white">Mapa del sitio</a></li>
            </ul>
        </nav>

        <div>
            <h2 class="text-sm font-semibold tracking-wide text-white uppercase">Contacto</h2>
            <address class="mt-4 space-y-2 text-sm not-italic text-marca-200">
                <p>{{ $institucion['direccion'] }}</p>
                <p>Tel. {{ $institucion['telefono'] }}</p>
                <p><a href="mailto:{{ $institucion['email'] }}" class="hover:text-white">{{ $institucion['email'] }}</a></p>
            </address>
            <div class="mt-4 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('registro.alumno') }}" class="rounded-md bg-white/10 px-3 py-1.5 hover:bg-white/20">Soy alumno</a>
                <a href="{{ route('registro.reclutador') }}" class="rounded-md bg-white/10 px-3 py-1.5 hover:bg-white/20">Soy empresa</a>
            </div>
        </div>

        <div class="rounded-xl bg-white/5 p-5 ring-1 ring-white/10">
            <h2 class="text-sm font-semibold tracking-wide text-white uppercase">Créditos del proyecto</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div><dt class="text-marca-300">Desarrollador</dt><dd class="font-medium text-white">{{ $proyecto['autor'] }} <span class="text-marca-300">({{ $proyecto['matricula'] }})</span></dd></div>
                <div><dt class="text-marca-300">Materia</dt><dd class="text-white">{{ $proyecto['materia'] }} · {{ $proyecto['clave'] }}</dd></div>
                <div><dt class="text-marca-300">Profesora</dt><dd class="text-white">{{ $proyecto['profesora'] }}</dd></div>
                <div><dt class="text-marca-300">Carrera / ciclo</dt><dd class="text-white">ITI · {{ $proyecto['ciclo'] }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-xs text-marca-300 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>© {{ now()->year }} {{ $institucion['nombre'] }} ({{ $institucion['siglas'] }}). Proyecto académico.</p>
            <p>Proyecto Final de {{ $proyecto['materia'] }} · Desarrollado por {{ $proyecto['autor'] }}</p>
        </div>
    </div>
</footer>
