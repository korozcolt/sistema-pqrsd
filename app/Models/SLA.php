<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TicketType;
use App\Enums\Priority;

class SLA extends Model
{
    use HasFactory;

    protected $table = 'slas';

    protected $fillable = [
        'ticket_type',
        'priority',
        'response_time',
        'resolution_time',
        'is_active'
    ];

    protected $casts = [
        'ticket_type' => TicketType::class,
        'priority' => Priority::class,
        'is_active' => 'boolean',
        'response_time' => 'integer',
        'resolution_time' => 'integer'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'type', 'ticket_type')
                    ->where('priority', $this->priority);
    }
}
