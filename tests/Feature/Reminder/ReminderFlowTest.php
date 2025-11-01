<?php

use App\Enums\Priority;
use App\Enums\ReminderType;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Jobs\ProcessTicketReminders;
use App\Models\Department;
use App\Models\Reminder;
use App\Models\SLA;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

// ==================== AUTOMATIC REMINDER CREATION ====================

it('creates 4 reminders automatically when ticket is created', function () {
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
});

it('creates reminders with correct types', function () {
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

    $reminderTypes = Reminder::where('ticket_id', $ticket->id)
        ->pluck('reminder_type')
        ->map(fn ($type) => $type->value)
        ->toArray();

    expect($reminderTypes)->toContain(ReminderType::HalfTimeResponse->value)
        ->toContain(ReminderType::DayBeforeResponse->value)
        ->toContain(ReminderType::HalfTimeResolution->value)
        ->toContain(ReminderType::DayBeforeResolution->value);
});

it('sets correct sent_at times for response reminders', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Create SLA with 8 hours response time
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

    // Get half-time response reminder
    $halfTimeReminder = Reminder::where('ticket_id', $ticket->id)
        ->where('reminder_type', ReminderType::HalfTimeResponse)
        ->first();

    expect($halfTimeReminder)->not->toBeNull()
        ->and($halfTimeReminder->sent_at)->not->toBeNull();

    // Should be sent at half the response time (4 hours from creation)
    $expectedTime = $ticket->created_at->addHours(4);
    expect($halfTimeReminder->sent_at->timestamp)
        ->toBeGreaterThanOrEqual($expectedTime->subMinute()->timestamp)
        ->toBeLessThanOrEqual($expectedTime->addMinute()->timestamp);
});

it('assigns reminders to the ticket owner', function () {
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

    foreach ($reminders as $reminder) {
        expect($reminder->sent_to)->toBe($user->id);
    }
});

// ==================== REMINDER MODEL METHODS ====================

it('marks reminder as read', function () {
    $reminder = Reminder::factory()->unread()->create();

    expect($reminder->is_read)->toBeFalse()
        ->and($reminder->read_at)->toBeNull();

    $reminder->markAsRead();

    expect($reminder->fresh()->is_read)->toBeTrue()
        ->and($reminder->fresh()->read_at)->not->toBeNull();
});

it('marks reminder as unread', function () {
    $reminder = Reminder::factory()->read()->create();

    expect($reminder->is_read)->toBeTrue()
        ->and($reminder->read_at)->not->toBeNull();

    $reminder->markAsUnread();

    expect($reminder->fresh()->is_read)->toBeFalse()
        ->and($reminder->fresh()->read_at)->toBeNull();
});

it('checks if reminder is read using isRead method', function () {
    $readReminder = Reminder::factory()->read()->create();
    $unreadReminder = Reminder::factory()->unread()->create();

    expect($readReminder->isRead())->toBeTrue()
        ->and($unreadReminder->isRead())->toBeFalse();
});

it('checks if reminder is pending using isPending method', function () {
    $readReminder = Reminder::factory()->read()->create();
    $unreadReminder = Reminder::factory()->unread()->create();

    expect($readReminder->isPending())->toBeFalse()
        ->and($unreadReminder->isPending())->toBeTrue();
});

// ==================== REMINDER SCOPES ====================

it('filters reminders using unread scope', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    Reminder::factory()->count(3)->unread()->create(['ticket_id' => $ticket->id]);
    Reminder::factory()->count(2)->read()->create(['ticket_id' => $ticket->id]);

    $unreadCount = Reminder::unread()->count();
    expect($unreadCount)->toBe(3);
});

it('filters reminders using read scope', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    Reminder::factory()->count(3)->unread()->create(['ticket_id' => $ticket->id]);
    Reminder::factory()->count(2)->read()->create(['ticket_id' => $ticket->id]);

    $readCount = Reminder::read()->count();
    expect($readCount)->toBe(2);
});

it('filters reminders using forTicket scope', function () {
    $ticket1 = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    $ticket2 = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    Reminder::factory()->count(4)->create(['ticket_id' => $ticket1->id]);
    Reminder::factory()->count(2)->create(['ticket_id' => $ticket2->id]);

    $ticket1Reminders = Reminder::forTicket($ticket1->id)->count();
    expect($ticket1Reminders)->toBe(4);
});

it('filters reminders using forUser scope', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Reminder::factory()->count(3)->create(['sent_to' => $user1->id]);
    Reminder::factory()->count(2)->create(['sent_to' => $user2->id]);

    $user1Reminders = Reminder::forUser($user1->id)->count();
    expect($user1Reminders)->toBe(3);
});

it('filters reminders using type scope', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    Reminder::factory()->halfTimeResponse()->create(['ticket_id' => $ticket->id]);
    Reminder::factory()->dayBeforeResponse()->create(['ticket_id' => $ticket->id]);
    Reminder::factory()->halfTimeResolution()->create(['ticket_id' => $ticket->id]);

    $halfTimeCount = Reminder::type(ReminderType::HalfTimeResponse)->count();
    expect($halfTimeCount)->toBe(1);
});

// ==================== REMINDER DELETION ON TICKET CLOSURE ====================

