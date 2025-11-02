<?php

use App\Enums\ReminderType;
use App\Jobs\ProcessTicketReminders;
use App\Models\Department;
use App\Models\Reminder;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketReminderNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    // Create system user for internal operations
    User::factory()->create([
        'id' => 1,
        'name' => 'System',
        'email' => 'system@sistema-pqrsd.local',
        'role' => \App\Enums\UserRole::Admin,
    ]);
});

// ==================== RESPONSE REMINDERS ====================

it('creates half-time response reminder when threshold is reached', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Ticket created 3 hours ago with 4-hour response SLA
    // Half-time = 2 hours (crossed 1 hour ago, should trigger)
    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subHours(3),
            'response_due_date' => now()->addHour(), // Total 4 hours
            'resolution_due_date' => now()->addDays(5),
        ]);

    // Clean up reminders created by listener to test Job behavior
    Reminder::where('ticket_id', $ticket->id)->delete();

    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify half-time response reminder was created
    $reminder = Reminder::where('ticket_id', $ticket->id)
        ->where('reminder_type', ReminderType::HalfTimeResponse)
        ->first();

    expect($reminder)->not->toBeNull()
        ->and($reminder->sent_to)->toBe($user->id)
        ->and($reminder->sent_at)->not->toBeNull();
});

it('creates day-before response reminder when threshold is reached', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Ticket with response due in 23 hours (within 24h window)
    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subHours(1),
            'response_due_date' => now()->addHours(23),
            'resolution_due_date' => now()->addDays(5),
        ]);

    // Manually create half-time reminder to avoid conflict
    Reminder::create([
        'ticket_id' => $ticket->id,
        'sent_to' => $user->id,
        'reminder_type' => ReminderType::HalfTimeResponse,
        'sent_at' => now()->subHours(1),
    ]);

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify day-before response reminder was created
    $reminder = Reminder::where('ticket_id', $ticket->id)
        ->where('reminder_type', ReminderType::DayBeforeResponse)
        ->first();

    expect($reminder)->not->toBeNull();
});

it('does not create duplicate response reminders', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subHours(3),
            'response_due_date' => now()->addHour(),
            'resolution_due_date' => now()->addDays(5),
        ]);

    // Clean up reminders created by listener
    Reminder::where('ticket_id', $ticket->id)->delete();

    // Run job twice
    $job1 = new ProcessTicketReminders;
    $job1->handle();

    $countAfterFirstRun = Reminder::where('ticket_id', $ticket->id)->count();

    $job2 = new ProcessTicketReminders;
    $job2->handle();

    $countAfterSecondRun = Reminder::where('ticket_id', $ticket->id)->count();

    // Verify no NEW reminders created on second run
    expect($countAfterSecondRun)->toBe($countAfterFirstRun);

    // Verify there are no duplicate reminder types
    $reminderTypes = Reminder::where('ticket_id', $ticket->id)
        ->pluck('reminder_type')
        ->map(fn ($type) => $type->value)
        ->toArray();

    $uniqueTypes = array_unique($reminderTypes);

    expect(count($reminderTypes))->toBe(count($uniqueTypes));
});

// ==================== RESOLUTION REMINDERS ====================

it('creates half-time resolution reminder when threshold is reached', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Ticket created 3 days ago with 6-day resolution SLA
    // Half-time = 3 days (should trigger now)
    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(3),
            'response_due_date' => now()->subDays(2), // Already past
            'resolution_due_date' => now()->addDays(3), // Total 6 days
        ]);

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify half-time resolution reminder was created
    $reminder = Reminder::where('ticket_id', $ticket->id)
        ->where('reminder_type', ReminderType::HalfTimeResolution)
        ->first();

    expect($reminder)->not->toBeNull();
});

it('creates day-before resolution reminder when threshold is reached', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Ticket with resolution due in 23 hours
    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(4),
            'response_due_date' => now()->subDays(3),
            'resolution_due_date' => now()->addHours(23),
        ]);

    // Manually create half-time reminder
    Reminder::create([
        'ticket_id' => $ticket->id,
        'sent_to' => $user->id,
        'reminder_type' => ReminderType::HalfTimeResolution,
        'sent_at' => now()->subDays(1),
    ]);

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify day-before resolution reminder was created
    $reminder = Reminder::where('ticket_id', $ticket->id)
        ->where('reminder_type', ReminderType::DayBeforeResolution)
        ->first();

    expect($reminder)->not->toBeNull();
});

// ==================== NOTIFICATIONS ====================

