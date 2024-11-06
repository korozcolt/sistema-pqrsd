<?php

namespace App\Models;

use App\Enums\StatusTicket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;

class TicketLog extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'changed_by',
        'previous_status',
        'new_status',
        'previous_department_id',
        'new_department_id',
        'change_reason',
        'changed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'previous_status' => StatusTicket::class,
        'new_status' => StatusTicket::class,
        'changed_at' => 'datetime',
    ];

    /**
     * Relation to the ticket this log belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User who made the change.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Previous department.
     */
    public function previousDepartment()
    {
        return $this->belongsTo(Department::class, 'previous_department_id');
    }

    /**
     * New department after the change.
     */
    public function newDepartment()
    {
        return $this->belongsTo(Department::class, 'new_department_id');
    }
}
