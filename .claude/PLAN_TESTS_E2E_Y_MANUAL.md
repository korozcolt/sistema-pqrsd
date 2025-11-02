# Plan de Trabajo: Tests E2E y Manual de Usuario

**Fecha de creación:** 2025-11-02
**Sistema:** PQRSD - Peticiones, Quejas, Reclamos, Sugerencias y Denuncias
**Versión actual:** 1.1.1

---

## 📋 Resumen Ejecutivo

Este plan de trabajo aborda dos necesidades críticas del sistema:

1. **Tests Automatizados E2E (End-to-End)** - Pruebas automatizadas de interfaz de usuario
2. **Manual de Usuario Completo** - Documentación detallada con capturas de pantalla

---

## 🎯 Objetivo 1: Tests Automatizados E2E (UI Testing)

### ¿Qué son los tests E2E?

Los tests End-to-End (E2E) son pruebas automatizadas que simulan el comportamiento de un usuario real navegando por la aplicación. A diferencia de los tests unitarios que prueban código, los tests E2E:

- ✅ Abren un navegador real (Chrome, Firefox, etc.)
- ✅ Navegan por las páginas como lo haría un usuario
- ✅ Hacen clic en botones, llenan formularios, verifican contenido
- ✅ Prueban la integración completa (frontend + backend + base de datos)
- ✅ Detectan errores de UI, JavaScript, validaciones, y flujos completos

### Herramienta Recomendada: Laravel Dusk

**Laravel Dusk** es la solución oficial de Laravel para testing de navegador. Características:

- 🔹 Integración nativa con Laravel, Livewire y Filament
- 🔹 Usa ChromeDriver (navegador Chrome automatizado)
- 🔹 Sintaxis simple y expresiva (estilo Pest/PHPUnit)
- 🔹 Capturas de pantalla automáticas en fallos
- 🔹 Soporte para JavaScript y componentes dinámicos
- 🔹 CI/CD friendly (puede correr en GitHub Actions, etc.)

### Instalación y Configuración

```bash
# 1. Instalar Laravel Dusk
composer require --dev laravel/dusk

# 2. Instalar Dusk en el proyecto
php artisan dusk:install

# 3. Configurar ChromeDriver
php artisan dusk:chrome-driver

# 4. Ejecutar tests E2E
php artisan dusk
```

### Estructura de Tests E2E

```
tests/Browser/
├── Auth/
│   ├── LoginTest.php
│   ├── RegisterTest.php
│   ├── PasswordResetTest.php
│   └── LogoutTest.php
├── Tickets/
│   ├── CreateTicketTest.php
│   ├── ListTicketsTest.php
│   ├── EditTicketTest.php
│   ├── CommentTicketTest.php
│   ├── CloseTicketTest.php
│   └── FilterTicketsTest.php
├── Admin/
│   ├── Users/
│   │   ├── CreateUserTest.php
│   │   ├── EditUserTest.php
│   │   └── DeleteUserTest.php
│   ├── Departments/
│   │   ├── ManageDepartmentsTest.php
│   │   └── AssignUsersTest.php
│   └── Categories/
│       └── ManageCategoriesTest.php
├── Notifications/
│   ├── ToastNotificationsTest.php
│   ├── EmailNotificationsTest.php
│   └── ReminderNotificationsTest.php
├── SLA/
│   ├── ResponseTimeTest.php
│   ├── ResolutionTimeTest.php
│   └── AutoCloseInactiveTest.php
└── Forms/
    ├── ValidationTest.php
    ├── FileUploadTest.php
    └── SelectOptionsTest.php
```

### Ejemplo de Test E2E

```php
<?php

namespace Tests\Browser\Tickets;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CreateTicketTest extends DuskTestCase
{
    /**
     * Test que un usuario puede crear un ticket correctamente
     */
    public function test_user_can_create_ticket(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/tickets/create')
                    ->assertSee('Crear Nuevo Ticket')
                    ->type('title', 'Mi primer ticket de prueba')
                    ->select('category_id', '1')
                    ->type('description', 'Esta es la descripción del problema')
                    ->attach('attachment', storage_path('tests/sample.pdf'))
                    ->press('Crear Ticket')
                    ->assertPathIs('/tickets')
                    ->assertSee('Ticket creado exitosamente')
                    ->assertSee('Mi primer ticket de prueba');
        });
    }

    /**
     * Test que valida campos requeridos
     */
    public function test_ticket_creation_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/tickets/create')
                    ->press('Crear Ticket')
                    ->assertSee('El campo título es requerido')
                    ->assertSee('El campo descripción es requerido');
        });
    }
}
```

