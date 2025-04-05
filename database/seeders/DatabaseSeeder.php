<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //DepartmentSeeder, SLASeeder and last SiteSeeder
        $this->call([
            UserSeeder::class,            // Primero creamos usuarios
            DepartmentSeeder::class,      // Luego departamentos
            SLASeeder::class,             // Configuramos SLAs
            TagSeeder::class,             // Creamos etiquetas
            TicketSeeder::class,          // Finalmente creamos tickets de ejemplo
        ]);
    }
}
