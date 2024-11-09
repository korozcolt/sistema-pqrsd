<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Enums\StatusGlobal;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::create([
            'name' => 'Oficina Principal',
            'code' => 'PRINCIPAL',
            'description' => 'Oficina Principal - terminal de transporte',
            'address' => 'Calle 38 #25 - 92 Sincelejo, Sucre',
            'phone' => '+573106543797',
            'email' => 'cooptorcoroma@hotmail.com',
            'status' => StatusGlobal::Active,
        ]);

        $this->command->info("Department '{$department->name}' ('{$department->code}') has been created.");
    }
}
