<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\StatusTicket;
use App\Enums\Priority;
use App\Enums\TicketType;

/**
 * @mixin IdeHelperTicket
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

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
        'resolution_at'
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

    protected static function booted()
    {
        static::created(function ($ticket) {
            // Generar número de ticket si no existe
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = 'TK-' . str_pad($ticket->id, 5, '0', STR_PAD_LEFT);
                $ticket->save();
            }

            // Buscar SLA aplicable y establecer fechas
            if ($sla = $ticket->sla()->first()) {
                $ticket->response_due_date = now()->addHours($sla->response_time);
                $ticket->resolution_due_date = now()->addHours($sla->resolution_time);
                $ticket->save();
            }
        });
    }
}
