<?php

use App\Enums\StatusTicket;
use App\Events\TicketStatusChanged;
use App\Models\Department;
use App\Models\Reminder;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\User;
use App\Services\TicketStateMachine;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    Notification::fake();
});

// ==================== VALID TRANSITIONS ====================

it('allows transition from Pending to In_Progress', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();

    expect($ticket->fresh()->status)->toBe(StatusTicket::In_Progress);
});

it('allows transition from Pending to Rejected', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    $ticket->status = StatusTicket::Rejected;
    $ticket->save();

    expect($ticket->fresh()->status)->toBe(StatusTicket::Rejected);
});

it('allows transition from In_Progress to Resolved', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->inProgress()
        ->create();

    $ticket->status = StatusTicket::Resolved;
    $ticket->save();

    expect($ticket->fresh()->status)->toBe(StatusTicket::Resolved);
});

it('allows transition from Resolved to Closed', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->resolved()
        ->create();

    $ticket->status = StatusTicket::Closed;
    $ticket->save();

    expect($ticket->fresh()->status)->toBe(StatusTicket::Closed);
});

it('allows transition from Resolved to Reopened', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->resolved()
        ->create();

    $ticket->status = StatusTicket::Reopened;
    $ticket->save();

    expect($ticket->fresh()->status)->toBe(StatusTicket::Reopened);
});

it('allows transition from Closed to Reopened', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->closed()
        ->create();

    $ticket->status = StatusTicket::Reopened;
    $ticket->save();

    expect($ticket->fresh()->status)->toBe(StatusTicket::Reopened);
});

it('allows transition from Reopened to In_Progress', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->reopened()
        ->create();

    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();

    expect($ticket->fresh()->status)->toBe(StatusTicket::In_Progress);
});

// ==================== STATE MACHINE VALIDATION ====================

it('validates transitions using State Machine', function () {
    $stateMachine = new TicketStateMachine;

    // Valid transitions
    expect($stateMachine->canTransition(StatusTicket::Pending, StatusTicket::In_Progress))->toBeTrue()
        ->and($stateMachine->canTransition(StatusTicket::In_Progress, StatusTicket::Resolved))->toBeTrue()
        ->and($stateMachine->canTransition(StatusTicket::Resolved, StatusTicket::Closed))->toBeTrue();

    // Invalid transitions
    expect($stateMachine->canTransition(StatusTicket::Pending, StatusTicket::Closed))->toBeFalse()
        ->and($stateMachine->canTransition(StatusTicket::Pending, StatusTicket::Resolved))->toBeFalse();
});

it('uses State Machine to transition ticket states', function () {
    $stateMachine = new TicketStateMachine;

    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    // Valid transition
    $result = $stateMachine->transition($ticket, StatusTicket::In_Progress, 'Starting work');
    expect($result)->toBeTrue()
        ->and($ticket->fresh()->status)->toBe(StatusTicket::In_Progress);
});

it('rejects invalid transitions using State Machine', function () {
    $stateMachine = new TicketStateMachine;

    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    // Try invalid transition (Pending -> Closed)
    $result = $stateMachine->transition($ticket, StatusTicket::Closed, 'Invalid attempt');

    expect($result)->toBeFalse()
        ->and($ticket->fresh()->status)->toBe(StatusTicket::Pending); // Should remain unchanged
});

it('uses ticket helper methods to check allowed transitions', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    // Check allowed transitions
    expect($ticket->canTransitionTo(StatusTicket::In_Progress))->toBeTrue()
        ->and($ticket->canTransitionTo(StatusTicket::Rejected))->toBeTrue()
        ->and($ticket->canTransitionTo(StatusTicket::Closed))->toBeFalse();

    // Get all allowed next states
    $allowedStates = $ticket->getAllowedNextStates();
    expect($allowedStates)->toHaveCount(2)
        ->and($allowedStates)->toContain(StatusTicket::In_Progress)
        ->and($allowedStates)->toContain(StatusTicket::Rejected);
});

it('uses ticket helper method to perform transition', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    $result = $ticket->transitionTo(StatusTicket::In_Progress, 'Starting to work');

    expect($result)->toBeTrue()
        ->and($ticket->fresh()->status)->toBe(StatusTicket::In_Progress);
});

it('identifies terminal states correctly', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->closed()
        ->create();

    // Closed should have only one allowed transition (to Reopened)
    expect($ticket->isInTerminalState())->toBeTrue();

    // Pending is not terminal
    $ticket->status = StatusTicket::Pending;
    expect($ticket->isInTerminalState())->toBeFalse();
});

// ==================== SIDE EFFECTS ====================

it('deletes reminders when ticket is resolved', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->inProgress()
        ->create();

    // Create some reminders
    Reminder::factory()->count(4)->create(['ticket_id' => $ticket->id]);

    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(4);

    // Resolve ticket
    $ticket->status = StatusTicket::Resolved;
    $ticket->save();

    // Reminders should be deleted
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

it('deletes reminders when ticket is closed', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->resolved()
        ->create();

    // Create some reminders
    Reminder::factory()->count(4)->create(['ticket_id' => $ticket->id]);

    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(4);

    // Close ticket
    $ticket->status = StatusTicket::Closed;
    $ticket->save();

    // Reminders should be deleted
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

it('deletes reminders when ticket is rejected', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    // Create some reminders
    Reminder::factory()->count(4)->create(['ticket_id' => $ticket->id]);

    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(4);

    // Reject ticket
    $ticket->status = StatusTicket::Rejected;
    $ticket->save();

    // Reminders should be deleted
    expect(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);
});

// ==================== EVENTS & NOTIFICATIONS ====================

it('dispatches TicketStatusChanged event on status change', function () {
    Event::fake();

    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    $oldStatus = $ticket->status;
    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();

    Event::assertDispatched(TicketStatusChanged::class, function ($event) use ($ticket, $oldStatus) {
        return $event->ticket->id === $ticket->id
            && $event->oldStatus === $oldStatus->value
            && $event->newStatus === StatusTicket::In_Progress->value;
    });
});

it('sends notification to user on status change', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()
        ->for($user)
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();

    Notification::assertSentTo(
        $user,
        \App\Notifications\TicketStatusUpdated::class,
        function ($notification) use ($ticket) {
            return $notification->ticket->id === $ticket->id;
        }
    );
});

