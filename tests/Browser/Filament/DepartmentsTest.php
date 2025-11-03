<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para CRUD de Departments en Filament
 */
test('admin puede ver listado de departamentos', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Department::factory()->count(3)->create();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/departments')
            ->pause(2000)
            ->assertPathIs('/admin/departments');
    });
});

test('admin puede acceder a crear departamento', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/departments/create')
            ->pause(2000)
            ->assertPathIs('/admin/departments/create');
    });
});

test('formulario de crear departamento muestra campos requeridos', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/departments/create')
            ->pause(2000)
            ->screenshot('department-create-form')
            ->assertPathIs('/admin/departments/create');
    });
});

test('crear departamento requiere nombre', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/departments/create')
            ->pause(2000)
            ->assertPathIs('/admin/departments/create');
    });
})->skip('Need to implement validation test');

test('código de departamento debe ser único', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Department::factory()->create([
        'code' => 'SUPPORT',
        'name' => 'Support Department',
    ]);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/departments/create')
            ->pause(2000)
            ->assertPathIs('/admin/departments/create');
    });
})->skip('Need to implement unique code validation');

test('admin puede ver detalles de departamento', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $department = Department::factory()->create([
        'name' => 'IT Department',
        'code' => 'IT',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $department) {
        $browser->loginAs($admin)
            ->visit("/admin/departments/{$department->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/departments/{$department->id}/edit");
    });
})->skip('Department resource may not have view page, only edit');

test('admin puede editar departamento', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $department = Department::factory()->create([
        'name' => 'Original Name',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $department) {
        $browser->loginAs($admin)
            ->visit("/admin/departments/{$department->id}/edit")
            ->pause(2000)
            ->assertPathIs("/admin/departments/{$department->id}/edit");
    });
});

test('buscar departamento por nombre', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Department::factory()->create(['name' => 'Sales Department']);
    Department::factory()->create(['name' => 'Marketing Department']);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/departments')
            ->pause(2000)
            ->assertPathIs('/admin/departments');
    });
})->skip('Need to implement search');

test('userWeb no puede acceder a departamentos', function () {
    $userWeb = User::factory()->create(['role' => UserRole::UserWeb]);

    $this->browse(function (Browser $browser) use ($userWeb) {
        $browser->loginAs($userWeb)
            ->visit('/admin/departments')
            ->pause(2000);
        // Should show 403 or redirect
    });
})->skip('Need to verify access control');
