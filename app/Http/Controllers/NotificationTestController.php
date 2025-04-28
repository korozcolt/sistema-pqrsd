<?php

namespace App\Http\Controllers;

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
use App\Notifications\TicketStatusUpdated;
use App\Notifications\TicketReminderNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotificationTestController extends Controller
{
    private $testUser;
    private $testTicket;
    private $testDepartment;

    public function testNotifications(Request $request)
    {
        $email = $request->input('email', env('TICKET_NOTIFICATION_EMAIL', 'soporte@torcoromaweb.com'));
        $testType = $request->input('type', 'all');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'Email no válido'], 400);
        }

        // Crear datos de prueba
        $this->setupTestData($email);

        $results = [];

        switch ($testType) {
            case 'new_ticket':
                $results[] = $this->testNewTicketNotification($email);
                break;
            case 'status_change':
                $results[] = $this->testStatusChangeNotification($email);
                break;
            case 'new_comment':
                $results[] = $this->testNewCommentNotification($email);
                break;
            case 'response_reminder':
                $results[] = $this->testReminderNotification($email, ReminderType::DayBeforeResponse);
                break;
            case 'resolution_reminder':
                $results[] = $this->testReminderNotification($email, ReminderType::DayBeforeResolution);
                break;
            case 'contact_form':
                $results[] = $this->testContactFormEmail($email);
                break;
            case 'all':
            default:
                $results[] = $this->testNewTicketNotification($email);
                $results[] = $this->testStatusChangeNotification($email);
                $results[] = $this->testNewCommentNotification($email);
                $results[] = $this->testReminderNotification($email, ReminderType::DayBeforeResponse);
                $results[] = $this->testReminderNotification($email, ReminderType::DayBeforeResolution);
                $results[] = $this->testContactFormEmail($email);
                break;
        }

        return response()->json([
            'message' => 'Pruebas completadas',
            'email' => $email,
            'results' => $results
        ]);
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

    private function testNewTicketNotification($email)
    {
        try {
            Notification::route('mail', $email)
                ->notify(new NewTicketNotification($this->testTicket));

            return ['type' => 'new_ticket', 'status' => 'success', 'message' => 'Notificación de nuevo ticket enviada'];
        } catch (\Exception $e) {
            return ['type' => 'new_ticket', 'status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function testStatusChangeNotification($email)
    {
        try {
            $oldStatus = StatusTicket::Pending;
            $newStatus = StatusTicket::In_Progress;

            Notification::route('mail', $email)
                ->notify(new TicketStatusUpdated(
                    $this->testTicket,
                    $oldStatus,
                    $newStatus
                ));

            return ['type' => 'status_change', 'status' => 'success', 'message' => 'Notificación de cambio de estado enviada'];
        } catch (\Exception $e) {
            return ['type' => 'status_change', 'status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function testNewCommentNotification($email)
    {
        try {
            $comment = new TicketComment([
                'id' => 9999,
                'ticket_id' => $this->testTicket->id,
                'user_id' => $this->testUser->id,
                'content' => 'Este es un comentario de prueba para verificar la notificación.',
                'is_internal' => false,
                'created_at' => Carbon::now()
            ]);

            $reflectionClass = new \ReflectionClass($comment);
            $reflectionProperty = $reflectionClass->getProperty('relations');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($comment, ['user' => $this->testUser]);

            Notification::route('mail', $email)
                ->notify(new NewTicketCommentNotification(
                    $this->testTicket,
                    $comment
                ));

            return ['type' => 'new_comment', 'status' => 'success', 'message' => 'Notificación de nuevo comentario enviada'];
        } catch (\Exception $e) {
            return ['type' => 'new_comment', 'status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function testReminderNotification($email, $reminderType)
    {
        try {
            Notification::route('mail', $email)
                ->notify(new TicketReminderNotification(
                    $this->testTicket,
                    $reminderType
                ));

            $typeLabel = $reminderType === ReminderType::DayBeforeResponse ?
                'tiempo de respuesta' : 'tiempo de resolución';

            return ['type' => 'reminder_'.$typeLabel, 'status' => 'success', 'message' => "Notificación de recordatorio ($typeLabel) enviada"];
        } catch (\Exception $e) {
            return ['type' => 'reminder', 'status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function testContactFormEmail($email)
    {
        try {
            $formData = [
                'name' => 'Cliente de Prueba',
                'email' => 'cliente@example.com',
                'phone' => '3001234567',
                'subject' => 'Asunto de prueba para formulario de contacto',
                'message' => 'Este es un mensaje de prueba enviado desde el controlador de prueba de notificaciones.'
            ];

            Mail::to($email)->send(new \App\Mail\ContactForm($formData));

            return ['type' => 'contact_form', 'status' => 'success', 'message' => 'Correo de formulario de contacto enviado'];
        } catch (\Exception $e) {
            return ['type' => 'contact_form', 'status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
