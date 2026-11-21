{{--
    Campo de formulario con etiqueta, ayuda y error de validación del servidor.
    Uso: <x-campo name="email" label="Correo" type="email" required :value="$usuario->email" />
         <x-campo name="carrera" label="Carrera" tipo="select" :opciones="[...]" />
         <x-campo name="resumen" label="Perfil" tipo="textarea" maxlength="1500" />
    "bolsa" indica la bolsa de errores (por ejemplo, las secciones del CV).
    "old" = false evita reutilizar valores anteriores cuando hay varios formularios iguales en la página.
--}}
@props([
    'name',
    'label',
    'tipo' => 'input',
    'type' => 'text',
    'value' => null,
    'opciones' => [],
    'placeholder' => null,
    'ayuda' => null,
    'bolsa' => 'default',
    'vacio' => 'Selecciona una opción',
    'old' => true,
])

@php
    $id = $attributes->get('id', $name.'-'.\Illuminate\Support\Str::random(4));
    $mensaje = $old ? $errors->getBag($bolsa)->first($name) : null;
    $predeterminado = $value instanceof \BackedEnum ? $value->value : ($value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value);
    $valor = $old ? old($name, $predeterminado) : $predeterminado;
    $clases = 'campo'.($mensaje ? ' campo-error' : '');
@endphp

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    <label for="{{ $id }}" class="campo-label">
        {{ $label }}
        @if ($attributes->has('required')) <span class="text-red-600" aria-hidden="true">*</span> @endif
    </label>

    @if ($tipo === 'select')
        <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->except(['class', 'id'])->merge(['class' => $clases]) }}
                @if ($mensaje) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>
            <option value="">{{ $vacio }}</option>
            @foreach ($opciones as $clave => $texto)
                <option value="{{ $clave }}" @selected((string) $valor === (string) $clave)>{{ $texto }}</option>
            @endforeach
        </select>
    @elseif ($tipo === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $attributes->get('rows', 4) }}" placeholder="{{ $placeholder }}"
                  {{ $attributes->except(['class', 'id', 'rows'])->merge(['class' => $clases]) }}
                  @if ($mensaje) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>{{ $valor }}</textarea>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" placeholder="{{ $placeholder }}"
               @if ($type !== 'password' && $type !== 'file') value="{{ $valor }}" @endif
               {{ $attributes->except(['class', 'id'])->merge(['class' => $type === 'file' ? 'block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-marca-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-marca-700 hover:file:bg-marca-100' : $clases]) }}
               @if ($mensaje) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>
    @endif

    @if ($ayuda && ! $mensaje)
        <p class="mt-1 text-xs text-slate-500">{{ $ayuda }}</p>
    @endif

    @if ($mensaje)
        <p id="{{ $id }}-error" class="mt-1 text-xs text-red-600" role="alert">{{ $mensaje }}</p>
    @endif
</div>
