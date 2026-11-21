<x-auth-tarjeta titulo="Registro de reclutador" descripcion="Registra tu empresa y comienza a publicar vacantes para el talento de la UPSLP." ancho="max-w-2xl">
    <form method="POST" action="{{ route('registro.reclutador.store') }}" class="space-y-6" data-validar>
        @csrf
        <fieldset class="space-y-5">
            <legend class="text-sm font-semibold tracking-wide text-marca-700 uppercase">Tus datos</legend>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="name" label="Nombre completo" required maxlength="255" autocomplete="name" />
                <x-campo name="email" label="Correo electrónico" type="email" required maxlength="255" autocomplete="email" />
                <x-campo name="puesto" label="Puesto" maxlength="100" placeholder="Ej. Analista de Recursos Humanos" />
                <x-campo name="telefono" label="Teléfono" type="tel" maxlength="20" pattern="[0-9 +()\-]{7,20}" data-mensaje-patron="Escribe un teléfono válido." />
            </div>
        </fieldset>

        <fieldset class="space-y-5">
            <legend class="text-sm font-semibold tracking-wide text-marca-700 uppercase">Tu empresa</legend>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="empresa_nombre" label="Nombre de la empresa" required maxlength="150" class="sm:col-span-2" />
                <x-campo name="rfc" label="RFC" maxlength="13" minlength="12" pattern="[A-Za-zÑñ&]{3,4}[0-9]{6}[A-Za-z0-9]{3}"
                         data-mensaje-patron="El RFC no tiene un formato válido." ayuda="Opcional. Evita registros duplicados." />
                <x-campo name="sector" label="Sector o giro" maxlength="100" placeholder="Ej. Manufactura" />
            </div>
        </fieldset>

        <fieldset class="space-y-5">
            <legend class="text-sm font-semibold tracking-wide text-marca-700 uppercase">Acceso</legend>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="password" label="Contraseña" type="password" required minlength="8" autocomplete="new-password" ayuda="Mínimo 8 caracteres." />
                <x-campo name="password_confirmation" label="Confirmar contraseña" type="password" required autocomplete="new-password" data-igual-a="password" />
            </div>
        </fieldset>

        <div>
            <label class="flex items-start gap-2 text-sm text-slate-600">
                <input type="checkbox" name="acepto_aviso" value="1" required @checked(old('acepto_aviso')) class="mt-0.5 size-4 rounded border-slate-300 text-marca-700 focus:ring-marca-600">
                Me comprometo a usar los datos de los candidatos únicamente para fines de reclutamiento.
            </label>
            @error('acepto_aviso') <p class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primario w-full">Registrar mi empresa</button>
    </form>

    <x-slot:pie>
        ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="enlace">Inicia sesión</a>
    </x-slot:pie>
</x-auth-tarjeta>
