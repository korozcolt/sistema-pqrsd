# Arquitectura de Autenticación - Sistema PQRSD

**Fecha:** 2025-11-02
**Versión:** 1.1.1

---

## ✅ Decisión de Arquitectura

**El sistema usa ÚNICAMENTE Filament para autenticación.**

### ¿Por qué?

- ✅ **Consistencia:** Una sola interfaz de usuario para todos los usuarios
- ✅ **Simplicidad:** No mantener dos sistemas de auth (Breeze + Filament)
- ✅ **Filament completo:** Panel admin robusto con autenticación integrada
- ✅ **Menos código:** No duplicar vistas, lógica, ni rutas de auth

---

## 🔐 URLs de Autenticación

### Login
```
URL: /admin/login
Método: GET, POST
Componente: Filament Auth
Traducción: Español (es)
```

### Panel Principal
```
URL: /admin
Método: GET
Componente: Filament Dashboard
Requiere: Autenticación
```

### Logout
```
URL: /admin/logout (manejado por Filament)
Método: POST
```

---

## 👥 Tipos de Usuarios

Todos los usuarios acceden por `/admin`:

| Rol | Acceso | Panel |
|-----|--------|-------|
| **superadmin** | /admin | Full access |
| **admin** | /admin | Admin panel |
| **receptionist** | /admin | Limited panel |
| **user_web** | /admin | User panel |

**Nota:** Filament maneja permisos y vistas según rol.

---

## ❌ Rutas Obsoletas (NO USAR)

Las siguientes rutas de Laravel Breeze **NO deberían usarse:**

```php
// ❌ NO USAR - Obsoletas de Breeze
/login          // Usar: /admin/login
/register       // Usar: Filament user creation
/dashboard      // Usar: /admin
/forgot-password // Usar: Filament password reset (si está habilitado)
```

### Acción Recomendada

Considerar **eliminar** estas rutas de `routes/auth.php` en el futuro para evitar confusión.

---

## 🧪 Tests E2E

Los tests E2E deben probar **SOLO el login de Filament:**

```php
// ✅ CORRECTO
$browser->visit('/admin/login')

// ❌ INCORRECTO
$browser->visit('/login')
```

**Ubicación de tests:** `tests/Browser/Filament/`

---

## 📝 Textos de Interfaz (Español)

### Página de Login (`/admin/login`)

```
Título: "Sistema PQRSD"
Subtítulo: "Entre a su cuenta"

Campos:
- Correo electrónico
- Contraseña

Opciones:
- Recordarme (checkbox)

Botón:
- Entrar
```

### Mensajes de Error

```
- "Las credenciales no coinciden con nuestros registros"
- "El campo correo electrónico es obligatorio"
- "El campo contraseña es obligatorio"
```

---

## 🔧 Configuración

### Archivo `.env`

```env
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
```

### Filament Config

Filament se configura automáticamente con:
- Idioma español
- Panel en `/admin`
- Auth integrado

---

## 🚀 Migrando de Breeze a Filament

Si tienes código que usa rutas de Breeze:

### Antes (Breeze)
```php
// ❌ Viejo
route('login')           // /login
route('dashboard')       // /dashboard
Auth::routes();
```

### Después (Filament)
```php
// ✅ Nuevo
route('filament.admin.auth.login')  // /admin/login
route('filament.admin.pages.dashboard') // /admin
// Filament maneja rutas automáticamente
```

---

## 📊 Estado Actual

### Tests E2E de Login (Filament)

✅ **5 de 5 tests pasando (100%)**

| Test | Estado |
|------|--------|
| Ver página de login | ✅ PASS |
| Iniciar sesión exitoso | ✅ PASS |
| Error con credenciales inválidas | ✅ PASS |
| Validación de campos requeridos | ✅ PASS |
| Acceso directo autenticado | ✅ PASS |

**Archivo:** `tests/Browser/Filament/LoginTest.php`

---

## 📚 Documentación Relacionada

- **Filament Docs:** https://filamentphp.com/docs/4.x
- **Tests E2E:** `ESTADO_TESTS_E2E.md`
- **Plan General:** `PLAN_TESTS_E2E_Y_MANUAL.md`

---

## ⚠️ Importante para Desarrolladores

1. **No crear rutas de auth fuera de Filament**
2. **Todos los usuarios usan `/admin`**
3. **Tests E2E deben probar Filament, no Breeze**
4. **Considerar eliminar archivos de Breeze** si no se usan

---

**Última actualización:** 2025-11-02
**Próxima revisión:** Considerar eliminar Breeze completamente
