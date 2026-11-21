# Vinculación en Línea — Caso de prueba integral (extremo a extremo)

**ID:** CP-01 · **Tipo:** prueba funcional manual de extremo a extremo
**Objetivo:** recorrer el flujo completo de la plataforma con los tres roles (administrador, reclutador y alumno) y verificar todos los requisitos del proyecto, incluidas reglas de negocio, seguridad y diseño responsivo.
**Duración estimada:** 45 a 60 minutos.

> Proyecto Final · Programación Web II · UPSLP · Dra. Roxana G. Herrera
> Desarrollador: José Gilberto Fajardo Zapata (182006)

---

## 0. Preparación

### 0.1 Entorno

```bash
php artisan migrate:fresh --seed   # base de datos limpia con datos de demostración
php artisan storage:link
npm run build
php artisan serve                  # http://localhost:8000
```

**Recomendación:** usa **dos navegadores** (o una ventana normal y una de incógnito) para tener dos roles con sesión al mismo tiempo, por ejemplo el reclutador en una y el alumno en otra.

### 0.2 Datos que ya existen (seeders)

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@upslp.edu.mx` | `Admin123!` |
| Reclutador de **TecnoSoft Potosí** | `reclutador@empresa.com` | `Reclutador123!` |
| Alumna **Ana Sofía Martínez López** | `alumno@alumnos.upslp.edu.mx` | `Alumno123!` |

Vacantes de TecnoSoft Potosí que se usan en la prueba:

| Vacante | Estado de vigencia |
|---|---|
| Desarrollador(a) Web PHP/Laravel | Vigente |
| Soporte Técnico TI | Por vencer (cierra en menos de 7 días) |
| QA Tester | Programada (aún no inicia) |
| Administrador(a) de Redes | Vencida |

### 0.3 Datos nuevos que se crearán durante la prueba

| Dato | Valor |
|---|---|
| Reclutador nuevo | **Ricardo Méndez Soto** · `ricardo@huasteca.mx` · `Prueba123!` |
| Empresa nueva | **Comercializadora Huasteca** · RFC `CHU200315AB1` · sector Comercio |
| Vacante nueva | **Analista de Inventarios Jr.** · Contabilidad y Administración |
| Alumno nuevo | **Luis Fernando Torres Ruiz** · `luis.torres@alumnos.upslp.edu.mx` · matrícula `213456` · ITI · `Prueba123!` |

Cómo leer las tablas de pasos: **Acción** es lo que haces, **Resultado esperado** es lo que debe ocurrir y **✓** es la casilla para marcar si se cumplió.

---

## Fase 1. Sitio público (sin iniciar sesión)

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 1.1 | Abre `http://localhost:8000` | Se ve la identidad "Vinculación en Línea · UPSLP", el banner con carrusel (cambia solo cada 6 s y se pausa al pasar el cursor), el buscador, los indicadores, las 12 áreas de conocimiento con su número de vacantes y las vacantes recientes | ☐ |
| 1.2 | Baja al pie de página | Aparecen el desarrollador **José Gilberto Fajardo Zapata (182006)**, la materia **Programación Web II · LC34 / T15A**, la profesora **Dra. Roxana G. Herrera**, la institución **UPSLP** y la navegación de pie de página | ☐ |
| 1.3 | Recorre el menú: Bolsa de trabajo, Quiénes somos, Estadísticas, Preguntas frecuentes y Contacto | Cada página carga con su encabezado y migas de pan; el menú marca la sección activa | ☐ |
| 1.4 | En *Preguntas frecuentes*, haz clic en varias preguntas | El acordeón abre y cierra cada respuesta | ☐ |
| 1.5 | En *Estadísticas* | Se dibujan 3 gráficas (barras por área, dona por estatus y línea por mes) y se pueden ver los mismos datos en tabla | ☐ |
| 1.6 | Abre *Mapa del sitio* y luego `/sitemap.xml` | El mapa lista todas las secciones; el XML incluye las URLs de las vacantes vigentes | ☐ |
| 1.7 | Escribe una URL inexistente, por ejemplo `/pagina-falsa` | Aparece la página 404 con el diseño del sitio, sin detalles técnicos | ☐ |

