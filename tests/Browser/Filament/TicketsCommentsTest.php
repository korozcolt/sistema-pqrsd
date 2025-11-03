<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para Comments en Tickets
 */
test('ver lista de comentarios en ticket', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    TicketComment::factory()->count(3)->create([
        'ticket_id' => $ticket->id,
        'user_id' => $admin->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(3000)
            ->screenshot('ticket-comments')
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
});

test('agregar comentario a ticket', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
})->skip('Need to implement add comment interaction');

test('comentario requiere contenido', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
})->skip('Need to implement validation test');

test('editar comentario propio', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    $comment = TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $admin->id,
        'comment' => 'Original comment',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
})->skip('Need to implement edit comment');

test('eliminar comentario propio', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    $comment = TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $admin->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
})->skip('Need to implement delete comment');

test('comentarios muestran usuario y fecha', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
        'name' => 'Admin User',
    ]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    TicketComment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $admin->id,
        'comment' => 'Test comment',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(3000)
            ->assertSee('Admin User');
    });
})->skip('Need to verify comment display format');
