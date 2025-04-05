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
        // Peticiones (Derecho de Petición) - según normativa colombiana
        // Estándar: 15 días hábiles (aproximadamente 360 horas)
        $this->createSLAsForType(TicketType::Petition->value, [
            Priority::Low->value => ['response' => 24, 'resolution' => 360], // 15 días
            Priority::Medium->value => ['response' => 16, 'resolution' => 360],
            Priority::High->value => ['response' => 8, 'resolution' => 360],
            Priority::Urgent->value => ['response' => 4, 'resolution' => 240], // 10 días para urgentes
        ]);

        // Quejas (Sobre el servicio)
        // Estándar Supertransporte: 15 días hábiles
        $this->createSLAsForType(TicketType::Complaint->value, [
            Priority::Low->value => ['response' => 24, 'resolution' => 360],
            Priority::Medium->value => ['response' => 16, 'resolution' => 360],
            Priority::High->value => ['response' => 8, 'resolution' => 360],
            Priority::Urgent->value => ['response' => 4, 'resolution' => 240],
        ]);

        // Reclamos (Sobre el servicio prestado)
        // Estándar Supertransporte: 15 días hábiles
        $this->createSLAsForType(TicketType::Claim->value, [
            Priority::Low->value => ['response' => 24, 'resolution' => 360],
            Priority::Medium->value => ['response' => 16, 'resolution' => 360],
            Priority::High->value => ['response' => 8, 'resolution' => 360],
            Priority::Urgent->value => ['response' => 4, 'resolution' => 240],
        ]);

        // Sugerencias (No tienen tiempo legal estricto)
        // Establecemos un estándar interno de 30 días hábiles
        $this->createSLAsForType(TicketType::Suggestion->value, [
            Priority::Low->value => ['response' => 48, 'resolution' => 720], // 30 días
            Priority::Medium->value => ['response' => 36, 'resolution' => 720],
            Priority::High->value => ['response' => 24, 'resolution' => 720],
            Priority::Urgent->value => ['response' => 12, 'resolution' => 480], // 20 días para urgentes
        ]);

        $this->command->info('SLAs configurados según normativa Supertransporte Colombia 2025');
    }

    private function createSLAsForType(string $ticketType, array $priorities): void
    {
        foreach ($priorities as $priorityValue => $times) {
            SLA::create([
                'ticket_type' => $ticketType,
                'priority' => $priorityValue,
                'response_time' => $times['response'],
                'resolution_time' => $times['resolution'],
                'is_active' => true,
            ]);
        }
    }
}
