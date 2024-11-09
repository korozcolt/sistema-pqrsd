<?php

namespace App\Models;

use App\Enums\Priority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TicketType;

class SLA extends Model
{
    use HasFactory;

    protected $table = 'slas';

    protected $fillable = [
        'ticket_type',
        'priority',
        'response_time',
        'resolution_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'ticket_type' => TicketType::class,
            'priority' => Priority::class,
            'is_active' => 'boolean',
        ];
    }

    // Scope para SLAs activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