### Búsqueda y filtros

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 1.8 | En el buscador del inicio escribe `desarrollador` y busca | La bolsa de trabajo muestra "Desarrollador(a) Web PHP/Laravel" | ☐ |
| 1.9 | Busca `TecnoSoft` | Aparecen las vacantes **vigentes** de esa empresa (la búsqueda también revisa el nombre de la empresa) | ☐ |
| 1.10 | Limpia los filtros. En **Área de conocimiento** elige *Tecnología de la Información* | Solo aparecen vacantes de esa área; el número entre paréntesis coincide con el resultado | ☐ |
| 1.11 | En **Vigencia** elige *Por vencer (7 días)* | Solo aparecen vacantes con la insignia ámbar "Por vencer · cierra en…" | ☐ |
| 1.12 | Elige *Vencidas* y luego *Todas* | Con *Vencidas* aparece "Administrador(a) de Redes" (insignia roja); con *Todas* se mezclan todos los estados | ☐ |
| 1.13 | Combina **Modalidad** = Remoto y **Orden** = Mayor salario | Los resultados respetan ambos criterios y se pueden recorrer con la paginación | ☐ |
| 1.14 | **Seguridad (SQLi):** busca `' OR '1'='1` | La página responde normalmente con "0 vacantes encontradas"; no hay errores de SQL | ☐ |

### Detalle de vacante como visitante

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 1.15 | Abre "Desarrollador(a) Web PHP/Laravel" | Se ven la descripción, los requisitos, las prestaciones, el salario, la vigencia y la tarjeta de la empresa; en lugar del formulario aparece el botón **"Iniciar sesión para postularme"** | ☐ |
| 1.16 | Abre "QA Tester" (filtro *Próximas a publicarse*) | Dice "Postulaciones cerradas · Esta vacante abre el …" | ☐ |
| 1.17 | Haz clic en "Ver perfil y vacantes" de la empresa | Se abre el perfil público de TecnoSoft Potosí con sus vacantes vigentes | ☐ |

### Contacto

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 1.18 | Envía el formulario de contacto vacío | Aparecen mensajes en español junto a cada campo, sin recargar la página | ☐ |
| 1.19 | Escribe el correo `correo-invalido` y un mensaje de 5 caracteres | Avisos: "Escribe un correo electrónico válido" y "Escribe al menos 20 caracteres"; el contador de caracteres se actualiza | ☐ |
| 1.20 | Llena el formulario correctamente (asunto: *Prueba de contacto CP-01*) y envíalo | Mensaje verde "Gracias por escribirnos…"; el mensaje quedará visible para el admin (paso 6.10) | ☐ |

### Diseño responsivo

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 1.21 | Abre las herramientas de desarrollador (F12), activa la vista móvil (por ejemplo, iPhone 12) y recorre el inicio y la bolsa de trabajo | Aparece el botón de menú ☰ que despliega la navegación; los filtros se ocultan tras "Mostrar filtros"; las tarjetas quedan en una sola columna y nada se desborda | ☐ |

---

## Fase 2. Registro y acceso

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 2.1 | Ve a `/login` e intenta entrar con `admin@upslp.edu.mx` y una contraseña incorrecta | "Estas credenciales no coinciden con nuestros registros." | ☐ |
| 2.2 | **Límite de intentos:** repite el intento fallido hasta completar 5 | Al sexto intento: "Demasiados intentos de acceso. Intenta de nuevo en N segundos." Espera un minuto antes de continuar | ☐ |
| 2.3 | Entra a `/alumno/panel` sin sesión | Te redirige a `/login` | ☐ |

---

## Fase 3. Reclutador nuevo: registro, empresa y vacantes

**Navegador A.**

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 3.1 | Ve a `/registro/reclutador`. Escribe el RFC `ABC` y contraseñas que no coinciden | Avisos: "El RFC no tiene un formato válido" y "La confirmación no coincide" | ☐ |
| 3.2 | Regístrate con los datos de la sección 0.3 (Ricardo Méndez, Comercializadora Huasteca, `CHU200315AB1`) y acepta el compromiso de uso de datos | Se inicia la sesión automáticamente y te lleva a **Mi empresa** con el mensaje "Cuenta creada…" | ☐ |
| 3.3 | Completa la descripción, el correo de contacto y el teléfono, y sube un logotipo (PNG o JPG de menos de 1 MB). Guarda | "Los datos de tu empresa se actualizaron."; el logotipo se ve en el formulario | ☐ |
| 3.4 | Haz clic en "Ver perfil público" | El perfil muestra el logotipo y la descripción | ☐ |

