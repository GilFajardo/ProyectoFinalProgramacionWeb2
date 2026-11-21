<x-auth-tarjeta titulo="Iniciar sesión" descripcion="Accede como alumno, reclutador o administrador.">
    <form method="POST" action="{{ route('login.store') }}" class="space-y-5" data-validar>
        @csrf
        <x-campo name="email" label="Correo electrónico" type="email" required autofocus autocomplete="username" maxlength="255" />

        <div x-data="{ visible: false }">
            <label for="password" class="campo-label">Contraseña <span class="text-red-600" aria-hidden="true">*</span></label>
            <div class="relative">
                <input id="password" name="password" :type="visible ? 'text' : 'password'" type="password" required autocomplete="current-password" class="campo pr-20">
                <button type="button" @click="visible = ! visible" class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-marca-700"
                        x-text="visible ? 'Ocultar' : 'Mostrar'" :aria-pressed="visible.toString()">Mostrar</button>
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 text-marca-700 focus:ring-marca-600">
            Mantener mi sesión iniciada
        </label>

        <button type="submit" class="btn-primario w-full">Entrar</button>
    </form>

    <x-slot:pie>
        ¿Aún no tienes cuenta?
        <a href="{{ route('registro.alumno') }}" class="enlace">Soy alumno</a> ·
        <a href="{{ route('registro.reclutador') }}" class="enlace">Soy reclutador</a>
    </x-slot:pie>
</x-auth-tarjeta>
