<?php

declare(strict_types=1);

use App\Enums\ReminderType;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Reminder;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para Reminders en el sistema
 */
test('admin puede ver listado de reminders', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Reminder::factory()->count(3)->create([
        'sent_to' => $admin->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/reminders')
            ->pause(2000)
            ->assertPathIs('/admin/reminders');
    });
});

test('ver reminders no leídos', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Reminder::factory()->count(2)->create([
        'sent_to' => $admin->id,
        'is_read' => false,
    ]);

    Reminder::factory()->create([
        'sent_to' => $admin->id,
        'is_read' => true,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/reminders')
            ->pause(2000)
            ->assertPathIs('/admin/reminders');
    });
})->skip('Need to implement unread filter');

test('marcar reminder como leído', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $reminder = Reminder::factory()->create([
        'sent_to' => $admin->id,
        'is_read' => false,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/reminders')
            ->pause(2000)
            ->assertPathIs('/admin/reminders');
    });
})->skip('Need to implement mark as read action');

test('filtrar reminders por tipo', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Reminder::factory()->create([
        'sent_to' => $admin->id,
        'reminder_type' => ReminderType::ResponseDue,
    ]);

    Reminder::factory()->create([
        'sent_to' => $admin->id,
        'reminder_type' => ReminderType::ResolutionDue,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/reminders')
            ->pause(2000)
            ->assertPathIs('/admin/reminders');
    });
})->skip('Need to implement type filter');

test('reminder muestra información del ticket', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    $ticket = Ticket::factory()->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'title' => 'Urgent Ticket',
    ]);

    Reminder::factory()->create([
        'ticket_id' => $ticket->id,
        'sent_to' => $admin->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/reminders')
            ->pause(2000)
            ->assertSee('Urgent Ticket');
    });
})->skip('Need to verify ticket info display');

test('usuario solo ve sus propios reminders', function () {
    $user1 = User::factory()->create(['role' => UserRole::Admin]);
    $user2 = User::factory()->create(['role' => UserRole::Admin]);

    Reminder::factory()->create([
        'sent_to' => $user1->id,
        'message' => 'Reminder for User 1',
    ]);

    Reminder::factory()->create([
        'sent_to' => $user2->id,
        'message' => 'Reminder for User 2',
    ]);

    $this->browse(function (Browser $browser) use ($user1) {
        $browser->loginAs($user1)
            ->visit('/admin/reminders')
            ->pause(2000)
            ->assertDontSee('Reminder for User 2');
    });
})->skip('Need to verify reminder isolation');
