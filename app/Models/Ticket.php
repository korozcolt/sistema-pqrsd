<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperTicket
 */
class Ticket extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'user_id',
        'department_id',
        'type',
        'status',
        'priority',
        'response_due_date',
        'resolution_due_date',
        'first_response_at',
        'resolution_at',
    ];

    protected $casts = [
        'type' => TicketType::class,
        'status' => StatusTicket::class,
        'priority' => Priority::class,
        'response_due_date' => 'datetime',
        'resolution_due_date' => 'datetime',
        'first_response_at' => 'datetime',
        'resolution_at' => 'datetime',
    ];

    /**
     * Configuración de ActivityLog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'status',
                'priority',
                'type',
                'department_id',
                'title',
                'description',
                'response_due_date',
                'resolution_due_date',
                'first_response_at',
                'resolution_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Ticket {$eventName}")
            ->useLogName('ticket');
    }

    /**
     * Genera un número de ticket único
     */
    public static function generateUniqueNumber(): string
    {
        // Intentar varias veces si hay colisiones
        $maxAttempts = 5;
        $attempt = 0;

        do {
            $attempt++;

            // Método 1: Basado en el máximo ID actual (incluyendo eliminados)
            $lastId = self::withTrashed()->max('id') ?? 0;
            $nextId = $lastId + 1;
            $ticketNumber = 'TK-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

            // Verificar si este número ya existe (incluyendo eliminados)
            $exists = self::withTrashed()
                ->where('ticket_number', $ticketNumber)
                ->exists();

            // Si no existe, podemos usarlo
            if (! $exists) {
                return $ticketNumber;
            }

            // Si llegamos aquí, hubo una colisión, intentemos otro enfoque
            if ($attempt >= 2) {
                // Método 2: Generar basado en timestamp + número aleatorio
                $timestamp = now()->format('ymd');
                $random = mt_rand(1000, 9999);
                $ticketNumber = 'TK-'.$timestamp.$random;

                $exists = self::withTrashed()
                    ->where('ticket_number', $ticketNumber)
                    ->exists();

                if (! $exists) {
                    return $ticketNumber;
                }
            }

        } while ($attempt < $maxAttempts);

        // Si llegamos aquí, todos los intentos fallaron
        // Generamos uno completamente aleatorio como último recurso
        $uniqueString = substr(md5(uniqid(mt_rand(), true)), 0, 8);

        return 'TK-'.$uniqueString;
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($ticket) {
            // Solo asignar un número si no tiene uno ya
            if (! $ticket->ticket_number) {
                $ticket->ticket_number = self::generateUniqueNumber();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function sla(): BelongsTo
    {
        return $this->belongsTo(SLA::class, 'type', 'ticket_type')
            ->where('priority', $this->priority)
            ->where('is_active', true);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'ticket_tags');
    }

    /**
     * Verificar si se puede cambiar al estado especificado
     */
    public function canTransitionTo(StatusTicket $newStatus): bool
    {
        $stateMachine = app(\App\Services\TicketStateMachine::class);

        return $stateMachine->canTransition($this->status, $newStatus);
    }

    /**
     * Cambiar el estado del ticket usando la State Machine
     */
    public function transitionTo(StatusTicket $newStatus, ?string $reason = null): bool
    {
        $stateMachine = app(\App\Services\TicketStateMachine::class);

        return $stateMachine->transition($this, $newStatus, $reason);
    }

    /**
     * Obtener los estados permitidos desde el estado actual
     *
     * @return array<StatusTicket>
     */
    public function getAllowedNextStates(): array
    {
        $stateMachine = app(\App\Services\TicketStateMachine::class);

        return $stateMachine->getAllowedTransitions($this->status);
    }

    /**
     * Verificar si el ticket está en un estado terminal
     */
    public function isInTerminalState(): bool
    {
        $stateMachine = app(\App\Services\TicketStateMachine::class);

        return $stateMachine->isTerminalState($this->status);
    }
}
