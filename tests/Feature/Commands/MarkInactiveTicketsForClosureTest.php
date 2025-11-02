<?php

use App\Console\Commands\MarkInactiveTicketsForClosure;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    // Create system user with ID 1 for internal comments
    User::factory()->create([
        'id' => 1,
        'name' => 'System',
        'email' => 'system@sistema-pqrsd.local',
        'role' => UserRole::Admin,
    ]);
});

// ==================== BASIC FUNCTIONALITY ====================

it('marks tickets inactive for 7 days', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(10),
        ]);

    // Staff commented 8 days ago (last activity)
    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $staff->id,
        'content' => 'Staff response',
        'created_at' => now()->subDays(8),
    ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify ticket was marked
    expect($ticket->fresh()->marked_for_closure_at)->not->toBeNull();
});

it('does not mark tickets with activity within 7 days', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(10),
        ]);

    // Staff commented 5 days ago (within threshold)
    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $staff->id,
        'content' => 'Recent staff response',
        'created_at' => now()->subDays(5),
    ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify ticket was NOT marked
    expect($ticket->fresh()->marked_for_closure_at)->toBeNull();
});

it('does not mark already marked tickets', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $markedAt = now()->subDays(2);

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'marked_for_closure_at' => $markedAt,
            'created_at' => now()->subDays(10),
        ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify mark date unchanged
    expect($ticket->fresh()->marked_for_closure_at->timestamp)
        ->toBe($markedAt->timestamp);
});

it('does not mark closed tickets', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->closed()
        ->create([
            'created_at' => now()->subDays(20),
        ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify ticket was NOT marked
    expect($ticket->fresh()->marked_for_closure_at)->toBeNull();
});

it('does not mark rejected tickets', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->rejected()
        ->create([
            'created_at' => now()->subDays(20),
        ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify ticket was NOT marked
    expect($ticket->fresh()->marked_for_closure_at)->toBeNull();
});

// ==================== CLIENT ACTIVITY DETECTION ====================

it('does not mark if client responded recently', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($webUser)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(10),
        ]);

    // Staff commented 8 days ago
    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $staff->id,
        'content' => 'Staff response',
        'created_at' => now()->subDays(8),
    ]);

    // Client responded 5 days ago (within 7 day threshold)
    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $webUser->id,
        'content' => 'Client response',
        'created_at' => now()->subDays(5),
    ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify ticket was NOT marked (client is still active)
    expect($ticket->fresh()->marked_for_closure_at)->toBeNull();
});

it('marks if only staff commented recently', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($webUser)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(15),
        ]);

    // Client commented 10 days ago
    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $webUser->id,
        'content' => 'Client question',
        'created_at' => now()->subDays(10),
    ]);

    // Staff responded 8 days ago (but client never replied)
    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $staff->id,
        'content' => 'Staff response',
        'created_at' => now()->subDays(8),
    ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify ticket WAS marked (client inactive after staff response)
    expect($ticket->fresh()->marked_for_closure_at)->not->toBeNull();
});

it('detects activity from all staff roles', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($webUser)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(15),
        ]);

    // SuperAdmin commented 8 days ago (should be detected as staff)
    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $superAdmin->id,
        'content' => 'SuperAdmin response',
        'created_at' => now()->subDays(8),
    ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify ticket was marked
    expect($ticket->fresh()->marked_for_closure_at)->not->toBeNull();
});

// ==================== AUTOMATIC COMMENT CREATION ====================

it('creates internal warning comment when marking', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(10),
        ]);

    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $staff->id,
        'created_at' => now()->subDays(8),
    ]);

    $initialCommentCount = $ticket->comments()->count();

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify internal comment was created
    $newCommentCount = $ticket->fresh()->comments()->count();
    expect($newCommentCount)->toBe($initialCommentCount + 1);

    $warningComment = $ticket->comments()->latest()->first();
    expect($warningComment->content)->toContain('inactividad')
        ->and($warningComment->content)->toContain('72 horas')
        ->and($warningComment->is_internal)->toBeTrue();
});

// ==================== NOTIFICATIONS ====================

it('sends warning notification to ticket owner', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(10),
        ]);

    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $staff->id,
        'created_at' => now()->subDays(8),
    ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify notification was sent
    Notification::assertSentTo(
        $user,
        \App\Notifications\TicketInactivityWarningNotification::class,
        function ($notification) use ($ticket) {
            return $notification->ticket->id === $ticket->id;
        }
    );
});

