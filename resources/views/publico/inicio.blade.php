@php
    $diapositivas = [
        ['imagen' => 'banner-1.svg', 'etiqueta' => 'Bolsa de trabajo UPSLP', 'titulo' => 'Tu primer empleo empieza aquí', 'texto' => 'Encuentra vacantes, prácticas profesionales y estadías en empresas de San Luis Potosí.'],
        ['imagen' => 'banner-2.svg', 'etiqueta' => 'Vinculación empresarial', 'titulo' => 'Conectamos talento con empresas', 'texto' => 'Las empresas publican vacantes y evalúan candidatos con un CV estructurado y verificable.'],
        ['imagen' => 'banner-3.svg', 'etiqueta' => 'Crece profesionalmente', 'titulo' => 'Construye un CV que destaque', 'texto' => 'Registra tu experiencia, formación, habilidades por área y documentos que avalen tu perfil.'],
    ];
@endphp

<x-layouts.app title="Inicio">
    {{-- Banner principal (carrusel accesible con Alpine) --}}
    <section x-data="carrusel({{ count($diapositivas) }})" x-init="iniciar()" @mouseenter="pausar()" @mouseleave="reanudar()"
             class="relative isolate overflow-hidden bg-marca-900" aria-roledescription="carrusel" aria-label="Banner principal">
        @foreach ($diapositivas as $i => $diapositiva)
            <div x-show="actual === {{ $i }}" @if ($i > 0) x-cloak @endif
                 x-transition:enter="transition-opacity duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 class="absolute inset-0 -z-10" aria-hidden="true">
                <img src="{{ asset('images/'.$diapositiva['imagen']) }}" alt="" class="size-full object-cover" width="1600" height="600" @if ($i > 0) loading="lazy" @endif fetchpriority="{{ $i === 0 ? 'high' : 'low' }}">
            </div>
        @endforeach

        <div class="mx-auto max-w-7xl px-4 pt-16 pb-28 sm:px-6 lg:px-8 lg:pt-24 lg:pb-36">
            <div class="max-w-2xl">
                @foreach ($diapositivas as $i => $diapositiva)
                    <div x-show="actual === {{ $i }}" @if ($i > 0) x-cloak @endif role="group" aria-roledescription="diapositiva" aria-label="{{ $i + 1 }} de {{ count($diapositivas) }}">
                        <p class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold tracking-wide text-acento-400 uppercase ring-1 ring-white/20">{{ $diapositiva['etiqueta'] }}</p>
                        @if ($i === 0)
                            <h1 class="mt-5 text-4xl font-bold tracking-tight text-white sm:text-5xl">{{ $diapositiva['titulo'] }}</h1>
                        @else
                            <p class="mt-5 text-4xl font-bold tracking-tight text-white sm:text-5xl">{{ $diapositiva['titulo'] }}</p>
                        @endif
                        <p class="mt-4 text-lg leading-relaxed text-marca-100">{{ $diapositiva['texto'] }}</p>
                    </div>
                @endforeach

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('vacantes.index') }}" class="btn-acento px-5 py-3 text-base">Explorar vacantes</a>
                    @guest
                        <a href="{{ route('registro.reclutador') }}" class="btn px-5 py-3 text-base text-white ring-1 ring-white/40 hover:bg-white/10">Publicar una vacante</a>
                    @endguest
                </div>
            </div>
        </div>

        <div class="absolute right-4 bottom-24 flex items-center gap-2 sm:right-8 lg:bottom-28">
            <button type="button" @click="anterior()" class="rounded-full bg-white/10 p-2 text-white hover:bg-white/20" aria-label="Diapositiva anterior">‹</button>
            @foreach ($diapositivas as $i => $diapositiva)
                <button type="button" @click="ir({{ $i }})" class="h-2 rounded-full transition-all" :class="actual === {{ $i }} ? 'w-6 bg-acento-500' : 'w-2 bg-white/40'" aria-label="Ir a la diapositiva {{ $i + 1 }}"></button>
            @endforeach
            <button type="button" @click="siguiente()" class="rounded-full bg-white/10 p-2 text-white hover:bg-white/20" aria-label="Diapositiva siguiente">›</button>
        </div>
    </section>

    {{-- Buscador --}}
    <section class="relative z-10 mx-auto -mt-14 max-w-5xl px-4 sm:px-6 lg:px-8" aria-label="Buscador de vacantes">
        <form method="GET" action="{{ route('vacantes.index') }}" class="tarjeta grid gap-3 p-4 shadow-lg sm:grid-cols-[1fr_220px_auto] sm:p-5" role="search">
            <div>
                <label for="q" class="sr-only">Puesto, palabra clave o empresa</label>
                <input id="q" name="q" type="search" maxlength="100" placeholder="Puesto, palabra clave o empresa…" class="campo h-12 text-base">
            </div>
            <div>
                <label for="categoria" class="sr-only">Área de conocimiento</label>
                <select id="categoria" name="categoria" class="campo h-12">
                    <option value="">Todas las áreas</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->slug }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primario h-12 px-6">Buscar</button>
        </form>
    </section>

    {{-- Indicadores --}}
    <section class="mx-auto mt-12 max-w-7xl px-4 sm:px-6 lg:px-8" aria-label="Indicadores">
        <dl class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['Vacantes vigentes', $resumen['vacantes_vigentes']],
                ['Empresas vinculadas', $resumen['empresas']],
                ['Alumnos registrados', $resumen['alumnos']],
                ['Postulaciones', $resumen['postulaciones']],
            ] as [$etiqueta, $valor])
                <div class="tarjeta p-5 text-center">
                    <dt class="text-sm text-slate-500">{{ $etiqueta }}</dt>
                    <dd class="mt-1 text-3xl font-bold text-marca-700">{{ number_format($valor) }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- Áreas de conocimiento --}}
    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Explora por área de conocimiento</h2>
                <p class="mt-1 text-slate-600">Vacantes vigentes en las 12 áreas de la bolsa de trabajo.</p>
            </div>
        </div>
        <ul class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($categorias as $categoria)
                <li>
                    <a href="{{ route('vacantes.index', ['categoria' => $categoria->slug]) }}"
                       class="tarjeta flex items-center justify-between gap-3 p-4 transition hover:ring-marca-300">
                        <span class="text-sm font-medium text-slate-800">{{ $categoria->nombre }}</span>
                        <span class="rounded-full bg-marca-50 px-2 py-0.5 text-xs font-semibold text-marca-700">{{ $categoria->vacantes_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Vacantes recientes --}}
    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Vacantes recientes</h2>
                <p class="mt-1 text-slate-600">Las ofertas más nuevas publicadas por las empresas.</p>
            </div>
            <a href="{{ route('vacantes.index') }}" class="enlace shrink-0 text-sm">Ver todas →</a>
        </div>

        @if ($recientes->isEmpty())
            <div class="mt-6"><x-vacio titulo="Aún no hay vacantes vigentes" descripcion="Vuelve pronto: las empresas publican nuevas oportunidades constantemente." /></div>
        @else
            <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($recientes as $vacante)
                    <x-vacante-card :vacante="$vacante" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Llamado a la acción para alumnos y empresas --}}
    <section class="mx-auto mt-16 grid max-w-7xl gap-5 px-4 sm:px-6 md:grid-cols-2 lg:px-8">
        <div class="rounded-2xl bg-marca-700 p-8 text-white">
            <h2 class="text-xl font-bold">¿Eres alumno o egresado de la UPSLP?</h2>
            <p class="mt-2 text-marca-100">Crea tu perfil, arma tu CV y postúlate en minutos. Da seguimiento al estatus de cada postulación.</p>
            <a href="{{ route('registro.alumno') }}" class="btn-acento mt-6">Crear mi cuenta de alumno</a>
        </div>
        <div class="rounded-2xl bg-white p-8 ring-1 ring-slate-200">
            <h2 class="text-xl font-bold text-slate-900">¿Buscas talento para tu empresa?</h2>
            <p class="mt-2 text-slate-600">Publica vacantes con periodo de vigencia, revisa CV estructurados y genera reportes en PDF.</p>
            <a href="{{ route('registro.reclutador') }}" class="btn-primario mt-6">Registrar mi empresa</a>
        </div>
    </section>
</x-layouts.app>
