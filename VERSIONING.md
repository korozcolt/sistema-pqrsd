# 🔖 Sistema de Versionado Automático

Este proyecto utiliza **Semantic Versioning 2.0.0** con versionado automático basado en **Conventional Commits**.

---

## 📋 Tabla de Contenidos

1. [Semantic Versioning](#semantic-versioning)
2. [Conventional Commits](#conventional-commits)
3. [Uso Manual](#uso-manual)
4. [Automatización (GitHub Actions)](#automatizacion)
5. [Archivos del Sistema](#archivos)
6. [Ejemplos](#ejemplos)
7. [FAQ](#faq)

---

## 📊 Semantic Versioning {#semantic-versioning}

El proyecto sigue el formato: **MAJOR.MINOR.PATCH**

```
1.2.3
│ │ │
│ │ └─ PATCH: Bug fixes, pequeñas mejoras
│ └─── MINOR: Nuevas funcionalidades (backward compatible)
└───── MAJOR: Cambios incompatibles (breaking changes)
```

### Cuándo incrementar cada número:

| Tipo | Cuándo | Ejemplo |
|------|--------|---------|
| **MAJOR** | Cambios incompatibles en la API | `1.5.2` → `2.0.0` |
| **MINOR** | Nueva funcionalidad compatible | `1.5.2` → `1.6.0` |
| **PATCH** | Bug fixes y mejoras menores | `1.5.2` → `1.5.3` |

---

## 📝 Conventional Commits {#conventional-commits}

El sistema detecta automáticamente el tipo de cambio basándose en el mensaje del commit.

### Formato:

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Tipos de Commit:

| Tipo | Bump | Descripción | Ejemplo |
|------|------|-------------|---------|
| `feat:` | **MINOR** | Nueva funcionalidad | `feat: agregar autenticación 2FA` |
| `feat!:` | **MAJOR** | Nueva funcionalidad (breaking) | `feat!: cambiar API de usuarios` |
| `fix:` | **PATCH** | Bug fix | `fix: corregir validación de email` |
| `perf:` | **PATCH** | Mejora de rendimiento | `perf: optimizar consultas SQL` |
| `refactor:` | **PATCH** | Refactorización | `refactor: simplificar TicketObserver` |
| `docs:` | **PATCH** | Documentación | `docs: actualizar README` |
| `style:` | **PATCH** | Formato de código | `style: aplicar PSR-12` |
| `test:` | **PATCH** | Tests | `test: agregar tests de tickets` |
| `chore:` | **PATCH** | Tareas de mantenimiento | `chore: actualizar dependencias` |

### Breaking Changes:

Cualquier commit con `!` o `BREAKING CHANGE:` en el footer incrementa **MAJOR**:

```bash
feat!: cambiar estructura de API

BREAKING CHANGE: Los endpoints de usuarios ahora requieren autenticación
```

---

## 🛠️ Uso Manual {#uso-manual}

### Comando Artisan

```bash
# Incrementar PATCH (1.0.0 → 1.0.1)
php artisan version:bump patch

# Incrementar MINOR (1.0.0 → 1.1.0)
php artisan version:bump minor

# Incrementar MAJOR (1.0.0 → 2.0.0)
php artisan version:bump major

# Con mensaje personalizado
php artisan version:bump minor --message="feat: nueva funcionalidad de reportes"

# Sin crear commit automático
php artisan version:bump patch --no-commit

# Dry run (ver qué pasaría sin hacer cambios)
php artisan version:bump minor --dry-run
```

### Lo que hace el comando:

1. ✅ Lee la versión actual de `VERSION`
2. ✅ Calcula la nueva versión
3. ✅ Actualiza archivo `VERSION`
4. ✅ Actualiza `config/version.php`
5. ✅ Actualiza `.env` (APP_VERSION)
6. ✅ Crea commit de git
7. ✅ Crea tag de git (v1.2.3)

### Ejemplo de flujo manual:

```bash
# 1. Hacer cambios
git add .
git commit -m "feat: agregar dashboard de métricas"

# 2. Bump version
php artisan version:bump minor

# 3. Push con tags
git push origin master --tags
```

---

## 🤖 Automatización (GitHub Actions) {#automatizacion}

El sistema incluye un **GitHub Action** que automáticamente:

1. 🔍 Detecta el tipo de commit (feat, fix, etc.)
2. 📈 Incrementa la versión según corresponda
3. 📝 Actualiza el CHANGELOG.md
4. 🏷️ Crea tag y release en GitHub
5. ✅ Hace push automáticamente

### Configuración

El archivo `.github/workflows/auto-version.yml` se activa cuando:

- ✅ Se hace push a `master` o `main`
- ✅ El commit **NO** contiene `[skip ci]`
- ✅ El commit **NO** es un bump de versión

### Detección Automática:

```bash
# Estos commits disparan auto-bump:

git commit -m "feat: nueva funcionalidad"          # → MINOR bump
git commit -m "fix: corregir bug"                  # → PATCH bump
git commit -m "feat!: cambio incompatible"         # → MAJOR bump
git commit -m "perf: optimizar queries"            # → PATCH bump

# Estos NO disparan auto-bump:

git commit -m "chore: bump version to 1.2.0 [skip ci]"
git commit -m "docs: actualizar README [skip ci]"
```

### Saltar CI:

Si no quieres que se ejecute el versionado automático:

```bash
git commit -m "docs: actualizar README [skip ci]"
```

---

## 📁 Archivos del Sistema {#archivos}

```
proyecto/
├── VERSION                              # Versión actual (1.0.0)
├── CHANGELOG.md                         # Historial de cambios
├── config/version.php                   # Configuración de versión
├── app/Console/Commands/
│   └── BumpVersionCommand.php          # Comando artisan
└── .github/workflows/
    └── auto-version.yml                # GitHub Action
```

### VERSION
```
1.0.0
```

### config/version.php
```php
return [
    'version' => env('APP_VERSION', '1.0.0'),
    'build' => env('APP_BUILD', null),
    'release_date' => env('APP_RELEASE_DATE', '2025-10-29'),
];
```

### .env
```bash
APP_VERSION=1.0.0
APP_RELEASE_DATE=2025-10-29
```

---

## 📚 Ejemplos {#ejemplos}

### Ejemplo 1: Bug Fix

```bash
# 1. Corregir bug
git add .
git commit -m "fix: corregir validación de email en formulario"
git push origin master

# → GitHub Action detecta "fix:"
# → Incrementa PATCH: 1.0.0 → 1.0.1
# → Crea tag v1.0.1
# → Actualiza CHANGELOG.md
```

### Ejemplo 2: Nueva Funcionalidad

```bash
# 1. Agregar funcionalidad
git add .
git commit -m "feat: agregar exportación de tickets a PDF"
git push origin master

# → GitHub Action detecta "feat:"
# → Incrementa MINOR: 1.0.1 → 1.1.0
# → Crea tag v1.1.0
```

### Ejemplo 3: Breaking Change

```bash
# 1. Cambio incompatible
git add .
git commit -m "feat!: cambiar estructura de API de tickets

BREAKING CHANGE: Los endpoints ahora requieren autenticación OAuth2"
git push origin master

# → GitHub Action detecta "feat!" o "BREAKING CHANGE:"
# → Incrementa MAJOR: 1.1.0 → 2.0.0
# → Crea tag v2.0.0
```

### Ejemplo 4: Múltiples Commits

```bash
# Varios commits antes de push
git commit -m "fix: corregir bug en login"
git commit -m "feat: agregar filtros avanzados"
git commit -m "docs: actualizar README"
git push origin master

# → Solo el último commit determina el bump
# → Como termina en "docs:", será PATCH
# → 1.0.0 → 1.0.1
```

---

## 🎯 Ver Versión Actual {#ver-version}

### En código:

```php
// Obtener versión
$version = config('version.version');
echo $version; // 1.0.0

// Obtener fecha de release
$release = config('version.release_date');
```

### En Blade:

```blade
<footer>
    Version {{ config('version.version') }}
    Released: {{ config('version.release_date') }}
</footer>
```

### En Filament:

```php
// En dashboard o footer
use Filament\Support\Facades\FilamentView;

FilamentView::registerRenderHook(
    'panels::footer',
    fn () => view('components.version')
);
```

### En terminal:

```bash
# Ver versión actual
cat VERSION

# Ver con detalles
php artisan tinker
>>> config('version.version')
>>> config('version.release_date')
```

---

## ❓ FAQ {#faq}

### ¿Puedo usar esto sin GitHub?

Sí, usa el comando manual:
```bash
php artisan version:bump patch
git push origin master --tags
```

### ¿Cómo empiezo desde cero?

```bash
# Crear versión inicial
echo "0.1.0" > VERSION
php artisan version:bump minor --message="feat: initial release"
```

### ¿Qué pasa si cometo un error?

```bash
# Deshacer último bump
git reset --hard HEAD~1
git tag -d v1.2.3
git push origin :refs/tags/v1.2.3
```

### ¿Puedo personalizar el formato?

Sí, edita:
- `app/Console/Commands/BumpVersionCommand.php` (lógica)
- `.github/workflows/auto-version.yml` (CI/CD)

### ¿Cómo veo todas las versiones?

```bash
# Tags de git
git tag -l

# Releases de GitHub
gh release list

# CHANGELOG
cat CHANGELOG.md
```

### ¿Funciona con GitLab o Bitbucket?

El comando artisan funciona en cualquier sistema.
Para CI/CD, necesitas adaptar `.github/workflows/auto-version.yml` a GitLab CI o Bitbucket Pipelines.

---

## 🔗 Referencias

- [Semantic Versioning 2.0.0](https://semver.org/)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [Keep a Changelog](https://keepachangelog.com/)

---

## 📞 Soporte

Si tienes problemas con el sistema de versionado:

1. Revisa los logs de GitHub Actions
2. Verifica que tu commit sigue Conventional Commits
3. Usa `--dry-run` para probar sin hacer cambios
4. Consulta este documento

---

*Sistema de versionado implementado en Sistema PQRSD - v1.0.0*
