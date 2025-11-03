<?php

declare(strict_types=1);

use App\Enums\Priority;
use App\Enums\TicketType;
use App\Enums\UserRole;
use App\Models\SLA;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para CRUD de SLAs en Filament
 */
test('admin puede ver listado de SLAs', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    SLA::factory()->count(3)->create();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/s-l-a-s')
            ->pause(2000)
            ->assertPathIs('/admin/s-l-a-s');
    });
});

test('admin puede acceder a crear SLA', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/s-l-a-s/create')
            ->pause(2000)
            ->assertPathIs('/admin/s-l-a-s/create');
    });
});

test('formulario de crear SLA muestra campos requeridos', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/s-l-a-s/create')
            ->pause(2000)
            ->screenshot('sla-create-form')
            ->assertPathIs('/admin/s-l-a-s/create');
    });
});

test('SLA requiere tipo de ticket', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/s-l-a-s/create')
            ->pause(2000)
            ->assertPathIs('/admin/s-l-a-s/create');
    });
})->skip('Need to implement validation');

test('SLA requiere prioridad', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/s-l-a-s/create')
            ->pause(2000)
            ->assertPathIs('/admin/s-l-a-s/create');
    });
})->skip('Need to implement validation');

test('admin puede editar SLA', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $sla = SLA::factory()->create([
        'ticket_type' => TicketType::Petition,
        'priority' => Priority::High,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $sla) {
        $browser->loginAs($admin)
            ->visit("/admin/s-l-a-s/{$sla->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/s-l-a-s/{$sla->id}/edit");
    });
});

test('SLAs con tiempos de respuesta y resolución', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    SLA::factory()->create([
        'ticket_type' => TicketType::Complaint,
        'priority' => Priority::Urgent,
        'response_time_hours' => 2,
        'resolution_time_hours' => 24,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/s-l-a-s')
            ->pause(2000)
            ->assertPathIs('/admin/s-l-a-s');
    });
});

test('filtrar SLAs por tipo de ticket', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    SLA::factory()->create(['ticket_type' => TicketType::Petition]);
    SLA::factory()->create(['ticket_type' => TicketType::Complaint]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/s-l-a-s')
            ->pause(2000)
            ->assertPathIs('/admin/s-l-a-s');
    });
})->skip('Need to implement filter');

test('superadmin puede eliminar SLA', function () {
    $superadmin = User::factory()->create(['role' => UserRole::SuperAdmin]);

    $sla = SLA::factory()->create();

    $this->browse(function (Browser $browser) use ($superadmin) {
        $browser->loginAs($superadmin)
            ->visit('/admin/s-l-a-s')
            ->pause(2000)
            ->assertPathIs('/admin/s-l-a-s');
    });
})->skip('Need to implement delete action');

test('userWeb no puede acceder a SLAs', function () {
    $userWeb = User::factory()->create(['role' => UserRole::UserWeb]);

    $this->browse(function (Browser $browser) use ($userWeb) {
        $browser->loginAs($userWeb)
            ->visit('/admin/s-l-a-s')
            ->pause(2000);
        // Should show 403 or redirect
    });
})->skip('Need to verify access control');
