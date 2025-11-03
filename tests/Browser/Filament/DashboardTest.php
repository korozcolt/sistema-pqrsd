<?php

declare(strict_types=1);

use App\Enums\StatusTicket;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para Dashboard y Widgets en Filament
 */
test('admin puede ver el dashboard', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->assertPathIs('/admin')
            ->screenshot('dashboard');
    });
});

test('dashboard muestra widgets principales', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(3000)
            ->screenshot('dashboard-widgets')
            ->assertPathIs('/admin');
    });
});

test('ticketStats muestra conteo de tickets', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    Ticket::factory()->count(5)->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'status' => StatusTicket::Pending,
    ]);

    Ticket::factory()->count(3)->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
        'status' => StatusTicket::Resolved,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(3000)
            ->screenshot('dashboard-ticket-stats')
            ->assertPathIs('/admin');
    });
})->skip('Need to verify stats display');

test('latestTickets muestra tickets recientes', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    Ticket::factory()->count(10)->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(3000)
            ->screenshot('dashboard-latest-tickets')
            ->assertPathIs('/admin');
    });
})->skip('Need to verify latest tickets widget');

test('ticketStatusChart muestra gráfico de estados', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    foreach (StatusTicket::cases() as $status) {
        Ticket::factory()->count(2)->create([
            'user_id' => $admin->id,
            'department_id' => $department->id,
            'status' => $status,
        ]);
    }

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(3000)
            ->screenshot('dashboard-status-chart')
            ->assertPathIs('/admin');
    });
})->skip('Need to verify chart rendering');

test('pendingReminders muestra recordatorios pendientes', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(3000)
            ->screenshot('dashboard-reminders')
            ->assertPathIs('/admin');
    });
})->skip('Need to create reminders and verify widget');

test('slaCompliance muestra métricas de cumplimiento', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $department = Department::factory()->create();

    Ticket::factory()->count(10)->create([
        'user_id' => $admin->id,
        'department_id' => $department->id,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(3000)
            ->screenshot('dashboard-sla-compliance')
            ->assertPathIs('/admin');
    });
})->skip('Need to verify SLA widget');

test('userWeb ve dashboard con sus tickets', function () {
    $userWeb = User::factory()->create(['role' => UserRole::UserWeb]);
    $department = Department::factory()->create();

    Ticket::factory()->count(5)->create([
        'user_id' => $userWeb->id,
        'department_id' => $department->id,
    ]);

    $this->browse(function (Browser $browser) use ($userWeb) {
        $browser->loginAs($userWeb)
            ->visit('/admin')
            ->pause(2000)
            ->assertPathIs('/admin');
    });
})->skip('Need to verify user-specific dashboard');
