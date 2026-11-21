<x-layouts.app title="Contacto">
    <x-encabezado titulo="Contacto"
                  descripcion="¿Tienes dudas, quieres registrar tu empresa o necesitas ayuda con tu cuenta? Escríbenos."
                  :migas="['Contacto' => null]" />

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1fr_360px] lg:px-8">
        <form method="POST" action="{{ route('contacto.store') }}" class="tarjeta space-y-5 p-6 sm:p-8" data-validar>
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="nombre" label="Nombre completo" required minlength="3" maxlength="120" autocomplete="name" />
                <x-campo name="email" label="Correo electrónico" type="email" required maxlength="150" autocomplete="email" />
            </div>
            <x-campo name="asunto" label="Asunto" required minlength="5" maxlength="150" />
            <div x-data="contador(3000, @js(old('mensaje', '')))">
                <x-campo name="mensaje" label="Mensaje" tipo="textarea" rows="6" required minlength="20" maxlength="3000" x-model="texto"
                         placeholder="Escribe tu mensaje (mínimo 20 caracteres)…" />
                <p class="mt-1 text-right text-xs text-slate-400"><span x-text="restantes">3000</span> caracteres restantes</p>
            </div>

            {{-- Campo trampa contra bots: oculto para personas. --}}
            <div class="hidden" aria-hidden="true">
                <label for="sitio_web">No llenar este campo</label>
                <input id="sitio_web" name="sitio_web" type="text" tabindex="-1" autocomplete="off">
            </div>

            <button type="submit" class="btn-primario">Enviar mensaje</button>
        </form>

        <aside class="space-y-5">
            <div class="tarjeta p-6">
                <h2 class="font-semibold text-slate-900">Oficina de Vinculación</h2>
                <address class="mt-3 space-y-2 text-sm not-italic text-slate-600">
                    <p>{{ config('upslp.institucion.nombre') }}</p>
                    <p>{{ config('upslp.institucion.direccion') }}</p>
                    <p>Tel. {{ config('upslp.institucion.telefono') }}</p>
                    <p><a href="mailto:{{ config('upslp.institucion.email') }}" class="enlace">{{ config('upslp.institucion.email') }}</a></p>
                </address>
            </div>
            <div class="tarjeta p-6">
                <h2 class="font-semibold text-slate-900">Horario de atención</h2>
                <p class="mt-2 text-sm text-slate-600">Lunes a viernes de 9:00 a 17:00 h.</p>
                <p class="mt-2 text-sm text-slate-600">Respondemos los mensajes en un máximo de 2 días hábiles.</p>
            </div>
        </aside>
    </div>
</x-layouts.app>
