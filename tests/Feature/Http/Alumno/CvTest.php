<?php

namespace Tests\Feature\Http\Alumno;

use App\Models\Alumno;
use App\Models\CvDocumento;
use App\Models\CvExperiencia;
use App\Models\Postulacion;
use App\Models\Reclutador;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CvTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_alumno_adds_current_experience_without_end_date(): void
    {
        $alumno = Alumno::factory()->create();

        $this->actingAs($alumno->user)->post(route('alumno.experiencias.store'), [
            'empresa' => 'Soluciones TI',
            'puesto' => 'Practicante',
            'fecha_inicio' => '2025-01-10',
            'fecha_fin' => '2025-03-01',
            'actual' => '1',
        ])->assertRedirect(route('alumno.cv').'#experiencia');

        $experiencia = $alumno->experiencias()->sole();
        $this->assertTrue($experiencia->actual);
        $this->assertNull($experiencia->fecha_fin);
    }

    public function test_experience_requires_end_date_unless_current(): void
    {
        $alumno = Alumno::factory()->create();

        $this->actingAs($alumno->user)->post(route('alumno.experiencias.store'), [
            'empresa' => 'Soluciones TI', 'puesto' => 'Practicante', 'fecha_inicio' => '2025-01-10',
        ])->assertSessionHasErrorsIn('experiencia', 'fecha_fin');
    }

    public function test_alumno_cannot_modify_another_alumnos_experience(): void
    {
        $ajena = CvExperiencia::factory()->create();

        $this->actingAs(Alumno::factory()->create()->user)
            ->delete(route('alumno.experiencias.destroy', $ajena))
            ->assertForbidden();

        $this->assertModelExists($ajena);
    }

    public function test_duplicate_skill_is_rejected(): void
    {
        $alumno = Alumno::factory()->create();
        $alumno->habilidades()->create(['nombre' => 'Laravel', 'nivel' => 'avanzado']);

        $this->actingAs($alumno->user)
            ->post(route('alumno.habilidades.store'), ['nombre' => 'Laravel', 'nivel' => 'basico'])
            ->assertSessionHasErrorsIn('habilidad', 'nombre');
    }

    public function test_document_is_stored_privately_and_downloadable_by_owner(): void
    {
        Storage::fake('local');
        $alumno = Alumno::factory()->create();

        $this->actingAs($alumno->user)->post(route('alumno.documentos.store'), [
            'nombre' => 'Certificado de inglés',
            'tipo' => 'certificado',
            'archivo' => UploadedFile::fake()->create('certificado.pdf', 300, 'application/pdf'),
        ])->assertRedirect(route('alumno.cv').'#documentos');

        $documento = $alumno->documentos()->sole();
        Storage::disk('local')->assertExists($documento->archivo_path);

        $this->actingAs($alumno->user)->get(route('alumno.documentos.show', $documento))->assertDownload('certificado-de-ingles.pdf');
    }

    public function test_rejects_executable_disguised_as_document(): void
    {
        Storage::fake('local');
        $alumno = Alumno::factory()->create();

        $this->actingAs($alumno->user)->post(route('alumno.documentos.store'), [
            'nombre' => 'Malicioso',
            'tipo' => 'otro',
            'archivo' => UploadedFile::fake()->create('script.php', 10, 'application/x-php'),
        ])->assertSessionHasErrorsIn('documento', 'archivo');

        $this->assertDatabaseCount('cv_documentos', 0);
    }

    public function test_document_access_is_limited_to_owner_and_recruiters_where_alumno_applied(): void
    {
        Storage::fake('local');
        $documento = CvDocumento::factory()->create();
        Storage::disk('local')->put($documento->archivo_path, '%PDF-1.4 prueba');

        $reclutadorConPostulacion = Reclutador::factory()->create();
        $postulacion = Postulacion::factory()
            ->for($documento->alumno)
            ->for(Vacante::factory()->for($reclutadorConPostulacion->empresa))
            ->create();

        $this->actingAs(Alumno::factory()->create()->user)->get(route('alumno.documentos.show', $documento))->assertForbidden();
        $this->actingAs(Reclutador::factory()->create()->user)
            ->get(route('reclutador.candidatos.documento', [$postulacion, $documento]))
            ->assertForbidden();
        $this->actingAs($reclutadorConPostulacion->user)
            ->get(route('reclutador.candidatos.documento', [$postulacion, $documento]))
            ->assertOk();
    }

    public function test_cv_pdf_downloads_for_owner(): void
    {
        $alumno = Alumno::factory()->create();

        $this->actingAs($alumno->user)
            ->get(route('alumno.cv.pdf'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
