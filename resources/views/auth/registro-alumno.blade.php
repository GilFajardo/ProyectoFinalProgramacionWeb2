<x-auth-tarjeta titulo="Registro de alumno" descripcion="Crea tu cuenta para armar tu CV y postularte a las vacantes." ancho="max-w-2xl">
    <form method="POST" action="{{ route('registro.alumno.store') }}" class="space-y-5" data-validar>
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <x-campo name="name" label="Nombre completo" required maxlength="255" autocomplete="name" class="sm:col-span-2" />
            <x-campo name="email" label="Correo electrónico" type="email" required maxlength="255" autocomplete="email"
                     ayuda="De preferencia tu correo institucional." />
            <x-campo name="matricula" label="Matrícula" required inputmode="numeric" pattern="\d{6,10}"
                     data-mensaje-patron="La matrícula debe tener entre 6 y 10 dígitos." maxlength="10" />
            <x-campo name="carrera" label="Carrera" tipo="select" required :opciones="array_combine($carreras, $carreras)" />
            <x-campo name="cuatrimestre" label="Cuatrimestre" type="number" min="1" max="10" />
            <x-campo name="password" label="Contraseña" type="password" required minlength="8" autocomplete="new-password"
                     ayuda="Mínimo 8 caracteres." />
            <x-campo name="password_confirmation" label="Confirmar contraseña" type="password" required autocomplete="new-password" data-igual-a="password" />
        </div>

        <div>
            <label class="flex items-start gap-2 text-sm text-slate-600">
                <input type="checkbox" name="acepto_aviso" value="1" required @checked(old('acepto_aviso')) class="mt-0.5 size-4 rounded border-slate-300 text-marca-700 focus:ring-marca-600">
                Acepto que mis datos y mi CV se compartan con las empresas a cuyas vacantes me postule.
            </label>
            @error('acepto_aviso') <p class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primario w-full">Crear mi cuenta</button>
    </form>

    <x-slot:pie>
        ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="enlace">Inicia sesión</a>
    </x-slot:pie>
</x-auth-tarjeta>
