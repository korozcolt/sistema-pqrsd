<?php

use App\Enums\Priority;
use App\Enums\ReminderType;
use App\Enums\StatusTicket;
use App\Enums\UserRole;
use App\Events\TicketCreatedEvent;
use App\Events\TicketStatusChanged;
use App\Listeners\CreateTicketLog;
use App\Listeners\CreateTicketReminder;
use App\Models\Department;
use App\Models\Reminder;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\User;

// ==================== CreateTicketLog Tests ====================

it('CreateTicketLog listener creates log entry', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()->for($user)->for($department)->create();

    $event = new TicketStatusChanged(
        ticket: $ticket,
        oldStatus: StatusTicket::Pending,
        newStatus: StatusTicket::In_Progress,
        changedBy: 1,
        reason: 'Starting work',
        oldDepartment: $department->id,
        newDepartment: $department->id,
        oldPriority: Priority::Medium,
        newPriority: Priority::Medium
    );

    $initialCount = TicketLog::where('ticket_id', $ticket->id)->count();

    $listener = new CreateTicketLog;
    $listener->handle($event);

    $finalCount = TicketLog::where('ticket_id', $ticket->id)->count();
    expect($finalCount)->toBe($initialCount + 1);

    $log = TicketLog::where('ticket_id', $ticket->id)->orderBy('id', 'desc')->first();
    expect($log->previous_status)->toBe(StatusTicket::Pending)
        ->and($log->new_status)->toBe(StatusTicket::In_Progress)
        ->and($log->changed_by)->toBe(1)
        ->and($log->change_reason)->toBe('Starting work');
});

// ==================== CreateTicketReminder Tests ====================

it('CreateTicketReminder listener creates 4 reminders', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->create([
            'response_due_date' => now()->addHours(24),
            'resolution_due_date' => now()->addDays(15),
        ]);

    $event = new TicketCreatedEvent($ticket);

    // Clear any existing reminders
    Reminder::where('ticket_id', $ticket->id)->delete();

    $listener = new CreateTicketReminder;
    $listener->handle($event);

    $reminders = Reminder::where('ticket_id', $ticket->id)->get();
    expect($reminders)->toHaveCount(4);

    $types = $reminders->pluck('reminder_type')->map(fn ($t) => $t->value)->toArray();
    expect($types)->toContain(ReminderType::HalfTimeResponse->value)
        ->toContain(ReminderType::DayBeforeResponse->value)
        ->toContain(ReminderType::HalfTimeResolution->value)
        ->toContain(ReminderType::DayBeforeResolution->value);
});

it('CreateTicketReminder sets correct sent_to field', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->create([
            'response_due_date' => now()->addHours(24),
            'resolution_due_date' => now()->addDays(15),
        ]);

    $event = new TicketCreatedEvent($ticket);

    Reminder::where('ticket_id', $ticket->id)->delete();

    $listener = new CreateTicketReminder;
    $listener->handle($event);

    $reminders = Reminder::where('ticket_id', $ticket->id)->get();

    foreach ($reminders as $reminder) {
        expect($reminder->sent_to)->toBe($user->id);
    }
});

// ==================== TicketCommentObserver Tests ====================

use App\Models\TicketComment;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    User::factory()->create([
        'id' => 1,
        'name' => 'System',
        'email' => 'system@sistema-pqrsd.local',
        'role' => UserRole::Admin,
    ]);
});

it('TicketCommentObserver notifies staff when user_web comments', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()->for($webUser)->for($department)->create();

    TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $webUser->id,
        'content' => 'User comment',
        'is_internal' => false,
    ]);

    Notification::assertSentOnDemand(\App\Notifications\NewTicketCommentNotification::class);
});

it('TicketCommentObserver notifies owner when staff comments', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()->for($webUser)->for($department)->create();

    TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $staff->id,
        'content' => 'Staff response',
        'is_internal' => false,
    ]);

    Notification::assertSentTo(
        $webUser,
        \App\Notifications\NewTicketCommentNotification::class
    );
});
