<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // SuperAdmin
        User::create([
            'name' => 'Administrador Principal',
            'email' => 'admin@cooptorcoroma.com',
            'password' => Hash::make('admin123'),
            'role' => UserRole::SuperAdmin->value, // 'superadmin'
            'email_verified_at' => now(),
        ]);

        // Admin
        User::create([
            'name' => 'Gerente PQRS',
            'email' => 'gerente@cooptorcoroma.com',
            'password' => Hash::make('gerente123'),
            'role' => UserRole::Admin->value, // 'admin'
            'email_verified_at' => now(),
        ]);

        // Recepcionista
        User::create([
            'name' => 'Recepcionista PQRS',
            'email' => 'recepcion@cooptorcoroma.com',
            'password' => Hash::make('recepcion123'),
            'role' => UserRole::Receptionist->value, // 'receptionist'
            'email_verified_at' => now(),
        ]);

        // Usuarios Web (clientes simulados)
        $userNames = [
            'María González', 'Juan Pérez', 'Ana Rodríguez',
            'Carlos Martínez', 'Patricia López', 'Luis Ramírez'
        ];

        foreach ($userNames as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@gmail.com';

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('usuario123'),
                'role' => UserRole::UserWeb->value, // 'user_web'
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Usuarios creados correctamente');
    }
}
