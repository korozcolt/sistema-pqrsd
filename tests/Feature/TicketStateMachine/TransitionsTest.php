<?php

use App\Enums\StatusTicket;
use App\Models\Ticket;
use App\Services\TicketStateMachine;

it('can apply valid transition to a ticket', function () {
    $ticket = Ticket::factory()->create([
        'status' => StatusTicket::Pending,
    ]);

    $stateMachine = new TicketStateMachine;
    $result = $stateMachine->transition($ticket, StatusTicket::In_Progress);

    expect($result)->toBeTrue();
    expect($ticket->fresh()->status)->toBe(StatusTicket::In_Progress);
});

it('prevents invalid transition on a ticket', function () {
    $ticket = Ticket::factory()->create([
        'status' => StatusTicket::Pending,
    ]);

    $stateMachine = new TicketStateMachine;
    $result = $stateMachine->transition($ticket, StatusTicket::Closed);

    expect($result)->toBeFalse();
    expect($ticket->fresh()->status)->toBe(StatusTicket::Pending);
});

it('can use ticket helper methods for transitions', function () {
    $ticket = Ticket::factory()->create([
        'status' => StatusTicket::Pending,
    ]);

    expect($ticket->canTransitionTo(StatusTicket::In_Progress))->toBeTrue();
    expect($ticket->canTransitionTo(StatusTicket::Closed))->toBeFalse();

    $ticket->transitionTo(StatusTicket::In_Progress);

    expect($ticket->fresh()->status)->toBe(StatusTicket::In_Progress);
});

it('returns allowed next states from ticket', function () {
    $ticket = Ticket::factory()->create([
        'status' => StatusTicket::Pending,
    ]);

    $allowedStates = $ticket->getAllowedNextStates();

    expect($allowedStates)
        ->toBeArray()
        ->toHaveCount(2);
});

it('identifies if ticket is in terminal state', function () {
    $closedTicket = Ticket::factory()->create([
        'status' => StatusTicket::Closed,
    ]);

    $pendingTicket = Ticket::factory()->create([
        'status' => StatusTicket::Pending,
    ]);

    expect($closedTicket->isInTerminalState())->toBeTrue();
    expect($pendingTicket->isInTerminalState())->toBeFalse();
});
