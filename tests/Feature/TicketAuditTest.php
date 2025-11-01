<?php

use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Models\Department;
use App\Models\SLA;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

it('logs ticket creation activity', function () {
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

    expect($activity)->not->toBeNull();
    expect($activity->log_name)->toBe('ticket');
});

it('logs ticket status update activity', function () {
    $ticket = Ticket::factory()->create([
        'status' => StatusTicket::Pending,
    ]);

    $ticket->update(['status' => StatusTicket::In_Progress]);

    $activity = Activity::where('subject_type', Ticket::class)
        ->where('subject_id', $ticket->id)
        ->where('event', 'updated')
        ->latest()
        ->first();

    expect($activity)->not->toBeNull();
    expect($activity->properties)->toHaveKey('old');
    expect($activity->properties)->toHaveKey('attributes');
    expect($activity->properties['old']['status'])->toBe(StatusTicket::Pending->value);
    expect($activity->properties['attributes']['status'])->toBe(StatusTicket::In_Progress->value);
});

it('logs ticket comment creation', function () {
    $ticket = Ticket::factory()->create();
    $user = User::factory()->create();

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

    expect($activity)->not->toBeNull();
    expect($activity->log_name)->toBe('ticket_comment');
});

it('logs user updates activity', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
    ]);

    $user->update(['name' => 'Updated Name']);

    $activity = Activity::where('subject_type', User::class)
        ->where('subject_id', $user->id)
        ->where('event', 'updated')
        ->first();

    expect($activity)->not->toBeNull();
    expect($activity->log_name)->toBe('user');
    expect($activity->properties['old']['name'])->toBe('Original Name');
    expect($activity->properties['attributes']['name'])->toBe('Updated Name');
});

it('logs SLA creation and updates', function () {
    $sla = SLA::create([
        'ticket_type' => TicketType::Petition,
        'priority' => Priority::High,
        'response_time' => 8,
        'resolution_time' => 360,
        'is_active' => true,
    ]);

    $createActivity = Activity::where('subject_type', SLA::class)
        ->where('subject_id', $sla->id)
        ->where('event', 'created')
        ->first();

    expect($createActivity)->not->toBeNull();
    expect($createActivity->log_name)->toBe('sla');

    $sla->update(['response_time' => 4]);

    $updateActivity = Activity::where('subject_type', SLA::class)
        ->where('subject_id', $sla->id)
        ->where('event', 'updated')
        ->first();

    expect($updateActivity)->not->toBeNull();
    expect($updateActivity->properties['old']['response_time'])->toBe(8);
    expect($updateActivity->properties['attributes']['response_time'])->toBe(4);
});

it('logs department updates', function () {
    $department = Department::factory()->create([
        'name' => 'Original Department',
    ]);

    $department->update(['name' => 'Updated Department']);

    $activity = Activity::where('subject_type', Department::class)
        ->where('subject_id', $department->id)
        ->where('event', 'updated')
        ->first();

    expect($activity)->not->toBeNull();
    expect($activity->log_name)->toBe('department');
});

it('does not log unchanged ticket updates', function () {
    $ticket = Ticket::factory()->create();

    $initialActivityCount = Activity::where('subject_type', Ticket::class)
        ->where('subject_id', $ticket->id)
        ->count();

    // Update without changing anything
    $ticket->update(['title' => $ticket->title]);

    $finalActivityCount = Activity::where('subject_type', Ticket::class)
        ->where('subject_id', $ticket->id)
        ->count();

    // Should not create new activity for unchanged data
    expect($finalActivityCount)->toBe($initialActivityCount);
});

it('logs only configured attributes for tickets', function () {
    $ticket = Ticket::factory()->create();

    // Change a logged attribute
    $ticket->update(['priority' => Priority::High]);

    $activity = Activity::where('subject_type', Ticket::class)
        ->where('subject_id', $ticket->id)
        ->where('event', 'updated')
        ->latest()
        ->first();

    expect($activity)->not->toBeNull();
    expect($activity->properties)->toHaveKey('attributes');
    expect($activity->properties['attributes'])->toHaveKey('priority');
});
