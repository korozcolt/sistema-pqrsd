# Changelog

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es/1.0.0/),
y este proyecto se adhiere a [Semantic Versioning](https://semver.org/lang/es/).

## [1.1.1] - 2025-11-02

### Añadido
- Suite completa de tests E2E mapeando todo el sistema
- Configuración de Laravel Dusk para pruebas E2E en Filament v4
- Suite completa de tests para automatizaciones
- Ruta home para mostrar vista de bienvenida

### Cambiado
- Documentación movida a carpeta .claude/
- Actualización de .gitignore para excluir configuraciones de AI/IDE

### Removido
- Documentación de fases 2 y 3 completadas (movida a .claude/)

## [1.1.0] - 2025-10-30

### Añadido
- **Cálculo automático de SLA**: Los tickets ahora calculan automáticamente sus fechas de respuesta y resolución basados en el SLA configurado
- Método `creating()` en TicketObserver para cálculo pre-guardado
- Método `calculateSLADates()` que busca SLA por tipo y prioridad
- Valores por defecto de SLA si no existe configuración (24h respuesta, 15 días resolución)
- Permisos para ejecutar pruebas y restaurar cambios en settings.local.json

### Cambiado
- **Sistema de queues completamente funcional**: Todas las notificaciones se procesan de forma asíncrona
- Tiempo de respuesta API reducido de ~30s a <500ms (98.3% mejora)
- Método `deleted()` del observer ahora solo logea en soft delete
- Método `forceDeleted()` del observer no intenta crear logs (CASCADE limpia automáticamente)
- Actualización de permisos en settings.local.json
- Ajustes en .gitignore

### Performance
- 98.3% reducción en tiempo de creación de tickets
- 100% procesamiento asíncrono de notificaciones
- 0 bloqueos en respuestas API

### Documentación
- Agregado FASE2_COMPLETADA.md con documentación completa
- Métricas de rendimiento y automatización documentadas

## [1.0.1] - 2025-10-30

### Añadido
- Sistema de versionado automático con Semantic Versioning
- Badge de versión en README
- Resumen ejecutivo del sistema de versionado

### Cambiado
- README actualizado con Laravel 12 y información de versiones correctas

## [1.0.0] - 2025-10-29

### Añadido
- Upgrade a Laravel 12 desde Laravel 11
- Upgrade a Filament 4 desde Filament 3
- Upgrade a Tailwind CSS 4 desde Tailwind CSS 3
- Sistema de versionado automático con Semantic Versioning
- Comando `php artisan version:bump` para gestionar versiones
- GitHub Actions para auto-bump de versión en commits
- Soporte para Conventional Commits
- Archivo VERSION para tracking de versión actual
- Configuración en config/version.php
- Seeder para usuario SuperAdmin personalizado
- Análisis completo del flujo de información y trazabilidad del sistema PQRSD
- Información de licencia para Chart.js
- Documentación de Fase 1 completada

### Cambiado
- Actualización de configuración y documentación del sistema PQRSD

### Corregido
- EventServiceProvider creado y listeners registrados
- Eventos disparados correctamente en TicketObserver
- Notificaciones de comentarios implementadas
- Duplicación de jobs eliminada
- Notificaciones ahora son asíncronas (ShouldQueue)
- Fase 1: correcciones críticas del flujo de información

### Infraestructura
- PHP: ^8.2
- Laravel: ^12.0
- Filament: ^4.0
- Livewire: ^3.4
- Tailwind CSS: ^4.0

## [0.9.0] - 2025-04-28

### Cambiado
- Eliminada implementación de ShouldQueue en notificación de nuevos tickets
- Eliminado middleware de autenticación y autorización en ruta de pruebas de notificaciones

### Añadido
- Controlador de pruebas de notificaciones
- Ruta para pruebas de envío de notificaciones

## [0.8.0] - 2025-04-27

### Añadido
- Generación automática de números de ticket
- Comando para probar notificaciones por correo electrónico

### Cambiado
- Refactor del manejo de eventos de tickets para utilizar registros de log en lugar de eventos
- Optimización del observador de tickets
- Notificaciones de tickets actualizadas para usar variable de entorno para email de PQRs

## [0.7.0] - 2025-04-26

### Añadido
- Registro manual de cambios de estado de tickets con valores de enums correctos

### Cambiado
- Refactor del manejo de cambios de estado de tickets usando registros de log en lugar de eventos

### Corregido
- Mejora en registro de cambios de estado en creación de tickets al manejar usuarios sin ID

## [0.6.0] - 2025-04-23

### Añadido
- Filtrado de tickets activos
- Generación automática de número de ticket

### Cambiado
- Registro de cambios de estado de tickets ahora se hace manualmente en lugar de usar eventos

## [0.5.0] - 2025-04-08

### Añadido
- Comando para probar notificaciones por correo electrónico en el sistema
- Comando para normalizar emails de usuarios eliminando tildes y caracteres especiales
- Gestión de tickets inactivos con notificaciones y cierre automático
- Comando log:info para registrar mensajes en el log
- Ruta para publicar el visor de logs con verificación de acceso

### Cambiado
- Versiones de PHP y Laravel actualizadas
- Documentación del proyecto mejorada

### Corregido
- Nombre de evento corregido al crear un ticket
- Colores de estado simplificados en tabla de tickets
- Verificación de autenticación eliminada en ruta de publicación del visor de logs
- Enlaces en README.md corregidos para apuntar al repositorio correcto

### Removido
- Línea de registro de cron comentada en programación de tareas

## [0.4.0] - 2025-04-05

### Añadido
- Badge de navegación para contar recordatorios y etiquetas
- Componente ACE editor con soporte de temas dinámicos y carga de extensiones

### Cambiado
- Refactor de migraciones y seeders de base de datos
- Descripción del proyecto actualizada en README
- Sección de instalación mejorada en README.md
- Fusión de rama dev con master

### Removido
- Archivos de bloqueo eliminados del control de versiones

## [0.3.0] - 2025-02-19

### Añadido
- Columna de estado a tablas de contenido, menús, elementos de menú, contenidos y widgets

### Cambiado
- Dependencias actualizadas
- Configuración de Docker mejorada

## [0.2.0] - 2025-01-26

### Añadido
- Soporte para despliegues en solicitudes de extracción
- Configuración de navegación
- Flujo de despliegue mejorado en GitHub Actions con permisos y optimización de caché
- Nueva configuración de servidor

### Cambiado
- Estado de temas eliminado en migración

### Removido
- Controlador de soporte eliminado de la ruta de verificación
- Desencadenador de solicitudes de extracción eliminado del flujo de despliegue

## [0.1.0] - 2025-01-26

### Añadido
- Nuevas secciones y componentes al sistema
- Actualización de .gitignore
- Registro del servicio de secciones

## [0.0.1] - 2024-12-15

### Añadido
- Comandos de CMS refactorizados con mejor legibilidad y formato de firmas
- Enums para tipos de componentes y estados de contenido
- Clases para gestión de SEO y construcción de páginas
- Configuración de imágenes
- Recursos para Menú, Página, Tema y Widget en Filament

## [0.0.1-alpha] - 2024-11-16

### Añadido
- Plantillas de email refactorizadas
- Plantilla de email para formulario de contacto
- Archivos de PHPStorm y IDE helper
- Límite de memoria para servicio laravel en docker-compose
- Restricción de acceso a red local
- Paso de migración de base de datos en flujo de despliegue
- Optimización de comandos Docker
- Commit inicial del proyecto

### Cambiado
- Páginas de recursos de Filament refactorizadas (SLA, Tag, User, Department, Reminder, TicketLog, TicketComment)
- Puerto 8080 mapeado correctamente en docker-compose
- Uso de última versión de actions/checkout en flujo de despliegue

## [0.0.1-pre-alpha] - 2024-11-05

### Añadido
- Configuración inicial del proyecto
- Sistema base PQRSD con gestión de tickets
- Gestión de usuarios y departamentos
- Sistema de notificaciones y recordatorios
- SLA tracking
- Sistema de etiquetas (tags) para tickets
- Sistema de comentarios en tickets
- Sistema de adjuntos en tickets
- Logs de actividad de tickets
- Configuración de Docker y Docker Compose
- Flujos de GitHub Actions para despliegue

---

## Leyenda

- **Añadido**: nuevas características
- **Cambiado**: cambios en funcionalidad existente
- **Deprecado**: características que serán removidas pronto
- **Removido**: características removidas
- **Corregido**: correcciones de bugs
- **Seguridad**: cambios relacionados con vulnerabilidades