it('sends notification to user when creating reminder', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subHours(3),
            'response_due_date' => now()->addHour(),
            'resolution_due_date' => now()->addDays(5),
        ]);

    // Clean up reminders created by listener
    Reminder::where('ticket_id', $ticket->id)->delete();

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify notification was sent to user
    Notification::assertSentTo(
        $user,
        TicketReminderNotification::class,
        function ($notification) use ($ticket) {
            return $notification->ticket->id === $ticket->id
                && $notification->reminderType === ReminderType::HalfTimeResponse;
        }
    );
});

it('sends notification to staff email when creating reminder', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subHours(2),
            'response_due_date' => now()->addHours(2),
            'resolution_due_date' => now()->addDays(5),
        ]);

    // Clean up reminders created by listener
    Reminder::where('ticket_id', $ticket->id)->delete();

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify on-demand notification was sent
    Notification::assertSentOnDemand(TicketReminderNotification::class);
});

// ==================== BATCH PROCESSING ====================

it('processes multiple tickets in single execution', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Create 3 tickets all needing half-time response reminder
    $tickets = collect();
    for ($i = 0; $i < 3; $i++) {
        $tickets->push(
            Ticket::factory()
                ->for($user)
                ->for($department)
                ->inProgress()
                ->create([
                    'created_at' => now()->subHours(2),
                    'response_due_date' => now()->addHours(2),
                    'resolution_due_date' => now()->addDays(5),
                ])
        );
    }

    // Run job once
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify reminders created for all tickets
    foreach ($tickets as $ticket) {
        $reminder = Reminder::where('ticket_id', $ticket->id)
            ->where('reminder_type', ReminderType::HalfTimeResponse)
            ->first();

        expect($reminder)->not->toBeNull();
    }
});

it('only processes tickets with both SLA dates', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Create a ticket with both dates (should be processed)
    $completeTicket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subHours(3),
            'response_due_date' => now()->addHour(),
            'resolution_due_date' => now()->addDays(5),
        ]);

    Reminder::where('ticket_id', $completeTicket->id)->delete();

    // Create a ticket missing resolution_due_date (should be skipped)
    $incompleteTicket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subHours(3),
            'response_due_date' => now()->addHour(),
            'resolution_due_date' => null,
        ]);

    Reminder::where('ticket_id', $incompleteTicket->id)->delete();

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify the complete ticket got processed
    expect(Reminder::where('ticket_id', $completeTicket->id)->count())->toBeGreaterThan(0);

    // Verify the incomplete ticket was skipped
    expect(Reminder::where('ticket_id', $incompleteTicket->id)->count())->toBe(0);
});

it('skips closed tickets', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->closed()
        ->create([
            'created_at' => now()->subHours(2),
            'response_due_date' => now()->addHours(2),
            'resolution_due_date' => now()->addDays(5),
        ]);

    // Clean up reminders created by listener
    Reminder::where('ticket_id', $ticket->id)->delete();

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify no reminders created for closed ticket
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

it('skips resolved tickets', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->resolved()
        ->create([
            'created_at' => now()->subHours(2),
            'response_due_date' => now()->addHours(2),
            'resolution_due_date' => now()->addDays(5),
        ]);

    // Clean up reminders created by listener
    Reminder::where('ticket_id', $ticket->id)->delete();

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify no reminders created for resolved ticket
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

it('skips rejected tickets', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->rejected()
        ->create([
            'created_at' => now()->subHours(2),
            'response_due_date' => now()->addHours(2),
            'resolution_due_date' => now()->addDays(5),
        ]);

    // Clean up reminders created by listener
    Reminder::where('ticket_id', $ticket->id)->delete();

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify no reminders created for rejected ticket
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

// ==================== EDGE CASES ====================

it('handles ticket with only response due date', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subHours(2),
            'response_due_date' => now()->addHours(2),
            'resolution_due_date' => null, // Missing
        ]);

    // Clean up reminders created by listener (should be fewer due to missing resolution_due_date)
    Reminder::where('ticket_id', $ticket->id)->delete();

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Should skip ticket due to missing resolution_due_date
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

it('creates both response and resolution reminders when both thresholds met', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Ticket at half-time for both response and resolution
    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subHours(2),
            'response_due_date' => now()->addHours(2), // 4h total, half = 2h
            'resolution_due_date' => now()->addHours(2), // Same timing
        ]);

    // Run job
    $job = new ProcessTicketReminders;
    $job->handle();

    // Verify both reminders created
    $responseReminder = Reminder::where('ticket_id', $ticket->id)
        ->where('reminder_type', ReminderType::HalfTimeResponse)
        ->first();

    $resolutionReminder = Reminder::where('ticket_id', $ticket->id)
        ->where('reminder_type', ReminderType::HalfTimeResolution)
        ->first();

    expect($responseReminder)->not->toBeNull()
        ->and($resolutionReminder)->not->toBeNull();
});
