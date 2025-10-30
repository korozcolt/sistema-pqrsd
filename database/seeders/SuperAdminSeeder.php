<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si el usuario ya existe
        $existingUser = User::where('email', 'ing.korozco@gmail.com')->first();

        if ($existingUser) {
            $this->command->warn('El usuario ing.korozco@gmail.com ya existe.');
            return;
        }

        // Crear SuperAdmin
        User::create([
            'name' => 'Kronnos Admin',
            'email' => 'ing.korozco@gmail.com',
            'password' => Hash::make('Admin123'),
            'role' => UserRole::SuperAdmin->value,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Usuario SuperAdmin creado correctamente:');
        $this->command->info('   Email: ing.korozco@gmail.com');
        $this->command->info('   Contraseña: Admin123');
        $this->command->info('   Rol: Super Administrador');
    }
}
