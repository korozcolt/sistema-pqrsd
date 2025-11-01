<?php

use App\Enums\StatusTicket;
use App\Services\TicketStateMachine;

beforeEach(function () {
    $this->stateMachine = new TicketStateMachine;
});

it('allows valid transition from pending to in_progress', function () {
    expect($this->stateMachine->canTransition(StatusTicket::Pending, StatusTicket::In_Progress))->toBeTrue();
});

it('allows valid transition from pending to rejected', function () {
    expect($this->stateMachine->canTransition(StatusTicket::Pending, StatusTicket::Rejected))->toBeTrue();
});

it('denies invalid transition from pending to closed', function () {
    expect($this->stateMachine->canTransition(StatusTicket::Pending, StatusTicket::Closed))->toBeFalse();
});

it('allows valid transition from in_progress to resolved', function () {
    expect($this->stateMachine->canTransition(StatusTicket::In_Progress, StatusTicket::Resolved))->toBeTrue();
});

it('allows valid transition from resolved to closed', function () {
    expect($this->stateMachine->canTransition(StatusTicket::Resolved, StatusTicket::Closed))->toBeTrue();
});

it('allows valid transition from resolved to reopened', function () {
    expect($this->stateMachine->canTransition(StatusTicket::Resolved, StatusTicket::Reopened))->toBeTrue();
});

it('denies invalid transition from resolved to pending', function () {
    expect($this->stateMachine->canTransition(StatusTicket::Resolved, StatusTicket::Pending))->toBeFalse();
});

it('allows transition from closed to reopened', function () {
    expect($this->stateMachine->canTransition(StatusTicket::Closed, StatusTicket::Reopened))->toBeTrue();
});

it('denies transition from closed to pending', function () {
    expect($this->stateMachine->canTransition(StatusTicket::Closed, StatusTicket::Pending))->toBeFalse();
});

it('returns allowed transitions for pending status', function () {
    $allowedTransitions = $this->stateMachine->getAllowedTransitions(StatusTicket::Pending);

    expect($allowedTransitions)
        ->toBeArray()
        ->toHaveCount(2)
        ->toContain(StatusTicket::In_Progress)
        ->toContain(StatusTicket::Rejected);
});

it('returns allowed transitions for in_progress status', function () {
    $allowedTransitions = $this->stateMachine->getAllowedTransitions(StatusTicket::In_Progress);

    expect($allowedTransitions)
        ->toBeArray()
        ->toHaveCount(3)
        ->toContain(StatusTicket::Resolved)
        ->toContain(StatusTicket::Rejected)
        ->toContain(StatusTicket::Pending);
});

it('identifies closed as terminal state', function () {
    expect($this->stateMachine->isTerminalState(StatusTicket::Closed))->toBeTrue();
});

it('identifies pending as non-terminal state', function () {
    expect($this->stateMachine->isTerminalState(StatusTicket::Pending))->toBeFalse();
});

it('identifies closed as restricted state', function () {
    expect($this->stateMachine->isRestrictedState(StatusTicket::Closed))->toBeTrue();
});

it('identifies rejected as restricted state', function () {
    expect($this->stateMachine->isRestrictedState(StatusTicket::Rejected))->toBeTrue();
});

it('identifies pending as non-restricted state', function () {
    expect($this->stateMachine->isRestrictedState(StatusTicket::Pending))->toBeFalse();
});

it('generates appropriate error message for invalid transition', function () {
    $message = $this->stateMachine->getTransitionErrorMessage(
        StatusTicket::Pending,
        StatusTicket::Closed
    );

    expect($message)
        ->toBeString()
        ->toContain('No se puede cambiar')
        ->toContain('Pendiente')
        ->toContain('Cerrado');
});

it('validates all transitions are using valid states', function () {
    $errors = $this->stateMachine->validateTransitions();

    expect($errors)->toBeArray()->toBeEmpty();
});
