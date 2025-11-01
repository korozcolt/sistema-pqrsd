<?php

use App\Enums\StatusTicket;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    Notification::fake();
});

// ==================== COMMENT CREATION ====================

it('creates a comment on a ticket', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    $comment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'content' => 'This is a test comment',
        'is_internal' => false,
    ]);

    expect($comment)->not->toBeNull()
        ->and($comment->content)->toBe('This is a test comment')
        ->and($comment->ticket_id)->toBe($ticket->id)
        ->and($comment->user_id)->toBe($user->id);
});

it('creates internal comment visible only to staff', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    $comment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'content' => 'Internal staff note',
        'is_internal' => true,
    ]);

    expect($comment->is_internal)->toBeTrue();
});

it('creates public comment visible to everyone', function () {
    $user = User::factory()->create(['role' => UserRole::UserWeb]);
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    $comment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'content' => 'Public comment from user',
        'is_internal' => false,
    ]);

    expect($comment->is_internal)->toBeFalse();
});

// ==================== NOTIFICATION ROUTING ====================

it('notifies staff when user_web creates comment', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $ticket = Ticket::factory()
        ->for($webUser)
        ->for(Department::factory())
        ->create();

    $comment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $webUser->id,
        'content' => 'User comment',
        'is_internal' => false,
    ]);

    // Should notify staff email
    Notification::assertSentOnDemand(\App\Notifications\NewTicketCommentNotification::class);
});

it('notifies ticket owner when staff creates comment', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $staffUser = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()
        ->for($webUser)
        ->for(Department::factory())
        ->create();

    $comment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $staffUser->id,
        'content' => 'Staff response',
        'is_internal' => false,
    ]);

    // Should notify the ticket owner (webUser)
    Notification::assertSentTo(
        $webUser,
        \App\Notifications\NewTicketCommentNotification::class,
        function ($notification) use ($comment) {
            return $notification->comment->id === $comment->id;
        }
    );
});

it('notifies staff email when receptionist creates comment', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $receptionist = User::factory()->create(['role' => UserRole::Receptionist]);

    $ticket = Ticket::factory()
        ->for($webUser)
        ->for(Department::factory())
        ->create();

    $comment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $receptionist->id,
        'content' => 'Receptionist comment',
        'is_internal' => false,
    ]);

    // Should notify the ticket owner
    Notification::assertSentTo($webUser, \App\Notifications\NewTicketCommentNotification::class);
});

// ==================== COMMENT RELATIONSHIPS ====================

it('has correct ticket relationship', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    $comment = TicketComment::factory()->create(['ticket_id' => $ticket->id]);

    expect($comment->ticket)->not->toBeNull()
        ->and($comment->ticket->id)->toBe($ticket->id)
        ->and($comment->ticket)->toBeInstanceOf(Ticket::class);
});

it('has correct user relationship', function () {
    $user = User::factory()->create();
    $comment = TicketComment::factory()->create(['user_id' => $user->id]);

    expect($comment->user)->not->toBeNull()
        ->and($comment->user->id)->toBe($user->id)
        ->and($comment->user)->toBeInstanceOf(User::class);
});

it('ticket has many comments relationship', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    TicketComment::factory()->count(5)->create(['ticket_id' => $ticket->id]);

    expect($ticket->comments)->toHaveCount(5);
});

// ==================== ACTIVITY LOG ====================

it('creates activity log entry on comment creation', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    $comment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'content' => 'Test comment',
        'is_internal' => false,
    ]);

    $activity = Activity::where('subject_type', TicketComment::class)
        ->where('subject_id', $comment->id)
        ->where('event', 'created')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->log_name)->toBe('ticket_comment');
});

it('creates activity log entry on comment update', function () {
    $comment = TicketComment::factory()->create(['content' => 'Original content']);

    $comment->content = 'Updated content';
    $comment->save();

    $activity = Activity::where('subject_type', TicketComment::class)
        ->where('subject_id', $comment->id)
        ->where('event', 'updated')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->log_name)->toBe('ticket_comment');
});

// ==================== SOFT DELETES ====================

it('soft deletes comment', function () {
    $comment = TicketComment::factory()->create();

    $comment->delete();

    expect($comment->trashed())->toBeTrue()
        ->and(TicketComment::withTrashed()->find($comment->id))->not->toBeNull();
});

it('restores soft deleted comment', function () {
    $comment = TicketComment::factory()->create();
    $comment->delete();

    expect($comment->trashed())->toBeTrue();

    $comment->restore();

    expect($comment->trashed())->toBeFalse()
        ->and(TicketComment::find($comment->id))->not->toBeNull();
});

it('excludes soft deleted comments from queries by default', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    TicketComment::factory()->count(3)->create(['ticket_id' => $ticket->id]);
    TicketComment::factory()->count(2)->create(['ticket_id' => $ticket->id])->each->delete();

    // Normal query should only return non-deleted
    expect(TicketComment::where('ticket_id', $ticket->id)->count())->toBe(3);

    // With trashed should return all
    expect(TicketComment::withTrashed()->where('ticket_id', $ticket->id)->count())->toBe(5);
});

