<?php

namespace Database\Seeders;

use App\Enums\NivelFormacion;
use App\Enums\NivelHabilidad;
use App\Enums\Rol;
use App\Models\Alumno;
use App\Models\Categoria;
use App\Models\CvExperiencia;
use App\Models\CvFormacion;
use App\Models\CvHabilidad;
use App\Models\Empresa;
use App\Models\Reclutador;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Usuarios de prueba con credenciales conocidas para la entrega del proyecto:
 * un administrador, un reclutador (con empresa) y un alumno (con CV).
 */
class UsuarioPruebaSeeder extends Seeder
{
    public const string EMAIL_ADMIN = 'admin@upslp.edu.mx';

    public const string EMAIL_RECLUTADOR = 'reclutador@empresa.com';

    public const string EMAIL_ALUMNO = 'alumno@alumnos.upslp.edu.mx';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->crearAdministrador();
        $this->crearReclutador();
        $this->crearAlumno();
    }

    private function crearAdministrador(): void
    {
        User::updateOrCreate(['email' => self::EMAIL_ADMIN], [
            'name' => 'Administrador Vinculación UPSLP',
            'password' => 'Admin123!',
            'role' => Rol::Admin,
            'activo' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function crearReclutador(): void
    {
        $empresa = Empresa::updateOrCreate(['rfc' => 'TSP150310AB1'], [
            'nombre' => 'TecnoSoft Potosí',
            'razon_social' => 'TecnoSoft Potosí S.A. de C.V.',
            'sector' => 'Tecnología',
            'descripcion' => 'Empresa potosina dedicada al desarrollo de software a la medida, aplicaciones web y soluciones en la nube para la industria del Bajío.',
            'sitio_web' => 'https://www.tecnosoftpotosi.com.mx',
            'email_contacto' => 'talento@tecnosoftpotosi.com.mx',
            'telefono' => '444 812 3040',
            'direccion' => 'Av. Venustiano Carranza 2425, Col. Polanco',
            'municipio' => 'San Luis Potosí',
            'estado' => 'San Luis Potosí',
            'verificada' => true,
            'activa' => true,
        ]);

        $usuario = User::updateOrCreate(['email' => self::EMAIL_RECLUTADOR], [
            'name' => 'Mariana Torres Rivera',
            'password' => 'Reclutador123!',
            'role' => Rol::Reclutador,
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        Reclutador::updateOrCreate(['user_id' => $usuario->id], [
            'empresa_id' => $empresa->id,
            'puesto' => 'Coordinadora de Atracción de Talento',
            'telefono' => '444 812 3041',
        ]);
    }

    private function crearAlumno(): void
    {
        $usuario = User::updateOrCreate(['email' => self::EMAIL_ALUMNO], [
            'name' => 'Ana Sofía Martínez López',
            'password' => 'Alumno123!',
            'role' => Rol::Alumno,
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        $alumno = Alumno::updateOrCreate(['user_id' => $usuario->id], [
            'matricula' => '190001',
            'carrera' => 'Ingeniería en Tecnologías de la Información',
            'cuatrimestre' => 9,
            'telefono' => '444 155 2233',
            'fecha_nacimiento' => '2003-05-14',
            'ciudad' => 'San Luis Potosí',
            'resumen' => 'Estudiante de ITI con experiencia en desarrollo web con PHP y Laravel, bases de datos MySQL y soporte técnico. Busco mi estadía profesional en un equipo de desarrollo.',
            'linkedin_url' => 'https://www.linkedin.com/in/ejemplo-ana-martinez',
        ]);

        // El CV de prueba se regenera completo para que el seeder sea idempotente.
        $alumno->experiencias()->delete();
        $alumno->formaciones()->delete();
        $alumno->habilidades()->delete();

        CvExperiencia::create([
            'alumno_id' => $alumno->id,
            'empresa' => 'Soluciones Digitales del Centro',
            'puesto' => 'Practicante de Desarrollo Web',
            'descripcion' => 'Mantenimiento de un sistema de inventarios en Laravel y creación de reportes con MySQL.',
            'fecha_inicio' => '2025-01-13',
            'fecha_fin' => '2025-04-25',
            'actual' => false,
        ]);

        CvExperiencia::create([
            'alumno_id' => $alumno->id,
            'empresa' => 'Centro de Cómputo UPSLP',
            'puesto' => 'Auxiliar de Soporte Técnico (servicio social)',
            'descripcion' => 'Atención a usuarios, configuración de equipos y apoyo en la red del laboratorio.',
            'fecha_inicio' => '2024-05-06',
            'fecha_fin' => '2024-11-29',
            'actual' => false,
        ]);

        CvFormacion::create([
            'alumno_id' => $alumno->id,
            'institucion' => 'Universidad Politécnica de San Luis Potosí',
            'titulo' => 'Ingeniería en Tecnologías de la Información',
            'nivel' => NivelFormacion::Licenciatura,
            'fecha_inicio' => '2022-09-01',
            'en_curso' => true,
        ]);

        CvFormacion::create([
            'alumno_id' => $alumno->id,
            'institucion' => 'Cisco Networking Academy',
            'titulo' => 'CCNA: Introduction to Networks',
            'nivel' => NivelFormacion::Certificacion,
            'fecha_inicio' => '2024-01-15',
            'fecha_fin' => '2024-04-30',
        ]);

        $ti = Categoria::where('slug', 'tecnologia-de-la-informacion')->value('id');
        $comunicaciones = Categoria::where('slug', 'comunicaciones')->value('id');

        foreach ([
            ['PHP / Laravel', NivelHabilidad::Avanzado, $ti],
            ['MySQL', NivelHabilidad::Avanzado, $ti],
            ['JavaScript', NivelHabilidad::Intermedio, $ti],
            ['Tailwind CSS', NivelHabilidad::Intermedio, $ti],
            ['Redes Cisco', NivelHabilidad::Basico, $ti],
            ['Comunicación efectiva', NivelHabilidad::Avanzado, $comunicaciones],
        ] as [$nombre, $nivel, $categoriaId]) {
            CvHabilidad::create([
                'alumno_id' => $alumno->id,
                'categoria_id' => $categoriaId,
                'nombre' => $nombre,
                'nivel' => $nivel,
            ]);
        }
    }
}
