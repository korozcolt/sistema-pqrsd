<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

/**
 * Tests E2E para Login de Filament Admin Panel
 * El sistema usa SOLO Filament para autenticación
 */

test('usuario puede ver la página de login de Filament', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/admin/login')
            ->assertSee('Sistema PQRSD')
            ->assertSee('Entre a su cuenta')
            ->assertSee('Correo electrónico')
            ->assertSee('Contraseña')
            ->assertSee('Recordarme')
            ->assertPresent('input[type="email"]')
            ->assertPresent('input[type="password"]');
    });
});

test('usuario puede iniciar sesión en Filament', function () {
    $user = User::factory()->create([
        'email' => 'usuario@test.com',
        'password' => bcrypt('password123'),
    ]);

    $this->browse(function (Browser $browser) {
        $browser->visit('/admin/login')
            ->type('input[type="email"]', 'usuario@test.com')
            ->type('input[type="password"]', 'password123')
            ->press('Entrar')
            ->pause(3000)
            ->assertPathIs('/admin');
    });
});

test('login de Filament muestra error con credenciales inválidas', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/admin/login')
            ->type('input[type="email"]', 'invalid@test.com')
            ->type('input[type="password"]', 'wrongpassword')
            ->press('Entrar')
            ->pause(2000)
            ->assertSee('credenciales no coinciden');
    });
});

test('login de Filament requiere email y password', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/admin/login')
            ->press('Entrar')
            ->pause(1000)
            ->assertPresent('input[type="email"]:required')
            ->assertPresent('input[type="password"]:required');
    });
});

test('usuario autenticado puede acceder directamente al panel de Filament', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/admin')
            ->pause(2000)
            ->assertPathIs('/admin');
    });
});