it('sends notification to staff email on status change', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();

    Notification::assertSentOnDemand(
        \App\Notifications\TicketStatusUpdated::class
    );
});

// ==================== LOGGING ====================

it('creates ticket log entry on status change', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    $initialLogCount = TicketLog::where('ticket_id', $ticket->id)->count();

    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();

    $newLogCount = TicketLog::where('ticket_id', $ticket->id)->count();
    expect($newLogCount)->toBeGreaterThan($initialLogCount);

    $latestLog = TicketLog::where('ticket_id', $ticket->id)
        ->latest()
        ->first();

    expect($latestLog->previous_status)->toBe(StatusTicket::Pending)
        ->and($latestLog->new_status)->toBe(StatusTicket::In_Progress);
});

it('creates activity log entry on status change', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    $initialActivityCount = Activity::where('subject_type', Ticket::class)
        ->where('subject_id', $ticket->id)
        ->count();

    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();

    $newActivityCount = Activity::where('subject_type', Ticket::class)
        ->where('subject_id', $ticket->id)
        ->count();

    expect($newActivityCount)->toBeGreaterThan($initialActivityCount);

    $latestActivity = Activity::where('subject_type', Ticket::class)
        ->where('subject_id', $ticket->id)
        ->latest()
        ->first();

    expect($latestActivity->event)->toBe('updated')
        ->and($latestActivity->log_name)->toBe('ticket');
});

// ==================== COMPLETE WORKFLOW SCENARIOS ====================

it('completes full workflow: Pending -> In_Progress -> Resolved -> Closed', function () {
    Event::fake();

    $user = User::factory()->create();
    $ticket = Ticket::factory()
        ->for($user)
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    // Step 1: Pending -> In_Progress
    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::In_Progress);

    // Step 2: In_Progress -> Resolved
    $ticket->status = StatusTicket::Resolved;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::Resolved);

    // Step 3: Resolved -> Closed
    $ticket->status = StatusTicket::Closed;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::Closed);

    // Verify events were dispatched for each transition
    Event::assertDispatched(TicketStatusChanged::class, 3);

    // Verify notifications were sent
    Notification::assertSentTo($user, \App\Notifications\TicketStatusUpdated::class, 3);
});

it('completes rejection workflow: Pending -> Rejected', function () {
    Event::fake();

    $user = User::factory()->create();
    $ticket = Ticket::factory()
        ->for($user)
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    // Create reminders
    Reminder::factory()->count(4)->create(['ticket_id' => $ticket->id]);

    // Reject ticket
    $ticket->status = StatusTicket::Rejected;
    $ticket->save();

    expect($ticket->fresh()->status)->toBe(StatusTicket::Rejected)
        ->and(Reminder::where('ticket_id', $ticket->id)->count())->toBe(0);

    Event::assertDispatched(TicketStatusChanged::class);
    Notification::assertSentTo($user, \App\Notifications\TicketStatusUpdated::class);
});

it('completes reopening workflow: Resolved -> Reopened -> In_Progress -> Resolved -> Closed', function () {
    Event::fake();

    $user = User::factory()->create();
    $ticket = Ticket::factory()
        ->for($user)
        ->for(Department::factory())
        ->resolved()
        ->create();

    // Step 1: Resolved -> Reopened
    $ticket->status = StatusTicket::Reopened;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::Reopened);

    // Step 2: Reopened -> In_Progress
    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::In_Progress);

    // Step 3: In_Progress -> Resolved
    $ticket->status = StatusTicket::Resolved;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::Resolved);

    // Step 4: Resolved -> Closed
    $ticket->status = StatusTicket::Closed;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::Closed);

    // Verify all transitions were tracked
    Event::assertDispatched(TicketStatusChanged::class, 4);
});

it('prevents invalid workflow: Pending -> Closed (direct)', function () {
    $stateMachine = new TicketStateMachine;

    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create(['status' => StatusTicket::Pending]);

    // Try to close directly (invalid)
    $result = $stateMachine->transition($ticket, StatusTicket::Closed);

    expect($result)->toBeFalse()
        ->and($ticket->fresh()->status)->toBe(StatusTicket::Pending);
});

it('handles complex workflow with backtracking: In_Progress -> Pending -> In_Progress -> Resolved', function () {
    Event::fake();

    $user = User::factory()->create();
    $ticket = Ticket::factory()
        ->for($user)
        ->for(Department::factory())
        ->inProgress()
        ->create();

    // Backtrack to Pending
    $ticket->status = StatusTicket::Pending;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::Pending);

    // Resume work
    $ticket->status = StatusTicket::In_Progress;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::In_Progress);

    // Complete
    $ticket->status = StatusTicket::Resolved;
    $ticket->save();
    expect($ticket->fresh()->status)->toBe(StatusTicket::Resolved);

    Event::assertDispatched(TicketStatusChanged::class, 3);
});
