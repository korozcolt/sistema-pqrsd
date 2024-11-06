<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TicketType;

class SLA extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_type',
        'response_time',
        'resolution_time',
    ];

    /**
     * El atributo que debe ser casteado como un enum.
     */
    protected function casts(): array
    {
        return [
            'ticket_type' => TicketType::class,  // Asumiendo que tienes un Enum llamado TicketType
        ];
    }
}
