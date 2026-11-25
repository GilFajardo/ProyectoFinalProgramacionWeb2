# Vinculación en Línea — Guía de instalación y despliegue

Guía para colaboradores del proyecto: cómo levantar la aplicación en tu equipo, cómo está organizada, cómo correr las pruebas y cómo publicarla en el hosting.

> Proyecto Final · Programación Web II (LC34 / T15A) · UPSLP · Otoño 2026
> Responsable: José Gilberto Fajardo Zapata · Repositorio: <https://github.com/GilFajardo/ProyectoFinalProgramacionWeb2>

**Contenido**

1. [Stack y requisitos](#1-stack-y-requisitos)
2. [Instalación local paso a paso](#2-instalación-local-paso-a-paso)
3. [Base de datos MySQL](#3-base-de-datos-mysql)
4. [Usuarios y datos de prueba](#4-usuarios-y-datos-de-prueba)
5. [Ejecutar la aplicación](#5-ejecutar-la-aplicación)
6. [Reportes PDF](#6-reportes-pdf)
7. [Archivos subidos](#7-archivos-subidos)
8. [Pruebas y calidad de código](#8-pruebas-y-calidad-de-código)
9. [Cómo está organizado el código](#9-cómo-está-organizado-el-código)
10. [Despliegue en el hosting](#10-despliegue-en-el-hosting)
11. [Solución de problemas](#11-solución-de-problemas)

---

## 1. Stack y requisitos

| Componente | Versión usada | Mínimo |
|---|---|---|
| PHP | 8.4 | 8.3 |
| Composer | 2.10 | 2.x |
| Laravel | 13.x | — |
| Filament (panel admin) | 5.x | — |
| MySQL | 8.4 | 8.0 (también funciona con MariaDB 10.6+) |
| Node.js | 24 | 20.19 o 22.12+ (lo exige Vite 8) |
| Tailwind CSS / Alpine.js / Chart.js | 4 / 3 / 4 | — |
| dompdf (`barryvdh/laravel-dompdf`) | 3.1 | — |

**Extensiones de PHP necesarias:** `pdo_mysql`, `mbstring`, `openssl`, `ctype`, `xml`, `dom`, `fileinfo`, `intl`, `gd` (imágenes en los PDF) y `zip` (la usa Composer).

Para comprobar lo que tienes instalado:

```bash
php -v
php -m        # lista de extensiones cargadas
composer -V
node -v
```

> **Windows con WAMP:** el proyecto se desarrolló en `C:\wamp64\www\` con PHP 8.4 y MySQL 8.4 de WAMP. Asegúrate de que el `php` de la terminal sea el de WAMP (`where php`).

---

## 2. Instalación local paso a paso

```bash
# 1. Clonar el repositorio
git clone https://github.com/GilFajardo/ProyectoFinalProgramacionWeb2.git
cd ProyectoFinalProgramacionWeb2

# 2. Dependencias de PHP (también publica los assets de Filament)
composer install

# 3. Archivo de entorno y llave de la aplicación
cp .env.example .env          # en PowerShell: Copy-Item .env.example .env
php artisan key:generate

# 4. Crear la base de datos (ver sección 3) y luego migrar con datos de prueba
php artisan migrate --seed

# 5. Enlace para servir logotipos y fotografías públicas
php artisan storage:link

# 6. Dependencias de frontend y compilación de assets
npm install
npm run build
```

Con esto ya puedes ir a la [sección 5](#5-ejecutar-la-aplicación) para levantar el servidor.

### Variables de `.env` importantes

`.env.example` ya trae los valores del proyecto; normalmente solo ajustas la base de datos.

| Variable | Valor local | Para qué sirve |
|---|---|---|
| `APP_NAME` | `"Vinculación en Línea"` | Nombre en títulos y correos |
| `APP_URL` | `http://localhost:8000` | Genera enlaces absolutos (sitemap, PDF) |
| `APP_LOCALE` / `APP_FAKER_LOCALE` | `es` / `es_MX` | Textos y datos falsos en español |
| `APP_TIMEZONE` | *(opcional)* | Por defecto `America/Mexico_City`; la vigencia de las vacantes depende de la fecha local |
| `DB_*` | ver sección 3 | Conexión a MySQL |
| `DB_ENGINE` | *(opcional)* | Por defecto `InnoDB`; no lo cambies |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | `database` | Usan tablas creadas por las migraciones |

> Nunca subas tu `.env` al repositorio (ya está en `.gitignore`).

---

## 3. Base de datos MySQL

### Crear la base de datos

Desde phpMyAdmin o la terminal de MySQL:

```sql
CREATE DATABASE vinculacion_upslp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Y en tu `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vinculacion_upslp
DB_USERNAME=root
DB_PASSWORD=
```

> En WAMP, MySQL y MariaDB pueden estar activos al mismo tiempo en puertos distintos. Verifica con `php artisan db:show` a cuál te estás conectando.

### Tablas principales

| Tabla | Contenido |
|---|---|
| `users` | Cuentas; la columna `role` vale `admin`, `reclutador` o `alumno` |
| `alumnos`, `reclutadores` | Perfil 1:1 de cada rol; el reclutador apunta a su empresa |
| `empresas` | Empresas (con baja lógica / *soft delete*) |
| `categorias` | Las 12 áreas de conocimiento obligatorias |
| `vacantes` | Ofertas con `fecha_inicio`, `fecha_fin` y `activa` (con *soft delete*) |
| `postulaciones` | **Índice `UNIQUE (vacante_id, alumno_id)`**: un alumno no puede postularse dos veces |
| `cv_experiencias`, `cv_formaciones`, `cv_habilidades`, `cv_documentos` | Secciones del CV estructurado |
| `mensajes_contacto` | Mensajes del formulario público |

El diseño completo (diagrama ER y diccionario de datos) está en [`01-arquitectura.md`](01-arquitectura.md).

### Dos particularidades del MySQL de WAMP

1. **Motor MyISAM por defecto.** WAMP configura MyISAM, que no soporta llaves foráneas y provoca el error *"Specified key was too long"*. El proyecto fuerza InnoDB en `config/database.php`, así que no necesitas hacer nada.
2. **`innodb_ft_max_token_size=10`.** Con este valor, la búsqueda de texto completo (FULLTEXT) ignora palabras de más de 10 letras. La búsqueda del sitio también compara el título con `LIKE`, así que funciona igual. Si quieres que el índice FULLTEXT cubra palabras largas en las descripciones:
   - En `my.ini`, en la sección `[mysqld]`, pon `innodb_ft_max_token_size=84`.
   - Reinicia MySQL.
   - Ejecuta `php artisan migrate:fresh --seed` para reconstruir el índice.

---

## 4. Usuarios y datos de prueba

`php artisan migrate --seed` ejecuta tres seeders (en `database/seeders/`):

| Seeder | Qué crea | ¿Va a producción? |
|---|---|---|
| `CategoriaSeeder` | Las 12 categorías obligatorias (idempotente) | **Sí** |
| `UsuarioPruebaSeeder` | Los 3 usuarios de prueba de la tabla de abajo | Sí, para la revisión del proyecto; después cambia las contraseñas |
| `DatosDemoSeeder` | 8 empresas ficticias de SLP, unas 36 vacantes, 25 alumnos con CV, unas 56 postulaciones y 8 mensajes | Opcional (demostración) |

### Credenciales de prueba

| Rol | Correo | Contraseña | Entra a |
|---|---|---|---|
| Administrador | `admin@upslp.edu.mx` | `Admin123!` | `/admin` (Filament) |
| Reclutador (TecnoSoft Potosí) | `reclutador@empresa.com` | `Reclutador123!` | `/reclutador/panel` |
| Alumna con CV y postulaciones | `alumno@alumnos.upslp.edu.mx` | `Alumno123!` | `/alumno/panel` |

Los usuarios generados por `DatosDemoSeeder` tienen la contraseña `password`.

### Comandos útiles

```bash
php artisan migrate:fresh --seed                    # reinicia la BD con todos los datos de prueba
php artisan db:seed --class=CategoriaSeeder         # solo categorías
php artisan db:seed --class=UsuarioPruebaSeeder     # solo los 3 usuarios de prueba
```

> `migrate:fresh` **borra todas las tablas**. Úsalo solo en tu equipo, nunca en el hosting.

---

## 5. Ejecutar la aplicación

### Opción A: servidor de Laravel (recomendada)

```bash
php artisan serve           # http://localhost:8000
npm run dev                 # en otra terminal, solo si vas a editar CSS/JS (recarga en vivo)
```

`composer run dev` levanta en paralelo los procesos de desarrollo con un solo comando.

### Opción B: WAMP / Apache

Crea un *VirtualHost* cuyo `DocumentRoot` apunte a la carpeta **`public/`** del proyecto, no a la raíz. Ajusta `APP_URL` a esa dirección.

### Mapa rápido de URLs

| Área | URL |
|---|---|
| Sitio público | `/`, `/vacantes`, `/quienes-somos`, `/preguntas-frecuentes`, `/contacto`, `/estadisticas`, `/mapa-del-sitio`, `/sitemap.xml` |
| Acceso | `/login`, `/registro/alumno`, `/registro/reclutador` |
| Portal del alumno | `/alumno/panel`, `/alumno/cv`, `/alumno/postulaciones`, `/alumno/perfil` |
| Portal del reclutador | `/reclutador/panel`, `/reclutador/vacantes`, `/reclutador/empresa`, `/reclutador/perfil` |
| Administración | `/admin` |

Para ver todas las rutas: `php artisan route:list --except-vendor`.

> **Sobre `npm run dev`:** mientras corre, crea el archivo `public/hot`, que le indica a Laravel que tome los estilos del servidor de Vite. Si cierras Vite a la fuerza y el archivo se queda, las páginas aparecen sin estilos. Bórralo o ejecuta `npm run build`.

---

## 6. Reportes PDF

Los PDF se generan con **dompdf** en el servidor; no necesitan Chrome ni servicios externos.

| Reporte | Dónde se genera |
|---|---|
| Vacantes, postulaciones y empresas (con filtros) | Admin: `/admin/reportes` y botones en cada listado |
| Ficha de empresa | Admin: acción en la tabla de empresas · Reclutador: panel |
| Vacantes y postulaciones de su empresa | Reclutador: `/reclutador/reportes/{vacantes\|postulaciones\|empresas}` |
| CV del alumno | Alumno: `/alumno/cv/pdf` · Reclutador y admin: en cada candidato |

**Cómo funciona:**
- Toda la lógica vive en `app/Services/ReporteService.php`.
- Las plantillas están en `resources/views/pdf/` (`layout`, `vacantes`, `postulaciones`, `empresas`, `cv`).
- El reclutador siempre recibe la empresa como parámetro, de modo que nunca ve datos de otras empresas.

**Notas técnicas:**
- **CSS limitado:** dompdf solo entiende CSS 2.1. Usa tablas y estilos sencillos; Tailwind no funciona dentro de los PDF.
- **Fuente:** se usa *DejaVu Sans* para acentos y ñ, con subconjunto de fuente activado. Los PDF de prueba pesaron entre 28 y 43 KB.
- **Numeración "Página X de Y":** la agrega el servicio sobre el *canvas*. **No actives `isPhpEnabled`** en dompdf, porque permitiría ejecutar PHP desde el HTML.
- **Memoria:** los PDF grandes necesitan memoria. Si ves *"Allowed memory size exhausted"*, sube `memory_limit` en `php.ini` (256M es suficiente).
- **Caché de fuentes:** dompdf guarda caché de fuentes en `storage/fonts/`, así que esa carpeta debe tener permiso de escritura en el hosting.

---

## 7. Archivos subidos

| Tipo | Disco | Ruta | Acceso |
|---|---|---|---|
| Logotipos de empresas | `public` | `storage/app/public/logos` | Público vía `/storage/...` (requiere `php artisan storage:link`) |
| Fotografías de alumnos | `public` | `storage/app/public/fotos` | Público |
| Documentos del CV | `local` (privado) | `storage/app/private/cv-documentos/{alumno_id}` | **Solo** a través de controladores con Policy: el alumno dueño, los reclutadores de empresas donde se postuló y el admin |

Validaciones:
- **Documentos del CV:** PDF, JPG o PNG de hasta 5 MB. Se valida el tipo real del archivo (MIME), no solo la extensión.
- **Logotipos:** hasta 1 MB.
- **Fotografías:** hasta 2 MB.

---

## 8. Pruebas y calidad de código

```bash
php artisan test --compact                                  # suite completa (121 pruebas)
php artisan test --compact tests/Feature/Http/Alumno        # una carpeta
php artisan test --compact --filter=postula                 # por nombre
vendor/bin/pint --dirty                                     # formatea los archivos PHP modificados
```

**Cómo están configuradas las pruebas:**
- Corren en **SQLite en memoria** (configurado en `phpunit.xml`), así que no tocan tu base de MySQL.
- `phpunit.xml` sube `memory_limit` a 512M, porque con Xdebug activo (WAMP lo trae encendido) la generación de PDF consume mucha memoria.
- Si las pruebas van lentas, desactiva Xdebug solo para esa ejecución: `XDEBUG_MODE=off php artisan test` (en PowerShell: `$env:XDEBUG_MODE='off'; php artisan test`).

| Carpeta | Qué cubre |
|---|---|
| `tests/Feature/Services` | Postulación única (servicio + índice UNIQUE) |
| `tests/Feature/Models` | Vigencia, filtros y búsqueda de vacantes |
| `tests/Feature/Policies` | Permisos por rol y por empresa |
| `tests/Feature/Filament` | Panel admin: acceso, formularios, acciones, PDF |
| `tests/Feature/Http` | Sitio público, autenticación, portales, CV, documentos, reportes, aislamiento entre empresas |

**Antes de abrir un pull request:** corre `vendor/bin/pint --dirty` y `php artisan test --compact`; ambos deben salir limpios.

---

## 9. Cómo está organizado el código

```
app/
├── Enums/            Rol, EstatusPostulacion, Modalidad, TipoContrato, NivelHabilidad, ...
├── Filament/         Panel admin: Resources, Pages (Reportes), Widgets, Actions reutilizables
├── Http/
│   ├── Controllers/  Publico/, Auth/, Alumno/, Reclutador/  (controladores delgados)
│   ├── Middleware/   EnsureRole (alias "role:alumno", "role:reclutador")
│   └── Requests/     Toda la validación (Form Requests)
├── Models/           Eloquent; Vacante concentra vigencia y búsqueda (scopes)
├── Policies/         VacantePolicy, PostulacionPolicy, EmpresaPolicy, CvDocumentoPolicy
└── Services/         PostulacionService, ReporteService (PDF), EstadisticaService
config/upslp.php      Datos institucionales, créditos del pie de página y catálogo de carreras
lang/es/              Mensajes de validación en español
resources/
├── css/app.css       Tema Tailwind 4 (colores "marca" y "acento", utilidades btn-*, campo, tarjeta)
├── js/               app.js (Alpine), validacion.js (validación en cliente), estadisticas.js (Chart.js)
└── views/            components/, publico/, auth/, alumno/, reclutador/, pdf/, errors/
```

### Reglas del proyecto

- **Roles:** el rol vive en `users.role`, se protege con el middleware `role:` y se refina con Policies; Filament usa las mismas Policies. El administrador tiene acceso total mediante `before()`.
- **Postulación única:** se controla en cuatro capas:
  1. botón deshabilitado en la interfaz;
  2. `StorePostulacionRequest` (VacantePolicy::postular);
  3. `PostulacionService` (transacción);
  4. índice `UNIQUE` en la base de datos.

  No elimines ninguna.
- **Vigencia:** una vacante está vigente si `activa = true` y `fecha_inicio <= hoy <= fecha_fin`. Usa siempre `Vacante::vigentes()` o `->conVigencia($estado)` en lugar de repetir la condición.
- **Seguridad:**
  - En Blade, imprime con `{{ }}`. Para texto con saltos de línea usa `{!! nl2br(e($texto)) !!}`.
  - Nunca tomes `empresa_id`, `alumno_id` ni `role` del formulario; obtenlos del usuario autenticado.
  - Todo formulario lleva `@csrf`.
- **Datos institucionales:** el pie de página y los créditos salen de `config/upslp.php`. Cámbialos ahí, no en las vistas.
- **Generadores:** crea archivos con `php artisan make:...` (`--no-interaction`) y sigue el estilo de los archivos vecinos: atributos `#[Fillable]` y `#[Table]`, tipos de retorno y PHPDoc.
- **Laravel Boost:** el proyecto incluye Laravel Boost (guías para asistentes de IA y servidor MCP); sus reglas están en `CLAUDE.md` / `AGENTS.md`.

### Ejemplo: agregar una página pública

1. Agrega la ruta con nombre en `routes/web.php`.
2. Crea el método en `app/Http/Controllers/Publico/PaginaController.php`.
3. Crea la vista en `resources/views/publico/` usando `<x-layouts.app title="...">` y `<x-encabezado>`.
4. Agrega el enlace en `components/navbar.blade.php`, `components/footer.blade.php` y `publico/mapa-sitio.blade.php`.
5. Agrega un caso al proveedor de datos de `tests/Feature/Http/Publico/SitioPublicoTest.php`.

---

## 10. Despliegue en el hosting

La aplicación es Laravel estándar: funciona en un VPS (Ubuntu + Nginx/Apache), en hosting compartido con PHP 8.3+, MySQL y acceso SSH, o en plataformas como Laravel Cloud o Forge. **No necesita** cron, colas en segundo plano, Redis ni servicios externos.

### 10.1 Preparar el servidor

- [ ] PHP 8.3+ con las extensiones de la [sección 1](#1-stack-y-requisitos) y `memory_limit` de al menos 256M.
- [ ] Base de datos MySQL 8 / MariaDB 10.6+ creada con `utf8mb4_unicode_ci`, y un usuario **dedicado** (no `root`) con permisos solo sobre esa base.
- [ ] Composer disponible en el servidor. Node no es necesario si compilas los assets en tu equipo.
- [ ] Certificado HTTPS (Let's Encrypt o el que ofrezca el hosting).

### 10.2 `.env` de producción

```env
APP_NAME="Vinculación en Línea"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.mx
APP_LOCALE=es
APP_FALLBACK_LOCALE=es

LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_bd
DB_USERNAME=usuario_bd
DB_PASSWORD=contraseña_segura

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp            # o "log" si no se enviarán correos
MAIL_FROM_ADDRESS="vinculacion@upslp.edu.mx"
```

> **`APP_DEBUG=false` es obligatorio en producción.** Con `true`, un error expone credenciales y código fuente.

### 10.3 Primer despliegue

```bash
git clone https://github.com/GilFajardo/ProyectoFinalProgramacionWeb2.git vinculacion
cd vinculacion

composer install --no-dev --optimize-autoloader
cp .env.example .env            # y edítalo con los valores de producción
php artisan key:generate

php artisan migrate --force
php artisan db:seed --class=CategoriaSeeder --force
php artisan db:seed --class=UsuarioPruebaSeeder --force
# Opcional, solo si se quiere mostrar con datos de demostración:
# php artisan db:seed --class=DatosDemoSeeder --force

php artisan storage:link

# Assets: si hay Node en el servidor
npm ci && npm run build
# Si no hay Node: ejecuta "npm run build" en tu equipo y sube la carpeta public/build/

php artisan optimize            # cachea config, rutas, eventos y vistas
php artisan filament:optimize   # cachea componentes e íconos de Filament
```

### 10.4 Servidor web y permisos

- [ ] El **document root** debe apuntar a `vinculacion/public`. En hosting compartido sin esa opción, configura el dominio o subdominio hacia esa carpeta. **No** muevas `index.php` fuera de `public/`.
- [ ] Permisos de escritura para el usuario del servidor web en `storage/` y `bootstrap/cache/`:
  ```bash
  chown -R www-data:www-data storage bootstrap/cache
  chmod -R 775 storage bootstrap/cache
  ```
- [ ] Ni `.env` ni la carpeta del proyecto deben quedar accesibles desde la web (el document root en `public/` lo garantiza).
- [ ] Nginx: usa el bloque `try_files $uri $uri/ /index.php?$query_string;`. Apache: el `public/.htaccess` incluido ya hace la reescritura (requiere `mod_rewrite`).

### 10.5 Verificación después de publicar

- [ ] `/`, `/vacantes` y `/estadisticas` cargan con estilos y gráficas.
- [ ] Puedes iniciar sesión con los tres usuarios de prueba y cada uno llega a su panel.
- [ ] Un alumno se postula y un segundo intento muestra "Ya te postulaste a esta vacante".
- [ ] Se descargan los PDF (reportes del admin, reportes del reclutador y CV).
- [ ] Un logotipo subido se ve en el perfil público de la empresa (confirma que `storage:link` funcionó).
- [ ] Una URL inexistente muestra la página 404 del sitio, sin detalles técnicos.
- [ ] `https://tu-dominio.mx/sitemap.xml` lista las vacantes con el dominio correcto.

### 10.6 Seguridad tras la revisión del proyecto

- [ ] Cambia las contraseñas de los tres usuarios de prueba desde `/admin/usuarios`, o desactívalos.
- [ ] Si cargaste `DatosDemoSeeder`, elimina las empresas y los alumnos ficticios antes de usarlo con datos reales.
- [ ] Revisa en `config/upslp.php` la dirección, el teléfono y el correo institucionales.

### 10.7 Actualizar el sitio (despliegues posteriores)

```bash
php artisan down                         # modo mantenimiento
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build                  # o sube public/build/ compilado
php artisan optimize:clear && php artisan optimize && php artisan filament:optimize
php artisan up
```

> Una migración ya ejecutada en producción **no se edita**. Cualquier cambio de esquema va en una migración nueva (`php artisan make:migration ...`).

### 10.8 Despliegue con Docker y Coolify (VPS)

El repositorio incluye un `Dockerfile` (PHP 8.4 + Nginx con la imagen `serversideup/php`, puerto **8080**). La imagen compila los assets y, en cada arranque, ejecuta `storage:link`, `migrate --force`, `optimize`, el `CategoriaSeeder` y `filament:optimize` (ver `docker/entrypoint.d/`).

1. En Coolify, crea en el mismo proyecto un recurso **MySQL 8**. Anota el *host interno*, la base, el usuario y la contraseña.
2. Agrega la aplicación desde el repositorio de GitHub con *Build Pack* **Dockerfile** y *Ports Exposes* **8080**. Asigna el dominio con `https://`.
3. En *Environment Variables* define al menos: `APP_KEY` (genera una con `php artisan key:generate --show`), `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `APP_LOCALE=es`, `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT=3306`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `SESSION_DRIVER=database`, `SESSION_SECURE_COOKIE=true`, `CACHE_STORE=database`, `QUEUE_CONNECTION=sync` y `LOG_CHANNEL=stderr`.
4. En *Persistent Storage* agrega un volumen con destino `/var/www/html/storage/app` para no perder logotipos, fotografías ni documentos del CV en cada despliegue.
5. Despliega. Para crear los usuarios de prueba, abre la *Terminal* del contenedor en Coolify y ejecuta `php artisan db:seed --class=UsuarioPruebaSeeder --force`.

Para probar la imagen en tu equipo: `docker build -t vinculacion .`

---

## 11. Solución de problemas

| Síntoma | Causa probable | Solución |
|---|---|---|
| `Specified key was too long; max key length is 1000 bytes` | Tablas en MyISAM | Verifica que `config/database.php` tenga `'engine' => env('DB_ENGINE', 'InnoDB')` y que no tengas `DB_ENGINE=MyISAM` en `.env` |
| `SQLSTATE[HY000] [2002]` o `Access denied` | MySQL apagado o credenciales incorrectas | Enciende MySQL en WAMP y revisa `DB_*`; `php artisan db:show` |
| Páginas sin estilos o *"Unable to locate file in Vite manifest"* | No se compilaron los assets o existe un `public/hot` viejo | `npm run build` y borra `public/hot` |
| El panel `/admin` sin estilos | Faltan los assets de Filament | `php artisan filament:upgrade` (se ejecuta solo con `composer install`) |
| Logotipos y fotos dan 404 | Falta el enlace simbólico | `php artisan storage:link` (en Windows, ejecuta la terminal como administrador si falla) |
| 419 *"La sesión expiró"* al enviar un formulario | Token CSRF vencido o `APP_URL` / dominio distinto | Recarga la página; en producción revisa `APP_URL` y `SESSION_DOMAIN` |
| *"Allowed memory size exhausted"* al generar PDF o correr pruebas | Límite de memoria bajo (más aún con Xdebug) | Sube `memory_limit` en `php.ini` o desactiva Xdebug |
| La búsqueda no encuentra palabras largas en descripciones | `innodb_ft_max_token_size` bajo (WAMP) | Ver [sección 3](#dos-particularidades-del-mysql-de-wamp) |
| Las vacantes aparecen vencidas un día antes o después | Zona horaria | Usa `APP_TIMEZONE=America/Mexico_City` (es el valor por defecto) |
| Cambié `.env` o una ruta en producción y no se refleja | Configuración en caché | `php artisan optimize:clear && php artisan optimize` |
| Error 500 sin detalle en producción | `APP_DEBUG=false` (correcto) | Revisa `storage/logs/laravel.log` |

Para ver la información del entorno: `php artisan about`.
