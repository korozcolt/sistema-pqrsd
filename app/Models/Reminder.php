<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use App\Models\User;

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
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sent_to');
    }

    // Scope para no leídos
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
