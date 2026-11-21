<x-layouts.app title="Mapa del sitio">
    <x-encabezado titulo="Mapa del sitio" descripcion="Todas las secciones de Vinculación en Línea en un solo lugar." :migas="['Mapa del sitio' => null]" />

    <div class="mx-auto grid max-w-7xl gap-6 px-4 py-10 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
        <nav class="tarjeta p-6" aria-labelledby="mapa-sitio-publico">
            <h2 id="mapa-sitio-publico" class="font-semibold text-slate-900">Sitio institucional</h2>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('inicio') }}" class="enlace">Inicio</a></li>
                <li><a href="{{ route('vacantes.index') }}" class="enlace">Bolsa de trabajo</a></li>
                <li><a href="{{ route('quienes-somos') }}" class="enlace">Quiénes somos / Acerca de</a></li>
                <li><a href="{{ route('estadisticas') }}" class="enlace">Estadísticas y seguimiento</a></li>
                <li><a href="{{ route('faq') }}" class="enlace">Preguntas frecuentes</a></li>
                <li><a href="{{ route('contacto') }}" class="enlace">Contacto</a></li>
                <li><a href="{{ route('sitemap') }}" class="enlace">sitemap.xml</a></li>
            </ul>
        </nav>

        <nav class="tarjeta p-6" aria-labelledby="mapa-sitio-areas">
            <h2 id="mapa-sitio-areas" class="font-semibold text-slate-900">Vacantes por área</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($categorias as $categoria)
                    <li><a href="{{ route('vacantes.index', ['categoria' => $categoria->slug]) }}" class="enlace">{{ $categoria->nombre }}</a></li>
                @endforeach
            </ul>
        </nav>

        <nav class="tarjeta p-6" aria-labelledby="mapa-sitio-alumnos">
            <h2 id="mapa-sitio-alumnos" class="font-semibold text-slate-900">Alumnos</h2>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('registro.alumno') }}" class="enlace">Registro de alumno</a></li>
                <li><a href="{{ route('login') }}" class="enlace">Iniciar sesión</a></li>
                <li><a href="{{ route('alumno.panel') }}" class="enlace">Mi panel</a></li>
                <li><a href="{{ route('alumno.cv') }}" class="enlace">Mi CV</a></li>
                <li><a href="{{ route('alumno.postulaciones.index') }}" class="enlace">Mis postulaciones</a></li>
                <li><a href="{{ route('alumno.perfil.edit') }}" class="enlace">Mi perfil</a></li>
            </ul>
        </nav>

        <nav class="tarjeta p-6" aria-labelledby="mapa-sitio-empresas">
            <h2 id="mapa-sitio-empresas" class="font-semibold text-slate-900">Empresas</h2>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('registro.reclutador') }}" class="enlace">Registro de reclutador</a></li>
                <li><a href="{{ route('reclutador.panel') }}" class="enlace">Panel del reclutador</a></li>
                <li><a href="{{ route('reclutador.vacantes.index') }}" class="enlace">Mis vacantes</a></li>
                <li><a href="{{ route('reclutador.vacantes.create') }}" class="enlace">Publicar vacante</a></li>
                <li><a href="{{ route('reclutador.empresa.edit') }}" class="enlace">Datos de la empresa</a></li>
                <li><a href="{{ url('/admin') }}" class="enlace">Administración (Filament)</a></li>
            </ul>
        </nav>
    </div>
</x-layouts.app>
