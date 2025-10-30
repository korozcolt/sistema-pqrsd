<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Enums\StatusGlobal;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Oficina Principal',
                'code' => 'PRINCIPAL',
                'description' => 'Oficina Principal de la Organización',
                'address' => 'Dirección de la oficina principal',
                'phone' => '+573000000001',
                'email' => 'principal@ejemplo.com',
                'status' => StatusGlobal::Active,
            ],
            [
                'name' => 'Departamento de Servicio al Cliente',
                'code' => 'SERVICLI',
                'description' => 'Gestión de PQRSD y atención al usuario',
                'address' => 'Dirección del departamento de servicio',
                'phone' => '+573000000002',
                'email' => 'servicio@ejemplo.com',
                'status' => StatusGlobal::Active,
            ],
            [
                'name' => 'Departamento Operativo',
                'code' => 'OPERATIV',
                'description' => 'Gestión de operaciones',
                'address' => 'Dirección del departamento operativo',
                'phone' => '+573000000003',
                'email' => 'operaciones@ejemplo.com',
                'status' => StatusGlobal::Active,
            ],
            [
                'name' => 'Departamento Administrativo',
                'code' => 'ADMIN',
                'description' => 'Administración y finanzas',
                'address' => 'Dirección del departamento administrativo',
                'phone' => '+573000000004',
                'email' => 'administracion@ejemplo.com',
                'status' => StatusGlobal::Active,
            ]
        ];

        foreach ($departments as $deptData) {
            $department = Department::create($deptData);
            $this->command->info("Departamento '{$department->name}' ('{$department->code}') creado.");
        }
    }
}
