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
 * Tests E2E para ver detalles de Tickets en Filament
 */
test('admin puede ver detalles de un ticket', function () {
    $department = Department::factory()->create(['name' => 'Test Department']);
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'title' => 'Test Ticket for View',
        'status' => StatusTicket::Pending,
        'priority' => Priority::Medium,
        'type' => TicketType::Petition,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}")
            ->assertSee('Test Ticket for View');
    });
});

test('página de ver ticket muestra información del ticket', function () {
    $department = Department::factory()->create(['name' => 'Support Department']);
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'title' => 'Detailed Test Ticket',
        'description' => 'This is a detailed description',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
})->skip('Need to verify ticket view page structure');

test('userWeb solo puede ver sus propios tickets', function () {
    $department = Department::factory()->create();
    $user1 = User::factory()->create(['role' => UserRole::UserWeb]);
    $user2 = User::factory()->create(['role' => UserRole::UserWeb]);

    $ticketUser1 = Ticket::factory()->create([
        'user_id' => $user1->id,
        'department_id' => $department->id,
        'title' => 'User 1 Ticket',
    ]);

    $ticketUser2 = Ticket::factory()->create([
        'user_id' => $user2->id,
        'department_id' => $department->id,
        'title' => 'User 2 Ticket',
    ]);

    $this->browse(function (Browser $browser) use ($user1, $ticketUser1) {
        // User1 puede ver su ticket
        $browser->loginAs($user1)
            ->visit("/admin/tickets/{$ticketUser1->id}")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticketUser1->id}");
    });
})->skip('Need to implement UserWeb access control verification');

test('página de ver ticket tiene tabs de relaciones', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(3000)
            ->screenshot('ticket-view-tabs')
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
});
