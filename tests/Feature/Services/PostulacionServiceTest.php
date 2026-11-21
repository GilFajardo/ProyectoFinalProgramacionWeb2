<?php

namespace Tests\Feature\Services;

use App\Enums\EstatusPostulacion;
use App\Exceptions\PostulacionNoPermitidaException;
use App\Models\Alumno;
use App\Models\Postulacion;
use App\Models\Vacante;
use App\Services\PostulacionService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PostulacionServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_creates_postulacion_en_proceso_for_vigente_vacante(): void
    {
        $alumno = Alumno::factory()->create();
        $vacante = Vacante::factory()->vigente()->create();

        $postulacion = app(PostulacionService::class)->postular($alumno, $vacante, 'Me interesa la vacante.');

        $this->assertModelExists($postulacion);
        $this->assertSame(EstatusPostulacion::EnProceso, $postulacion->fresh()->estatus);
        $this->assertSame('Me interesa la vacante.', $postulacion->carta_presentacion);
    }

    public function test_rejects_second_postulacion_to_same_vacante(): void
    {
        $alumno = Alumno::factory()->create();
        $vacante = Vacante::factory()->vigente()->create();
        $servicio = app(PostulacionService::class);
        $servicio->postular($alumno, $vacante);

        try {
            $servicio->postular($alumno, $vacante);
            $this->fail('Se esperaba PostulacionNoPermitidaException por postulación duplicada.');
        } catch (PostulacionNoPermitidaException $e) {
            $this->assertSame('Ya te postulaste a esta vacante.', $e->getMessage());
        }

        $this->assertSame(1, Postulacion::whereBelongsTo($alumno)->whereBelongsTo($vacante)->count());
    }

    public function test_allows_same_alumno_to_apply_to_different_vacantes(): void
    {
        $alumno = Alumno::factory()->create();
        $servicio = app(PostulacionService::class);

        $servicio->postular($alumno, Vacante::factory()->vigente()->create());
        $servicio->postular($alumno, Vacante::factory()->vigente()->create());

        $this->assertSame(2, $alumno->postulaciones()->count());
    }

    public function test_database_rejects_duplicate_pair_even_bypassing_service(): void
    {
        $postulacion = Postulacion::factory()->create();

        $this->expectException(UniqueConstraintViolationException::class);

        Postulacion::factory()->create([
            'vacante_id' => $postulacion->vacante_id,
            'alumno_id' => $postulacion->alumno_id,
        ]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function vacantesNoVigentes(): array
    {
        return [
            'vencida' => ['vencida'],
            'programada (aún no inicia)' => ['programada'],
            'inactiva' => ['inactiva'],
        ];
    }

    #[DataProvider('vacantesNoVigentes')]
    public function test_rejects_postulacion_to_vacante_that_is_not_vigente(string $estado): void
    {
        $alumno = Alumno::factory()->create();
        $vacante = Vacante::factory()->{$estado}()->create();

        $this->expectException(PostulacionNoPermitidaException::class);
        $this->expectExceptionMessage('Esta vacante no se encuentra vigente.');

        app(PostulacionService::class)->postular($alumno, $vacante);
    }
}
