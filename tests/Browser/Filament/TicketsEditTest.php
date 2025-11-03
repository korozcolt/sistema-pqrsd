<?php

declare(strict_types=1);

use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para editar Tickets en Filament
 */
test('admin puede acceder a la página de editar ticket', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'title' => 'Ticket to Edit',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}/edit");
    });
});

test('formulario de editar muestra datos existentes del ticket', function () {
    $department = Department::factory()->create(['name' => 'IT Support']);
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'title' => 'Original Title',
        'status' => StatusTicket::Pending,
        'priority' => Priority::High,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}/edit")
            ->pause(3000)
            ->screenshot('edit-ticket-form')
            ->assertInputValue('input[name="title"]', 'Original Title');
    });
})->skip('Need to verify exact input selectors');

test('userWeb no puede editar tickets', function () {
    $department = Department::factory()->create();
    $userWeb = User::factory()->create(['role' => UserRole::UserWeb]);

    $ticket = Ticket::factory()->create([
        'user_id' => $userWeb->id,
        'department_id' => $department->id,
        'title' => 'UserWeb Ticket',
    ]);

    $this->browse(function (Browser $browser) use ($userWeb, $ticket) {
        $browser->loginAs($userWeb)
            ->visit("/admin/tickets/{$ticket->id}/edit")
            ->pause(2000);
        // Debería redirigir o mostrar 403
    });
})->skip('Need to verify access control behavior');

test('admin puede cambiar prioridad de ticket', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'priority' => Priority::Low,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}/edit");
    });
})->skip('Need to implement priority change and submit');

test('admin puede cambiar status de ticket', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'status' => StatusTicket::Pending,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}/edit");
    });
})->skip('Need to implement status change and submit');
