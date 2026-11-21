<x-portal titulo="Mi perfil" descripcion="Actualiza tus datos principales y los datos generales de tu CV.">
    <form method="POST" action="{{ route('alumno.perfil.update') }}" enctype="multipart/form-data" class="space-y-6" data-validar>
        @csrf
        @method('PUT')

        <section class="tarjeta space-y-5 p-6">
            <h2 class="font-semibold text-slate-900">Datos de la cuenta</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="name" label="Nombre completo" required maxlength="255" :value="$usuario->name" />
                <x-campo name="email" label="Correo electrónico" type="email" required maxlength="255" :value="$usuario->email" />
            </div>
        </section>

        <section class="tarjeta space-y-5 p-6">
            <h2 class="font-semibold text-slate-900">Datos académicos y de contacto</h2>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <x-campo name="matricula" label="Matrícula" required inputmode="numeric" pattern="\d{6,10}" data-mensaje-patron="La matrícula debe tener entre 6 y 10 dígitos." :value="$alumno->matricula" />
                <x-campo name="carrera" label="Carrera" tipo="select" required :opciones="array_combine($carreras, $carreras)" :value="$alumno->carrera" class="lg:col-span-2" />
                <x-campo name="cuatrimestre" label="Cuatrimestre" type="number" min="1" max="10" :value="$alumno->cuatrimestre" />
                <x-campo name="telefono" label="Teléfono" type="tel" maxlength="20" pattern="[0-9 +()\-]{7,20}" data-mensaje-patron="Escribe un teléfono válido." :value="$alumno->telefono" />
                <x-campo name="fecha_nacimiento" label="Fecha de nacimiento" type="date" :value="$alumno->fecha_nacimiento" />
                <x-campo name="ciudad" label="Ciudad" maxlength="100" :value="$alumno->ciudad" />
                <x-campo name="linkedin_url" label="LinkedIn" type="url" maxlength="255" placeholder="https://www.linkedin.com/in/…" :value="$alumno->linkedin_url" />
                <x-campo name="portafolio_url" label="Portafolio o GitHub" type="url" maxlength="255" placeholder="https://…" :value="$alumno->portafolio_url" />
            </div>
            <div x-data="contador(1500, @js(old('resumen', $alumno->resumen ?? '')))">
                <x-campo name="resumen" label="Perfil profesional" tipo="textarea" rows="4" maxlength="1500" x-model="texto" :value="$alumno->resumen"
                         placeholder="Describe brevemente tus fortalezas, intereses y lo que buscas." />
                <p class="mt-1 text-right text-xs text-slate-400"><span x-text="restantes"></span> caracteres restantes</p>
            </div>
            <x-campo name="foto" label="Fotografía" type="file" accept="image/jpeg,image/png,image/webp" ayuda="JPG, PNG o WEBP de hasta 2 MB." />
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
