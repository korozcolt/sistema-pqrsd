<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Enums\Priority;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'user_id',    // El usuario que creó el ticket
        'type',       // Enum TicketType
        'status',     // Enum StatusTicket
        'priority',   // Enum Priority
        'department_id',  // Agregado: referencia al departamento si corresponde
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => TicketType::class,
            'status' => StatusTicket::class,
            'priority' => Priority::class,
        ];
    }
    /**
     * The user who created the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The department the ticket belongs to (if applicable).
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * The attachments for the ticket.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * The history of status changes for the ticket.
     */
    public function history()
    {
        return $this->hasMany(TicketLog::class);
    }
}
