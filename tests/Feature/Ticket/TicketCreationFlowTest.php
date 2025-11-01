<?php

use App\Enums\Priority;
use App\Enums\ReminderType;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Models\Department;
use App\Models\Reminder;
use App\Models\SLA;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    // Fake notifications to test without sending
    Notification::fake();
});

it('creates ticket with all required fields', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    expect($ticket)->not->toBeNull()
        ->and($ticket->title)->toBe('Test Ticket')
        ->and($ticket->status)->toBe(StatusTicket::Pending)
        ->and($ticket->ticket_number)->toStartWith('TK-');
});

it('auto-generates unique ticket number', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket1 = Ticket::factory()->create([
        'user_id' => $user->id,
        'department_id' => $department->id,
    ]);

    $ticket2 = Ticket::factory()->create([
        'user_id' => $user->id,
        'department_id' => $department->id,
    ]);

    expect($ticket1->ticket_number)->not->toBe($ticket2->ticket_number)
        ->and($ticket1->ticket_number)->toStartWith('TK-')
        ->and($ticket2->ticket_number)->toStartWith('TK-');
});

it('calculates SLA dates automatically on creation', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Create SLA configuration
    SLA::create([
        'ticket_type' => TicketType::Petition,
        'priority' => Priority::High,
        'response_time' => 8,
        'resolution_time' => 360,
        'is_active' => true,
    ]);

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::High,
    ]);

    expect($ticket->response_due_date)->not->toBeNull()
        ->and($ticket->resolution_due_date)->not->toBeNull();

    // Verify dates are approximately correct (within 1 minute tolerance)
    $expectedResponse = now()->addHours(8);
    $expectedResolution = now()->addHours(360);

    expect($ticket->response_due_date->timestamp)
        ->toBeGreaterThanOrEqual($expectedResponse->subMinute()->timestamp)
        ->toBeLessThanOrEqual($expectedResponse->addMinute()->timestamp);
});

it('uses default SLA dates when no SLA configuration exists', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    expect($ticket->response_due_date)->not->toBeNull()
        ->and($ticket->resolution_due_date)->not->toBeNull();

    // Default: 24 hours response, 15 days resolution
    $expectedResponse = now()->addHours(24);
    $expectedResolution = now()->addDays(15);

    expect($ticket->response_due_date->timestamp)
        ->toBeGreaterThanOrEqual($expectedResponse->subMinute()->timestamp)
        ->toBeLessThanOrEqual($expectedResponse->addMinute()->timestamp);
});

it('creates 4 automatic reminders on ticket creation', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    $reminders = Reminder::where('ticket_id', $ticket->id)->get();

    expect($reminders)->toHaveCount(4);

    // Verify all 4 reminder types exist
    $types = $reminders->pluck('reminder_type')->map(fn ($t) => $t->value)->toArray();
    expect($types)->toContain(ReminderType::ResponseDue->value)
        ->toContain(ReminderType::ResponseOverdue->value)
        ->toContain(ReminderType::ResolutionDue->value)
        ->toContain(ReminderType::ResolutionOverdue->value);
});

it('creates ticket log entry on creation', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    $log = TicketLog::where('ticket_id', $ticket->id)->first();

    expect($log)->not->toBeNull()
        ->and($log->new_status)->toBe(StatusTicket::Pending)
        ->and($log->change_reason)->toContain('Ticket Created');
});

it('creates activity log entry on creation', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    $activity = Activity::where('subject_type', Ticket::class)
        ->where('subject_id', $ticket->id)
        ->where('event', 'created')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->log_name)->toBe('ticket');
});

it('dispatches TicketCreatedEvent on creation', function () {
    Event::fake();

    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    Event::assertDispatched(\App\Events\TicketCreatedEvent::class, function ($event) use ($ticket) {
        return $event->ticket->id === $ticket->id;
    });
});

it('sends notification to user on ticket creation', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    Notification::assertSentTo(
        $user,
        \App\Notifications\NewTicketNotification::class,
        function ($notification, $channels) use ($ticket) {
            return $notification->ticket->id === $ticket->id;
        }
    );
});

it('sends notification to staff email on ticket creation', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    Notification::assertSentOnDemand(
        \App\Notifications\NewTicketNotification::class
    );
});

it('handles ticket creation with all optional fields', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
        'response_due_date' => now()->addDays(1),
        'resolution_due_date' => now()->addDays(7),
        'first_response_at' => now(),
        'resolution_at' => null,
    ]);

    expect($ticket)->not->toBeNull()
        ->and($ticket->response_due_date)->not->toBeNull()
        ->and($ticket->resolution_due_date)->not->toBeNull()
        ->and($ticket->first_response_at)->not->toBeNull();
});

it('completes full ticket creation flow end-to-end', function () {
    Event::fake();

    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Create SLA
    SLA::create([
        'ticket_type' => TicketType::Petition,
        'priority' => Priority::High,
        'response_time' => 8,
        'resolution_time' => 360,
        'is_active' => true,
    ]);

    // Create ticket
    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::High,
    ]);

    // Verify ticket created
    expect($ticket->id)->not->toBeNull();

    // Verify ticket number generated
    expect($ticket->ticket_number)->toStartWith('TK-');

    // Verify SLA dates calculated
    expect($ticket->response_due_date)->not->toBeNull();
    expect($ticket->resolution_due_date)->not->toBeNull();

    // Verify 4 reminders created
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(4);

    // Verify ticket log created
    expect(TicketLog::where('ticket_id', $ticket->id)->count())->toBeGreaterThan(0);

    // Verify activity log created
    expect(Activity::where('subject_type', Ticket::class)
        ->where('subject_id', $ticket->id)
        ->count())->toBeGreaterThan(0);

    // Verify event dispatched
    Event::assertDispatched(\App\Events\TicketCreatedEvent::class);

    // Verify notifications sent
    Notification::assertSentTo($user, \App\Notifications\NewTicketNotification::class);
    Notification::assertSentOnDemand(\App\Notifications\NewTicketNotification::class);
});
