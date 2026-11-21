{{-- Datos de la empresa: alta (si el reclutador aún no tiene empresa) o edición. --}}
<x-portal :titulo="$empresa ? 'Mi empresa' : 'Registra tu empresa'"
          :descripcion="$empresa ? 'Esta información aparece en tus vacantes y en el perfil público de la empresa.' : 'Para publicar vacantes necesitas registrar la empresa que representas.'">
    @if ($empresa)
        <x-slot:acciones>
            <a href="{{ route('empresas.show', $empresa) }}" class="btn-secundario" target="_blank">Ver perfil público</a>
        </x-slot:acciones>
    @endif

    @if ($empresa && ! $empresa->activa)
        <div class="mb-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-800 ring-1 ring-amber-200">
            Tu empresa está desactivada por la oficina de Vinculación, por lo que no puedes publicar vacantes. Escríbenos desde <a href="{{ route('contacto') }}" class="font-semibold underline">Contacto</a>.
        </div>
    @endif

    <form method="POST" action="{{ $empresa ? route('reclutador.empresa.update') : route('reclutador.empresa.store') }}" enctype="multipart/form-data" class="space-y-6" data-validar>
        @csrf
        @if ($empresa) @method('PUT') @endif

        <section class="tarjeta space-y-5 p-6">
            <h2 class="font-semibold text-slate-900">Datos generales</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="nombre" label="Nombre comercial" required maxlength="150" :value="$empresa?->nombre" />
                <x-campo name="razon_social" label="Razón social" maxlength="200" :value="$empresa?->razon_social" />
                <x-campo name="rfc" label="RFC" maxlength="13" minlength="12" pattern="[A-Za-zÑñ&]{3,4}[0-9]{6}[A-Za-z0-9]{3}" data-mensaje-patron="El RFC no tiene un formato válido." :value="$empresa?->rfc" />
                <x-campo name="sector" label="Sector o giro" maxlength="100" :value="$empresa?->sector" />
            </div>
            <x-campo name="descripcion" label="Descripción de la empresa" tipo="textarea" rows="4" maxlength="5000" :value="$empresa?->descripcion" />
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                @if ($empresa?->logo_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($empresa->logo_path) }}" alt="Logotipo actual" class="h-16 w-auto rounded-lg object-contain ring-1 ring-slate-200">
                @endif
                <x-campo name="logo" label="Logotipo" type="file" accept="image/jpeg,image/png,image/webp" ayuda="JPG, PNG o WEBP de hasta 1 MB." class="flex-1" />
            </div>
        </section>

        <section class="tarjeta space-y-5 p-6">
            <h2 class="font-semibold text-slate-900">Contacto y ubicación</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="email_contacto" label="Correo de contacto" type="email" maxlength="150" :value="$empresa?->email_contacto" />
                <x-campo name="telefono" label="Teléfono" type="tel" maxlength="20" pattern="[0-9 +()\-]{7,20}" data-mensaje-patron="Escribe un teléfono válido." :value="$empresa?->telefono" />
                <x-campo name="sitio_web" label="Sitio web" type="url" maxlength="255" placeholder="https://…" :value="$empresa?->sitio_web" class="sm:col-span-2" />
                <x-campo name="direccion" label="Dirección" maxlength="255" :value="$empresa?->direccion" class="sm:col-span-2" />
                <x-campo name="municipio" label="Municipio" required maxlength="100" :value="$empresa?->municipio ?? 'San Luis Potosí'" />
                <x-campo name="estado" label="Estado" required maxlength="100" :value="$empresa?->estado ?? 'San Luis Potosí'" />
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn-primario">{{ $empresa ? 'Guardar cambios' : 'Registrar empresa' }}</button>
        </div>
    </form>
</x-portal>
