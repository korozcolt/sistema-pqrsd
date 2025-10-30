# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