// ==================== BATCH PROCESSING ====================

it('marks multiple inactive tickets in single execution', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    // Create 5 inactive tickets
    $tickets = collect();
    for ($i = 0; $i < 5; $i++) {
        $ticket = Ticket::factory()
            ->for($user)
            ->for($department)
            ->inProgress()
            ->create([
                'created_at' => now()->subDays(15 + $i),
            ]);

        TicketComment::factory()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $staff->id,
            'created_at' => now()->subDays(8 + $i),
        ]);

        $tickets->push($ticket);
    }

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful()
        ->expectsOutput('Se marcaron 5 tickets para cierre por inactividad.');

    // Verify all tickets were marked
    foreach ($tickets as $ticket) {
        expect($ticket->fresh()->marked_for_closure_at)->not->toBeNull();
    }
});

it('reports correct count when no tickets need marking', function () {
    // No inactive tickets in database
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful()
        ->expectsOutput('Se marcaron 0 tickets para cierre por inactividad.');
});

it('handles mixed scenarios correctly', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    // Ticket 1: Should mark (staff commented 8 days ago, no client response)
    $ticket1 = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create(['created_at' => now()->subDays(15)]);

    TicketComment::factory()->create([
        'ticket_id' => $ticket1->id,
        'user_id' => $staff->id,
        'created_at' => now()->subDays(8),
    ]);

    // Ticket 2: Should NOT mark (client responded 5 days ago)
    $ticket2 = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create(['created_at' => now()->subDays(15)]);

    TicketComment::factory()->create([
        'ticket_id' => $ticket2->id,
        'user_id' => $staff->id,
        'created_at' => now()->subDays(8),
    ]);
    TicketComment::factory()->create([
        'ticket_id' => $ticket2->id,
        'user_id' => $user->id,
        'created_at' => now()->subDays(5),
    ]);

    // Ticket 3: Should NOT mark (already marked)
    $ticket3 = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(15),
            'marked_for_closure_at' => now()->subDays(3),
        ]);

    // Ticket 4: Should NOT mark (closed)
    $ticket4 = Ticket::factory()
        ->for($user)
        ->for($department)
        ->closed()
        ->create(['created_at' => now()->subDays(20)]);

    // Ticket 5: Should mark (staff commented 9 days ago, no client response)
    $ticket5 = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create(['created_at' => now()->subDays(20)]);

    TicketComment::factory()->create([
        'ticket_id' => $ticket5->id,
        'user_id' => $staff->id,
        'created_at' => now()->subDays(9),
    ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful()
        ->expectsOutput('Se marcaron 2 tickets para cierre por inactividad.');

    // Verify outcomes
    expect($ticket1->fresh()->marked_for_closure_at)->not->toBeNull() // Should mark
        ->and($ticket2->fresh()->marked_for_closure_at)->toBeNull() // Should remain unmarked
        ->and($ticket3->fresh()->marked_for_closure_at)->not->toBeNull() // Already marked
        ->and($ticket4->fresh()->marked_for_closure_at)->toBeNull() // Closed, should not mark
        ->and($ticket5->fresh()->marked_for_closure_at)->not->toBeNull(); // Should mark
});

// ==================== EDGE CASES ====================

it('does not mark tickets with no comments', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create([
            'created_at' => now()->subDays(15),
        ]);

    // Ensure no comments exist
    expect($ticket->comments()->count())->toBe(0);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Ticket should NOT be marked (requires staff comment to trigger inactivity)
    expect($ticket->fresh()->marked_for_closure_at)->toBeNull();
});

it('sets marked_for_closure_at to current timestamp', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $staff = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create(['created_at' => now()->subDays(10)]);

    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $staff->id,
        'created_at' => now()->subDays(8),
    ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Verify marked_for_closure_at is approximately now
    $markedAt = $ticket->fresh()->marked_for_closure_at;
    expect($markedAt)->not->toBeNull()
        ->and($markedAt->isToday())->toBeTrue()
        ->and(now()->diffInMinutes($markedAt))->toBeLessThan(5);
});

it('does not mark tickets in resolved state waiting for client confirmation', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->resolved()
        ->create([
            'created_at' => now()->subDays(10),
            'resolution_at' => now()->subDays(2),
        ]);

    // Run command
    $this->artisan(MarkInactiveTicketsForClosure::class)
        ->assertSuccessful();

    // Resolved tickets should not be marked (they follow different closure flow)
    expect($ticket->fresh()->marked_for_closure_at)->toBeNull();
});
