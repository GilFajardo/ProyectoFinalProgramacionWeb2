{{-- Formulario de formación (alta o edición). $formacion es null para un registro nuevo. --}}
@php
    $formId = $formacion ? 'formacion-'.$formacion->id : 'formacion-nueva';
    $activo = old('_form') === $formId;
@endphp

<form method="POST" data-validar
      action="{{ $formacion ? route('alumno.formaciones.update', $formacion) : route('alumno.formaciones.store') }}"
      class="space-y-4 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-200"
      x-data="{ enCurso: {{ ($activo ? old('en_curso') : $formacion?->en_curso) ? 'true' : 'false' }} }">
    @csrf
    @if ($formacion) @method('PUT') @endif
    <input type="hidden" name="_form" value="{{ $formId }}">

    <div class="grid gap-4 sm:grid-cols-2">
        <x-campo name="titulo" label="Título, carrera o curso" required maxlength="150" :value="$formacion?->titulo" :old="$activo" bolsa="formacion" />
        <x-campo name="institucion" label="Institución" required maxlength="150" :value="$formacion?->institucion" :old="$activo" bolsa="formacion" />
        <x-campo name="nivel" label="Nivel" tipo="select" required :opciones="$nivelesFormacion" :value="$formacion?->nivel" :old="$activo" bolsa="formacion" />
        <x-campo name="fecha_inicio" label="Fecha de inicio" type="date" :max="now()->toDateString()" :value="$formacion?->fecha_inicio" :old="$activo" bolsa="formacion" />
        <div>
            <x-campo name="fecha_fin" label="Fecha de término" type="date" :value="$formacion?->fecha_fin" :old="$activo" bolsa="formacion"
                     x-bind:disabled="enCurso" data-mayor-igual="fecha_inicio" data-mensaje-mayor-igual="Debe ser posterior a la fecha de inicio." />
            <label class="mt-2 flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="en_curso" value="1" x-model="enCurso" class="size-4 rounded border-slate-300 text-marca-700 focus:ring-marca-600">
                En curso
            </label>
        </div>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="btn-primario">{{ $formacion ? 'Guardar cambios' : 'Agregar formación' }}</button>
        <button type="button" class="btn-secundario" @click="editando = null">Cancelar</button>
    </div>
</form>