### Cobertura Propuesta de Tests E2E

| Módulo | Tests Estimados | Prioridad |
|--------|----------------|-----------|
| **Autenticación** | 8-10 | 🔴 Alta |
| **Tickets (CRUD)** | 15-20 | 🔴 Alta |
| **Comentarios** | 5-8 | 🟡 Media |
| **Usuarios Admin** | 10-12 | 🟡 Media |
| **Departamentos/Categorías** | 6-8 | 🟢 Baja |
| **Notificaciones** | 8-10 | 🟡 Media |
| **SLA/Automatizaciones** | 10-12 | 🔴 Alta |
| **Validaciones/Formularios** | 15-20 | 🟡 Media |
| **TOTAL** | **77-100 tests** | - |

### Beneficios de Tests E2E

✅ **Detecta bugs de UI** - Errores de diseño, CSS, JavaScript
✅ **Previene regresiones** - Cambios que rompen funcionalidad existente
✅ **Documenta flujos** - Los tests sirven como documentación viva
✅ **Confianza en despliegues** - Sabes que todo funciona antes de producción
✅ **Ahorro de tiempo** - Automatiza testing manual repetitivo

---

## 📚 Objetivo 2: Manual de Usuario Completo

### ¿Qué es el Manual de Usuario?

Un documento completo y visual que guía a los usuarios nuevos (y existentes) a través de todas las funcionalidades del sistema. Debe ser:

- 📖 **Claro y conciso** - Lenguaje simple, sin tecnicismos
- 🖼️ **Visual** - Capturas de pantalla en cada paso
- 🎯 **Orientado a tareas** - "Cómo hacer X" en lugar de "La pantalla Y tiene..."
- 🔍 **Navegable** - Índice, búsqueda, tabla de contenidos
- 📱 **Accesible** - PDF descargable, HTML online

### Estructura Propuesta del Manual

