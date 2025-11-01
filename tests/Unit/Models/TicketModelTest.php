<?php

use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Models\Ticket;

it('generates unique ticket number on creation', function () {
    $ticket = new Ticket;
    $ticket->title = 'Test';
    $ticket->description = 'Test';
    $ticket->user_id = 1;
    $ticket->department_id = 1;
    $ticket->type = TicketType::Petition;
    $ticket->status = StatusTicket::Pending;
    $ticket->priority = Priority::Medium;

    expect($ticket->ticket_number)->toBeNull();

    // The ticket number should be generated in the creating event
    // We can't test this without touching the database
})->skip('Requires database interaction');

it('has correct fillable attributes', function () {
    $fillable = (new Ticket)->getFillable();

    expect($fillable)->toContain('ticket_number')
        ->toContain('title')
        ->toContain('description')
        ->toContain('user_id')
        ->toContain('department_id')
        ->toContain('type')
        ->toContain('status')
        ->toContain('priority')
        ->toContain('response_due_date')
        ->toContain('resolution_due_date')
        ->toContain('first_response_at')
        ->toContain('resolution_at');
});

it('casts attributes correctly', function () {
    $casts = (new Ticket)->getCasts();

    expect($casts)->toHaveKey('type')
        ->toHaveKey('status')
        ->toHaveKey('priority')
        ->toHaveKey('response_due_date')
        ->toHaveKey('resolution_due_date')
        ->toHaveKey('first_response_at')
        ->toHaveKey('resolution_at');
});

it('uses soft deletes', function () {
    $ticket = new Ticket;

    expect($ticket)->toHaveMethod('trashed')
        ->toHaveMethod('restore')
        ->toHaveMethod('forceDelete');
});

it('has activity log trait', function () {
    $ticket = new Ticket;

    expect($ticket)->toHaveMethod('getActivitylogOptions');
});

it('has state machine helper methods', function () {
    $ticket = new Ticket;

    expect($ticket)->toHaveMethod('canTransitionTo')
        ->toHaveMethod('transitionTo')
        ->toHaveMethod('getAllowedNextStates')
        ->toHaveMethod('isInTerminalState');
});

it('generates sequential ticket numbers', function () {
    $number1 = Ticket::generateUniqueNumber();
    $number2 = Ticket::generateUniqueNumber();

    expect($number1)->toStartWith('TK-')
        ->and($number2)->toStartWith('TK-')
        ->and($number1)->not->toBe($number2);
});

it('generates ticket number with correct format', function () {
    $number = Ticket::generateUniqueNumber();

    expect($number)->toMatch('/^TK-\d{5}|TK-\d{10}|TK-[a-f0-9]{8}$/');
});
