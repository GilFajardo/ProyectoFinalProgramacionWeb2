<x-portal titulo="Mi perfil" descripcion="Tus datos como reclutador.">
    <form method="POST" action="{{ route('reclutador.perfil.update') }}" class="space-y-6" data-validar>
        @csrf
        @method('PUT')

        <section class="tarjeta space-y-5 p-6">
            <h2 class="font-semibold text-slate-900">Datos personales</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="name" label="Nombre completo" required maxlength="255" :value="$usuario->name" />
                <x-campo name="email" label="Correo electrónico" type="email" required maxlength="255" :value="$usuario->email" />
                <x-campo name="puesto" label="Puesto" maxlength="100" :value="$reclutador->puesto" />
                <x-campo name="telefono" label="Teléfono" type="tel" maxlength="20" pattern="[0-9 +()\-]{7,20}" data-mensaje-patron="Escribe un teléfono válido." :value="$reclutador->telefono" />
            </div>
        </section>

        <section class="tarjeta space-y-5 p-6">
            <div>
                <h2 class="font-semibold text-slate-900">Cambiar contraseña</h2>
                <p class="text-sm text-slate-500">Déjalo en blanco si no deseas cambiarla.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-3">
                <x-campo name="password_actual" label="Contraseña actual" type="password" autocomplete="current-password" />
                <x-campo name="password" label="Nueva contraseña" type="password" minlength="8" autocomplete="new-password" />
                <x-campo name="password_confirmation" label="Confirmar nueva contraseña" type="password" autocomplete="new-password" data-igual-a="password" />
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn-primario">Guardar cambios</button>
        </div>
    </form>
</x-portal>
