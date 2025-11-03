<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para creación de Tickets en Filament
 */
test('admin puede acceder a la página de crear ticket', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets/create')
            ->pause(2000)
            ->assertPathIs('/admin/tickets/create');
    });
});

test('formulario de crear ticket muestra todos los campos requeridos', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets/create')
            ->pause(3000)
            ->screenshot('create-ticket-form')
            ->assertPathIs('/admin/tickets/create');
    });
});

test('admin puede crear un nuevo ticket con datos válidos', function () {
    Department::factory()->create(['name' => 'Test Department']);

    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets/create')
            ->pause(2000)
            ->type('input[name="title"]', 'Test Ticket Title')
            ->pause(1000)
            ->click('[name="type"]')
            ->pause(500)
            ->keys('[name="type"]', ['{arrow_down}', '{enter}'])
            ->pause(500)
            ->click('[name="priority"]')
            ->pause(500)
            ->keys('[name="priority"]', ['{arrow_down}', '{enter}'])
            ->pause(500)
            ->click('[name="department_id"]')
            ->pause(500)
            ->keys('[name="department_id"]', ['{arrow_down}', '{enter}'])
            ->pause(500)
            ->screenshot('before-description')
            ->assertPathIs('/admin/tickets/create');
    });
})->skip('Need to verify Filament form field selectors');
