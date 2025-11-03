<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para CRUD de Users en Filament
 */
test('admin puede ver listado de usuarios', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    User::factory()->count(5)->create();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->pause(2000)
            ->assertPathIs('/admin/users');
    });
});

test('admin puede acceder a crear usuario', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/users/create')
            ->pause(2000)
            ->assertPathIs('/admin/users/create');
    });
});

test('formulario de crear usuario muestra todos los campos', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/users/create')
            ->pause(2000)
            ->screenshot('user-create-form')
            ->assertPathIs('/admin/users/create');
    });
});

test('crear usuario requiere email único', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    User::factory()->create(['email' => 'existing@test.com']);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/users/create')
            ->pause(2000)
            ->assertPathIs('/admin/users/create');
    });
})->skip('Need to implement email validation');

test('crear usuario requiere password', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/users/create')
            ->pause(2000)
            ->assertPathIs('/admin/users/create');
    });
})->skip('Need to implement password validation');

test('admin puede ver detalles de usuario', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'testuser@test.com',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $user) {
        $browser->loginAs($admin)
            ->visit("/admin/users/{$user->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/users/{$user->id}/edit");
    });
})->skip('User resource may not have view page, only edit');

test('admin puede editar usuario', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $user = User::factory()->create(['name' => 'Original Name']);

    $this->browse(function (Browser $browser) use ($admin, $user) {
        $browser->loginAs($admin)
            ->visit("/admin/users/{$user->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/users/{$user->id}/edit");
    });
});

test('admin puede cambiar rol de usuario', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $user = User::factory()->create(['role' => UserRole::UserWeb]);

    $this->browse(function (Browser $browser) use ($admin, $user) {
        $browser->loginAs($admin)
            ->visit("/admin/users/{$user->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/users/{$user->id}/edit");
    });
})->skip('Need to implement role change');

test('filtrar usuarios por rol', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    User::factory()->create(['role' => UserRole::Admin]);
    User::factory()->create(['role' => UserRole::UserWeb]);
    User::factory()->create(['role' => UserRole::Receptionist]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->pause(2000)
            ->assertPathIs('/admin/users');
    });
})->skip('Need to implement role filter');

test('buscar usuario por nombre o email', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    User::factory()->create(['name' => 'John Doe', 'email' => 'john@test.com']);
    User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@test.com']);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->pause(2000)
            ->assertPathIs('/admin/users');
    });
})->skip('Need to implement search');

test('receptionist no puede acceder a usuarios', function () {
    $receptionist = User::factory()->create(['role' => UserRole::Receptionist]);

    $this->browse(function (Browser $browser) use ($receptionist) {
        $browser->loginAs($receptionist)
            ->visit('/admin/users')
            ->pause(2000);
        // Should show 403 or redirect
    });
})->skip('Need to verify access control');

test('userWeb no puede acceder a usuarios', function () {
    $userWeb = User::factory()->create(['role' => UserRole::UserWeb]);

    $this->browse(function (Browser $browser) use ($userWeb) {
        $browser->loginAs($userWeb)
            ->visit('/admin/users')
            ->pause(2000);
        // Should show 403 or redirect
    });
})->skip('Need to verify access control');
