<div align="center">
    <img src="https://torcoromaweb.com/images/logo.png" alt="Logo Torcoroma WEB" width="200"/>

# Torcoroma WEB 🌐

**Plataforma Integral de Gestión de PQRS para Transporte**

[![Versión PHP](https://img.shields.io/badge/PHP-8.2%2B-blue?style=for-the-badge&logo=php)](https://www.php.net/)
[![Versión Laravel](https://img.shields.io/badge/Laravel-11.x-red?style=for-the-badge&logo=laravel)](https://laravel.com/)
[![Estado de Construcción](https://img.shields.io/badge/build-passing-brightgreen?style=for-the-badge&logo=github)](https://github.com/korozcolt/torcoroma_web_project)
[![Licencia](https://img.shields.io/badge/licencia-Apache%202.0-green?style=for-the-badge)](LICENSE)

</div>

## 🚀 Descripción del Proyecto

Torcoroma WEB es un sistema avanzado de gestión de Peticiones, Quejas, Reclamos y Sugerencias (PQRS) diseñado específicamente para empresas de transporte en Colombia. Cumple con las normativas de Supertransporte 2025 y ofrece una experiencia administrativa completa a través de un panel Filament altamente personalizado.

## ✨ Características Principales

- 🎫 **Sistema de Tickets PQRS**: Gestión completa del ciclo de vida de tickets según normativa colombiana
- ⏱️ **Gestión de SLA**: Configuración de tiempos de respuesta y resolución según tipos de ticket
- 🔔 **Sistema de Recordatorios**: Notificaciones automáticas para plazos de respuesta y resolución
- 📊 **Paneles Analíticos**: Estadísticas detalladas sobre tickets y tiempos de respuesta
- 👥 **Control de Roles**: Jerarquía de usuarios (SuperAdmin, Admin, Recepcionista, Usuario Web)
- 🏢 **Gestión de Departamentos**: Organización por áreas administrativas
- 🏷️ **Sistema de Etiquetas**: Categorización flexible de tickets
- 📱 **API RESTful**: Interfaz de programación para integración con aplicaciones móviles
- 📄 **Generador de Sitemap**: Creación automática de sitemaps para SEO
- 🌐 **Portal Web para Clientes**: Interfaz pública para creación y seguimiento de tickets
- 📣 **Notificaciones Multi-Canal**: Correo electrónico y sistema interno de notificaciones

## 🧩 Módulos del Sistema

### 📋 Sistema de Tickets PQRS
- Creación, seguimiento y resolución de tickets
- Categorización por tipo (Petición, Queja, Reclamo, Sugerencia)
- Asignación de prioridades (Baja, Media, Alta, Urgente)
- Control de estados (Pendiente, En Progreso, Resuelto, Cerrado, Rechazado, Reabierto)
- Comentarios públicos y privados
- Gestión de archivos adjuntos

### 🔌 API RESTful
- Endpoints para creación y consulta de tickets
- Autenticación segura
- Integración con aplicaciones de terceros

### 📱 Portal Web para Clientes
- Formulario de creación de tickets
- Sistema de consulta de estado
- Comunicación directa con el equipo de soporte
- Diseño responsive para móviles y tablets

## 🖥️ Requisitos del Servidor

### 📋 Extensiones PHP Mínimas

- ✅ PHP >= 8.2
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

### 🛢️ Bases de Datos Soportadas
- MySQL 8.0+
- MariaDB 10.5+
- PostgreSQL 13.0+
- SQLite 3.8.8+

## 🛠️ Instalación y Configuración

### Inicio Rápido

```bash
# Clonar el repositorio
git clone https://github.com/korozcolt/torcoroma_web_project.git

# Navegar al directorio del proyecto
cd torcoroma-web

# Instalar dependencias
composer install
npm install

# Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Compilar assets
npm run build

# Iniciar servidor de desarrollo local
php artisan serve
```

### Configuración del Programador de Tareas

Para que los recordatorios de tickets y otras tareas programadas funcionen automáticamente, configure el programador de tareas de Laravel:

```bash
# Añadir al crontab del servidor
* * * * * cd /ruta/a/su/proyecto && php artisan schedule:run >> /dev/null 2>&1
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
```

## 🗄️ Estructura de la Base de Datos

El sistema utiliza las siguientes tablas principales:

- **users**: Usuarios del sistema con roles definidos
- **departments**: Departamentos o áreas de la empresa
- **tickets**: Tickets PQRS con información detallada
- **slas**: Configuración de acuerdos de nivel de servicio
- **ticket_logs**: Historial de cambios en tickets
- **ticket_comments**: Comentarios en tickets
- **ticket_attachments**: Archivos adjuntos a tickets
- **reminders**: Sistema de recordatorios para plazos de tickets
- **tags**: Etiquetas para categorización de tickets

## 📂 Estructura de Directorios Principales

```
app/
├── Console/Commands/          # Comandos Artisan personalizados
├── Enums/                     # Enumeraciones para tipos y estados
├── Events/                    # Eventos del sistema
├── Filament/                  # Recursos para el panel admin (Filament)
├── Http/Controllers/          # Controladores
├── Jobs/                      # Trabajos en cola
├── Listeners/                 # Oyentes de eventos
├── Livewire/                  # Componentes Livewire
├── Mail/                      # Plantillas de correo
├── Models/                    # Modelos Eloquent
├── Notifications/             # Notificaciones
├── Observers/                 # Observadores de modelos
├── Providers/                 # Proveedores de servicios
├── Rules/                     # Reglas de validación personalizadas
└── Services/                  # Servicios de la aplicación

config/                        # Archivos de configuración
database/
├── migrations/                # Migraciones de la base de datos
└── seeders/                   # Seeders para datos iniciales

public/                        # Archivos públicos
├── build/                     # Assets compilados
├── css/                       # Hojas de estilo
├── js/                        # Scripts JavaScript
└── images/                    # Imágenes del sitio

resources/
├── css/                       # Estilos fuente
├── js/                        # JavaScript fuente
└── views/                     # Vistas Blade

routes/                        # Definición de rutas
├── api.php                    # Rutas de API
├── web.php                    # Rutas web
└── console.php                # Rutas de consola

storage/                       # Almacenamiento de la aplicación
tests/                         # Pruebas automatizadas
```

## 👥 Equipo

### Desarrolladores Principales

| Nombre | Rol | Contacto | Contribuciones |
|--------|-----|----------|----------------|
| Kristian Orozco | Desarrollador Líder | [@kronnos](https://github.com/korozcolt/) | Arquitectura, Backend, ChatBot |

### Contribuidores

[![Contribuidores](https://img.shields.io/github/contributors/korozcolt/torcoroma_web_project?style=for-the-badge)](https://github.com/korozcolt/torcoroma_web_project/graphs/contributors)

- Agradecemos a todos los contribuidores que hacen posible este proyecto
- Las contribuciones son bienvenidas bajo los términos de la Licencia Apache 2.0

## 🌟 Nuevas Características (2025)

### API RESTful Mejorada
- Endpoints completos para gestión de tickets desde aplicaciones externas
- Autenticación mediante tokens seguros
- Integración con aplicaciones de terceros

### Portal Web Mejorado
- Formulario integrado para creación de tickets por usuarios
- Sistema de seguimiento de tickets vía web
- Diseño responsive optimizado para móviles
- Integración de ReCaptcha para prevención de spam

### Mejoras de Rendimiento
- Optimización de consultas a la base de datos
- Implementación de cache para aceleración de respuestas
- Compresión de assets para reducción de tiempos de carga
- Generación automática de sitemap para SEO

### Automatizaciones
- Sistema de recordatorios automáticos para tickets pendientes
- Notificaciones por correo para actualizaciones de tickets
- Cierre automático de tickets inactivos
- Generación de reportes periódicos

## 🔍 Solución de Problemas Comunes

### Problemas en Servidor Compartido

Si encuentra problemas al desplegar en servidor compartido:

1. **Limpiar todas las cachés**:

   ```
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   php artisan optimize:clear
   ```

2. **Verificar permisos de directorios**:

   ```
   chmod -R 775 storage bootstrap/cache
   ```

3. **Comprobar configuración de entorno**:
   - Verifique que el archivo `.env` tiene la configuración correcta
   - Asegúrese de que `APP_ENV=production` y `APP_DEBUG=false`

4. **Revisar logs para diagnóstico**:
   - Consulte `storage/logs/laravel.log`
   - Acceda al visor de logs integrado en: `https://su-dominio.com/log-viewer`
   - Temporalmente active `APP_DEBUG=true` para ver errores detallados

   > **Nota**: La aplicación incluye un Log Viewer mejorado disponible en `https://tickets.torcoromaweb.com/log-viewer` que proporciona una interfaz organizada para analizar los logs del sistema.

### Problemas de Correo Electrónico

Si experimenta problemas con el envío de correos:

1. **Verificar configuración SMTP**:
   ```
   php artisan mail:test
   ```

2. **Revisar cola de correos**:
   ```
   php artisan queue:monitor
   ```

3. **Verificar logs de correo**:
   - Revisar `storage/logs/laravel.log` para errores relacionados con el correo

### Problemas con el Panel de Administración

Si no puede acceder al panel de administración:

1. **Verificar credenciales de administrador**:
   ```
   php artisan user:info admin@cooptorcoroma.com
   ```

2. **Restablecer contraseña de administrador**:
   ```
   php artisan user:reset-password admin@cooptorcoroma.com
   ```

3. **Comprobar permisos de archivos**:
   ```
   php artisan filament:check-permissions
   ```

## 📄 Licencia

### Licencia Apache 2.0

[![Licencia Apache](https://img.shields.io/badge/Licencia-Apache%202.0-blue?style=for-the-badge)](http://www.apache.org/licenses/LICENSE-2.0)

#### Resumen de Términos Clave

- ✅ Uso comercial permitido
- ✅ Modificación
- ✅ Distribución
- ✅ Uso privado
- 🔒 Cambios deben ser documentados
- 📝 Atribución al proyecto original requerida

#### Texto Completo de la Licencia

El texto completo de la licencia está disponible en el archivo LICENSE adjunto en este repositorio.

### Responsabilidad Legal

- El software se proporciona "tal cual", sin garantías
- Torcoroma WEB no se hace responsable de daños derivados del uso
- Se recomienda revisión legal antes de uso comercial

---

<div align="center">
    <sub>🛡️ Licenciado bajo Apache 2.0 | © 2025 Torcoroma WEB</sub>
</div>
