<x-layouts.app title="Quiénes somos" description="Conoce Vinculación en Línea, la bolsa de trabajo de la UPSLP.">
    <x-encabezado titulo="Quiénes somos"
                  descripcion="Vinculación en Línea es la plataforma de la UPSLP que acerca a su comunidad estudiantil con el sector productivo."
                  :migas="['Quiénes somos' => null]" />

    <div class="mx-auto max-w-7xl space-y-12 px-4 py-10 sm:px-6 lg:px-8">
        <section class="grid gap-8 lg:grid-cols-2 lg:items-center">
            <div class="space-y-4 leading-relaxed text-slate-700">
                <h2 class="text-2xl font-bold text-slate-900">Acerca de la plataforma</h2>
                <p>
                    La {{ config('upslp.institucion.nombre') }} impulsa la inserción laboral de sus alumnos y egresados mediante
                    la vinculación con empresas e instituciones de San Luis Potosí y la región del Bajío.
                </p>
                <p>
                    <strong>Vinculación en Línea</strong> concentra en un solo lugar la publicación de vacantes, prácticas profesionales y estadías,
                    la construcción de un CV estructurado y el seguimiento transparente de cada postulación.
                </p>
            </div>
            <img src="{{ asset('images/banner-2.svg') }}" alt="Ilustración de una red de conexiones entre personas y empresas" class="aspect-video w-full rounded-2xl object-cover shadow-sm" loading="lazy" width="1600" height="600">
        </section>

        <section class="grid gap-5 md:grid-cols-3">
            @foreach ([
                ['Misión', 'Facilitar la vinculación entre el talento de la UPSLP y el sector productivo, con información oportuna, procesos claros y seguimiento a cada postulación.'],
                ['Visión', 'Ser el principal punto de encuentro entre la comunidad universitaria y las empresas de la región, reconocido por su utilidad y confiabilidad.'],
                ['Valores', 'Transparencia, igualdad de oportunidades, responsabilidad en el manejo de datos personales y compromiso con el desarrollo regional.'],
            ] as [$titulo, $texto])
                <div class="tarjeta p-6">
                    <h3 class="text-lg font-semibold text-marca-700">{{ $titulo }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $texto }}</p>
                </div>
            @endforeach
        </section>

        <section>
            <h2 class="text-2xl font-bold text-slate-900">¿Cómo funciona?</h2>
            <ol class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Regístrate', 'Crea tu cuenta como alumno o como reclutador de una empresa.'],
                    ['Completa tu perfil', 'Los alumnos arman su CV; las empresas completan sus datos.'],
                    ['Publica o postúlate', 'Las empresas publican vacantes con vigencia; los alumnos se postulan una vez por vacante.'],
                    ['Da seguimiento', 'El reclutador actualiza el estatus: EN PROCESO, ACEPTADO o RECHAZADO.'],
                ] as $i => [$titulo, $texto])
                    <li class="tarjeta p-6">
                        <span class="flex size-9 items-center justify-center rounded-full bg-marca-700 font-bold text-white">{{ $i + 1 }}</span>
                        <h3 class="mt-4 font-semibold text-slate-900">{{ $titulo }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $texto }}</p>
                    </li>
                @endforeach
            </ol>
        </section>

        <section class="tarjeta p-6 sm:p-8">
            <h2 class="text-xl font-bold text-slate-900">Acerca de este proyecto</h2>
            <p class="mt-2 text-slate-600">
                Este sitio es el Proyecto Final de la asignatura <strong>{{ config('upslp.proyecto.materia') }}</strong>
                ({{ config('upslp.proyecto.clave') }}), de la carrera {{ config('upslp.proyecto.carrera') }}, ciclo {{ config('upslp.proyecto.ciclo') }},
                impartida por la {{ config('upslp.proyecto.profesora') }}.
            </p>
            <p class="mt-2 text-slate-600">Desarrollado por <strong>{{ config('upslp.proyecto.autor') }}</strong> (matrícula {{ config('upslp.proyecto.matricula') }}) con Laravel, Filament, Tailwind CSS y Alpine.js.</p>
        </section>
    </div>
</x-layouts.app>
