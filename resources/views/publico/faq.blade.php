<x-layouts.app title="Preguntas frecuentes">
    <x-encabezado titulo="Preguntas frecuentes"
                  descripcion="Resolvemos las dudas más comunes de alumnos y empresas sobre la bolsa de trabajo."
                  :migas="['Preguntas frecuentes' => null]" />

    <div class="mx-auto max-w-4xl space-y-10 px-4 py-10 sm:px-6 lg:px-8">
        @foreach ($grupos as $grupo => $preguntas)
            <section>
                <h2 class="text-xl font-bold text-slate-900">{{ $grupo }}</h2>
                <div class="tarjeta mt-4 divide-y divide-slate-200" x-data="{ abierta: null }">
                    @foreach ($preguntas as $pregunta => $respuesta)
                        @php($id = \Illuminate\Support\Str::slug($grupo.'-'.$loop->index))
                        <div>
                            <h3>
                                <button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-medium text-slate-900 hover:bg-slate-50"
                                        @click="abierta = abierta === '{{ $id }}' ? null : '{{ $id }}'"
                                        :aria-expanded="(abierta === '{{ $id }}').toString()" aria-controls="{{ $id }}">
                                    {{ $pregunta }}
                                    <svg class="size-5 shrink-0 text-slate-400 transition" :class="abierta === '{{ $id }}' && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                                </button>
                            </h3>
                            <div id="{{ $id }}" x-show="abierta === '{{ $id }}'" x-transition.opacity x-cloak class="px-5 pb-4 text-sm leading-relaxed text-slate-600">
                                {{ $respuesta }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="rounded-2xl bg-marca-50 p-6 text-center">
            <p class="font-medium text-marca-900">¿No encontraste lo que buscabas?</p>
            <a href="{{ route('contacto') }}" class="btn-primario mt-4">Escríbenos</a>
        </div>
    </div>
</x-layouts.app>
