<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use App\Models\TicketLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateTicketLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketStatusChanged $event): void
    {
        TicketLog::create([
            'ticket_id' => $event->ticket->id,
            'changed_by' => $event->changedBy,
            'previous_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'previous_department_id' => $event->oldDepartment,
            'new_department_id' => $event->newDepartment,
            'previous_priority' => $event->oldPriority,
            'new_priority' => $event->newPriority,
            'change_reason' => $event->reason,
            'changed_at' => now(),
        ]);
    }
}
