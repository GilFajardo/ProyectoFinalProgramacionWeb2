<?php

/*
|--------------------------------------------------------------------------
| Datos institucionales de Vinculación en Línea
|--------------------------------------------------------------------------
|
| Información de identidad, autoría y catálogos usados en el sitio público
| (pie de página, páginas informativas) y en los formularios de registro.
|
*/

return [

    'institucion' => [
        'nombre' => 'Universidad Politécnica de San Luis Potosí',
        'siglas' => 'UPSLP',
        'sistema' => 'Vinculación en Línea',
        'direccion' => 'Urbano Villalón 500, Col. La Ladrillera, C.P. 78363, San Luis Potosí, S.L.P.',
        'telefono' => '444 870 2100',
        'email' => env('MAIL_FROM_ADDRESS', 'vinculacion@upslp.edu.mx'),
        'sitio_web' => 'https://www.upslp.edu.mx',
    ],

    'proyecto' => [
        'autor' => 'José Gilberto Fajardo Zapata',
        'matricula' => '182006',
        'materia' => 'Programación Web II',
        'clave' => 'LC34 / T15A',
        'carrera' => 'Ingeniería en Tecnologías de la Información (ITI)',
        'ciclo' => 'Otoño 2026',
        'profesora' => 'Dra. Roxana G. Herrera',
    ],

    /*
    | Carreras ofertadas, usadas en el registro y perfil del alumno.
    | Ajusta esta lista si cambia la oferta educativa.
    */
    'carreras' => [
        'Ingeniería en Tecnologías de la Información',
        'Ingeniería en Telemática',
        'Ingeniería en Sistemas y Tecnologías Industriales',
        'Ingeniería en Tecnologías de Manufactura',
        'Licenciatura en Administración y Gestión',
        'Licenciatura en Mercadotecnia Internacional',
        'Licenciatura en Relaciones Comerciales Internacionales',
    ],

    /*
    | Días antes de fecha_fin en que una vacante se considera "por vencer".
    */
    'dias_por_vencer' => 7,

];
