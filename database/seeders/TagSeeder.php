<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Ruta Intermunicipal', 'description' => 'Relacionado con rutas entre municipios', 'color' => '#4a6cf7'],
            ['name' => 'Ruta Urbana', 'description' => 'Relacionado con rutas dentro del municipio', 'color' => '#6cf74a'],
            ['name' => 'Conductor', 'description' => 'Problemas relacionados con conductores', 'color' => '#f74a4a'],
            ['name' => 'Vehículo', 'description' => 'Problemas relacionados con el estado de los vehículos', 'color' => '#f7bb4a'],
            ['name' => 'Horarios', 'description' => 'Incumplimiento de horarios', 'color' => '#a64af7'],
            ['name' => 'Tiquetes', 'description' => 'Problemas con tiquetes o reservas', 'color' => '#4af7e2'],
            ['name' => 'Equipaje', 'description' => 'Problemas con equipaje', 'color' => '#f74a8a'],
            ['name' => 'Terminales', 'description' => 'Problemas en terminales de transporte', 'color' => '#8a8a8a'],
            ['name' => 'Alta Prioridad', 'description' => 'Requiere atención inmediata', 'color' => '#ff0000'],
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }

        $this->command->info('Tags creados correctamente');
    }
}