### Alta de vacante (con validaciones)

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 3.5 | Ve a **Mis vacantes → + Publicar vacante**. Pon una fecha de fin anterior a la de inicio, salario mínimo 15000 y máximo 9000 | Avisos en el cliente: "Debe ser posterior o igual al inicio de vigencia" y "Debe ser mayor o igual que el salario mínimo" | ☐ |
| 3.6 | Llena la vacante: título **Analista de Inventarios Jr.**, área *Contabilidad y Administración*, *Tiempo completo*, *Presencial*, 2 plazas, salario de 12000 a 15000, inicio **hoy** y fin **dentro de 30 días**. En la descripción escribe un texto normal y al final `<script>alert('xss')</script>` | Mensaje "Vacante publicada correctamente."; te lleva al detalle de la vacante con 0 candidatos | ☐ |
| 3.7 | **Seguridad (XSS):** haz clic en "Ver publicación" | La descripción muestra literalmente `<script>alert('xss')</script>` como texto; **no** aparece ninguna alerta | ☐ |
| 3.8 | Vuelve a **Mis vacantes** | La vacante aparece con la insignia verde "Vigente" | ☐ |

### Aislamiento entre empresas

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 3.9 | Con la sesión de Ricardo, escribe en la barra la dirección `/reclutador/vacantes/1/edit` (vacante 1 = TecnoSoft) | Página **403 "Acceso no permitido"** | ☐ |
| 3.10 | Intenta abrir `/alumno/panel` y `/admin` | 403 en ambas | ☐ |

---

## Fase 4. Alumno nuevo: registro, CV y postulación

**Navegador B.**

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 4.1 | En `/registro/alumno` escribe la matrícula `12AB` | Aviso: "La matrícula debe tener entre 6 y 10 dígitos." | ☐ |
| 4.2 | Intenta registrarte con la matrícula `190001` (la de Ana Sofía) | Error del servidor: la matrícula ya está registrada | ☐ |
| 4.3 | Regístrate con los datos de Luis Fernando (sección 0.3) | Se inicia la sesión y te lleva a **Mi CV**: "¡Bienvenido(a)! Completa tu CV…" | ☐ |

### Construcción del CV

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 4.4 | Ve a **Mi perfil**: teléfono `444 123 4567`, perfil profesional (observa el contador de caracteres) y una fotografía. Guarda | "Tu perfil se actualizó correctamente."; en Mi CV se ven la foto y el resumen | ☐ |
| 4.5 | **Experiencia → + Agregar:** puesto *Practicante de Almacén*, empresa *Distribuidora del Centro*, inicio `2025-01-13`, **sin** fecha de fin y **sin** marcar "Trabajo aquí actualmente". Guarda | Error: "Indica la fecha de fin o marca «Trabajo aquí actualmente»"; el formulario sigue abierto con tus datos | ☐ |
| 4.6 | Marca "Trabajo aquí actualmente" y guarda | El campo de fecha de fin se desactiva; la experiencia aparece como "ene 2025 – Actual" | ☐ |
| 4.7 | Haz clic en **Editar** sobre esa experiencia, cambia el puesto a *Auxiliar de Inventarios* y guarda | El cambio se refleja en la lista | ☐ |
| 4.8 | **Formación → + Agregar:** *Ingeniería en Tecnologías de la Información*, UPSLP, nivel Licenciatura, marca "En curso" | Aparece "… · En curso" | ☐ |
| 4.9 | **Habilidades:** agrega *Excel* (Contabilidad y Administración, Avanzado), *SQL* (Tecnología de la Información, Intermedio) y *Trabajo en equipo* (General, Avanzado) | Se agrupan por área con una barra de nivel | ☐ |
| 4.10 | Intenta agregar otra vez *Excel* | "Ya agregaste esta habilidad a tu CV." | ☐ |
| 4.11 | Haz clic en **Descargar CV en PDF** y guarda el archivo | Se descarga `cv-213456.pdf` con todas las secciones y los créditos en el pie | ☐ |
| 4.12 | **Documentos:** intenta subir un archivo `.txt` o `.exe` | Error: el archivo debe ser PDF, JPG o PNG | ☐ |
| 4.13 | Sube el PDF del paso 4.11 con el nombre *Constancia de estudios* y tipo *Constancia* | Aparece en la lista con su tamaño; "Descargar" lo baja con el nombre `constancia-de-estudios.pdf` | ☐ |
| 4.14 | Ve a **Resumen** (panel) | El avance del CV marca **100 %** (perfil, experiencia, formación, 3 habilidades y documentos) | ☐ |

