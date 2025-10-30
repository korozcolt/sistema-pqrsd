# 📊 Análisis Completo del Flujo de Información - Sistema PQRSD

## 📋 Índice
1. [Mapeo de Modelos y Relaciones](#mapeo-de-modelos)
2. [Flujo de Creación de Tickets](#flujo-creacion)
3. [Sistema de Notificaciones](#sistema-notificaciones)
4. [Automatizaciones y Jobs](#automatizaciones)
5. [Trazabilidad y Logs](#trazabilidad)
6. [Problemas Identificados](#problemas)
7. [Mejoras Propuestas](#mejoras)

---

## 🗺️ Mapeo de Modelos y Relaciones {#mapeo-de-modelos}

### Modelos Principales

```
User
├── tickets (hasMany Ticket)
├── comments (hasMany TicketComment)
├── attachments (hasMany TicketAttachment)
└── reminders (hasMany Reminder)

Ticket
├── user (belongsTo User)
├── department (belongsTo Department)
├── sla (belongsTo SLA) - relación dinámica por tipo y prioridad
├── comments (hasMany TicketComment)
├── attachments (hasMany TicketAttachment)
├── logs (hasMany TicketLog) ✅ TRAZABILIDAD
├── reminders (hasMany Reminder)
└── tags (belongsToMany Tag)

Department
├── tickets (hasMany Ticket)
└── slas (hasMany SLA)

SLA
├── tickets (hasMany Ticket)
└── department (belongsTo Department)
```

---

## 🔄 Flujo de Creación de Tickets {#flujo-creacion}

### 1️⃣ Punto de Entrada: API Pública

**Controller:** `App\Http\Controllers\Api\PublicTicketController@store`

```
┌─────────────────────────────────────┐
│  Cliente envía formulario web       │
│  POST /api/tickets                  │
└─────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  Validación de datos                │
│  - Nombre, email, título, desc      │
│  - Tipo de ticket                   │
└─────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  Verificar/Crear Usuario            │
│  User::firstOrCreate()              │
└─────────────────────────────────────┘
                 │
                 ├─ Si usuario NUEVO
                 │  └─► 📧 NewUserCredentials
                 │
                 ▼
┌─────────────────────────────────────┐
│  Crear Ticket                       │
│  Ticket::create()                   │
│  - Estado: Pending                  │
│  - Prioridad: Medium                │
│  - Department: auto-asignado        │
└─────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  TRIGGER: TicketObserver@created    │
└─────────────────────────────────────┘
                 │
                 ├─► 📧 NewTicketNotification → Usuario
                 ├─► 📧 NewTicketNotification → Email Config
                 └─► 📝 TicketLog creado (status: null → pending)
```

### 2️⃣ Punto de Entrada: Panel Filament

**Resource:** `App\Filament\Resources\TicketResource`

Mismo flujo que la API pero iniciado desde el panel administrativo.

---

## 📨 Sistema de Notificaciones {#sistema-notificaciones}

### Notificaciones Disponibles

| Notificación | Trigger | Destinatarios | Canal |
|-------------|---------|---------------|-------|
| `NewTicketNotification` | Ticket creado | Usuario + Email Config | Mail |
| `NewUserCredentials` | Usuario nuevo | Usuario nuevo | Mail |
| `TicketStatusUpdated` | Status cambió | Usuario + Email Config | Mail |
| `NewTicketCommentNotification` | Comentario nuevo | Usuario + Email Config | Mail |
| `TicketReminderNotification` | Job de recordatorios | Usuario + Email Config | Mail |
| `TicketInactivityWarningNotification` | Ticket inactivo 7 días | Usuario + Asignado | Mail |
| `TicketInactivityClosedNotification` | Ticket cerrado automáticamente | Usuario + Asignado | Mail |

### 🚨 PROBLEMA IDENTIFICADO #1: Notificaciones sin Queue

```php
// En TicketObserver@created
$ticket->user->notify(new NewTicketNotification($ticket));
```

**Problema:** Las notificaciones se envían de forma **síncrona** bloqueando la respuesta al usuario.

**Impacto:** Tiempo de respuesta lento si el servidor de email falla o es lento.

---

## ⚙️ Automatizaciones y Jobs {#automatizaciones}

### Jobs Programados (routes/console.php)

```php
// ⏰ Cada hora
Schedule::job(new ProcessTicketReminders)->hourly();

// ⏰ Cada 5 minutos
Schedule::command('tickets:check-reminders')->everyFiveMinutes();

// ⏰ Cada día
Schedule::command('tickets:mark-inactive')->daily();

// ⏰ Cada hora
Schedule::command('tickets:close-inactive')->hourly();
```

### 🔴 PROBLEMA IDENTIFICADO #2: Duplicación de Lógica

El **Job** `ProcessTicketReminders` y el **Command** `CheckTicketReminders` hacen lo mismo:

```php
// Command
class CheckTicketReminders extends Command {
    public function handle() {
        ProcessTicketReminders::dispatch(); // ⚠️ Despacha el Job
    }
}

// routes/console.php
Schedule::job(new ProcessTicketReminders)->hourly();        // ⚠️ Job directo
Schedule::command('tickets:check-reminders')->everyFiveMinutes(); // ⚠️ Command que ejecuta el Job
```

**Resultado:** El Job se ejecuta **cada 5 minutos Y cada hora** = Duplicado

---

## 📝 Trazabilidad y Logs {#trazabilidad}

### Sistema de Logs (TicketLog)

#### ✅ Qué se registra:

1. **Creación de ticket** (`TicketObserver@created`)
   - `previous_status`: null
   - `new_status`: pending
   - `changed_by`: user_id o admin

2. **Actualización de ticket** (`TicketObserver@updated`)
   - Cambios en: status, department_id, priority
   - `previous_*` y `new_*` values
   - `changed_by`: Auth::id()

3. **Eliminación** (`TicketObserver@deleted`)
   - `previous_status`: current
   - `new_status`: closed
   - `change_reason`: "Ticket Deleted"

4. **Restauración** (`TicketObserver@restored`)
   - `previous_status`: closed
   - `new_status`: reopened
   - `change_reason`: "Ticket Restored"

5. **Cierre automático** (`CloseInactiveTickets`)
   - Vía evento `TicketStatusChanged`
   - `changed_by`: null (sistema)
   - `change_reason`: "Cierre automático por inactividad"

### 🟡 PROBLEMA IDENTIFICADO #3: Listeners No Registrados

Existen 2 listeners pero **NO están registrados** en ningún provider:

```php
// app/Listeners/CreateTicketLog.php - ⚠️ NO USADO
class CreateTicketLog {
    public function handle(TicketStatusChanged $event): void {
        TicketLog::create([...]);
    }
}

// app/Listeners/CreateTicketReminder.php - ⚠️ NO USADO
class CreateTicketReminder {
    public function handle(TicketCreatedEvent $event): void {
        Reminder::create([...]);
    }
}
```

**Resultado:** Estos listeners **nunca se ejecutan** porque no están registrados en el EventServiceProvider.

---

## 🔴 Problemas Identificados {#problemas}

### CRÍTICO 🔴

#### 1. Listeners Huérfanos
- **Listeners:** `CreateTicketLog`, `CreateTicketReminder`
- **Problema:** No están registrados en ningún provider
- **Impacto:** Código muerto que confunde, no aporta funcionalidad
- **Solución:** Registrar en EventServiceProvider o eliminar

#### 2. Eventos sin Dispatch
- **Evento:** `TicketCreatedEvent`
- **Problema:** Nunca se dispara con `event()`
- **Listener:** `CreateTicketReminder` espera este evento
- **Impacto:** Los reminders automáticos no se crean en creación
- **Solución:** Disparar evento en TicketObserver@created

#### 3. Comentarios sin Notificación
- **API:** `PublicTicketController@addComment`
- **Problema:** Se crea el comentario pero **no se notifica al staff**
- **Impacto:** El staff no sabe cuando el cliente responde
- **Solución:** Agregar notificación en creación de comentario

### ALTO 🟠

#### 4. Duplicación de Jobs
- **Comandos:** `ProcessTicketReminders` se ejecuta 2x
- **Impacto:** Notificaciones duplicadas, carga innecesaria
- **Solución:** Usar solo el Job cada hora, eliminar el comando

#### 5. Notificaciones Síncronas
- **Problema:** `notify()` es síncrono, no usa colas
- **Impacto:** Respuestas lentas si email falla
- **Solución:** Implementar `ShouldQueue` en notificaciones

#### 6. Sin Observer para Comments
- **Problema:** Los comentarios se crean sin observer
- **Impacto:** No hay trazabilidad ni notificaciones automáticas
- **Solución:** Crear `TicketCommentObserver`

### MEDIO 🟡

#### 7. SLA No se Calcula Automáticamente
- **Modelo:** `Ticket` tiene relación con SLA
- **Problema:** Las fechas `response_due_date` y `resolution_due_date` no se calculan automáticamente
- **Impacto:** Hay que calcularlas manualmente en cada creación
- **Solución:** Calcular automáticamente en `TicketObserver@creating`

#### 8. Falta Validación de Estados
- **Problema:** No hay validación de transiciones de estado válidas
- **Impacto:** Se puede cambiar de cualquier estado a cualquier estado
- **Solución:** Implementar State Machine o validación de transiciones

---

## ✅ Mejoras Propuestas {#mejoras}

### 🎯 Prioridad ALTA

#### 1. Crear EventServiceProvider

```php
<?php

namespace App\Providers;

use App\Events\TicketCreatedEvent;
use App\Events\TicketStatusChanged;
use App\Listeners\CreateTicketLog;
use App\Listeners\CreateTicketReminder;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TicketCreatedEvent::class => [
            CreateTicketReminder::class,
        ],
        TicketStatusChanged::class => [
            CreateTicketLog::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
```

#### 2. Disparar Eventos en TicketObserver

```php
// En TicketObserver@created
event(new TicketCreatedEvent($ticket));

// En TicketObserver@updated (cuando cambia status)
if (isset($changes['status'])) {
    event(new TicketStatusChanged(
        ticket: $ticket,
        oldStatus: $ticket->getOriginal('status'),
        newStatus: $ticket->status,
        changedBy: Auth::id(),
        oldDepartment: $ticket->department_id,
        newDepartment: $ticket->department_id,
        oldPriority: $ticket->priority,
        newPriority: $ticket->priority,
        reason: null
    ));
}
```

#### 3. Crear TicketCommentObserver

```php
<?php

namespace App\Observers;

use App\Models\TicketComment;
use App\Notifications\NewTicketCommentNotification;
use Illuminate\Support\Facades\Notification;

class TicketCommentObserver
{
    public function created(TicketComment $comment): void
    {
        $ticket = $comment->ticket;

        // Si el comentario es del cliente, notificar al staff
        if ($comment->user->role === 'user_web') {
            Notification::route('mail', env('TICKET_NOTIFICATION_EMAIL'))
                ->notify(new NewTicketCommentNotification($ticket, $comment));
        }

        // Si el comentario es del staff, notificar al cliente
        else {
            $ticket->user->notify(new NewTicketCommentNotification($ticket, $comment));
        }
    }
}
```

#### 4. Implementar Queue en Notificaciones

```php
class NewTicketNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // El resto del código igual
}
```

#### 5. Calcular SLA Automáticamente

```php
// En TicketObserver@creating
public function creating(Ticket $ticket): void
{
    // Buscar SLA correspondiente
    $sla = SLA::where('ticket_type', $ticket->type)
        ->where('priority', $ticket->priority)
        ->where('is_active', true)
        ->first();

    if ($sla) {
        $ticket->response_due_date = now()->addHours($sla->response_time_hours);
        $ticket->resolution_due_date = now()->addHours($sla->resolution_time_hours);
    }
}
```

#### 6. Consolidar Jobs de Reminders

```php
// routes/console.php - ELIMINAR duplicado
// ❌ Schedule::command('tickets:check-reminders')->everyFiveMinutes();
✅ Schedule::job(new ProcessTicketReminders)->hourly();
```

### 🎯 Prioridad MEDIA

#### 7. Agregar Middleware de Logs

```php
// Para todas las acciones importantes
use Illuminate\Support\Facades\Log;

Log::channel('tickets')->info('Ticket created', [
    'ticket_id' => $ticket->id,
    'ticket_number' => $ticket->ticket_number,
    'user_id' => $ticket->user_id,
    'ip' => request()->ip(),
]);
```

#### 8. Implementar State Machine

```php
class TicketStateMachine
{
    private const TRANSITIONS = [
        'pending' => ['in_progress', 'rejected'],
        'in_progress' => ['resolved', 'closed', 'pending'],
        'resolved' => ['closed', 'reopened'],
        'closed' => ['reopened'],
        'reopened' => ['in_progress', 'resolved'],
        'rejected' => [],
    ];

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? []);
    }
}
```

#### 9. Agregar Auditoría Completa

```php
// Usar paquete spatie/laravel-activitylog
use Spatie\Activitylog\Traits\LogsActivity;

class Ticket extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
}
```

### 🎯 Prioridad BAJA

#### 10. Dashboard de Métricas en Tiempo Real

- Tickets abiertos vs cerrados
- Tiempo promedio de respuesta
- SLA compliance rate
- Tickets por departamento
- Gráficos de tendencias

#### 11. Webhooks para Integraciones

- Notificar sistemas externos cuando:
  - Se crea un ticket
  - Cambia el status
  - Se agrega un comentario

---

## 📊 Diagrama de Flujo Completo

```
┌─────────────┐
│   ENTRADA   │
│  (API/Web)  │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────────┐
│  Usuario Validado/Creado        │
│  ├─ Si nuevo → 📧 Credentials   │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  Ticket::create()               │
│  ├─ ticket_number auto          │
│  ├─ status: pending             │
│  └─ SLA fechas ⚠️ MANUAL        │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  TicketObserver@created         │
│  ├─► 📧 Usuario                 │
│  ├─► 📧 Email Config            │
│  ├─► 📝 TicketLog               │
│  └─► ⚠️ NO dispara Event        │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  AUTOMATIZACIONES               │
│  ├─ Cada hora: Reminders        │
│  ├─ Cada día: Mark Inactive     │
│  └─ Cada hora: Close Inactive   │
└─────────────────────────────────┘
```

---

## 🎯 Plan de Acción Recomendado

### Fase 1: Correcciones Críticas (1-2 días)
1. ✅ Crear EventServiceProvider
2. ✅ Registrar listeners existentes
3. ✅ Disparar eventos en TicketObserver
4. ✅ Eliminar duplicación de jobs
5. ✅ Agregar notificación de comentarios

### Fase 2: Mejoras de Rendimiento (2-3 días)
6. ✅ Implementar ShouldQueue en notificaciones
7. ✅ Calcular SLA automáticamente
8. ✅ Crear TicketCommentObserver
9. ✅ Configurar queues con Redis/Database

### Fase 3: Mejoras de Calidad (3-5 días)
10. ✅ Implementar State Machine
11. ✅ Agregar auditoría completa
12. ✅ Crear logs estructurados
13. ✅ Tests unitarios para flujos críticos

### Fase 4: Funcionalidades Adicionales (1-2 semanas)
14. ✅ Dashboard de métricas
15. ✅ Webhooks para integraciones
16. ✅ Reportes automáticos
17. ✅ Búsqueda avanzada

---

## 📈 Métricas de Éxito

Después de implementar las mejoras:

- ✅ **100%** de trazabilidad en todos los cambios
- ✅ **<500ms** tiempo de respuesta en creación de tickets
- ✅ **0** notificaciones perdidas o duplicadas
- ✅ **95%+** SLA compliance
- ✅ **0** transiciones de estado inválidas
- ✅ **100%** cobertura de tests en flujos críticos

---

*Generado por Claude Code - Análisis completo del Sistema PQRSD*
*Fecha: 2025-10-29*