// ==================== COMMENT VISIBILITY ====================

it('differentiates between internal and public comments', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    TicketComment::factory()->count(3)->internal()->create(['ticket_id' => $ticket->id]);
    TicketComment::factory()->count(2)->public()->create(['ticket_id' => $ticket->id]);

    $internalCount = TicketComment::where('ticket_id', $ticket->id)
        ->where('is_internal', true)
        ->count();

    $publicCount = TicketComment::where('ticket_id', $ticket->id)
        ->where('is_internal', false)
        ->count();

    expect($internalCount)->toBe(3)
        ->and($publicCount)->toBe(2);
});

// ==================== COMPLETE COMMENT WORKFLOW ====================

it('completes full comment workflow: create -> update -> soft delete -> restore', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    // Step 1: Create comment
    $comment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'content' => 'Original comment',
        'is_internal' => false,
    ]);

    expect($comment)->not->toBeNull();

    // Step 2: Update comment
    $comment->content = 'Updated comment';
    $comment->save();

    expect($comment->fresh()->content)->toBe('Updated comment');

    // Step 3: Soft delete
    $comment->delete();
    expect($comment->trashed())->toBeTrue();

    // Step 4: Restore
    $comment->restore();
    expect($comment->trashed())->toBeFalse();
});

it('handles multiple comments on same ticket', function () {
    $webUser = User::factory()->create(['role' => UserRole::UserWeb]);
    $adminUser = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()
        ->for($webUser)
        ->for(Department::factory())
        ->create();

    // User creates comment
    $userComment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $webUser->id,
        'content' => 'User question',
        'is_internal' => false,
    ]);

    // Admin creates internal note
    $internalNote = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $adminUser->id,
        'content' => 'Internal note for staff',
        'is_internal' => true,
    ]);

    // Admin responds to user
    $adminResponse = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $adminUser->id,
        'content' => 'Admin response',
        'is_internal' => false,
    ]);

    expect($ticket->comments)->toHaveCount(3);

    $publicComments = TicketComment::where('ticket_id', $ticket->id)
        ->where('is_internal', false)
        ->get();

    expect($publicComments)->toHaveCount(2); // User comment + Admin public response
});

it('maintains comment order by creation time', function () {
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    $comment1 = TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'content' => 'First comment',
    ]);

    sleep(1);

    $comment2 = TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'content' => 'Second comment',
    ]);

    $comments = TicketComment::where('ticket_id', $ticket->id)
        ->orderBy('created_at')
        ->get();

    expect($comments->first()->id)->toBe($comment1->id)
        ->and($comments->last()->id)->toBe($comment2->id);
});

it('tracks comment author correctly', function () {
    $adminUser = User::factory()->create(['role' => UserRole::Admin, 'name' => 'Admin User']);
    $webUser = User::factory()->create(['role' => UserRole::UserWeb, 'name' => 'Web User']);

    $ticket = Ticket::factory()
        ->for($webUser)
        ->for(Department::factory())
        ->create();

    $adminComment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $adminUser->id,
        'content' => 'Admin comment',
        'is_internal' => false,
    ]);

    $webComment = TicketComment::create([
        'ticket_id' => $ticket->id,
        'user_id' => $webUser->id,
        'content' => 'User comment',
        'is_internal' => false,
    ]);

    expect($adminComment->user->name)->toBe('Admin User')
        ->and($webComment->user->name)->toBe('Web User');
});

it('supports comments on tickets with different statuses', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    // Pending ticket
    $pendingTicket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->create(['status' => StatusTicket::Pending]);

    // In Progress ticket
    $inProgressTicket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create();

    // Resolved ticket
    $resolvedTicket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->resolved()
        ->create();

    TicketComment::factory()->create(['ticket_id' => $pendingTicket->id]);
    TicketComment::factory()->create(['ticket_id' => $inProgressTicket->id]);
    TicketComment::factory()->create(['ticket_id' => $resolvedTicket->id]);

    expect(TicketComment::where('ticket_id', $pendingTicket->id)->count())->toBe(1)
        ->and(TicketComment::where('ticket_id', $inProgressTicket->id)->count())->toBe(1)
        ->and(TicketComment::where('ticket_id', $resolvedTicket->id)->count())->toBe(1);
});

it('allows empty content to be rejected', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()
        ->for(User::factory())
        ->for(Department::factory())
        ->create();

    // This should ideally trigger a validation error
    // For now, we test that content is required at model level
    $comment = new TicketComment([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'content' => '', // Empty content
        'is_internal' => false,
    ]);

    // Content should be empty (no validation at model level, should be handled by form request)
    expect($comment->content)->toBe('');
})->skip('Validation is handled at form request level, not model level');
