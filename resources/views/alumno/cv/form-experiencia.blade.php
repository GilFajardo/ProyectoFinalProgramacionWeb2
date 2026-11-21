{{-- Formulario de experiencia (alta o edición). $experiencia es null para un registro nuevo. --}}
@php
    $formId = $experiencia ? 'experiencia-'.$experiencia->id : 'experiencia-nueva';
    $activo = old('_form') === $formId;
@endphp

<form method="POST" data-validar
      action="{{ $experiencia ? route('alumno.experiencias.update', $experiencia) : route('alumno.experiencias.store') }}"
      class="space-y-4 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-200"
      x-data="{ actual: {{ ($activo ? old('actual') : $experiencia?->actual) ? 'true' : 'false' }} }">
    @csrf
    @if ($experiencia) @method('PUT') @endif
    <input type="hidden" name="_form" value="{{ $formId }}">

    <div class="grid gap-4 sm:grid-cols-2">
        <x-campo name="puesto" label="Puesto" required maxlength="150" :value="$experiencia?->puesto" :old="$activo" bolsa="experiencia" />
        <x-campo name="empresa" label="Empresa" required maxlength="150" :value="$experiencia?->empresa" :old="$activo" bolsa="experiencia" />
        <x-campo name="fecha_inicio" label="Fecha de inicio" type="date" required :max="now()->toDateString()" :value="$experiencia?->fecha_inicio" :old="$activo" bolsa="experiencia" />
        <div>
            <x-campo name="fecha_fin" label="Fecha de fin" type="date" :max="now()->toDateString()" :value="$experiencia?->fecha_fin" :old="$activo" bolsa="experiencia"
                     x-bind:disabled="actual" x-bind:required="! actual" data-mayor-igual="fecha_inicio" data-mensaje-mayor-igual="Debe ser posterior a la fecha de inicio." />
            <label class="mt-2 flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="actual" value="1" x-model="actual" class="size-4 rounded border-slate-300 text-marca-700 focus:ring-marca-600">
                Trabajo aquí actualmente
            </label>
        </div>
    </div>
    <x-campo name="descripcion" label="Actividades y logros" tipo="textarea" rows="3" maxlength="2000" :value="$experiencia?->descripcion" :old="$activo" bolsa="experiencia" />

    <div class="flex gap-2">
        <button type="submit" class="btn-primario">{{ $experiencia ? 'Guardar cambios' : 'Agregar experiencia' }}</button>
        <button type="button" class="btn-secundario" @click="editando = null">Cancelar</button>
    </div>
</form>
