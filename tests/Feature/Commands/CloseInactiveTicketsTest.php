<?php

use App\Console\Commands\CloseInactiveTickets;
use App\Enums\StatusTicket;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    // Create system user with ID 1 for automated changes
    User::factory()->create([
        'id' => 1,
        'name' => 'System',
        'email' => 'system@sistema-pqrsd.local',
        'role' => UserRole::Admin,
    ]);
});

// ==================== BASIC FUNCTIONALITY ====================

it('closes tickets marked for closure after 72 hours', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    // Create ticket marked for closure 73 hours ago (past the 72h threshold)
    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => now()->subHours(73),
        ]);

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify ticket was closed
    expect($ticket->fresh()->status)->toBe(StatusTicket::Closed)
        ->and($ticket->fresh()->resolution_at)->not->toBeNull();
});

it('does not close tickets marked for closure within 72 hours', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    // Create ticket marked for closure 50 hours ago (within the 72h grace period)
    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => now()->subHours(50),
        ]);

    $originalStatus = $ticket->status;

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify ticket was NOT closed
    expect($ticket->fresh()->status)->toBe($originalStatus)
        ->and($ticket->fresh()->resolution_at)->toBeNull();
});

it('does not close already closed tickets', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->closed()
        ->create([
            'marked_for_closure_at' => now()->subHours(100),
            'resolution_at' => now()->subDays(2),
        ]);

    $originalResolutionAt = $ticket->resolution_at;

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify resolution_at wasn't changed
    expect($ticket->fresh()->resolution_at->timestamp)
        ->toBe($originalResolutionAt->timestamp);
});

it('does not close rejected tickets', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->rejected()
        ->create([
            'marked_for_closure_at' => now()->subHours(100),
        ]);

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify status unchanged
    expect($ticket->fresh()->status)->toBe(StatusTicket::Rejected);
});

it('does not close tickets without marked_for_closure_at', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => null,
        ]);

    $originalStatus = $ticket->status;

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify ticket was NOT closed
    expect($ticket->fresh()->status)->toBe($originalStatus);
});

// ==================== CLIENT ACTIVITY DETECTION ====================

it('does not close if client commented after being marked', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $markedAt = now()->subHours(100); // Marked 100 hours ago

    $ticket = Ticket::factory()
        ->for($webUser)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => $markedAt,
        ]);

    // Client commented AFTER being marked (50 hours ago)
    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $webUser->id,
        'content' => 'Client response after warning',
        'created_at' => $markedAt->copy()->addHours(50),
    ]);

    $originalStatus = $ticket->status;

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify ticket was NOT closed
    expect($ticket->fresh()->status)->toBe($originalStatus)
        ->and($ticket->fresh()->marked_for_closure_at)->toBeNull(); // Mark should be removed
});

it('closes if only staff commented after being marked', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $staffUser = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $markedAt = now()->subHours(100);

    $ticket = Ticket::factory()
        ->for($webUser)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => $markedAt,
        ]);

    // Staff commented AFTER being marked (should not prevent closure)
    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $staffUser->id,
        'content' => 'Staff follow up',
        'created_at' => $markedAt->copy()->addHours(50),
    ]);

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify ticket WAS closed (staff comments don't count as client activity)
    expect($ticket->fresh()->status)->toBe(StatusTicket::Closed);
});

// ==================== AUTOMATIC COMMENT CREATION ====================

it('creates automatic closure comment with correct content', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => now()->subHours(80),
            'created_at' => now()->subDays(10), // 10 days old
        ]);

    $initialCommentCount = $ticket->comments()->count();

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify automatic comment was created
    $newCommentCount = $ticket->fresh()->comments()->count();
    expect($newCommentCount)->toBe($initialCommentCount + 1);

    $autoComment = $ticket->comments()->latest()->first();
    expect($autoComment->content)->toContain('cerrado automáticamente')
        ->and($autoComment->content)->toContain('días de inactividad')
        ->and($autoComment->is_internal)->toBeFalse();
});

// ==================== NOTIFICATIONS ====================

it('sends notification to ticket owner on automatic closure', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => now()->subHours(80),
        ]);

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify notification was sent
    Notification::assertSentTo(
        $user,
        \App\Notifications\TicketInactivityClosedNotification::class,
        function ($notification) use ($ticket) {
            return $notification->ticket->id === $ticket->id;
        }
    );
});

// ==================== EVENTS ====================

// Note: Event dispatching is tested in TicketStatusFlowTest.php via the Observer
// The command no longer manually dispatches events - the Observer handles it automatically

// ==================== BATCH PROCESSING ====================

it('closes multiple tickets in single execution', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    // Create 5 tickets marked for closure
    $tickets = collect();
    for ($i = 0; $i < 5; $i++) {
        $tickets->push(
            Ticket::factory()
                ->for($user)
                ->for($department)
                ->inProgress()
                ->create([
                    'marked_for_closure_at' => now()->subHours(80 + $i),
                ])
        );
    }

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful()
        ->expectsOutput('Se cerraron 5 tickets automáticamente por inactividad.');

    // Verify all tickets were closed
    foreach ($tickets as $ticket) {
        expect($ticket->fresh()->status)->toBe(StatusTicket::Closed);
    }
});

it('reports correct count when no tickets need closing', function () {
    // No tickets in database
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful()
        ->expectsOutput('Se cerraron 0 tickets automáticamente por inactividad.');
});

it('handles mixed scenarios correctly', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    // Ticket 1: Should close (marked 80h ago)
    $ticket1 = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => now()->subHours(80),
        ]);

    // Ticket 2: Should NOT close (marked 50h ago - within grace period)
    $ticket2 = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => now()->subHours(50),
        ]);

    // Ticket 3: Should NOT close (client responded after mark)
    $markedAt = now()->subHours(100);
    $ticket3 = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => $markedAt,
        ]);
    TicketComment::factory()->create([
        'ticket_id' => $ticket3->id,
        'user_id' => $user->id,
        'created_at' => $markedAt->copy()->addHours(20),
    ]);

    // Ticket 4: Should close (marked 90h ago, no client response)
    $ticket4 = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => now()->subHours(90),
        ]);

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful()
        ->expectsOutput('Se cerraron 2 tickets automáticamente por inactividad.');

    // Verify outcomes
    expect($ticket1->fresh()->status)->toBe(StatusTicket::Closed) // Should close
        ->and($ticket2->fresh()->status)->toBe(StatusTicket::In_Progress) // Should remain open
        ->and($ticket3->fresh()->status)->toBe(StatusTicket::In_Progress) // Should remain open (client responded)
        ->and($ticket3->fresh()->marked_for_closure_at)->toBeNull() // Mark removed
        ->and($ticket4->fresh()->status)->toBe(StatusTicket::Closed); // Should close
});

// ==================== EDGE CASES ====================

it('handles tickets with no comments gracefully', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => now()->subHours(80),
            'created_at' => now()->subDays(5),
        ]);

    // Ensure no comments exist
    expect($ticket->comments()->count())->toBe(0);

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify ticket was closed and automatic comment was added
    expect($ticket->fresh()->status)->toBe(StatusTicket::Closed)
        ->and($ticket->fresh()->comments()->count())->toBe(1);
});

it('sets resolution_at timestamp when closing', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => now()->subHours(80),
            'resolution_at' => null,
        ]);

    // Run command
    $this->artisan(CloseInactiveTickets::class)
        ->assertSuccessful();

    // Verify resolution_at was set
    expect($ticket->fresh()->resolution_at)->not->toBeNull()
        ->and($ticket->fresh()->resolution_at->isToday())->toBeTrue();
});