```
MANUAL_USUARIO_PQRSD.md (o .pdf)
│
├── 1. INTRODUCCIÓN
│   ├── 1.1 ¿Qué es el Sistema PQRSD?
│   ├── 1.2 Requisitos del sistema
│   ├── 1.3 Convenciones de este manual
│   └── 1.4 Soporte y contacto
│
├── 2. INICIO RÁPIDO
│   ├── 2.1 Primer acceso al sistema
│   ├── 2.2 Registro de nueva cuenta
│   ├── 2.3 Recuperación de contraseña
│   └── 2.4 Navegación básica
│
├── 3. AUTENTICACIÓN
│   ├── 3.1 Iniciar sesión
│   ├── 3.2 Cerrar sesión
│   ├── 3.3 Cambiar contraseña
│   └── 3.4 Editar perfil de usuario
│
├── 4. DASHBOARD Y NAVEGACIÓN
│   ├── 4.1 Panel principal (Dashboard)
│   ├── 4.2 Menú de navegación
│   ├── 4.3 Notificaciones
│   └── 4.4 Barra de búsqueda global
│
├── 5. GESTIÓN DE TICKETS
│   ├── 5.1 ¿Qué es un ticket?
│   ├── 5.2 Crear un nuevo ticket
│   │   ├── Seleccionar categoría
│   │   ├── Escribir título y descripción
│   │   ├── Adjuntar archivos
│   │   └── Establecer prioridad
│   ├── 5.3 Listar mis tickets
│   ├── 5.4 Buscar y filtrar tickets
│   │   ├── Por estado
│   │   ├── Por categoría
│   │   ├── Por fecha
│   │   └── Por palabra clave
│   ├── 5.5 Ver detalles de un ticket
│   ├── 5.6 Editar un ticket
│   ├── 5.7 Cerrar un ticket
│   └── 5.8 Reabrir un ticket
│
├── 6. COMENTARIOS Y SEGUIMIENTO
│   ├── 6.1 Agregar comentario a un ticket
│   ├── 6.2 Ver historial de comentarios
│   ├── 6.3 Adjuntar archivos en comentarios
│   ├── 6.4 Notificaciones de nuevos comentarios
│   └── 6.5 Comentarios internos (solo staff)
│
├── 7. GESTIÓN DE USUARIOS (Admin)
│   ├── 7.1 Listar usuarios
│   ├── 7.2 Crear nuevo usuario
│   ├── 7.3 Editar usuario
│   ├── 7.4 Asignar roles y permisos
│   ├── 7.5 Desactivar/activar usuario
│   └── 7.6 Eliminar usuario
│
├── 8. DEPARTAMENTOS Y CATEGORÍAS
│   ├── 8.1 Gestión de departamentos
│   ├── 8.2 Asignar usuarios a departamentos
│   ├── 8.3 Gestión de categorías
│   └── 8.4 Configurar tiempos SLA por categoría
│
├── 9. NOTIFICACIONES Y ALERTAS
│   ├── 9.1 Tipos de notificaciones
│   ├── 9.2 Notificaciones en pantalla (toast)
│   ├── 9.3 Notificaciones por email
│   ├── 9.4 Configurar preferencias de notificación
│   └── 9.5 Marcar notificaciones como leídas
│
├── 10. SLA Y TIEMPOS DE RESPUESTA
│   ├── 10.1 ¿Qué es el SLA?
│   ├── 10.2 Tiempos de respuesta
│   ├── 10.3 Tiempos de resolución
│   ├── 10.4 Recordatorios automáticos
│   ├── 10.5 Cierre automático por inactividad
│   └── 10.6 Indicadores visuales de SLA
│
├── 11. REPORTES Y ESTADÍSTICAS
│   ├── 11.1 Dashboard de estadísticas
│   ├── 11.2 Reportes de tickets por estado
│   ├── 11.3 Reportes de tickets por categoría
│   ├── 11.4 Reportes de desempeño
│   └── 11.5 Exportar datos (PDF, Excel)
│
├── 12. CONFIGURACIÓN DEL SISTEMA
│   ├── 12.1 Configuración general
│   ├── 12.2 Configuración de email
│   ├── 12.3 Configuración de SLA
│   └── 12.4 Configuración de notificaciones
│
├── 13. PREGUNTAS FRECUENTES (FAQ)
│   ├── ¿Cómo cambio mi contraseña?
│   ├── ¿Cómo adjunto archivos?
│   ├── ¿Por qué no recibo notificaciones?
│   ├── ¿Cómo cierro un ticket?
│   ├── ¿Qué significa cada estado?
│   └── ... (20-30 preguntas más)
│
├── 14. SOLUCIÓN DE PROBLEMAS
│   ├── No puedo iniciar sesión
│   ├── Olvidé mi contraseña
│   ├── No puedo adjuntar archivos
│   ├── No recibo emails del sistema
│   └── Errores comunes y soluciones
│
└── 15. GLOSARIO Y APÉNDICES
    ├── Glosario de términos
    ├── Atajos de teclado
    └── Información de contacto
```

### Herramientas para Crear el Manual

**Opción 1: Markdown + Screenshots (Recomendado)**
- Escribir en Markdown (fácil de versionar en Git)
- Capturas con herramientas nativas (macOS: Cmd+Shift+4, Windows: Win+Shift+S)
- Generar PDF con Pandoc o MkDocs
- Hospedar HTML con MkDocs Material o VitePress

**Opción 2: Google Docs / Microsoft Word**
- Editor WYSIWYG (lo que ves es lo que obtienes)
- Colaboración en tiempo real
- Exportar a PDF fácilmente
- Menos control sobre versiones

**Opción 3: Herramientas especializadas**
- **Scribe** - Genera documentación automática mientras usas la app
- **Tango** - Similar a Scribe
- **iorad** - Tutoriales interactivos
- **GitBook** - Documentación profesional online

### Estándares de Capturas de Pantalla

✅ **Resolución:** 1920x1080 (Full HD)
✅ **Formato:** PNG (mejor calidad) o JPG (menor tamaño)
✅ **Anotaciones:** Resaltar áreas importantes con cajas rojas/flechas
✅ **Nomenclatura:** `01-login-pantalla-principal.png`
✅ **Ubicación:** `docs/manual/images/`
✅ **Datos de prueba:** Usar datos realistas pero ficticios

