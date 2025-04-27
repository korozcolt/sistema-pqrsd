<?php

namespace App\Console\Commands;

use App\Enums\Priority;
use App\Enums\ReminderType;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use App\Notifications\NewTicketCommentNotification;
use App\Notifications\NewTicketNotification;
use App\Notifications\TicketCreated;
use App\Notifications\TicketReminderNotification;
use App\Notifications\TicketStatusUpdated;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class TestNotificationsCommand extends Command
{
    protected $signature = 'notifications:test {email? : Email para enviar las notificaciones de prueba} {--all : Ejecutar todas las notificaciones}';
    protected $description = 'Prueba todos los tipos de notificaciones por correo electrónico del sistema';

    private $testUser;
    private $testTicket;
    private $testDepartment;

    public function handle()
    {
        $email = $this->argument('email') ?? env('TICKET_NOTIFICATION_EMAIL', 'soporte@torcoromaweb.com');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("El email proporcionado no es válido: $email");
            return 1;
        }

        $this->info("Probando notificaciones enviando a: $email");

        // Crear datos de prueba temporales
        $this->setupTestData($email);

        // Determinar qué notificaciones probar
        if ($this->option('all')) {
            $this->runAllTests($email);
        } else {
            $this->showMenu($email);
        }

        $this->info("Pruebas completadas.");
        return 0;
    }

    private function setupTestData($email)
    {
        // Crear o recuperar un usuario de prueba
        $this->testUser = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Usuario de Prueba',
                'password' => bcrypt('password'),
                'role' => 'user_web'
            ]
        );

        // Obtener o crear un departamento de prueba
        $this->testDepartment = Department::first() ?? Department::create([
            'name' => 'Departamento de Prueba',
            'code' => 'TESTDEPT',
            'description' => 'Departamento creado para pruebas',
            'address' => 'Dirección de prueba',
            'phone' => '1234567890',
            'email' => 'test@example.com',
            'status' => 'active'
        ]);

        // Crear un ticket de prueba (no se guarda en la base de datos)
        $this->testTicket = new Ticket([
            'id' => 9999,
            'ticket_number' => 'TK-TEST',
            'title' => 'Ticket de prueba para notificaciones',
            'description' => 'Este es un ticket generado para probar las notificaciones por correo.',
            'user_id' => $this->testUser->id,
            'department_id' => $this->testDepartment->id,
            'type' => TicketType::Petition,
            'status' => StatusTicket::Pending,
            'priority' => Priority::Medium,
            'response_due_date' => Carbon::now()->addDays(3),
            'resolution_due_date' => Carbon::now()->addDays(15),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    private function showMenu($email)
    {
        $options = [
            1 => 'Notificación de nuevo ticket',
            2 => 'Notificación de cambio de estado',
            3 => 'Notificación de nuevo comentario',
            4 => 'Notificación de recordatorio (tiempo de respuesta)',
            5 => 'Notificación de recordatorio (tiempo de resolución)',
            6 => 'Correo de contacto',
            7 => 'Todas las notificaciones',
            0 => 'Salir'
        ];

        $this->info("\nSelecciona una notificación para probar:");

        foreach ($options as $key => $option) {
            $this->line("  [$key] $option");
        }

        $choice = $this->ask('Opción');

        switch ($choice) {
            case 0:
                return;
            case 1:
                $this->testNewTicketNotification($email);
                break;
            case 2:
                $this->testStatusChangeNotification($email);
                break;
            case 3:
                $this->testNewCommentNotification($email);
                break;
            case 4:
                $this->testReminderNotification($email, ReminderType::DayBeforeResponse);
                break;
            case 5:
                $this->testReminderNotification($email, ReminderType::DayBeforeResolution);
                break;
            case 6:
                $this->testContactFormEmail($email);
                break;
            case 7:
                $this->runAllTests($email);
                break;
            default:
                $this->error('Opción no válida');
                $this->showMenu($email);
                break;
        }

        if ($choice != 0 && $choice != 7) {
            if ($this->confirm('¿Deseas probar otra notificación?')) {
                $this->showMenu($email);
            }
        }
    }

    private function runAllTests($email)
    {
        $this->testNewTicketNotification($email);
        $this->testStatusChangeNotification($email);
        $this->testNewCommentNotification($email);
        $this->testReminderNotification($email, ReminderType::DayBeforeResponse);
        $this->testReminderNotification($email, ReminderType::DayBeforeResolution);
        $this->testContactFormEmail($email);
    }

    private function testNewTicketNotification($email)
    {
        $this->info('Enviando notificación de nuevo ticket...');

        try {
            // Usar la notificación directa
            Notification::route('mail', $email)
                ->notify(new NewTicketNotification($this->testTicket));

            $this->info('✓ Notificación de nuevo ticket enviada correctamente.');
        } catch (\Exception $e) {
            $this->error('Error enviando notificación: ' . $e->getMessage());
        }
    }

    private function testStatusChangeNotification($email)
    {
        $this->info('Enviando notificación de cambio de estado...');

        try {
            $oldStatus = StatusTicket::Pending;
            $newStatus = StatusTicket::In_Progress;

            // Usar la notificación directa
            Notification::route('mail', $email)
                ->notify(new TicketStatusUpdated(
                    $this->testTicket,
                    $oldStatus,
                    $newStatus
                ));

            $this->info('✓ Notificación de cambio de estado enviada correctamente.');
        } catch (\Exception $e) {
            $this->error('Error enviando notificación: ' . $e->getMessage());
        }
    }

    private function testNewCommentNotification($email)
    {
        $this->info('Enviando notificación de nuevo comentario...');

        try {
            // Crear un comentario de prueba
            $comment = new TicketComment([
                'id' => 9999,
                'ticket_id' => $this->testTicket->id,
                'user_id' => $this->testUser->id,
                'content' => 'Este es un comentario de prueba para verificar la notificación.',
                'is_internal' => false,
                'created_at' => Carbon::now()
            ]);

            // Usamos reflection para asignar la relación user
            $reflectionClass = new \ReflectionClass($comment);
            $reflectionProperty = $reflectionClass->getProperty('relations');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($comment, ['user' => $this->testUser]);

            // Usar la notificación directa
            Notification::route('mail', $email)
                ->notify(new NewTicketCommentNotification(
                    $this->testTicket,
                    $comment
                ));

            $this->info('✓ Notificación de nuevo comentario enviada correctamente.');
        } catch (\Exception $e) {
            $this->error('Error enviando notificación: ' . $e->getMessage());
        }
    }

    private function testReminderNotification($email, $reminderType)
    {
        $typeLabel = $reminderType === ReminderType::DayBeforeResponse ?
            'tiempo de respuesta' : 'tiempo de resolución';

        $this->info("Enviando notificación de recordatorio ($typeLabel)...");

        try {
            // Usar la notificación directa
            Notification::route('mail', $email)
                ->notify(new TicketReminderNotification(
                    $this->testTicket,
                    $reminderType
                ));

            $this->info("✓ Notificación de recordatorio de $typeLabel enviada correctamente.");
        } catch (\Exception $e) {
            $this->error('Error enviando notificación: ' . $e->getMessage());
        }
    }

    private function testContactFormEmail($email)
    {
        $this->info('Enviando correo de formulario de contacto...');

        try {
            $formData = [
                'name' => 'Cliente de Prueba',
                'email' => 'cliente@example.com',
                'phone' => '3001234567',
                'subject' => 'Asunto de prueba para formulario de contacto',
                'message' => 'Este es un mensaje de prueba enviado desde el comando de prueba de notificaciones. Permite verificar el formato y diseño del correo que se envía cuando alguien completa el formulario de contacto en el sitio web.'
            ];

            Mail::to($email)->send(new \App\Mail\ContactForm($formData));

            $this->info('✓ Correo de formulario de contacto enviado correctamente.');
        } catch (\Exception $e) {
            $this->error('Error enviando correo: ' . $e->getMessage());
        }
    }
}
