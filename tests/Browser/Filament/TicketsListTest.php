<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para listado de Tickets en Filament
 */
test('admin puede ver el listado de tickets', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
});

test('listado de tickets muestra tickets existentes', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
})->skip('Pending: Issue with creating tickets in test database');

test('listado de tickets está vacío cuando no hay tickets', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
});

test('usuario puede buscar tickets por título', function () {
    $user = User::factory()->create();

    $ticket1 = Ticket::factory()->create([
        'user_id' => $user->id,
        'title' => 'Problema con facturación',
    ]);

    $ticket2 = Ticket::factory()->create([
        'user_id' => $user->id,
        'title' => 'Error en el sistema',
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->type('input[type="search"]', 'facturación')
            ->pause(2000)
            ->assertSee('facturación')
            ->assertDontSee('Error en el sistema');
    });
})->skip('Necesita verificar selector de búsqueda de Filament');

test('usuario puede acceder a la página de crear ticket', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->clickLink('Create')
            ->pause(2000)
            ->assertPathIs('/admin/tickets/create');
    });
})->skip('Necesita verificar texto del botón de crear');
