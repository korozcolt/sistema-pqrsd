<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Enums\Priority;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'ticket_number',
        'title',
        'description',
        'status',
        'priority',
        'type',
        'response_due_date',
        'resolution_due_date',
        'first_response_at',
        'resolution_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => TicketType::class,
            'status' => StatusTicket::class,
            'priority' => Priority::class,
            'response_due_date' => 'date',
            'resolution_due_date' => 'date',
            'first_response_at' => 'datetime',
            'resolution_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'ticket_tags')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    // Scope para filtrar por estado
    public function scopeStatus($query, StatusTicket $status)
    {
        return $query->where('status', $status);
    }

    // Scope para filtrar por prioridad
    public function scopePriority($query, Priority $priority)
    {
        return $query->where('priority', $priority);
    }

    // Scope para filtrar por tipo
    public function scopeType($query, TicketType $type)
    {
        return $query->where('type', $type);
    }
}
