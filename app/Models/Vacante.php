<?php

namespace App\Models;

use App\Enums\Modalidad;
use App\Enums\TipoContrato;
use Database\Factories\VacanteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Oferta de empleo publicada por una empresa con un periodo de vigencia.
 *
 * Una vacante está VIGENTE cuando: activa = true y fecha_inicio <= hoy <= fecha_fin.
 */
#[Fillable([
    'empresa_id', 'reclutador_id', 'categoria_id', 'titulo', 'descripcion',
    'requisitos', 'prestaciones', 'modalidad', 'tipo_contrato', 'salario_min',
    'salario_max', 'ubicacion', 'plazas', 'fecha_inicio', 'fecha_fin', 'activa',
])]
class Vacante extends Model
{
    /** @use HasFactory<VacanteFactory> */
    use HasFactory, SoftDeletes;

    public const string VIGENCIA_VIGENTE = 'vigente';

    public const string VIGENCIA_POR_VENCER = 'por_vencer';

    public const string VIGENCIA_PROGRAMADA = 'programada';

    public const string VIGENCIA_VENCIDA = 'vencida';

    public const string VIGENCIA_INACTIVA = 'inactiva';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'modalidad' => Modalidad::class,
            'tipo_contrato' => TipoContrato::class,
            'salario_min' => 'decimal:2',
            'salario_max' => 'decimal:2',
            'plazas' => 'integer',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'activa' => 'boolean',
        ];
    }

    /**
     * Genera un slug único a partir del título al crear la vacante.
     */
    protected static function booted(): void
    {
        static::creating(function (Vacante $vacante): void {
            if (blank($vacante->slug)) {
                $vacante->slug = Str::slug(Str::limit($vacante->titulo, 150, '')).'-'.Str::lower(Str::random(6));
            }
        });
    }

    /**
     * Etiquetas legibles para los estados de vigencia (filtros y badges).
     *
     * @return array<string, string>
     */
    public static function opcionesVigencia(): array
    {
        return [
            self::VIGENCIA_VIGENTE => 'Vigentes',
            self::VIGENCIA_POR_VENCER => 'Por vencer',
            self::VIGENCIA_PROGRAMADA => 'Próximas a publicarse',
            self::VIGENCIA_VENCIDA => 'Vencidas',
        ];
    }

    /**
     * Etiqueta singular de un estado de vigencia (para insignias).
     */
    public static function etiquetaVigencia(string $estado): string
    {
        return match ($estado) {
            self::VIGENCIA_VIGENTE => 'Vigente',
            self::VIGENCIA_POR_VENCER => 'Por vencer',
            self::VIGENCIA_PROGRAMADA => 'Programada',
            self::VIGENCIA_VENCIDA => 'Vencida',
            default => 'Inactiva',
        };
    }

    /**
     * Color de Filament asociado a un estado de vigencia.
     */
    public static function colorVigencia(string $estado): string
    {
        return match ($estado) {
            self::VIGENCIA_VIGENTE => 'success',
            self::VIGENCIA_POR_VENCER => 'warning',
            self::VIGENCIA_PROGRAMADA => 'info',
            self::VIGENCIA_VENCIDA => 'danger',
            default => 'gray',
        };
    }

    /**
     * @return BelongsTo<Empresa, $this>
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * @return BelongsTo<Reclutador, $this>
     */
    public function reclutador(): BelongsTo
    {
        return $this->belongsTo(Reclutador::class);
    }

    /**
     * @return BelongsTo<Categoria, $this>
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * @return HasMany<Postulacion, $this>
     */
    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class);
    }

    /**
     * Vacantes activas dentro de su periodo de vigencia.
     */
    #[Scope]
    protected function vigentes(Builder $query): void
    {
        $hoy = today();

        $query->where('activa', true)
            ->whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy);
    }

    /**
     * Filtra por estado de vigencia (vigente, por_vencer, programada, vencida).
     */
    #[Scope]
    protected function conVigencia(Builder $query, ?string $vigencia): void
    {
        $hoy = today();

        match ($vigencia) {
            self::VIGENCIA_VIGENTE => $query->vigentes(),
            self::VIGENCIA_POR_VENCER => $query->vigentes()
                ->whereDate('fecha_fin', '<=', $hoy->copy()->addDays(config('upslp.dias_por_vencer'))),
            self::VIGENCIA_PROGRAMADA => $query->where('activa', true)->whereDate('fecha_inicio', '>', $hoy),
            self::VIGENCIA_VENCIDA => $query->whereDate('fecha_fin', '<', $hoy),
            default => null,
        };
    }

    /**
     * Filtra por el slug de la categoría (área de conocimiento).
     */
    #[Scope]
    protected function deCategoria(Builder $query, ?string $slug): void
    {
        if (filled($slug)) {
            $query->whereHas('categoria', fn (Builder $categoria) => $categoria->where('slug', $slug));
        }
    }

    /**
     * Búsqueda en sitio por título, descripción o nombre de la empresa.
     *
     * En MySQL/MariaDB usa el índice FULLTEXT en modo booleano con prefijos;
     * en otros motores (SQLite en pruebas) recurre a LIKE. Todos los valores
     * se envían como bindings, por lo que la consulta es segura ante SQLi.
     */
    #[Scope]
    protected function buscar(Builder $query, ?string $termino): void
    {
        $termino = trim((string) $termino);

        if ($termino === '') {
            return;
        }

        $palabras = collect(preg_split('/\s+/u', preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $termino)))
            ->filter(fn (string $palabra): bool => mb_strlen($palabra) >= 3)
            ->take(8);

        $usaFullText = in_array($query->getConnection()->getDriverName(), ['mysql', 'mariadb'], true)
            && $palabras->isNotEmpty();

        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $termino).'%';

        $query->where(function (Builder $q) use ($like, $palabras, $usaFullText): void {
            // El título siempre se compara con LIKE: así se encuentran palabras largas aunque
            // el servidor tenga un innodb_ft_max_token_size bajo (WAMP usa 10 por defecto).
            $q->where('titulo', 'like', $like);

            if ($usaFullText) {
                $booleano = $palabras->map(fn (string $palabra): string => '+'.$palabra.'*')->implode(' ');
                $q->orWhereFullText(['titulo', 'descripcion'], $booleano, ['mode' => 'boolean']);
            } else {
                $q->orWhere('descripcion', 'like', $like);
            }

            $q->orWhereHas('empresa', fn (Builder $empresa) => $empresa->where('nombre', 'like', $like));
        });
    }

    /**
     * Estado de vigencia calculado: vigente, por_vencer, programada, vencida o inactiva.
     *
     * @return Attribute<string, never>
     */
    protected function estadoVigencia(): Attribute
    {
        return Attribute::get(function (): string {
            $hoy = today();

            return match (true) {
                ! $this->activa => self::VIGENCIA_INACTIVA,
                $this->fecha_inicio->gt($hoy) => self::VIGENCIA_PROGRAMADA,
                $this->fecha_fin->lt($hoy) => self::VIGENCIA_VENCIDA,
                $this->fecha_fin->lte($hoy->copy()->addDays(config('upslp.dias_por_vencer'))) => self::VIGENCIA_POR_VENCER,
                default => self::VIGENCIA_VIGENTE,
            };
        });
    }

    /**
     * Indica si la vacante acepta postulaciones en este momento.
     */
    public function estaVigente(): bool
    {
        return in_array($this->estado_vigencia, [self::VIGENCIA_VIGENTE, self::VIGENCIA_POR_VENCER], true);
    }

    /**
     * Rango salarial con formato de moneda mexicana, o "A convenir".
     *
     * @return Attribute<string, never>
     */
    protected function salarioFormateado(): Attribute
    {
        return Attribute::get(function (): string {
            $formato = fn ($monto): string => '$'.number_format((float) $monto, 0, '.', ',');

            return match (true) {
                $this->salario_min !== null && $this->salario_max !== null => $formato($this->salario_min).' – '.$formato($this->salario_max).' MXN',
                $this->salario_min !== null => 'Desde '.$formato($this->salario_min).' MXN',
                $this->salario_max !== null => 'Hasta '.$formato($this->salario_max).' MXN',
                default => 'A convenir',
            };
        });
    }
}