### Postulación y regla de unicidad

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 4.15 | En la bolsa de trabajo busca `inventarios` y abre **Analista de Inventarios Jr.** | Aparece el formulario "Postúlate a esta vacante" con la carta de presentación opcional | ☐ |
| 4.16 | Escribe una carta breve y presiona **Postularme** (prueba hacer doble clic) | El botón cambia a "Enviando…" y se desactiva; mensaje "¡Listo! Te postulaste…"; la tarjeta ahora dice **"Ya te postulaste"** con el estatus **EN PROCESO** y el botón deshabilitado "✓ Postulación enviada" | ☐ |
| 4.17 | **Regla de unicidad:** recarga la página | Sigue el botón deshabilitado; no existe forma de volver a postularse desde la interfaz | ☐ |
| 4.18 | **Unicidad en el servidor:** abre las herramientas de desarrollador (F12) → Consola y ejecuta el código de abajo | La página indica **"Ya te postulaste a esta vacante."**; en *Mis postulaciones* hay una sola postulación a esa vacante | ☐ |
| 4.19 | Postúlate también a "Desarrollador(a) Web PHP/Laravel" (TecnoSoft) | Postulación exitosa; un alumno sí puede postularse a vacantes distintas | ☐ |
| 4.20 | Busca "Administrador(a) de Redes" (vigencia *Vencidas*) y ábrela | "Postulaciones cerradas"; no hay botón para postularse | ☐ |
| 4.21 | Ve a **Mis postulaciones** | Se listan 2 postulaciones en EN PROCESO; los filtros por estatus funcionan | ☐ |

Código para el paso 4.18. Reenvía la postulación directamente al servidor, sin pasar por el botón:

