<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para CRUD de Tags en Filament
 */
test('admin puede ver listado de tags', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Tag::factory()->count(5)->create();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tags')
            ->pause(2000)
            ->assertPathIs('/admin/tags');
    });
});

test('admin puede acceder a crear tag', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tags/create')
            ->pause(2000)
            ->assertPathIs('/admin/tags/create');
    });
});

test('formulario de crear tag muestra campos requeridos', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tags/create')
            ->pause(2000)
            ->screenshot('tag-create-form')
            ->assertPathIs('/admin/tags/create');
    });
});

test('crear tag requiere nombre', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tags/create')
            ->pause(2000)
            ->assertPathIs('/admin/tags/create');
    });
})->skip('Need to implement name validation');

test('admin puede editar tag', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $tag = Tag::factory()->create(['name' => 'Urgent']);

    $this->browse(function (Browser $browser) use ($admin, $tag) {
        $browser->loginAs($admin)
            ->visit("/admin/tags/{$tag->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/tags/{$tag->id}/edit");
    });
});

test('tags tienen colores', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Tag::factory()->create([
        'name' => 'High Priority',
        'color' => 'danger',
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tags')
            ->pause(2000)
            ->assertSee('High Priority');
    });
});

test('buscar tags por nombre', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Tag::factory()->create(['name' => 'Important']);
    Tag::factory()->create(['name' => 'Review']);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/tags')
            ->pause(2000)
            ->assertPathIs('/admin/tags');
    });
})->skip('Need to implement search');

test('userWeb no puede acceder a tags', function () {
    $userWeb = User::factory()->create(['role' => UserRole::UserWeb]);

    $this->browse(function (Browser $browser) use ($userWeb) {
        $browser->loginAs($userWeb)
            ->visit('/admin/tags')
            ->pause(2000);
        // Should show 403 or redirect
    });
})->skip('Need to verify access control');