it('deletes all reminders when ticket is closed', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->resolved()
        ->create();

    Reminder::factory()->count(4)->create(['ticket_id' => $ticket->id]);
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(4);

    $ticket->status = StatusTicket::Closed;
    $ticket->save();

    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

it('deletes all reminders when ticket is resolved', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->inProgress()
        ->create();

    Reminder::factory()->count(4)->create(['ticket_id' => $ticket->id]);
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(4);

    $ticket->status = StatusTicket::Resolved;
    $ticket->save();

    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

it('deletes all reminders when ticket is rejected', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    Reminder::factory()->count(4)->create(['ticket_id' => $ticket->id]);
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(4);

    $ticket->status = StatusTicket::Rejected;
    $ticket->save();

    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

it('keeps reminders when ticket transitions to In_Progress', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    Reminder::factory()->count(4)->create(['ticket_id' => $ticket->id]);

    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();

    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(4);
});

// ==================== PROCESS TICKET REMINDERS JOB ====================

it('processes reminders job without errors', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Create active ticket
    Ticket::factory()
        ->for($user)
        ->for($department)
        ->create([
            'status' => StatusTicket::In_Progress,
            'response_due_date' => now()->addHours(2),
            'resolution_due_date' => now()->addDays(5),
        ]);

    $job = new ProcessTicketReminders;
    $job->handle();

    expect(true)->toBeTrue(); // Job executed without throwing exception
});

it('does not process reminders for closed tickets', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for(Department::factory())
        ->closed()
        ->create();

    $initialReminderCount = Reminder::where('ticket_id', $ticket->id)->count();

    $job = new ProcessTicketReminders;
    $job->handle();

    $finalReminderCount = Reminder::where('ticket_id', $ticket->id)->count();

    // No new reminders should be created for closed tickets
    expect($finalReminderCount)->toBe($initialReminderCount);
});

it('prevents duplicate reminders for the same type', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->create([
            'status' => StatusTicket::In_Progress,
            'response_due_date' => now()->subHour(), // Past due
            'resolution_due_date' => now()->addDays(5),
        ]);

    // Create an existing reminder
    Reminder::create([
        'ticket_id' => $ticket->id,
        'sent_to' => $user->id,
        'reminder_type' => ReminderType::HalfTimeResponse,
        'sent_at' => now()->subHours(2),
    ]);

    $initialCount = Reminder::where('ticket_id', $ticket->id)
        ->where('reminder_type', ReminderType::HalfTimeResponse)
        ->count();

    $job = new ProcessTicketReminders;
    $job->handle();

    $finalCount = Reminder::where('ticket_id', $ticket->id)
        ->where('reminder_type', ReminderType::HalfTimeResponse)
        ->count();

    // Should not create duplicate
    expect($finalCount)->toBe($initialCount);
});

// ==================== REMINDER RELATIONSHIPS ====================

it('has correct ticket relationship', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    $reminder = Reminder::factory()->create(['ticket_id' => $ticket->id]);

    expect($reminder->ticket)->not->toBeNull()
        ->and($reminder->ticket->id)->toBe($ticket->id)
        ->and($reminder->ticket)->toBeInstanceOf(Ticket::class);
});

it('has correct user relationship', function () {
    $user = User::factory()->create();

    $reminder = Reminder::factory()->create(['sent_to' => $user->id]);

    expect($reminder->user)->not->toBeNull()
        ->and($reminder->user->id)->toBe($user->id)
        ->and($reminder->user)->toBeInstanceOf(User::class);
});

// ==================== COMPLETE REMINDER WORKFLOW ====================

it('completes full reminder workflow: create -> process -> read', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Step 1: Create ticket (reminders created automatically)
    $ticket = Ticket::create([
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    // Verify reminders were created
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(4);

    // Step 2: Get one reminder
    $reminder = Reminder::where('ticket_id', $ticket->id)->first();
    expect($reminder->is_read)->toBeFalse();

    // Step 3: Mark as read
    $reminder->markAsRead();
    expect($reminder->fresh()->is_read)->toBeTrue();

    // Step 4: Close ticket (reminders deleted)
    $ticket->status = StatusTicket::Closed;
    $ticket->save();

    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

it('handles multiple tickets with independent reminders', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket1 = Ticket::create([
        'title' => 'Ticket 1',
        'description' => 'Description 1',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Petition,
        'status' => StatusTicket::Pending,
        'priority' => Priority::High,
    ]);

    $ticket2 = Ticket::create([
        'title' => 'Ticket 2',
        'description' => 'Description 2',
        'user_id' => $user->id,
        'department_id' => $department->id,
        'type' => TicketType::Complaint,
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
    ]);

    // Both tickets should have their own reminders
    expect(Reminder::where('ticket_id', $ticket1->id)->count())->toBe(4)
        ->and(Reminder::where('ticket_id', $ticket2->id)->count())->toBe(4);

    // Close ticket1, only its reminders should be deleted
    $ticket1->status = StatusTicket::Closed;
    $ticket1->save();

    expect(Reminder::where('ticket_id', $ticket1->id)->count())->toBe(0)
        ->and(Reminder::where('ticket_id', $ticket2->id)->count())->toBe(4);
});
