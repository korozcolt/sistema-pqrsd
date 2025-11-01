<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperSLA
 */
class SLA extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'slas';

    protected $fillable = [
        'ticket_type',
        'priority',
        'response_time',
        'resolution_time',
        'is_active',
    ];

    protected $casts = [
        'ticket_type' => TicketType::class,
        'priority' => Priority::class,
        'is_active' => 'boolean',
        'response_time' => 'integer',
        'resolution_time' => 'integer',
    ];

    /**
     * Configuración de ActivityLog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['ticket_type', 'priority', 'response_time', 'resolution_time', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "SLA {$eventName}")
            ->useLogName('sla');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'type', 'ticket_type')
            ->where('priority', $this->priority);
    }
}
