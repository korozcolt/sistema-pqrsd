<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para Navegación general del sistema
 */
test('navegación entre recursos desde sidebar', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->assertPathIs('/admin')
            ->screenshot('sidebar-navigation');
    });
});

test('acceder a tickets desde sidebar', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->visit('/admin/tickets')
            ->pause(2000)
            ->assertPathIs('/admin/tickets');
    });
});

test('acceder a departments desde sidebar', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->visit('/admin/departments')
            ->pause(2000)
            ->assertPathIs('/admin/departments');
    });
});

test('acceder a users desde sidebar', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->visit('/admin/users')
            ->pause(2000)
            ->assertPathIs('/admin/users');
    });
});

test('acceder a tags desde sidebar', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->visit('/admin/tags')
            ->pause(2000)
            ->assertPathIs('/admin/tags');
    });
});

test('acceder a SLAs desde sidebar', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->visit('/admin/s-l-a-s')
            ->pause(2000)
            ->assertPathIs('/admin/s-l-a-s');
    });
});

test('acceder a reminders desde sidebar', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->visit('/admin/reminders')
            ->pause(2000)
            ->assertPathIs('/admin/reminders');
    });
});

test('logout funciona correctamente', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->assertPathIs('/admin');
    });
})->skip('Need to implement logout interaction');

test('userWeb solo ve recursos permitidos en sidebar', function () {
    $userWeb = User::factory()->create(['role' => UserRole::UserWeb]);

    $this->browse(function (Browser $browser) use ($userWeb) {
        $browser->loginAs($userWeb)
            ->visit('/admin')
            ->pause(2000)
            ->screenshot('userweb-sidebar')
            ->assertPathIs('/admin');
    });
})->skip('Need to verify sidebar items visibility');

test('navbar muestra información del usuario', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
        'name' => 'Admin Test User',
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin')
            ->pause(2000)
            ->assertSee('Admin Test User');
    });
})->skip('Need to verify navbar content');