```js
const f = document.createElement('form');
f.method = 'POST';
f.action = location.pathname.replace(/vacantes\/.*/, '') + 'alumno/vacantes/' + prompt('ID de la vacante (ver en el panel admin)') + '/postular';
f.innerHTML = `<input name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content ?? document.querySelector('input[name=_token]')?.value}">`;
document.body.append(f);
f.submit();
```

> **Alternativa sin consola para 4.18:** en la pestaña Red (Network) de F12, haz clic derecho sobre la petición `POST .../postular` del paso 4.16 y elige *"Reenviar"* (*Resend* / *Replay XHR*).

---

## Fase 5. Reclutador: evaluación de candidatos y reportes

**Navegador A (Ricardo).**

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 5.1 | Ve a **Resumen** | Indicadores: 1 vacante vigente y 1 postulación; Luis Fernando aparece en "Candidatos por revisar" | ☐ |
| 5.2 | Abre **Mis vacantes → Candidatos** de *Analista de Inventarios Jr.* | Aparece la tarjeta de Luis Fernando con sus habilidades y la insignia EN PROCESO | ☐ |
| 5.3 | Haz clic en **Revisar perfil** | Se ven el perfil, la carta de presentación, la experiencia, la formación, las habilidades por área y los documentos | ☐ |
| 5.4 | Descarga *Constancia de estudios* e **Imprimir CV (PDF)** | El documento se descarga; el CV se abre en una pestaña nueva, listo para imprimir | ☐ |
| 5.5 | En **Evaluación** elige **ACEPTADO**, escribe el comentario *"¡Felicidades! Te contactaremos el lunes."* y guarda | "Estatus actualizado a ACEPTADO."; aparece "Último cambio: hace unos segundos" | ☐ |
| 5.6 | Vuelve a la vacante y usa los filtros por estatus (Todos / EN PROCESO / ACEPTADO / RECHAZADO) | Los contadores y la lista cambian según el filtro | ☐ |
| 5.7 | **Seguridad:** ve a `/reclutador/candidatos/1` (postulación de otra empresa) | 403 | ☐ |
| 5.8 | En el panel, abre los 3 **Reportes PDF**: Mis vacantes, Postulaciones y Ficha de mi empresa | Cada uno se abre en una pestaña nueva con el encabezado institucional y **solo** datos de Comercializadora Huasteca | ☐ |
| 5.9 | **Editar vacante:** cambia el número de plazas a 3 y desmarca "Publicada". Guarda | La vacante deja de aparecer en la bolsa pública; con sesión de alumno, su URL directa da 404 | ☐ |
| 5.10 | Vuelve a marcar "Publicada" | La vacante regresa a la bolsa de trabajo | ☐ |
| 5.11 | **Mi perfil:** cambia el puesto. Intenta cambiar la contraseña con una "contraseña actual" incorrecta | El puesto se guarda; el cambio de contraseña falla con "La contraseña actual es incorrecta." | ☐ |

**Navegador B (Luis Fernando).**

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 5.12 | Recarga **Mis postulaciones** | *Analista de Inventarios Jr.* aparece con la insignia verde **ACEPTADO**, el comentario de la empresa y "Actualizado hace…" | ☐ |
| 5.13 | Revisa el **Resumen** | El contador de ACEPTADO marca 1 | ☐ |

### Reclutador de prueba con datos de demostración

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 5.14 | Cierra la sesión de Ricardo y entra como `reclutador@empresa.com` | El panel de TecnoSoft muestra la sección "Por vencer esta semana" con *Soporte Técnico TI* | ☐ |
| 5.15 | Abre la vacante *Desarrollador(a) Web PHP/Laravel* y cambia a **RECHAZADO** la postulación de Luis Fernando, con un comentario | En la cuenta de Luis Fernando, esa postulación aparece como **RECHAZADO** con el comentario | ☐ |
| 5.16 | Da de baja la vacante *QA Tester* desde **Mis vacantes** | Pide confirmación; mensaje "…se dio de baja"; desaparece del listado y de la bolsa pública | ☐ |

---

## Fase 6. Administrador (Filament)

Entra en `/login` o en `/admin/login` con `admin@upslp.edu.mx` / `Admin123!`.

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 6.1 | **Dashboard** | 4 indicadores (empresas activas, vacantes vigentes, alumnos y postulaciones con su % de aceptación), gráfica de vacantes por área, dona por estatus, tendencia de 6 meses y tabla de últimas postulaciones (incluye las de Luis Fernando) | ☐ |
| 6.2 | **Empresas:** busca *Huasteca* y abre su detalle | Se ven sus datos, logotipo y resumen; en las pestañas inferiores, Ricardo Méndez como reclutador vinculado y su vacante | ☐ |
| 6.3 | **Vínculo empresa–reclutador:** en la empresa *Grupo Logístico Potosino* usa **Vincular reclutador** y elige al reclutador "sin empresa" que creó el seeder | Queda vinculado; en **Usuarios** (filtro "Reclutadores sin empresa") ya no aparece | ☐ |
| 6.4 | Edita *Comercializadora Huasteca*, desactiva **Activa** y guarda. En el navegador de Ricardo intenta **+ Publicar vacante** | Ricardo es redirigido a "Mi empresa" con un aviso; aparece la advertencia de empresa desactivada. Después vuelve a activarla | ☐ |
| 6.5 | Marca la empresa como **Verificada** | En el detalle público de su vacante aparece "✓ Empresa verificada" | ☐ |
| 6.6 | **Vacantes:** recorre las pestañas Vigentes / Por vencer / Próximas / Vencidas y usa los filtros de categoría y empresa | Los listados coinciden con la vigencia de cada vacante; el menú lateral muestra el número de vigentes | ☐ |
| 6.7 | Sobre *Administrador(a) de Redes* (vencida), usa **⋮ → Extender vigencia** con una fecha 15 días después | Pasa a "Vigente"; ahora aparece en la bolsa pública y acepta postulaciones | ☐ |
| 6.8 | Crea una vacante desde **Nueva vacante** con fecha de fin anterior a la de inicio | Error de validación en "Fin de vigencia"; no se guarda | ☐ |
| 6.9 | **Postulaciones:** filtra por la pestaña ACEPTADO, abre la de Luis Fernando y usa **Cambiar estatus** → EN PROCESO | Se actualiza; Luis lo ve reflejado. Prueba también **CV en PDF** | ☐ |
| 6.10 | **Mensajes de contacto** | El mensaje *"Prueba de contacto CP-01"* (paso 1.20) aparece en negritas como no leído; **Marcar leído** lo cambia y el contador del menú baja | ☐ |
| 6.11 | **Categorías** | Existen exactamente las 12 categorías obligatorias; las que tienen vacantes no muestran el botón de eliminar | ☐ |
| 6.12 | **Reportes PDF:** genera los reportes de *Vacantes* (filtro: Vigencia = Vigentes), *Postulaciones* (filtro: Estatus = ACEPTADO) y *Empresas* | Se descargan los 3 PDF con los filtros aplicados indicados en el encabezado, totales, tablas, numeración de páginas y créditos en el pie | ☐ |
| 6.13 | **Usuarios:** desactiva la cuenta de Luis Fernando (interruptor *Activa*). Intenta iniciar sesión con ella | No puede entrar ("Estas credenciales no coinciden…"). Después reactívala | ☐ |
| 6.14 | Intenta desactivarte o eliminarte a ti mismo (admin) | El interruptor está deshabilitado y no aparece la acción de eliminar en tu propio usuario | ☐ |
| 6.15 | Menú de usuario → **Ir al sitio público** | Regresa al sitio institucional con la sesión de admin; el menú ofrece "Panel de administración" | ☐ |

---

## Fase 7. Cierre de sesión y protección CSRF

| # | Acción | Resultado esperado | ✓ |
|---|---|---|---|
| 7.1 | En cualquier sesión, usa **Cerrar sesión** | Vuelves al inicio con el mensaje "Cerraste sesión correctamente."; las páginas privadas redirigen a login | ☐ |
| 7.2 | **CSRF:** abre `/contacto`, borra la cookie de sesión del sitio (F12 → Aplicación → Cookies) y envía el formulario | Página **419 "La sesión expiró"**; el mensaje no se guarda | ☐ |

---

## Matriz de trazabilidad (requisito → pasos)

| Requisito del proyecto | Pasos |
|---|---|
| 12 categorías obligatorias | 1.1, 1.10, 6.11 |
| Identidad, logo, banner, menús responsivos, mapa de sitio, navegación en el pie | 1.1–1.3, 1.6, 1.21 |
| Pie de página con autor, materia, institución y profesora | 1.2, 4.11, 6.12 |
| Inicio, Quiénes somos, FAQ, Contacto funcional, Estadísticas | 1.1–1.5, 1.18–1.20 |
| **Admin:** gestionar empresas y vínculo con reclutadores | 6.2–6.5 |
| **Admin:** gestionar ofertas y su vigencia | 6.6–6.8 |
| **Admin:** métricas y reportes PDF | 6.1, 6.12 |
| **Reclutador:** registro e inicio de sesión | 3.2, 5.14 |
| **Reclutador:** perfil y datos de la empresa | 3.3–3.4, 5.11 |
| **Reclutador:** alta, consulta, modificación y baja de vacantes con validación | 3.5–3.8, 5.9–5.10, 5.16 |
| **Reclutador:** revisar candidatos y CV; estatus ACEPTADO / RECHAZADO / EN PROCESO | 5.2–5.6, 5.15 |
| **Reclutador:** reportes PDF | 5.4, 5.8 |
| **Alumno:** registro, inicio de sesión y perfil | 4.1–4.4 |
| **Alumno:** CV con formularios (empleos, formación, habilidades por área, documentos) | 4.5–4.14 |
| **Alumno:** consulta de ofertas con búsqueda y filtros de vigencia y categoría | 1.8–1.13, 4.15 |
| **Alumno:** postulación **sin duplicados** (interfaz y servidor) | 4.16–4.18 |
| **Alumno:** consulta del estatus de cada postulación | 4.21, 5.12–5.13, 5.15 |
| Seguridad: autenticación y sesiones | 2.1–2.3, 6.13, 7.1 |
| Seguridad: autorización por rol y por empresa | 3.9–3.10, 5.7 |
| Seguridad: CSRF / XSS / SQLi | 7.2 / 3.7 / 1.14 |
| Validación de formularios (cliente y servidor) | 1.18–1.19, 3.1, 3.5, 4.1–4.2, 4.5, 4.10, 4.12, 6.8 |

---

## Registro de ejecución

| Campo | Valor |
|---|---|
| Fecha de ejecución | |
| Ejecutado por | |
| Navegadores / dispositivo | |
| Pasos aprobados | ___ / 88 |
| Incidencias encontradas | |

**Criterio de aceptación:** el caso CP-01 se aprueba si todos los pasos se cumplen. Cualquier paso de seguridad (1.14, 3.7, 3.9, 3.10, 4.18, 5.7, 7.2) o de regla de negocio (4.16–4.18, 4.20) que falle se considera **crítico**.

> **Complemento automatizado:** los mismos flujos están cubiertos por 121 pruebas automáticas (`php artisan test --compact`); este caso de prueba valida además la experiencia visual y la interacción real en el navegador.
