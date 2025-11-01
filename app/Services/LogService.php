<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Servicio de logging estructurado con contexto automático
 *
 * Agrega contexto útil a todos los logs para mejor trazabilidad
 */
class LogService
{
    /**
     * Generar un request ID único para rastrear requests
     */
    public static function generateRequestId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Obtener contexto base que se agrega a todos los logs
     */
    protected static function getBaseContext(): array
    {
        $context = [
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
        ];

        // Agregar información del usuario autenticado si existe
        if (Auth::check()) {
            $user = Auth::user();
            $context['user'] = [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role->value ?? null,
            ];
        }

        // Agregar información del request si existe
        if (app()->runningInConsole() === false && request()) {
            $context['request'] = [
                'method' => request()->method(),
                'url' => request()->fullUrl(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ];
        }

        return $context;
    }

    /**
     * Log de información
     */
    public static function info(string $message, array $context = []): void
    {
        Log::info($message, array_merge(self::getBaseContext(), $context));
    }

    /**
     * Log de advertencia
     */
    public static function warning(string $message, array $context = []): void
    {
        Log::warning($message, array_merge(self::getBaseContext(), $context));
    }

    /**
     * Log de error
     */
    public static function error(string $message, array $context = [], ?\Throwable $exception = null): void
    {
        if ($exception) {
            $context['exception'] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        Log::error($message, array_merge(self::getBaseContext(), $context));
    }

    /**
     * Log de debug
     */
    public static function debug(string $message, array $context = []): void
    {
        Log::debug($message, array_merge(self::getBaseContext(), $context));
    }

    /**
     * Log de actividad de ticket
     */
    public static function ticketActivity(Ticket $ticket, string $action, array $additionalContext = []): void
    {
        $context = array_merge([
            'ticket' => [
                'id' => $ticket->id,
                'number' => $ticket->ticket_number,
                'status' => $ticket->status->value,
                'type' => $ticket->type->value,
                'priority' => $ticket->priority->value,
            ],
            'action' => $action,
        ], $additionalContext);

        self::info("Ticket activity: {$action}", $context);
    }

    /**
     * Log de cambio de estado de ticket
     */
    public static function ticketStatusChange(
        Ticket $ticket,
        string $oldStatus,
        string $newStatus,
        ?string $reason = null
    ): void {
        $context = [
            'ticket' => [
                'id' => $ticket->id,
                'number' => $ticket->ticket_number,
            ],
            'status_change' => [
                'from' => $oldStatus,
                'to' => $newStatus,
                'reason' => $reason,
            ],
        ];

        self::info("Ticket status changed from {$oldStatus} to {$newStatus}", $context);
    }

    /**
     * Log de autenticación de usuario
     */
    public static function userAuthentication(User $user, string $action, bool $success = true): void
    {
        $context = [
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role->value ?? null,
            ],
            'action' => $action,
            'success' => $success,
        ];

        $level = $success ? 'info' : 'warning';
        $message = $success
            ? "User {$action} successful"
            : "User {$action} failed";

        Log::$level($message, array_merge(self::getBaseContext(), $context));
    }

    /**
     * Log de operación de base de datos
     */
    public static function databaseOperation(
        string $operation,
        string $table,
        ?int $affectedRows = null,
        array $additionalContext = []
    ): void {
        $context = array_merge([
            'database' => [
                'operation' => $operation,
                'table' => $table,
                'affected_rows' => $affectedRows,
            ],
        ], $additionalContext);

        self::debug("Database operation: {$operation} on {$table}", $context);
    }

    /**
     * Log de notificación enviada
     */
    public static function notificationSent(
        string $notificationClass,
        string $channel,
        $recipient,
        bool $success = true
    ): void {
        $context = [
            'notification' => [
                'class' => $notificationClass,
                'channel' => $channel,
                'success' => $success,
            ],
        ];

        if ($recipient instanceof User) {
            $context['recipient'] = [
                'type' => 'user',
                'id' => $recipient->id,
                'email' => $recipient->email,
            ];
        } elseif (is_string($recipient)) {
            $context['recipient'] = [
                'type' => 'email',
                'email' => $recipient,
            ];
        }

        $level = $success ? 'info' : 'error';
        $message = $success
            ? 'Notification sent successfully'
            : 'Notification failed to send';

        Log::$level($message, array_merge(self::getBaseContext(), $context));
    }

    /**
     * Log de inicio de job
     */
    public static function jobStarted(string $jobClass, array $payload = []): void
    {
        $context = [
            'job' => [
                'class' => $jobClass,
                'payload' => $payload,
                'started_at' => now()->toIso8601String(),
            ],
        ];

        self::info("Job started: {$jobClass}", $context);
    }

    /**
     * Log de finalización de job
     */
    public static function jobCompleted(string $jobClass, float $executionTime, bool $success = true): void
    {
        $context = [
            'job' => [
                'class' => $jobClass,
                'execution_time' => $executionTime,
                'success' => $success,
                'completed_at' => now()->toIso8601String(),
            ],
        ];

        $level = $success ? 'info' : 'error';
        $message = $success
            ? "Job completed successfully: {$jobClass}"
            : "Job failed: {$jobClass}";

        Log::$level($message, array_merge(self::getBaseContext(), $context));
    }

    /**
     * Log de validación fallida
     */
    public static function validationFailed(string $context, array $errors): void
    {
        $logContext = [
            'validation' => [
                'context' => $context,
                'errors' => $errors,
            ],
        ];

        self::warning("Validation failed: {$context}", $logContext);
    }

    /**
     * Log de operación SLA
     */
    public static function slaOperation(string $operation, array $slaData): void
    {
        $context = [
            'sla' => [
                'operation' => $operation,
                'data' => $slaData,
            ],
        ];

        self::info("SLA operation: {$operation}", $context);
    }

    /**
     * Log de performance (para operaciones lentas)
     */
    public static function performanceMetric(string $operation, float $duration, array $additionalContext = []): void
    {
        $context = array_merge([
            'performance' => [
                'operation' => $operation,
                'duration_ms' => round($duration * 1000, 2),
            ],
        ], $additionalContext);

        // Log como warning si toma más de 1 segundo
        $level = $duration > 1.0 ? 'warning' : 'debug';

        Log::$level("Performance metric: {$operation}", array_merge(self::getBaseContext(), $context));
    }
}
