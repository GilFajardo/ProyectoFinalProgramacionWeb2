<?php

use App\Http\Controllers\Alumno;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistroAlumnoController;
use App\Http\Controllers\Auth\RegistroReclutadorController;
use App\Http\Controllers\Publico;
use App\Http\Controllers\Reclutador;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sitio público institucional
|--------------------------------------------------------------------------
*/
Route::get('/', Publico\InicioController::class)->name('inicio');
Route::get('/vacantes', [Publico\VacanteController::class, 'index'])->name('vacantes.index');
Route::get('/vacantes/{vacante:slug}', [Publico\VacanteController::class, 'show'])->name('vacantes.show');
Route::get('/empresas/{empresa}', Publico\EmpresaController::class)->name('empresas.show');

Route::controller(Publico\PaginaController::class)->group(function (): void {
    Route::get('/quienes-somos', 'quienesSomos')->name('quienes-somos');
    Route::get('/preguntas-frecuentes', 'faq')->name('faq');
    Route::get('/mapa-del-sitio', 'mapaSitio')->name('mapa-sitio');
});

Route::get('/contacto', [Publico\ContactoController::class, 'create'])->name('contacto');
Route::post('/contacto', [Publico\ContactoController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contacto.store');

Route::get('/estadisticas', Publico\EstadisticaController::class)->name('estadisticas');
Route::get('/sitemap.xml', Publico\SitemapController::class)->name('sitemap');

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/registro/alumno', [RegistroAlumnoController::class, 'create'])->name('registro.alumno');
    Route::post('/registro/alumno', [RegistroAlumnoController::class, 'store'])->name('registro.alumno.store');
    Route::get('/registro/reclutador', [RegistroReclutadorController::class, 'create'])->name('registro.reclutador');
    Route::post('/registro/reclutador', [RegistroReclutadorController::class, 'store'])->name('registro.reclutador.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Portal del alumno
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:alumno'])->prefix('alumno')->name('alumno.')->group(function (): void {
    Route::get('/panel', Alumno\PanelController::class)->name('panel');
    Route::get('/perfil', [Alumno\PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [Alumno\PerfilController::class, 'update'])->name('perfil.update');

    Route::get('/cv', [Alumno\CvController::class, 'show'])->name('cv');
    Route::get('/cv/pdf', [Alumno\CvController::class, 'pdf'])->name('cv.pdf');
    Route::resource('cv/experiencias', Alumno\ExperienciaController::class)->only(['store', 'update', 'destroy'])->parameters(['experiencias' => 'experiencia']);
    Route::resource('cv/formaciones', Alumno\FormacionController::class)->only(['store', 'update', 'destroy'])->parameters(['formaciones' => 'formacion']);
    Route::resource('cv/habilidades', Alumno\HabilidadController::class)->only(['store', 'destroy'])->parameters(['habilidades' => 'habilidad']);
    Route::resource('cv/documentos', Alumno\DocumentoController::class)->only(['store', 'show', 'destroy'])->parameters(['documentos' => 'documento']);

    Route::get('/postulaciones', [Alumno\PostulacionController::class, 'index'])->name('postulaciones.index');
    Route::post('/vacantes/{vacante}/postular', [Alumno\PostulacionController::class, 'store'])->name('postulaciones.store');
});

/*
|--------------------------------------------------------------------------
| Portal del reclutador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:reclutador'])->prefix('reclutador')->name('reclutador.')->group(function (): void {
    Route::get('/panel', Reclutador\PanelController::class)->name('panel');
    Route::get('/perfil', [Reclutador\PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [Reclutador\PerfilController::class, 'update'])->name('perfil.update');

    Route::get('/empresa', [Reclutador\EmpresaController::class, 'edit'])->name('empresa.edit');
    Route::post('/empresa', [Reclutador\EmpresaController::class, 'store'])->name('empresa.store');
    Route::put('/empresa', [Reclutador\EmpresaController::class, 'update'])->name('empresa.update');

    Route::resource('vacantes', Reclutador\VacanteController::class);
    Route::get('/candidatos/{postulacion}', [Reclutador\CandidatoController::class, 'show'])->name('candidatos.show');
    Route::patch('/candidatos/{postulacion}', [Reclutador\CandidatoController::class, 'update'])->name('candidatos.update');
    Route::get('/candidatos/{postulacion}/cv', [Reclutador\CandidatoController::class, 'cv'])->name('candidatos.cv');
    Route::get('/candidatos/{postulacion}/documentos/{documento}', [Reclutador\CandidatoController::class, 'documento'])
        ->name('candidatos.documento');

    Route::get('/reportes/{tipo}', Reclutador\ReporteController::class)
        ->whereIn('tipo', ['vacantes', 'postulaciones', 'empresas'])
        ->name('reportes');
});
