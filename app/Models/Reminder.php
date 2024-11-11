<?php

namespace App\Models;

use App\Enums\ReminderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * @mixin IdeHelperReminder
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $sent_to
 * @property ReminderType $reminder_type
 * @property bool $is_read
 * @property Carbon|null $sent_at
 * @property Carbon|null $read_at
 *
 * @property-read Ticket $ticket
 * @property-read User $user
 *
 * @method static Builder|Reminder unread()
 * @method static Builder|Reminder read()
 * @method static Builder|Reminder forTicket(int $ticketId)
 * @method static Builder|Reminder forUser(int $userId)
 * @method static Builder|Reminder type(ReminderType $type)
 */
class Reminder extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'sent_to',
        'reminder_type',
        'is_read',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'reminder_type' => ReminderType::class,
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    /**
     * Get the ticket associated with the reminder.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user the reminder was sent to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_to');
    }

    /**
     * Scope para recordatorios no leídos.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope para recordatorios leídos.
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope para filtrar por ticket.
     */
    public function scopeForTicket(Builder $query, int $ticketId): Builder
    {
        return $query->where('ticket_id', $ticketId);
    }

    /**
     * Scope para filtrar por usuario.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('sent_to', $userId);
    }

    /**
     * Scope para filtrar por tipo de recordatorio.
     */
    public function scopeType(Builder $query, ReminderType $type): Builder
    {
        return $query->where('reminder_type', $type);
    }

    /**
     * Marcar el recordatorio como leído.
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Marcar el recordatorio como no leído.
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Verificar si el recordatorio está leído.
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Verificar si el recordatorio está pendiente.
     */
    public function isPending(): bool
    {
        return !$this->is_read;
    }

    /**
     * Verificar si el recordatorio está vencido.
     */
    public function isOverdue(): bool
    {
        return $this->sent_at->isPast();
    }

    /**
     * Obtener el tiempo transcurrido desde que se envió.
     */
    public function timeSinceSent(): string
    {
        return $this->sent_at->diffForHumans();
    }

    /**
     * Obtener el tiempo transcurrido desde que se leyó.
     */
    public function timeSinceRead(): ?string
    {
        return $this->read_at ? $this->read_at->diffForHumans() : null;
    }
}