### Ejemplo de Sección del Manual

```markdown
## 5.2 Crear un Nuevo Ticket

### Pasos para crear un ticket:

1. **Acceder al formulario de creación**

   Desde el menú principal, haz clic en "Tickets" → "Crear Nuevo Ticket"

   ![Menú Crear Ticket](images/05-crear-ticket-menu.png)

2. **Completar información básica**

   - **Título:** Describe brevemente el problema (máximo 255 caracteres)
   - **Categoría:** Selecciona la categoría que mejor describe tu solicitud
   - **Prioridad:** Baja, Media, Alta, o Crítica

   ![Formulario Ticket](images/05-crear-ticket-formulario.png)

3. **Escribir descripción detallada**

   Proporciona todos los detalles necesarios para entender el problema:
   - ¿Qué sucedió?
   - ¿Cuándo ocurrió?
   - ¿Qué esperabas que sucediera?

   ![Descripción Ticket](images/05-crear-ticket-descripcion.png)

4. **Adjuntar archivos (opcional)**

   Puedes adjuntar capturas de pantalla, documentos, o cualquier archivo
   relevante (máximo 10MB por archivo).

   Formatos permitidos: PDF, PNG, JPG, DOC, XLS

   ![Adjuntar Archivos](images/05-crear-ticket-adjuntos.png)

5. **Enviar ticket**

   Haz clic en el botón "Crear Ticket" para enviar tu solicitud.

   Recibirás una notificación de confirmación y un email con el número
   de ticket asignado.

   ![Confirmación](images/05-crear-ticket-confirmacion.png)

### 💡 Consejos

- ✅ Sé específico en el título (❌ "No funciona" → ✅ "Error al cargar dashboard")
- ✅ Incluye capturas de pantalla si es posible
- ✅ Proporciona pasos para reproducir el problema
- ✅ Verifica que seleccionaste la categoría correcta

### ⚠️ Errores comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "El campo título es requerido" | Dejaste el título vacío | Completa el campo título |
| "Archivo muy grande" | El archivo supera 10MB | Comprime el archivo o sube uno más pequeño |
| "Categoría inválida" | La categoría fue eliminada | Recarga la página y selecciona otra categoría |
```

### Estimación de Tiempo

| Sección | Páginas | Screenshots | Tiempo Estimado |
|---------|---------|-------------|-----------------|
| Introducción | 3-5 | 2-3 | 4 horas |
| Autenticación | 5-8 | 10-15 | 8 horas |
| Dashboard | 3-5 | 8-10 | 6 horas |
| Gestión Tickets | 15-20 | 30-40 | 20 horas |
| Comentarios | 5-8 | 10-15 | 8 horas |
| Admin Usuarios | 10-12 | 20-25 | 15 horas |
| Departamentos | 5-8 | 10-12 | 8 horas |
| Notificaciones | 5-8 | 12-15 | 8 horas |
| SLA | 8-10 | 15-20 | 12 horas |
| Reportes | 8-10 | 15-20 | 12 horas |
| Configuración | 5-8 | 10-15 | 8 horas |
| FAQ | 10-15 | 5-10 | 12 horas |
| Troubleshooting | 8-10 | 10-15 | 10 horas |
| **TOTAL** | **90-130** | **157-215** | **131 horas** |

**Nota:** Tiempo para 1 persona trabajando full-time = **3-4 semanas**

---

## 📊 Plan de Ejecución

### Fase 1: Tests E2E Críticos (Semana 1-2)

**Objetivo:** Tener cobertura básica de flujos críticos

1. ✅ Instalar y configurar Laravel Dusk
2. ✅ Crear tests de autenticación (login, registro, logout)
3. ✅ Crear tests de CRUD de tickets
4. ✅ Crear tests de comentarios
5. ✅ Ejecutar y validar todos los tests

**Entregable:** ~30-40 tests E2E básicos funcionando

---

### Fase 2: Tests E2E Avanzados (Semana 3-4)

**Objetivo:** Cubrir módulos admin y automatizaciones

1. ✅ Tests de gestión de usuarios (admin)
2. ✅ Tests de departamentos y categorías
3. ✅ Tests de notificaciones
4. ✅ Tests de SLA y automatizaciones
5. ✅ Tests de validaciones y formularios

