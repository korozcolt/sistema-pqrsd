<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Section;
use App\Enums\{StatusGlobal, SectionType};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestPageSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(
            ['slug' => '_home'],
            [
                'title' => 'Inicio',
                'layout' => 'default',
                'meta_description' => 'Empresa líder en transporte terrestre de pasajeros en la costa caribe colombiana desde 1953.',
                'meta_keywords' => 'transporte, buses, colombia, costa caribe, pasajeros',
                'status' => StatusGlobal::Active,
                'searchable' => true,
                'template' => 'default'
            ]
        );
    }
}
