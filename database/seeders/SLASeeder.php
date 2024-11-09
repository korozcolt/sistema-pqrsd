<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SLA;
use App\Enums\TicketType;
use App\Enums\Priority;

class SLASeeder extends Seeder
{
    public function run(): void
    {
        // Peticiones (Derecho de Petición)
        // Estándar: 15 días hábiles
        $this->createSLAsForType(TicketType::Petition, [
            'low' => ['response' => 24, 'resolution' => 360], // 15 días
            'medium' => ['response' => 16, 'resolution' => 360],
            'high' => ['response' => 8, 'resolution' => 360],
            'urgent' => ['response' => 4, 'resolution' => 360],
        ]);

        // Quejas (Sobre el servicio)
        // Estándar: 15 días hábiles
        $this->createSLAsForType(TicketType::Complaint, [
            'low' => ['response' => 24, 'resolution' => 360],
            'medium' => ['response' => 16, 'resolution' => 360],
            'high' => ['response' => 8, 'resolution' => 360],
            'urgent' => ['response' => 4, 'resolution' => 360],
        ]);

        // Reclamos (Sobre el servicio prestado)
        // Estándar: 15 días hábiles
        $this->createSLAsForType(TicketType::Claim, [
            'low' => ['response' => 24, 'resolution' => 360],
            'medium' => ['response' => 16, 'resolution' => 360],
            'high' => ['response' => 8, 'resolution' => 360],
            'urgent' => ['response' => 4, 'resolution' => 360],
        ]);

        // Sugerencias (No tienen tiempo legal, pero establecemos un estándar)
        // Estándar: 30 días hábiles
        $this->createSLAsForType(TicketType::Suggestion, [
            'low' => ['response' => 48, 'resolution' => 720], // 30 días
            'medium' => ['response' => 36, 'resolution' => 720],
            'high' => ['response' => 24, 'resolution' => 720],
            'urgent' => ['response' => 12, 'resolution' => 720],
        ]);
    }

    private function createSLAsForType(TicketType $type, array $priorities): void
    {
        foreach ($priorities as $priorityValue => $times) {
            $priority = Priority::from($priorityValue);
            SLA::create([
                'ticket_type' => $type,
                'priority' => $priority->value,
                'response_time' => $times['response'],
                'resolution_time' => $times['resolution'],
                'is_active' => true,
            ]);
        }
    }
}