**Entregable:** ~70-100 tests E2E completos

---

### Fase 3: Manual de Usuario - Estructura (Semana 5)

**Objetivo:** Definir estructura y capturar screenshots

1. ✅ Definir índice completo del manual
2. ✅ Preparar entorno de testing con datos de prueba
3. ✅ Capturar todas las screenshots necesarias (~200 imágenes)
4. ✅ Organizar y nombrar screenshots
5. ✅ Crear plantilla base del documento

**Entregable:** Estructura completa + screenshots organizados

---

### Fase 4: Manual de Usuario - Contenido (Semana 6-8)

**Objetivo:** Redactar todo el contenido del manual

1. ✅ Redactar secciones 1-5 (Intro, Inicio, Auth, Dashboard, Tickets)
2. ✅ Redactar secciones 6-10 (Comentarios, Usuarios, Dept, Notif, SLA)
3. ✅ Redactar secciones 11-15 (Reportes, Config, FAQ, Troubleshoot, Glosario)
4. ✅ Insertar screenshots en cada sección
5. ✅ Revisar y corregir contenido

**Entregable:** Manual completo en Markdown

---

### Fase 5: Publicación y Mantenimiento (Semana 9)

**Objetivo:** Publicar manual y establecer proceso de actualización

1. ✅ Generar versión PDF del manual
2. ✅ Publicar versión HTML (opcional, con MkDocs)
3. ✅ Crear proceso de actualización del manual
4. ✅ Capacitar al equipo en uso del manual
5. ✅ Establecer ciclo de revisión (trimestral/semestral)

**Entregable:** Manual publicado y proceso de mantenimiento

---

## 🎯 Métricas de Éxito

### Tests E2E

- ✅ **70-100 tests E2E** cubriendo todos los módulos críticos
- ✅ **85%+ tasa de éxito** en ejecución de tests
- ✅ **< 20 minutos** tiempo total de ejecución de suite completa
- ✅ **Integración CI/CD** - tests corriendo en cada PR/push
- ✅ **0 falsos positivos** - tests confiables y estables

### Manual de Usuario

- ✅ **90-130 páginas** de documentación completa
- ✅ **150-200 screenshots** de alta calidad
- ✅ **100% de módulos** documentados
- ✅ **Versiones PDF + HTML** disponibles
- ✅ **Feedback positivo** de usuarios nuevos (>80% satisfacción)

---

## 💰 Recursos Necesarios

### Para Tests E2E

- **Tiempo de desarrollo:** 80-120 horas (2-3 semanas)
- **Hardware:** Servidor/máquina con Chrome instalado
- **Software:** ChromeDriver (gratis), Laravel Dusk (gratis)
- **Conocimientos:** PHP, Laravel, Testing, Dusk/Selenium

### Para Manual de Usuario

- **Tiempo de desarrollo:** 120-160 horas (3-4 semanas)
- **Software:** Editor Markdown, herramienta de screenshots
- **Conocimientos:** Redacción técnica, diseño gráfico básico
- **Opcional:** Herramienta de documentación (Scribe, MkDocs)

---

## 🚀 Próximos Pasos

### Inmediatos (Esta semana)

1. ✅ Revisar y aprobar este plan de trabajo
2. ✅ Decidir: ¿empezar con Tests E2E o Manual primero?
3. ✅ Asignar recursos y responsables
4. ✅ Configurar entorno de desarrollo/testing

### Mediano plazo (Próximas 2 semanas)

1. ✅ Instalar Laravel Dusk y crear primeros tests
2. ✅ Definir estructura final del manual
3. ✅ Comenzar captura de screenshots

### Largo plazo (Próximos 2 meses)

1. ✅ Completar suite de tests E2E
2. ✅ Publicar manual de usuario v1.0
3. ✅ Integrar tests en CI/CD
4. ✅ Capacitar equipo en mantenimiento

---

## 📞 Contacto y Soporte

¿Preguntas sobre este plan? Contacta a:
- **Equipo de desarrollo:** [email]
- **Project manager:** [email]
- **Documentación:** [Wiki/Confluence URL]

---

**Generado:** 2025-11-02
**Autor:** Claude Code
**Versión:** 1.0
**Próxima revisión:** 2025-11-09
