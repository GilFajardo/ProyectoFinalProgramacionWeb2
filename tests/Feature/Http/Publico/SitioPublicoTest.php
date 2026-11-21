<?php

namespace Tests\Feature\Http\Publico;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SitioPublicoTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function paginasPublicas(): array
    {
        return [
            'inicio' => ['/'],
            'bolsa de trabajo' => ['/vacantes'],
            'quiénes somos' => ['/quienes-somos'],
            'preguntas frecuentes' => ['/preguntas-frecuentes'],
            'contacto' => ['/contacto'],
            'estadísticas' => ['/estadisticas'],
            'mapa del sitio' => ['/mapa-del-sitio'],
            'login' => ['/login'],
            'registro alumno' => ['/registro/alumno'],
            'registro reclutador' => ['/registro/reclutador'],
        ];
    }

    #[DataProvider('paginasPublicas')]
    public function test_public_page_renders_with_footer_credits(string $url): void
    {
        Vacante::factory()->count(2)->create();

        $this->get($url)
            ->assertOk()
            ->assertSee('José Gilberto Fajardo Zapata')
            ->assertSee('Programación Web II')
            ->assertSee('Dra. Roxana G. Herrera')
            ->assertSee('Universidad Politécnica de San Luis Potosí');
    }

    public function test_bolsa_shows_only_vigentes_by_default_and_all_when_requested(): void
    {
        $vigente = Vacante::factory()->vigente()->create(['titulo' => 'Puesto Vigente Uno']);
        $vencida = Vacante::factory()->vencida()->create(['titulo' => 'Puesto Vencido Dos']);

        $this->get('/vacantes')->assertSee($vigente->titulo)->assertDontSee($vencida->titulo);
        $this->get('/vacantes?vigencia=todas')->assertSee($vigente->titulo)->assertSee($vencida->titulo);
    }

    public function test_bolsa_filters_by_categoria_and_search_term(): void
    {
        $ti = Categoria::factory()->create(['slug' => 'tecnologia-de-la-informacion']);
        $enTi = Vacante::factory()->for($ti)->create(['titulo' => 'Desarrollador Laravel']);
        $otra = Vacante::factory()->create(['titulo' => 'Contador General']);

        $this->get('/vacantes?categoria=tecnologia-de-la-informacion')->assertSee($enTi->titulo)->assertDontSee($otra->titulo);
        $this->get('/vacantes?q=contador')->assertSee($otra->titulo)->assertDontSee($enTi->titulo);
    }

    public function test_bolsa_rejects_invalid_filter_values(): void
    {
        $this->get('/vacantes?vigencia=cualquiera')->assertSessionHasErrors('vigencia');
    }

    public function test_vacante_detail_escapes_html_in_description(): void
    {
        $vacante = Vacante::factory()->create(['descripcion' => 'Texto <script>alert("xss")</script> final']);

        $this->get(route('vacantes.show', $vacante))
            ->assertOk()
            ->assertDontSee('<script>alert("xss")</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_inactive_vacante_is_hidden_from_public(): void
    {
        $vacante = Vacante::factory()->inactiva()->create();

        $this->get(route('vacantes.show', $vacante))->assertNotFound();
    }

    public function test_guest_sees_login_call_to_action_on_vigente_vacante(): void
    {
        $vacante = Vacante::factory()->vigente()->create();

        $this->get(route('vacantes.show', $vacante))->assertSee('Iniciar sesión para postularme');
    }

    public function test_inactive_empresa_profile_is_not_found(): void
    {
        $this->get(route('empresas.show', Empresa::factory()->inactiva()->create()))->assertNotFound();
    }

    public function test_sitemap_lists_vigente_vacantes_as_xml(): void
    {
        $vacante = Vacante::factory()->vigente()->create();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('vacantes.show', $vacante), false);
    }
}
