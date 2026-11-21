<?php

namespace App\Services;

use App\Enums\EstatusPostulacion;
use App\Models\Alumno;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Postulacion;
use App\Models\Vacante;
use Illuminate\Database\Eloquent\Builder;

/**
 * Métricas de la bolsa de trabajo. Se usan en el dashboard de Filament,
 * en el portal del reclutador y en la página pública de estadísticas.
 *
 * Si se indica una empresa, todas las métricas se limitan a ella.
 */
class EstadisticaService
{
    /**
     * Indicadores principales.
     *
     * @return array{empresas: int, vacantes_vigentes: int, vacantes_total: int, alumnos: int, postulaciones: int, aceptadas: int, tasa_aceptacion: float}
     */
    public function resumen(?Empresa $empresa = null): array
    {
        $postulaciones = $this->postulacionesQuery($empresa);
        $total = (clone $postulaciones)->count();
        $aceptadas = (clone $postulaciones)->where('estatus', EstatusPostulacion::Aceptado)->count();

        return [
            'empresas' => $empresa ? 1 : Empresa::activas()->count(),
            'vacantes_vigentes' => $this->vacantesQuery($empresa)->vigentes()->count(),
            'vacantes_total' => $this->vacantesQuery($empresa)->count(),
            'alumnos' => Alumno::count(),
            'postulaciones' => $total,
            'aceptadas' => $aceptadas,
            'tasa_aceptacion' => $total > 0 ? round($aceptadas / $total * 100, 1) : 0.0,
        ];
    }

    /**
     * Número de vacantes por categoría (incluye categorías sin vacantes).
     *
     * @return array<string, int> [nombre de categoría => total]
     */
    public function vacantesPorCategoria(?Empresa $empresa = null, bool $soloVigentes = false): array
    {
        return Categoria::query()
            ->withCount(['vacantes' => function (Builder $query) use ($empresa, $soloVigentes): void {
                if ($empresa) {
                    $query->whereBelongsTo($empresa);
                }

                if ($soloVigentes) {
                    $query->vigentes();
                }
            }])
            ->orderBy('nombre')
            ->pluck('vacantes_count', 'nombre')
            ->all();
    }

    /**
     * Número de postulaciones por estatus (EN PROCESO, ACEPTADO, RECHAZADO).
     *
     * @return array<string, int> [etiqueta => total]
     */
    public function postulacionesPorEstatus(?Empresa $empresa = null): array
    {
        $conteo = $this->postulacionesQuery($empresa)
            ->selectRaw('estatus, COUNT(*) as total')
            ->groupBy('estatus')
            ->pluck('total', 'estatus');

        $resultado = [];

        foreach (EstatusPostulacion::cases() as $estatus) {
            $resultado[$estatus->getLabel()] = (int) ($conteo[$estatus->value] ?? 0);
        }

        return $resultado;
    }

    /**
     * Postulaciones recibidas en los últimos meses (incluye meses en cero).
     *
     * @return array<string, int> [mes "nov 2026" => total]
     */
    public function postulacionesPorMes(int $meses = 6, ?Empresa $empresa = null): array
    {
        $inicio = now()->startOfMonth()->subMonths($meses - 1);

        $conteo = $this->postulacionesQuery($empresa)
            ->where('created_at', '>=', $inicio)
            ->pluck('created_at')
            ->countBy(fn ($fecha): string => $fecha->format('Y-m'));

        $resultado = [];

        for ($i = 0; $i < $meses; $i++) {
            $mes = $inicio->copy()->addMonths($i);
            $resultado[$mes->translatedFormat('M Y')] = (int) ($conteo[$mes->format('Y-m')] ?? 0);
        }

        return $resultado;
    }

    /**
     * @return Builder<Vacante>
     */
    private function vacantesQuery(?Empresa $empresa): Builder
    {
        return Vacante::query()->when($empresa, fn (Builder $q) => $q->whereBelongsTo($empresa));
    }

    /**
     * @return Builder<Postulacion>
     */
    private function postulacionesQuery(?Empresa $empresa): Builder
    {
        return Postulacion::query()
            ->when($empresa, fn (Builder $q) => $q->whereRelation('vacante', 'empresa_id', $empresa->id));
    }
}
