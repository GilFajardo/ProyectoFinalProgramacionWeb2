<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\View\View;

/**
 * Páginas informativas: Quiénes somos, Preguntas frecuentes y Mapa del sitio.
 */
class PaginaController extends Controller
{
    public function quienesSomos(): View
    {
        return view('publico.quienes-somos');
    }

    public function faq(): View
    {
        return view('publico.faq', [
            'grupos' => [
                'Alumnos' => [
                    '¿Quién puede registrarse como alumno?' => 'Cualquier estudiante o egresado(a) de la UPSLP con matrícula vigente. El registro es gratuito.',
                    '¿Cómo me postulo a una vacante?' => 'Inicia sesión, abre la vacante que te interese y presiona «Postularme». Puedes agregar una carta de presentación opcional.',
                    '¿Puedo postularme dos veces a la misma vacante?' => 'No. El sistema solo permite una postulación por vacante; si ya te postulaste, verás el estatus en lugar del botón.',
                    '¿Qué significan los estatus?' => 'EN PROCESO: la empresa está revisando tu perfil. ACEPTADO: fuiste seleccionado(a) y la empresa te contactará. RECHAZADO: en esta ocasión no continúas en el proceso.',
                    '¿Qué documentos puedo adjuntar a mi CV?' => 'Certificados, constancias, cartas de recomendación, títulos o tu CV en PDF. Se aceptan archivos PDF, JPG o PNG de hasta 5 MB. Solo los ven las empresas a las que te postules.',
                ],
                'Empresas y reclutadores' => [
                    '¿Cómo publico una vacante?' => 'Regístrate como reclutador con los datos de tu empresa. Desde tu portal podrás crear vacantes con su periodo de vigencia.',
                    '¿Cuánto tiempo permanece publicada una vacante?' => 'El tiempo que definas con las fechas de inicio y fin. Al vencer deja de recibir postulaciones, pero puedes extender su vigencia.',
                    '¿Mi empresa ya está registrada, qué hago?' => 'Escríbenos desde el formulario de contacto y te vincularemos con la empresa existente.',
                    '¿Puedo descargar reportes?' => 'Sí. Desde tu portal puedes generar reportes PDF de tus vacantes, postulaciones y la ficha de tu empresa.',
                ],
                'General' => [
                    '¿Tiene costo usar la plataforma?' => 'No. Vinculación en Línea es un servicio gratuito de la UPSLP para su comunidad y las empresas de la región.',
                    '¿Cómo protegen mis datos?' => 'Las contraseñas se almacenan cifradas, los documentos se guardan en un almacenamiento privado y solo las empresas a las que te postulas pueden consultar tu CV.',
                ],
            ],
        ]);
    }

    public function mapaSitio(): View
    {
        return view('publico.mapa-sitio', [
            'categorias' => Categoria::orderBy('nombre')->get(['nombre', 'slug']),
        ]);
    }
}
