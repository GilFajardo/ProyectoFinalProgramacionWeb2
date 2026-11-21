<?php

namespace App\Services;

use App\Enums\EstatusPostulacion;
use App\Models\Alumno;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Postulacion;
use App\Models\Vacante;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Database\Eloquent\Builder;

/**
 * Genera los reportes PDF institucionales con dompdf.
 *
 * Los mismos métodos sirven al administrador (todas las empresas) y al
 * reclutador (pasando su empresa, que limita el alcance del reporte).
 */
class ReporteService
{
    public const string VACANTES = 'vacantes';

    public const string POSTULACIONES = 'postulaciones';

    public const string EMPRESAS = 'empresas';

    /**
     * @return array<string, string>
     */
    public static function tipos(): array
    {
        return [
            self::VACANTES => 'Vacantes',
            self::POSTULACIONES => 'Postulaciones',
            self::EMPRESAS => 'Empresas',
        ];
    }

    /**
     * Reporte de vacantes con su vigencia y número de postulaciones.
     *
     * @param  array{categoria_id?: int|string|null, vigencia?: string|null, empresa_id?: int|string|null}  $filtros
     */
    public function vacantes(array $filtros = [], ?Empresa $empresa = null): DomPdf
    {
        $empresaId = $empresa?->id ?? ($filtros['empresa_id'] ?? null);

        $vacantes = Vacante::query()
            ->with(['empresa', 'categoria'])
            ->withCount([
                'postulaciones',
                'postulaciones as aceptadas_count' => fn (Builder $q) => $q->where('estatus', EstatusPostulacion::Aceptado),
            ])
            ->when($empresaId, fn (Builder $q) => $q->where('empresa_id', $empresaId))
            ->when($filtros['categoria_id'] ?? null, fn (Builder $q, $id) => $q->where('categoria_id', $id))
            ->conVigencia($filtros['vigencia'] ?? null)
            ->orderByDesc('fecha_inicio')
            ->get();

        return $this->pdf('pdf.vacantes', 'Reporte de vacantes', [
            'vacantes' => $vacantes,
            'porEstado' => $vacantes->countBy('estado_vigencia'),
            'filtros' => $this->describirFiltros($filtros, $empresa),
        ]);
    }

    /**
     * Reporte de postulaciones con datos del alumno y su estatus.
     *
     * @param  array{estatus?: string|null, vacante_id?: int|string|null, empresa_id?: int|string|null}  $filtros
     */
    public function postulaciones(array $filtros = [], ?Empresa $empresa = null): DomPdf
    {
        $empresaId = $empresa?->id ?? ($filtros['empresa_id'] ?? null);

        $postulaciones = Postulacion::query()
            ->with(['alumno.user', 'vacante.empresa'])
            ->when($empresaId, fn (Builder $q) => $q->whereRelation('vacante', 'empresa_id', $empresaId))
            ->when($filtros['vacante_id'] ?? null, fn (Builder $q, $id) => $q->where('vacante_id', $id))
            ->when($filtros['estatus'] ?? null, fn (Builder $q, $estatus) => $q->where('estatus', $estatus))
            ->latest()
            ->get();

        return $this->pdf('pdf.postulaciones', 'Reporte de postulaciones', [
            'postulaciones' => $postulaciones,
            'porEstatus' => $postulaciones->countBy(fn (Postulacion $p): string => $p->estatus->getLabel()),
            'filtros' => $this->describirFiltros($filtros, $empresa),
        ]);
    }

    /**
     * Reporte de empresas con sus reclutadores, vacantes y postulaciones.
     * Si se indica una empresa, el reporte es la ficha de esa empresa.
     */
    public function empresas(?Empresa $empresa = null): DomPdf
    {
        $empresas = Empresa::query()
            ->with('reclutadores.user')
            ->withCount([
                'vacantes',
                'vacantes as vigentes_count' => fn (Builder $q) => $q->vigentes(),
                'postulaciones',
            ])
            ->when($empresa, fn (Builder $q) => $q->whereKey($empresa->id))
            ->orderBy('nombre')
            ->get();

        return $this->pdf('pdf.empresas', $empresa ? 'Ficha de empresa' : 'Reporte de empresas', [
            'empresas' => $empresas,
            'filtros' => $empresa ? ['Empresa' => $empresa->nombre] : [],
        ]);
    }

    /**
     * CV estructurado de un alumno, listo para imprimir.
     */
    public function cv(Alumno $alumno): DomPdf
    {
        $alumno->load(['user', 'experiencias', 'formaciones', 'habilidades.categoria', 'documentos']);

        return $this->pdf('pdf.cv', 'Currículum Vitae', ['alumno' => $alumno], 'portrait');
    }

    /**
     * Genera el reporte indicado por su tipo.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function generar(string $tipo, array $filtros = [], ?Empresa $empresa = null): DomPdf
    {
        return match ($tipo) {
            self::VACANTES => $this->vacantes($filtros, $empresa),
            self::POSTULACIONES => $this->postulaciones($filtros, $empresa),
            self::EMPRESAS => $this->empresas($empresa),
        };
    }

    /**
     * Nombre de archivo descriptivo: reporte-vacantes-2026-11-21.pdf
     */
    public function nombreArchivo(string $tipo): string
    {
        return 'reporte-'.$tipo.'-'.now()->format('Y-m-d_His').'.pdf';
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    private function pdf(string $vista, string $titulo, array $datos, string $orientacion = 'landscape'): DomPdf
    {
        $pdf = Pdf::loadView($vista, $datos + [
            'titulo' => $titulo,
            'generadoEn' => now(),
            'generadoPor' => auth()->user()?->name,
        ])->setPaper('letter', $orientacion)
            ->setOption('isFontSubsettingEnabled', true);

        // Se renderiza aquí para numerar páginas sin habilitar PHP embebido en dompdf.
        $pdf->render();
        $canvas = $pdf->getDomPDF()->getCanvas();
        $canvas->page_text($canvas->get_width() - 100, $canvas->get_height() - 32, 'Página {PAGE_NUM} de {PAGE_COUNT}', null, 7, [0.42, 0.45, 0.5]);

        return $pdf;
    }

    /**
     * Traduce los filtros aplicados a texto legible para el encabezado del PDF.
     *
     * @param  array<string, mixed>  $filtros
     * @return array<string, string>
     */
    private function describirFiltros(array $filtros, ?Empresa $empresa): array
    {
        $descripcion = [];

        if ($empresa || filled($filtros['empresa_id'] ?? null)) {
            $descripcion['Empresa'] = $empresa?->nombre ?? (string) Empresa::withTrashed()->find($filtros['empresa_id'])?->nombre;
        }

        if (filled($filtros['categoria_id'] ?? null)) {
            $descripcion['Categoría'] = (string) Categoria::find($filtros['categoria_id'])?->nombre;
        }

        if (filled($filtros['vigencia'] ?? null)) {
            $descripcion['Vigencia'] = Vacante::opcionesVigencia()[$filtros['vigencia']] ?? $filtros['vigencia'];
        }

        if (filled($filtros['estatus'] ?? null)) {
            $descripcion['Estatus'] = EstatusPostulacion::tryFrom($filtros['estatus'])?->getLabel() ?? $filtros['estatus'];
        }

        if (filled($filtros['vacante_id'] ?? null)) {
            $descripcion['Vacante'] = (string) Vacante::withTrashed()->find($filtros['vacante_id'])?->titulo;
        }

        return $descripcion;
    }
}
