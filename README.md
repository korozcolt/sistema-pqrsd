<div align="center">

# Sistema PQRSD 📋

**Plataforma Integral de Gestión de Peticiones, Quejas, Reclamos, Sugerencias y Denuncias**

[![Versión PHP](https://img.shields.io/badge/PHP-8.2%2B-blue?style=for-the-badge&logo=php)](https://www.php.net/)
[![Versión Laravel](https://img.shields.io/badge/Laravel-12.x-red?style=for-the-badge&logo=laravel)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/Filament-4.x-orange?style=for-the-badge&logo=laravel)](https://filamentphp.com/)
[![Versión](https://img.shields.io/badge/versión-1.1.1-orange?style=for-the-badge)](VERSION)
[![Licencia](https://img.shields.io/badge/licencia-Apache%202.0-green?style=for-the-badge)](LICENSE)

</div>

---

## 📖 Índice

- [Descripción](#-descripción-del-proyecto)
- [Características](#-características-principales)
- [Módulos](#-módulos-del-sistema)
- [Stack Tecnológico](#-stack-tecnológico)
- [Requisitos](#-requisitos-del-servidor)
- [Instalación](#-instalación-y-configuración)
- [Estructura](#-estructura-de-directorios)
- [Base de Datos](#-estructura-de-la-base-de-datos)
- [API](#-api-restful)
- [Pruebas](#-pruebas-automatizadas)
- [Despliegue](#-despliegue)
- [Usuarios por Defecto](#-usuarios-por-defecto)
- [Solución de Problemas](#-solución-de-problemas-comunes)
- [Licencia](#-licencia)

---

## 🚀 Descripción del Proyecto

Sistema PQRSD es una plataforma avanzada de gestión de Peticiones, Quejas, Reclamos, Sugerencias y Denuncias diseñada para empresas y organizaciones que necesitan gestionar de forma eficiente las solicitudes de sus usuarios. El sistema ofrece una experiencia administrativa completa a través de un panel Filament altamente personalizado y una API RESTful para integración con aplicaciones externas.

### 🎯 Objetivo

Proporcionar una solución integral y moderna para la gestión de PQRSD con trazabilidad completa, cumplimiento de SLA, notificaciones automáticas y análisis detallado de métricas.

---

## ✨ Características Principales

- 🎫 **Sistema de Tickets PQRSD**: Gestión completa del ciclo de vida de tickets con generación automática de números únicos
- ⏱️ **Gestión de SLA**: Cálculo automático de tiempos de respuesta y resolución según tipos de ticket y prioridad
- 🔔 **Sistema de Recordatorios**: Notificaciones automáticas para plazos de respuesta y resolución
- 📊 **Paneles Analíticos**: Estadísticas detalladas sobre tickets y tiempos de respuesta
- 👥 **Control de Roles**: Jerarquía de usuarios (SuperAdmin, Admin, Recepcionista, Usuario Web)
- 🏢 **Gestión de Departamentos**: Organización por áreas administrativas
- 🏷️ **Sistema de Etiquetas**: Categorización flexible de tickets
- 📱 **API RESTful**: Interfaz de programación para integración con aplicaciones externas
- 📄 **Generador de Sitemap**: Creación automática de sitemaps para SEO
- 🌐 **Portal Web para Usuarios**: Interfaz pública para creación y seguimiento de tickets
- 📣 **Notificaciones Multi-Canal**: Correo electrónico y sistema interno de notificaciones asíncronas
- 📝 **Trazabilidad Completa**: Log detallado de todos los cambios en tickets con Spatie Activity Log
- 🔄 **Máquina de Estados**: Control de transiciones de estado de tickets con validación
- 🚀 **Procesamiento Asíncrono**: Sistema de colas para notificaciones con 98.3% de mejora en rendimiento
- 🧪 **Tests E2E**: Suite completa de pruebas con Laravel Dusk para Filament
- 🔍 **Visor de Logs**: Interfaz integrada para monitoreo de logs del sistema

---

## 🧩 Módulos del Sistema

### 📋 Sistema de Tickets PQRSD

- Creación, seguimiento y resolución de tickets
- Categorización por tipo (Petición, Queja, Reclamo, Sugerencia, Denuncia)
- Asignación de prioridades (Baja, Media, Alta, Urgente)
- Control de estados con máquina de estados:
  - Pendiente → En Progreso → Resuelto → Cerrado
  - Soporte para Rechazado y Reabierto
  - Validación de transiciones permitidas
- Comentarios con soporte público/privado
- Gestión de archivos adjuntos
- Generación automática de números de ticket únicos (formato: TK-XXXXX)
- Marcado automático para cierre de tickets inactivos
- Cierre automático de tickets sin actividad

### ⏱️ Sistema de SLA

- Configuración por tipo de ticket y prioridad
- Cálculo automático de fechas de respuesta y resolución
- Valores por defecto si no existe configuración:
  - Respuesta: 24 horas
  - Resolución: 15 días
- Tracking de cumplimiento de SLA
- Alertas de vencimiento

### 🔔 Sistema de Recordatorios

- Recordatorios automáticos de plazos
- Tipos de recordatorio: Respuesta y Resolución
- Notificaciones programadas
- Gestión de tickets inactivos

### 🔌 API RESTful

Endpoints disponibles:

#### Públicos (sin autenticación)
- `GET /api/public/ticket-types` - Obtener tipos de tickets
- `POST /api/public/tickets` - Crear nuevo ticket
- `GET /api/public/tickets/{ticket_number}` - Consultar ticket
- `GET /api/public/tickets/verify/{ticket_number}` - Verificar existencia
- `POST /api/public/tickets/{ticket_number}/comments` - Agregar comentario
- `POST /api/public/tickets/{ticket_number}/close` - Cerrar ticket

#### Autenticados (Sanctum)
- `GET /api/user` - Información del usuario autenticado

### 📱 Portal Web para Usuarios

- Formulario de creación de tickets
- Sistema de consulta de estado
- Comunicación directa con el equipo de soporte
- Diseño responsive para móviles y tablets
- Seguimiento de tickets sin autenticación

### 🎨 Panel Administrativo (Filament 4)

Recursos principales:
- **Tickets**: CRUD completo con filtros avanzados
- **Usuarios**: Gestión de usuarios y roles
- **Departamentos**: Organización departamental
- **SLAs**: Configuración de acuerdos de nivel de servicio
- **Etiquetas**: Sistema de categorización
- **Recordatorios**: Gestión de notificaciones programadas

Características del panel:
- Navegación intuitiva
- Filtros y búsqueda avanzada
- Exportación de datos
- Importación masiva
- Widgets de estadísticas
- Dashboard personalizable

---

## 🛠️ Stack Tecnológico

### Backend
- **PHP**: 8.4.15 (mínimo 8.2)
- **Laravel**: 12.36.1
- **Filament**: 4.1.10
- **Livewire**: 3.6.4
- **Volt**: 1.8.0

### Frontend
- **Tailwind CSS**: 4.1.16
- **Alpine.js**: Incluido con Livewire
- **Vite**: Para compilación de assets

### Autenticación & Seguridad
- **Laravel Sanctum**: 4.2.0 (autenticación API)
- **Laravel Breeze**: 2.3.8 (autenticación web)

### Utilidades
- **Spatie Activity Log**: 4.10+ (trazabilidad)
- **Spatie Sitemap**: 7.2+ (SEO)
- **Spatie Eloquent Sortable**: 4.4+ (ordenamiento)
- **Laravel Prompts**: 0.3.7 (comandos interactivos)
- **Opcodesio Log Viewer**: 3.11+ (visor de logs)
- **RealRashid Sweet Alert**: 7.2+ (alertas)

### Testing
- **Pest**: 3.8.4
- **PHPUnit**: 11.5.33
- **Laravel Dusk**: 8.3.3 (tests E2E)

### Desarrollo
- **Laravel Boost**: 1.6+ (MCP server)
- **Laravel Pint**: 1.25.1 (code style)
- **Laravel Sail**: 1.47.0 (Docker)
- **Rector**: 2.2.7 (refactoring)
- **IDE Helper**: 3.2+ (autocompletado)

---

## 🖥️ Requisitos del Servidor

### 📋 Extensiones PHP Mínimas

- ✅ PHP >= 8.2 (recomendado 8.4)
- ✅ Extensión BCMath
- ✅ Extensión Ctype
- ✅ Extensión cURL
- ✅ Extensión DOM
- ✅ Extensión Fileinfo
- ✅ Extensión JSON
- ✅ Extensión Mbstring
- ✅ Extensión OpenSSL
- ✅ Extensión PCRE
- ✅ Extensión PDO
- ✅ Extensión Tokenizer
- ✅ Extensión XML
- ✅ Extensión GD o Imagick (para procesamiento de imágenes)
- ✅ Extensión Zip (para exportaciones)

### 🛢️ Bases de Datos Soportadas

- MySQL 8.0+ (recomendado)
- MariaDB 10.5+
- PostgreSQL 13.0+
- SQLite 3.8.8+ (solo para desarrollo)

### 📦 Otros Requisitos

- Composer 2.x
- Node.js 20.x y npm 10.x (para compilación de assets)
- Git (para control de versiones)

---

## 🛠️ Instalación y Configuración

### Instalación Rápida

```bash
# 1. Clonar el repositorio
git clone https://github.com/korozcolt/sistema-pqrsd.git

# 2. Navegar al directorio del proyecto
cd sistema-pqrsd

# 3. Instalar dependencias de PHP
composer install

# 4. Instalar dependencias de Node.js
npm install

# 5. Configurar variables de entorno
cp .env.example .env

# 6. Generar clave de aplicación
php artisan key:generate

# 7. Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pqrsd
# DB_USERNAME=root
# DB_PASSWORD=root

# 8. Ejecutar migraciones y seeders
php artisan migrate --seed

# 9. Compilar assets
npm run build

# 10. Crear enlace simbólico para storage
php artisan storage:link

# 11. Iniciar servidor de desarrollo
php artisan serve
```

El sistema estará disponible en: `http://localhost:8000`

Panel de administración: `http://localhost:8000/admin`

### Configuración de Variables de Entorno

Variables importantes en `.env`:

```env
# Aplicación
APP_NAME="Sistema PQRSD"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=America/Bogota
APP_URL=http://localhost:8000
APP_LOCALE=es

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pqrsd
DB_USERNAME=root
DB_PASSWORD=root

# Correo Electrónico
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@localhost"
MAIL_FROM_NAME="${APP_NAME}"

# Colas
QUEUE_CONNECTION=database

# Caché
CACHE_STORE=database

# Sesión
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Límites de carga
UPLOAD_MAX_FILESIZE=10240  # En KB (10MB)
MAX_EXECUTION_TIME=600     # En segundos

# reCAPTCHA (opcional)
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
```

### Configuración del Programador de Tareas

Para que los recordatorios y tareas automáticas funcionen, configure el cron:

```bash
# Editar crontab
crontab -e

# Agregar esta línea
* * * * * cd /ruta/a/su/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### Configuración del Worker de Colas

Para procesar notificaciones asíncronas:

```bash
# Iniciar worker en desarrollo
php artisan queue:work

# En producción con supervisord
[program:pqrsd-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/a/su/proyecto/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/ruta/a/su/proyecto/storage/logs/worker.log
stopwaitsecs=3600
```

### Comandos Artisan Personalizados

```bash
# Verificar y enviar recordatorios de tickets
php artisan tickets:check-reminders

# Generar sitemap para SEO
php artisan sitemap:generate

# Probar envío de correos
php artisan mail:test email@ejemplo.com

# Actualizar tipos de recordatorios
php artisan reminders:update-types

# Normalizar emails de usuarios
php artisan users:normalize-emails

# Gestionar versiones del sistema
php artisan version:bump

# Limpiar todas las cachés
php artisan optimize:clear

# Limpiar caché de Filament
php artisan filament:cache-clear
```

---

## 🗄️ Estructura de la Base de Datos

El sistema utiliza las siguientes tablas principales:

### Tablas Core

- **users**: Usuarios del sistema con roles y permisos
  - Campos: id, name, email, password, role, department_id, timestamps

- **departments**: Departamentos o áreas de la organización
  - Campos: id, name, description, is_active, timestamps

- **tickets**: Tickets PQRSD con información completa
  - Campos: id, ticket_number, title, description, user_id, department_id, type, status, priority, response_due_date, resolution_due_date, first_response_at, resolution_at, marked_for_closure_at, timestamps, soft_deletes

- **slas**: Configuración de acuerdos de nivel de servicio
  - Campos: id, ticket_type, priority, response_time_hours, resolution_time_hours, is_active, timestamps

- **ticket_logs**: Historial completo de cambios en tickets
  - Campos: id, ticket_id, user_id, action, old_status, new_status, description, timestamps

- **ticket_comments**: Comentarios en tickets
  - Campos: id, ticket_id, user_id, content, is_internal, timestamps, soft_deletes

- **ticket_attachments**: Archivos adjuntos a tickets
  - Campos: id, ticket_id, file_name, file_path, file_size, mime_type, timestamps

- **reminders**: Sistema de recordatorios automatizados
  - Campos: id, ticket_id, type, due_date, sent_at, timestamps

- **tags**: Etiquetas para categorización
  - Campos: id, name, slug, color, description, timestamps

- **ticket_tags**: Relación many-to-many entre tickets y etiquetas
  - Campos: id, ticket_id, tag_id, timestamps

### Tablas del Sistema

- **cache**: Almacenamiento de caché
- **cache_locks**: Bloqueos de caché
- **sessions**: Sesiones de usuario
- **jobs**: Cola de trabajos
- **job_batches**: Lotes de trabajos
- **failed_jobs**: Trabajos fallidos
- **personal_access_tokens**: Tokens de API (Sanctum)
- **activity_log**: Log de actividades (Spatie)

### Enumeraciones (Enums)

- **TicketType**: Peticion, Queja, Reclamo, Sugerencia, Denuncia
- **StatusTicket**: Pendiente, EnProgreso, Resuelto, Cerrado, Rechazado, Reabierto
- **Priority**: Baja, Media, Alta, Urgente
- **ReminderType**: Respuesta, Resolucion

---

## 📂 Estructura de Directorios Principales

```
sistema-pqrsd/
├── app/
│   ├── Console/
│   │   └── Commands/          # Comandos Artisan personalizados
│   ├── Enums/                 # Enumeraciones (TicketType, Status, Priority)
│   ├── Events/                # Eventos del sistema
│   ├── Filament/              # Recursos para el panel admin
│   │   ├── Admin/
│   │   │   ├── Resources/     # Recursos CRUD (Tickets, Users, etc.)
│   │   │   └── Pages/         # Páginas personalizadas
│   │   └── Widgets/           # Widgets del dashboard
│   ├── Http/
│   │   ├── Controllers/       # Controladores
│   │   │   ├── Api/           # Controladores de API
│   │   │   └── Auth/          # Autenticación
│   │   └── Middleware/        # Middleware personalizado
│   ├── Jobs/                  # Trabajos en cola
│   ├── Listeners/             # Oyentes de eventos
│   ├── Livewire/              # Componentes Livewire/Volt
│   ├── Mail/                  # Plantillas de correo (Mailables)
│   ├── Models/                # Modelos Eloquent
│   ├── Notifications/         # Notificaciones
│   ├── Observers/             # Observadores de modelos
│   ├── Providers/             # Proveedores de servicios
│   ├── Rules/                 # Reglas de validación personalizadas
│   └── Services/              # Servicios (TicketStateMachine, etc.)
├── bootstrap/
│   ├── app.php               # Bootstrap de la aplicación
│   ├── cache/                # Archivos de caché de bootstrap
│   └── providers.php         # Registro de proveedores
├── config/                   # Archivos de configuración
│   ├── app.php              # Configuración principal
│   ├── database.php         # Configuración de base de datos
│   ├── filament.php         # Configuración de Filament
│   ├── mail.php             # Configuración de correo
│   └── version.php          # Configuración de versionado
├── database/
│   ├── factories/           # Factories para pruebas
│   ├── migrations/          # Migraciones de base de datos
│   └── seeders/             # Seeders para datos iniciales
├── public/                  # Archivos públicos accesibles
│   ├── build/              # Assets compilados por Vite
│   ├── css/                # Estilos adicionales
│   ├── js/                 # Scripts adicionales
│   ├── images/             # Imágenes del sitio
│   └── index.php           # Punto de entrada
├── resources/
│   ├── css/                # Estilos fuente (Tailwind)
│   │   └── app.css         # Archivo principal de estilos
│   ├── js/                 # JavaScript fuente
│   │   └── app.js          # Archivo principal de scripts
│   └── views/              # Vistas Blade
│       ├── components/     # Componentes Blade reutilizables
│       ├── layouts/        # Layouts principales
│       ├── livewire/       # Vistas de componentes Livewire
│       └── vendor/         # Vistas de paquetes publicadas
├── routes/
│   ├── api.php             # Rutas de API
│   ├── web.php             # Rutas web
│   └── console.php         # Rutas de consola
├── storage/
│   ├── app/                # Almacenamiento de la aplicación
│   │   ├── private/        # Archivos privados
│   │   └── public/         # Archivos públicos
│   ├── framework/          # Archivos del framework
│   │   ├── cache/          # Caché
│   │   ├── sessions/       # Sesiones
│   │   └── views/          # Vistas compiladas
│   └── logs/               # Logs de la aplicación
├── tests/
│   ├── Browser/            # Tests E2E con Laravel Dusk
│   ├── Feature/            # Tests de características
│   └── Unit/               # Tests unitarios
├── .env.example            # Ejemplo de variables de entorno
├── .gitignore              # Archivos ignorados por Git
├── artisan                 # CLI de Laravel
├── composer.json           # Dependencias de PHP
├── package.json            # Dependencias de Node.js
├── phpunit.xml             # Configuración de PHPUnit
├── tailwind.config.js      # Configuración de Tailwind CSS
├── vite.config.js          # Configuración de Vite
├── CHANGELOG.md            # Registro de cambios
├── README.md               # Este archivo
└── VERSION                 # Versión actual del sistema
```

---

## 📱 API RESTful

### Autenticación

La API utiliza Laravel Sanctum para autenticación. Los endpoints públicos no requieren autenticación.

### Endpoints Disponibles

#### Tickets

**Crear Ticket**
```http
POST /api/public/tickets
Content-Type: application/json

{
  "title": "Título del ticket",
  "description": "Descripción detallada",
  "type": "Peticion",
  "priority": "Media",
  "email": "usuario@ejemplo.com",
  "name": "Nombre del Usuario"
}
```

**Consultar Ticket**
```http
GET /api/public/tickets/{ticket_number}
```

**Verificar Ticket**
```http
GET /api/public/tickets/verify/{ticket_number}
```

**Agregar Comentario**
```http
POST /api/public/tickets/{ticket_number}/comments
Content-Type: application/json

{
  "content": "Contenido del comentario"
}
```

**Cerrar Ticket**
```http
POST /api/public/tickets/{ticket_number}/close
```

#### Catálogo

**Obtener Tipos de Tickets**
```http
GET /api/public/ticket-types
```

Respuesta:
```json
{
  "data": [
    "Peticion",
    "Queja",
    "Reclamo",
    "Sugerencia",
    "Denuncia"
  ]
}
```

---

## 🧪 Pruebas Automatizadas

El sistema cuenta con una suite completa de pruebas:

### Ejecutar Pruebas

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar pruebas específicas
php artisan test --filter=TicketTest

# Ejecutar pruebas con cobertura
php artisan test --coverage

# Ejecutar tests E2E con Dusk
php artisan dusk

# Ejecutar test específico de Dusk
php artisan dusk tests/Browser/TicketTest.php
```

### Tipos de Pruebas

- **Unit Tests**: Pruebas unitarias de modelos y servicios
- **Feature Tests**: Pruebas de características y flujos completos
- **Browser Tests**: Pruebas E2E con Laravel Dusk

### Cobertura

El proyecto mantiene una cobertura de pruebas superior al 80% en componentes críticos.

---

## 🚀 Despliegue

### Despliegue en Producción

```bash
# 1. Clonar repositorio en servidor
git clone https://github.com/korozcolt/sistema-pqrsd.git

# 2. Instalar dependencias
composer install --optimize-autoloader --no-dev
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos y ejecutar migraciones
php artisan migrate --force

# 5. Compilar assets para producción
npm run build

# 6. Crear enlace simbólico
php artisan storage:link

# 7. Optimizar aplicación
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize

# 8. Configurar permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Despliegue con Docker

```bash
# Construir e iniciar contenedores
docker-compose up -d --build

# Ejecutar migraciones
docker-compose exec app php artisan migrate --force

# Ver logs
docker-compose logs -f app

# Detener contenedores
docker-compose down
```

### Despliegue con Laravel Sail

```bash
# Iniciar Sail
./vendor/bin/sail up -d

# Ejecutar comandos
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run build

# Detener Sail
./vendor/bin/sail down
```

### Configuración de Nginx

```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /ruta/a/sistema-pqrsd/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 👥 Usuarios por Defecto

Después de ejecutar los seeders (`php artisan db:seed`), el sistema crea los siguientes usuarios:

| Rol | Email | Contraseña | Permisos |
|-----|-------|------------|----------|
| SuperAdmin | admin@ejemplo.com | admin123 | Acceso total al sistema |
| Admin | gerente@ejemplo.com | gerente123 | Gestión de tickets y usuarios |
| Recepcionista | recepcion@ejemplo.com | recepcion123 | Gestión de tickets |

**⚠️ IMPORTANTE**: Cambie estas contraseñas inmediatamente en producción.

---

## 🔍 Solución de Problemas Comunes

### Problemas en Servidor Compartido

**1. Limpiar todas las cachés**:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan filament:cache-clear
php artisan optimize:clear
```

**2. Verificar permisos de directorios**:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**3. Comprobar configuración de entorno**:
- Verifique que el archivo `.env` tiene la configuración correcta
- Asegúrese de que `APP_ENV=production` y `APP_DEBUG=false`
- Verifique `APP_URL` apunta al dominio correcto

**4. Revisar logs para diagnóstico**:
- Consulte `storage/logs/laravel.log`
- Acceda al visor de logs: `https://su-dominio.com/log-viewer`
- Temporalmente active `APP_DEBUG=true` para ver errores detallados (desactivar después)

### Problemas de Correo Electrónico

**1. Verificar configuración SMTP**:
```bash
php artisan mail:test email@ejemplo.com
```

**2. Revisar cola de correos**:
```bash
php artisan queue:monitor
php artisan queue:work --verbose
```

**3. Verificar logs de correo**:
- Revisar `storage/logs/laravel.log` para errores relacionados con el correo

### Problemas con el Panel de Administración

**1. Verificar URL de acceso**:
- Panel administrativo: `https://su-dominio.com/admin`
- Login: `https://su-dominio.com/admin/login`

**2. Limpiar caché de configuración**:
```bash
php artisan config:clear
php artisan filament:cache-clear
```

**3. Problemas con assets de Filament**:
```bash
php artisan filament:assets
npm run build
```

### Problemas con la Cola de Trabajos

**1. Verificar que el worker esté corriendo**:
```bash
# Ver procesos activos
ps aux | grep queue:work

# Iniciar worker manualmente
php artisan queue:work --verbose
```

**2. Revisar trabajos fallidos**:
```bash
# Listar trabajos fallidos
php artisan queue:failed

# Reintentar trabajo específico
php artisan queue:retry [job_id]

# Reintentar todos
php artisan queue:retry all
```

### Problemas de Rendimiento

**1. Optimizar aplicación**:
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**2. Verificar configuración de caché**:
- Asegúrese de que `CACHE_STORE=database` o use Redis para mejor rendimiento

**3. Configurar OPcache**:
- Habilite OPcache en PHP para mejor rendimiento

### Error "Class not found"

```bash
composer dump-autoload
php artisan clear-compiled
php artisan optimize:clear
```

### Error "419 Page Expired"

**1. Verificar CSRF**:
- Asegúrese de que las sesiones estén configuradas correctamente
- Verifique que `SESSION_DRIVER` en `.env` sea válido

**2. Limpiar sesiones**:
```bash
php artisan session:clear
```

---

## 📚 Documentación Adicional

- [CHANGELOG.md](CHANGELOG.md) - Registro completo de cambios
- [Laravel Documentation](https://laravel.com/docs/12.x)
- [Filament Documentation](https://filamentphp.com/docs/4.x)
- [Livewire Documentation](https://livewire.laravel.com/docs/3.x)

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Cree una rama para su característica (`git checkout -b feature/AmazingFeature`)
3. Commit sus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abra un Pull Request

---

## 📄 Licencia

### Licencia Apache 2.0

[![Licencia Apache](https://img.shields.io/badge/Licencia-Apache%202.0-blue?style=for-the-badge)](http://www.apache.org/licenses/LICENSE-2.0)

#### Resumen de Términos Clave

- ✅ Uso comercial permitido
- ✅ Modificación permitida
- ✅ Distribución permitida
- ✅ Uso privado permitido
- 🔒 Cambios deben ser documentados
- 📝 Atribución al proyecto original requerida
- ⚖️ El software incluye una licencia de patente

#### Texto Completo de la Licencia

El texto completo de la licencia está disponible en el archivo [LICENSE](LICENSE) adjunto en este repositorio o en:
[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

### Responsabilidad Legal

- El software se proporciona "tal cual", sin garantías de ningún tipo
- Los autores no se hacen responsables de daños derivados del uso del software
- Se recomienda revisión legal antes de uso comercial

---

## 📞 Soporte

Para problemas, preguntas o sugerencias:

- 📧 Email: korozcolt@gmail.com
- 🐛 Issues: [GitHub Issues](https://github.com/korozcolt/sistema-pqrsd/issues)
- 💬 Discussions: [GitHub Discussions](https://github.com/korozcolt/sistema-pqrsd/discussions)

---

<div align="center">

### 🌟 Si este proyecto te ha sido útil, considera darle una estrella ⭐

---

<sub>🛡️ Licenciado bajo Apache 2.0 | © 2024-2025 Sistema PQRSD</sub>

**Desarrollado con ❤️ usando Laravel, Filament y Livewire**

</div>
