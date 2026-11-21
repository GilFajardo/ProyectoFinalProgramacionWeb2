{{-- Formulario de alta y modificación de vacantes. --}}
@php($esNueva = ! $vacante->exists)

<x-portal :titulo="$esNueva ? 'Publicar vacante' : 'Editar vacante'"
          :descripcion="$esNueva ? 'Completa la información de la oferta y define su periodo de vigencia.' : $vacante->titulo">
    <form method="POST" action="{{ $esNueva ? route('reclutador.vacantes.store') : route('reclutador.vacantes.update', $vacante) }}" class="space-y-6" data-validar>
        @csrf
        @if (! $esNueva) @method('PUT') @endif

        <section class="tarjeta space-y-5 p-6">
            <h2 class="font-semibold text-slate-900">Descripción del puesto</h2>
            <x-campo name="titulo" label="Título del puesto" required minlength="5" maxlength="150" :value="$vacante->titulo" placeholder="Ej. Desarrollador(a) Web Jr." />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="categoria_id" label="Área de conocimiento" tipo="select" required :opciones="$categorias" :value="$vacante->categoria_id" />
                <x-campo name="tipo_contrato" label="Tipo de contratación" tipo="select" required :opciones="$tiposContrato" :value="$vacante->tipo_contrato" />
            </div>
            <div x-data="contador(10000, @js(old('descripcion', $vacante->descripcion ?? '')))">
                <x-campo name="descripcion" label="Descripción" tipo="textarea" rows="6" required minlength="30" maxlength="10000" x-model="texto" :value="$vacante->descripcion"
                         placeholder="Funciones, responsabilidades y lo que ofrece el puesto (mínimo 30 caracteres)." />
                <p class="mt-1 text-right text-xs text-slate-400"><span x-text="restantes"></span> caracteres restantes</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="requisitos" label="Requisitos" tipo="textarea" rows="5" maxlength="5000" :value="$vacante->requisitos" placeholder="Un requisito por línea." />
                <x-campo name="prestaciones" label="Prestaciones" tipo="textarea" rows="5" maxlength="5000" :value="$vacante->prestaciones" placeholder="Una prestación por línea." />
            </div>
        </section>

        <section class="tarjeta space-y-5 p-6">
            <h2 class="font-semibold text-slate-900">Condiciones</h2>
            <div class="grid gap-5 sm:grid-cols-3">
                <x-campo name="modalidad" label="Modalidad" tipo="select" required :opciones="$modalidades" :value="$vacante->modalidad" />
                <x-campo name="ubicacion" label="Ubicación" maxlength="150" :value="$vacante->ubicacion" placeholder="Ej. Zona Industrial, S.L.P." />
                <x-campo name="plazas" label="Plazas" type="number" required min="1" max="999" :value="$vacante->plazas" />
                <x-campo name="salario_min" label="Salario mínimo mensual (MXN)" type="number" min="0" step="100" :value="$vacante->salario_min" />
                <x-campo name="salario_max" label="Salario máximo mensual (MXN)" type="number" min="0" step="100" :value="$vacante->salario_max"
                         data-mayor-igual="salario_min" data-mensaje-mayor-igual="Debe ser mayor o igual que el salario mínimo." ayuda="Déjalos vacíos para mostrar «A convenir»." />
            </div>
        </section>

        <section class="tarjeta space-y-5 p-6">
            <div>
                <h2 class="font-semibold text-slate-900">Periodo de vigencia</h2>
                <p class="text-sm text-slate-500">La vacante solo recibe postulaciones entre estas fechas (máximo un año).</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-campo name="fecha_inicio" label="Inicio de vigencia" type="date" required :min="$esNueva ? today()->toDateString() : null" :value="$vacante->fecha_inicio" />
                <x-campo name="fecha_fin" label="Fin de vigencia" type="date" required :min="today()->toDateString()" :max="today()->addYear()->toDateString()" :value="$vacante->fecha_fin"
                         data-mayor-igual="fecha_inicio" data-mensaje-mayor-igual="Debe ser posterior o igual al inicio de vigencia." />
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="hidden" name="activa" value="0">
                <input type="checkbox" name="activa" value="1" @checked(old('activa', $vacante->activa)) class="size-4 rounded border-slate-300 text-marca-700 focus:ring-marca-600">
                Publicada (desmárcala para ocultarla temporalmente sin darla de baja)
            </label>
        </section>

        <div class="flex justify-end gap-2">
            <a href="{{ $esNueva ? route('reclutador.vacantes.index') : route('reclutador.vacantes.show', $vacante) }}" class="btn-secundario">Cancelar</a>
            <button type="submit" class="btn-primario">{{ $esNueva ? 'Publicar vacante' : 'Guardar cambios' }}</button>
        </div>
    </form>
</x-portal>
