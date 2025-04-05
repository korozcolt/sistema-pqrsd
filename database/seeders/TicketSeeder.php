<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Models\TicketComment;
use App\Models\Tag;
use App\Enums\StatusTicket;
use App\Enums\Priority;
use App\Enums\TicketType;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $userWeb = User::where('role', UserRole::UserWeb->value)->get();
        $departments = Department::all();
        $tags = Tag::all();
        $staff = User::whereIn('role', [UserRole::Admin->value, UserRole::Receptionist->value])->get();

        // Autenticar un usuario staff para el proceso
        $adminUser = User::where('role', UserRole::Admin->value)->first();
        Auth::login($adminUser);

        // Crear tickets de ejemplo con diferentes estados y tipos
        $ticketData = [
            [
                'title' => 'Petición de información sobre horarios Sincelejo-Montería',
                'description' => 'Solicito información detallada sobre los horarios de buses en la ruta Sincelejo-Montería para los fines de semana.',
                'type' => TicketType::Petition->value,
                'status' => StatusTicket::Pending->value,
                'priority' => Priority::Low->value,
            ],
            [
                'title' => 'Queja por retraso en salida del bus',
                'description' => 'El día 10 de marzo, el bus con placa ABC-123 programado para las 8:00 AM salió con 45 minutos de retraso sin ninguna explicación.',
                'type' => TicketType::Complaint->value,
                'status' => StatusTicket::In_Progress->value,
                'priority' => Priority::Medium->value,
            ],
            [
                'title' => 'Reclamo por daño en equipaje',
                'description' => 'Durante mi viaje del 15 de marzo en la ruta Sincelejo-Barranquilla, mi maleta sufrió daños considerables. Tengo fotos del estado en que me fue entregada.',
                'type' => TicketType::Claim->value,
                'status' => StatusTicket::In_Progress->value,
                'priority' => Priority::High->value,
            ],
            [
                'title' => 'Sugerencia para mejorar el sistema de compra online',
                'description' => 'Sugiero implementar un sistema de notificaciones por SMS para confirmar las compras de tiquetes realizadas por la web.',
                'type' => TicketType::Suggestion->value,
                'status' => StatusTicket::Resolved->value,
                'priority' => Priority::Low->value,
            ],
            [
                'title' => 'Petición formal de copia de contrato',
                'description' => 'Solicito copia del contrato de transporte según lo establecido en el artículo 4 de la resolución 1023 de Supertransporte.',
                'type' => TicketType::Petition->value,
                'status' => StatusTicket::Closed->value,
                'priority' => Priority::Medium->value,
            ],
            [
                'title' => 'Queja por maltrato de conductor',
                'description' => 'El conductor de placa XYZ-789 fue descortés y agresivo con los pasajeros durante el recorrido Sincelejo-Cartagena del día 20 de marzo.',
                'type' => TicketType::Complaint->value,
                'status' => StatusTicket::In_Progress->value,
                'priority' => Priority::Urgent->value,
            ],
            [
                'title' => 'Reclamo por cobro excesivo de tiquete',
                'description' => 'Se me cobró un sobrecosto no justificado en el tiquete para la ruta Sincelejo-Medellín del día 18 de marzo. El precio publicado era diferente.',
                'type' => TicketType::Claim->value,
                'status' => StatusTicket::Pending->value,
                'priority' => Priority::High->value,
            ],
            [
                'title' => 'Sugerencia para implementar WiFi en buses',
                'description' => 'Considero que sería un valor agregado importante que los buses de larga distancia contaran con servicio de WiFi gratuito para los pasajeros.',
                'type' => TicketType::Suggestion->value,
                'status' => StatusTicket::Resolved->value,
                'priority' => Priority::Low->value,
            ],
            [
                'title' => 'Petición de reembolso por viaje cancelado',
                'description' => 'Solicito el reembolso del tiquete #78954 para el viaje Sincelejo-Bogotá del 22 de marzo que fue cancelado sin previo aviso.',
                'type' => TicketType::Petition->value,
                'status' => StatusTicket::In_Progress->value,
                'priority' => Priority::High->value,
            ],
            [
                'title' => 'Queja por falta de aire acondicionado',
                'description' => 'El servicio premium que pagué prometía aire acondicionado, pero durante todo el viaje Sincelejo-Santa Marta del 25 de marzo no funcionó.',
                'type' => TicketType::Complaint->value,
                'status' => StatusTicket::Resolved->value,
                'priority' => Priority::Medium->value,
            ],
            [
                'title' => 'Reclamo por pérdida de equipaje',
                'description' => 'No me han entregado una maleta que fue documentada en el viaje Sincelejo-Barranquilla del 30 de marzo. Contiene objetos de valor y documentos importantes.',
                'type' => TicketType::Claim->value,
                'status' => StatusTicket::Pending->value,
                'priority' => Priority::Urgent->value,
            ],
            [
                'title' => 'Sugerencia de puntos de venta adicionales',
                'description' => 'Sería conveniente tener puntos de venta en centros comerciales o sectores céntricos además de las terminales de transporte.',
                'type' => TicketType::Suggestion->value,
                'status' => StatusTicket::Resolved->value,
                'priority' => Priority::Low->value,
            ],
            [
                'title' => 'Petición de información sobre rutas especiales',
                'description' => 'Necesito saber si existen rutas o servicios especiales para grupos grandes durante temporada de festividades en julio.',
                'type' => TicketType::Petition->value,
                'status' => StatusTicket::Closed->value,
                'priority' => Priority::Medium->value,
            ],
            [
                'title' => 'Queja por sobreventa de tiquetes',
                'description' => 'El bus Sincelejo-Cartagena del 2 de abril vendió más tiquetes que asientos disponibles, causando grandes inconvenientes a varios pasajeros.',
                'type' => TicketType::Complaint->value,
                'status' => StatusTicket::In_Progress->value,
                'priority' => Priority::High->value,
            ],
            [
                'title' => 'Reclamo por incumplimiento de ruta',
                'description' => 'El bus de la ruta Sincelejo-Medellín del 5 de abril no cumplió con las paradas establecidas en el itinerario, causándome llegar a un destino incorrecto.',
                'type' => TicketType::Claim->value,
                'status' => StatusTicket::Pending->value,
                'priority' => Priority::High->value,
            ]
        ];

        $createdDays = [30, 25, 20, 15, 12, 10, 8, 7, 6, 5, 4, 3, 2, 1, 0];

        foreach ($ticketData as $index => $data) {
            $user = $userWeb->random();
            $department = $departments->random();

            // Generar ticket_number manualmente
            $ticketNumber = 'TK-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT);

            // Crear ticket incluyendo el ticket_number
            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'title' => $data['title'],
                'description' => $data['description'],
                'user_id' => $user->id,
                'department_id' => $department->id,
                'type' => $data['type'],
                'status' => $data['status'],
                'priority' => $data['priority'],
                'created_at' => Carbon::now()->subDays($createdDays[$index]),
            ]);

            // Asignar tags aleatorios (1-3 tags)
            $randomTags = $tags->random(rand(1, 3));
            foreach ($randomTags as $tag) {
                $ticket->tags()->attach($tag->id);
            }

            // Agregar comentarios si el ticket no está pendiente
            if ($data['status'] != StatusTicket::Pending->value) {
                // Comentario de personal
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $staff->random()->id,
                    'content' => 'Estamos revisando su caso. Gracias por su paciencia.',
                    'is_internal' => false,
                    'created_at' => Carbon::parse($ticket->created_at)->addHours(rand(1, 24)),
                ]);

                // Comentario interno si aplica
                if (in_array($data['status'], [StatusTicket::In_Progress->value, StatusTicket::Resolved->value])) {
                    TicketComment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $staff->random()->id,
                        'content' => 'Este caso requiere verificación adicional con el departamento operativo.',
                        'is_internal' => true,
                        'created_at' => Carbon::parse($ticket->created_at)->addHours(rand(25, 48)),
                    ]);
                }

                // Respuesta del usuario
                if (rand(0, 1)) {
                    TicketComment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $user->id,
                        'content' => 'Gracias por la atención. Quedo atento a la resolución.',
                        'is_internal' => false,
                        'created_at' => Carbon::parse($ticket->created_at)->addHours(rand(49, 72)),
                    ]);
                }

                // Comentario de cierre si está resuelto o cerrado
                if (in_array($data['status'], [StatusTicket::Resolved->value, StatusTicket::Closed->value])) {
                    TicketComment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $staff->random()->id,
                        'content' => 'Su caso ha sido resuelto. Agradecemos su comprensión y esperamos que siga utilizando nuestros servicios.',
                        'is_internal' => false,
                        'created_at' => Carbon::parse($ticket->created_at)->addHours(rand(73, 120)),
                    ]);
                }
            }
        }

        // Cerrar sesión del administrador
        Auth::logout();

        $this->command->info('Tickets y comentarios de ejemplo creados correctamente');
    }
}
