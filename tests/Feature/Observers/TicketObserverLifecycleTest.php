<?php

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    // Create system user for automated operations
    $systemUser = User::factory()->create([
        'id' => 1,
        'name' => 'System',
        'email' => 'system@sistema-pqrsd.local',
        'role' => UserRole::Admin,
    ]);

    Auth::login($systemUser);
});

// ==================== SOFT DELETE ====================

it('creates log entry when ticket is soft deleted', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create();

    $initialLogCount = TicketLog::where('ticket_id', $ticket->id)->count();

    // Soft delete
    $ticket->delete();

    // Verify log was created
    $newLogCount = TicketLog::where('ticket_id', $ticket->id)->count();
    expect($newLogCount)->toBeGreaterThan($initialLogCount);

    $deleteLog = TicketLog::where('ticket_id', $ticket->id)
        ->orderBy('id', 'desc')
        ->first();

    expect($deleteLog->change_reason)->toBe('Ticket Deleted');
});

it('ticket can be soft deleted and restored', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create();

    // Soft delete
    $ticket->delete();

    expect($ticket->trashed())->toBeTrue();

    // Should still be in database
    expect(Ticket::withTrashed()->find($ticket->id))->not->toBeNull();
});

// ==================== RESTORE ====================

it('creates log entry when ticket is restored', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create();

    // Soft delete first
    $ticket->delete();

    $logCountAfterDelete = TicketLog::where('ticket_id', $ticket->id)->count();

    // Restore
    $ticket->restore();

    // Verify log was created
    $logCountAfterRestore = TicketLog::where('ticket_id', $ticket->id)->count();
    expect($logCountAfterRestore)->toBeGreaterThan($logCountAfterDelete);

    $restoreLog = TicketLog::where('ticket_id', $ticket->id)
        ->orderBy('id', 'desc')
        ->first();

    expect($restoreLog->change_reason)->toBe('Ticket Restored');
});

it('restored ticket is no longer trashed', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create();

    $ticket->delete();
    expect($ticket->trashed())->toBeTrue();

    $ticket->restore();
    expect($ticket->fresh()->trashed())->toBeFalse();
});

// ==================== FORCE DELETE ====================

it('permanently deletes ticket with forceDelete', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create();

    $ticketId = $ticket->id;

    // Force delete
    $ticket->forceDelete();

    // Should not exist even with withTrashed
    expect(Ticket::withTrashed()->find($ticketId))->toBeNull();
});

it('force delete removes ticket completely from database', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $ticket = Ticket::factory()
        ->for($user)
        ->for($department)
        ->inProgress()
        ->create();

    $ticketId = $ticket->id;

    // Force delete
    $ticket->forceDelete();

    // Verify cannot be found
    expect(Ticket::find($ticketId))->toBeNull()
        ->and(Ticket::withTrashed()->find($ticketId))->toBeNull();
});
