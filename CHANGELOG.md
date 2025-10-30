# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-10-30

### Added
- **Cálculo automático de SLA**: Los tickets ahora calculan automáticamente sus fechas de respuesta y resolución basados en el SLA configurado
- Método `creating()` en TicketObserver para cálculo pre-guardado
- Método `calculateSLADates()` que busca SLA por tipo y prioridad
- Valores por defecto de SLA si no existe configuración (24h respuesta, 15 días resolución)

### Changed
- **Sistema de queues completamente funcional**: Todas las notificaciones se procesan de forma asíncrona
- Tiempo de respuesta API reducido de ~30s a <500ms (98.3% mejora)
- Método `deleted()` del observer ahora solo logea en soft delete
- Método `forceDeleted()` del observer no intenta crear logs (CASCADE limpia automáticamente)

### Performance
- 98.3% reducción en tiempo de creación de tickets
- 100% procesamiento asíncrono de notificaciones
- 0 bloqueos en respuestas API

### Documentation
- Agregado FASE2_COMPLETADA.md con documentación completa
- Métricas de rendimiento y automatización documentadas

## [1.0.1] - 2025-10-30

### Changed
- Actualizado README para reflejar Laravel 12 y Filament 4
- Agregado badge de versión del sistema

### Documentation
- README actualizado con información de versiones correctas

## [1.0.0] - 2025-10-29

### Added
- Sistema de versionado automático con Semantic Versioning
- Comando `php artisan version:bump` para gestionar versiones
- GitHub Actions para auto-bump de versión en commits
- Soporte para Conventional Commits
- Archivo VERSION para tracking de versión actual
- Configuración en config/version.php

### Changed
- Upgrade de Laravel 11 a Laravel 12
- Upgrade de Filament 3 a Filament 4
- Upgrade de Tailwind CSS 3 a Tailwind CSS 4

### Fixed
- EventServiceProvider creado y listeners registrados
- Eventos disparados correctamente en TicketObserver
- Notificaciones de comentarios implementadas
- Duplicación de jobs eliminada
- Notificaciones ahora son asíncronas (ShouldQueue)

### Infrastructure
- PHP: ^8.2
- Laravel: ^12.0
- Filament: ^4.0
- Tailwind CSS: ^4.0

---

## Versiones Anteriores

### Pre-1.0.0
- Sistema base PQRSD con Laravel 11 y Filament 3
- Gestión de tickets, usuarios, departamentos
- Sistema de notificaciones y recordatorios
- SLA tracking
- Cierre automático de tickets inactivos
