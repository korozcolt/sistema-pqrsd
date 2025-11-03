<?php

declare(strict_types=1);

use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para filtros y búsqueda de Tickets en Filament
 */
test('filtrar tickets por status', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'title' => 'Pending Ticket',
        'status' => StatusTicket::Pending,
    ]);

    Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'title' => 'Resolved Ticket',
        'status' => StatusTicket::Resolved,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
})->skip('Need to implement filter interaction');

test('filtrar tickets por prioridad', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'priority' => Priority::High,
    ]);

    Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'priority' => Priority::Low,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
})->skip('Need to implement priority filter');

test('filtrar tickets por tipo', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'type' => TicketType::Request,
    ]);

    Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'type' => TicketType::Complaint,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
})->skip('Need to implement type filter');

test('filtrar tickets por departamento', function () {
    $dept1 = Department::factory()->create(['name' => 'Department A']);
    $dept2 = Department::factory()->create(['name' => 'Department B']);
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $dept1->id,
    ]);

    Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $dept2->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
})->skip('Need to implement department filter');

test('buscar tickets por número', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'ticket_number' => 'TK-12345',
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
})->skip('Need to implement search functionality');

test('limpiar filtros restaura listado completo', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Ticket::factory()->count(5)->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
})->skip('Need to implement clear filters');
