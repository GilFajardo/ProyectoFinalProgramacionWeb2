<?php

namespace App\Enums\Concerns;

/**
 * Utilidades compartidas por los Enums del dominio que implementan getLabel().
 */
trait TieneOpciones
{
    /**
     * Opciones para controles <select> en Blade: [valor => etiqueta].
     *
     * @return array<string, string>
     */
    public static function opciones(): array
    {
        $opciones = [];

        foreach (self::cases() as $caso) {
            $opciones[$caso->value] = (string) $caso->getLabel();
        }

        return $opciones;
    }

    /**
     * Lista de valores válidos, útil para reglas de validación.
     *
     * @return list<string>
     */
    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
