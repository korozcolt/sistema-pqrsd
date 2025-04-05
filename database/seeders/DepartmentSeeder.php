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
                'description' => 'Oficina Principal - Terminal de Transporte',
                'address' => 'Calle 38 #25 - 92 Sincelejo, Sucre',
                'phone' => '+573106543797',
                'email' => 'cooptorcoroma@hotmail.com',
                'status' => StatusGlobal::Active,
            ],
            [
                'name' => 'Departamento de Servicio al Cliente',
                'code' => 'SERVICLI',
                'description' => 'Gestión de PQRs y atención al usuario',
                'address' => 'Calle 38 #25 - 92 Sincelejo, Sucre',
                'phone' => '+573106543798',
                'email' => 'servicio@cooptorcoroma.com',
                'status' => StatusGlobal::Active,
            ],
            [
                'name' => 'Departamento Operativo',
                'code' => 'OPERATIV',
                'description' => 'Gestión de flotas y operaciones',
                'address' => 'Terminal de Transportes, Módulo 5, Sincelejo',
                'phone' => '+573106543799',
                'email' => 'operaciones@cooptorcoroma.com',
                'status' => StatusGlobal::Active,
            ],
            [
                'name' => 'Departamento Administrativo',
                'code' => 'ADMIN',
                'description' => 'Administración y finanzas',
                'address' => 'Calle 38 #25 - 92 Sincelejo, Sucre',
                'phone' => '+573106543800',
                'email' => 'administracion@cooptorcoroma.com',
                'status' => StatusGlobal::Active,
            ]
        ];

        foreach ($departments as $deptData) {
            $department = Department::create($deptData);
            $this->command->info("Departamento '{$department->name}' ('{$department->code}') creado.");
        }
    }
}
