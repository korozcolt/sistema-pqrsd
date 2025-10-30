<?php

namespace App\Events;

use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $oldStatus;
    public $newStatus;
    public $changedBy;
    public $reason;
    public $oldDepartment;
    public $newDepartment;
    public $oldPriority;
    public $newPriority;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Ticket $ticket,
        ?StatusTicket $oldStatus,
        ?StatusTicket $newStatus,
        ?int $changedBy,
        ?string $reason = null,
        ?int $oldDepartment = null,
        ?int $newDepartment = null,
        ?Priority $oldPriority = null,
        ?Priority $newPriority = null
    ) {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus?->value;
        $this->newStatus = $newStatus?->value;
        $this->changedBy = $changedBy;
        $this->reason = $reason;
        $this->oldDepartment = $oldDepartment;
        $this->newDepartment = $newDepartment;
        $this->oldPriority = $oldPriority?->value;
        $this->newPriority = $newPriority?->value;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
