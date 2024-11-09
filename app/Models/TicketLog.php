<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\StatusTicket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;

class TicketLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'changed_by',
        'previous_status',
        'new_status',
        'previous_department_id',
        'new_department_id',
        'previous_priority',
        'new_priority',
        'change_reason',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'previous_status' => StatusTicket::class,
            'new_status' => StatusTicket::class,
            'previous_priority' => Priority::class,
            'new_priority' => Priority::class,
            'changed_at' => 'datetime',
        ];
    }

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function previousDepartment()
    {
        return $this->belongsTo(Department::class, 'previous_department_id');
    }

    public function newDepartment()
    {
        return $this->belongsTo(Department::class, 'new_department_id');
    }
}

