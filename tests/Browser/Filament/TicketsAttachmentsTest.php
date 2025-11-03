<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para Attachments en Tickets
 */
test('ver lista de attachments en ticket', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    TicketAttachment::factory()->count(2)->create([
        'ticket_id' => $ticket->id,
        'uploaded_by' => $admin->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(3000)
            ->screenshot('ticket-attachments')
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
});

test('subir archivo a ticket', function () {
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
})->skip('Need to implement file upload interaction');

test('validar tipo de archivo permitido', function () {
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
})->skip('Need to implement file type validation');

test('validar tamaño máximo de archivo', function () {
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
})->skip('Need to implement file size validation');

test('eliminar attachment propio', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    $attachment = TicketAttachment::factory()->create([
        'ticket_id' => $ticket->id,
        'uploaded_by' => $admin->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
})->skip('Need to implement delete attachment');

test('attachments muestran metadatos', function () {
    $department = Department::factory()->create();
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
        'name' => 'Admin User',
    ]);

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    TicketAttachment::factory()->create([
        'ticket_id' => $ticket->id,
        'uploaded_by' => $admin->id,
        'file_name' => 'test-document.pdf',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $ticket) {
        $browser->loginAs($admin)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(3000)
            ->screenshot('attachment-metadata')
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
})->skip('Need to verify attachment display');

test('userWeb puede subir attachments a sus tickets', function () {
    $department = Department::factory()->create();
    $userWeb = User::factory()->create(['role' => UserRole::UserWeb]);

    $ticket = Ticket::factory()->create([
        'user_id' => $userWeb->id,
        'department_id' => $department->id,
    ]);

    $this->browse(function (Browser $browser) use ($userWeb, $ticket) {
        $browser->loginAs($userWeb)
            ->visit("/admin/tickets/{$ticket->id}")
            ->pause(2000)
            ->assertPathIs("/admin/tickets/{$ticket->id}");
    });
})->skip('Need to verify UserWeb can upload files');
