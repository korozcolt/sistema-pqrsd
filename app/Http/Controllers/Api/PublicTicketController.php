<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketComment;
use App\Enums\TicketType;
use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\UserRole;
use App\Notifications\NewUserCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PublicTicketController extends Controller
{
    /**
     * Crear un nuevo ticket desde el portal público
     */
    public function store(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:' . implode(',', array_column(TicketType::cases(), 'value')),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar si el usuario ya existe
            $userExists = User::where('email', $request->email)->exists();

            // Si no existe, generar una contraseña aleatoria para el nuevo usuario
            $password = $userExists ? null : Str::random(10);

            // Crear o recuperar usuario
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'password' => Hash::make($password ?: Str::random(16)),
                    'role' => UserRole::UserWeb->value
                ]
            );

            // Si el usuario es nuevo, enviar notificación con credenciales
            if (!$userExists && $password) {
                $user->notify(new NewUserCredentials($user, $password));
            }

            // Crear ticket (la notificación de ticket se maneja automáticamente)
            $ticket = Ticket::create([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'user_id' => $user->id,
                'status' => StatusTicket::Pending,
                'priority' => Priority::Medium,
                'department_id' => $this->getDefaultDepartmentId()
            ]);

            // Procesar archivos adjuntos si existen
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $path = $attachment->store('ticket-attachments');

                    $ticket->attachments()->create([
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $attachment->getMimeType(),
                        'file_size' => $attachment->getSize(),
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Ticket creado correctamente',
                'data' => [
                    'ticket_number' => $ticket->ticket_number,
                    'user_created' => !$userExists
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un ticket específico
     */
    public function show(Request $request, $ticket_number)
    {
        $validator = Validator::make([
            'email' => $request->header('X-User-Email'),
            'password' => $request->header('X-User-Password')
        ], [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incompletas',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar credenciales del usuario
        $user = User::where('email', $request->header('X-User-Email'))->first();

        if (!$user || !Hash::check($request->header('X-User-Password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        // Buscar el ticket
        $ticket = Ticket::with(['comments.user', 'attachments', 'user', 'department'])
            ->where('ticket_number', $ticket_number)
            ->where('user_id', $user->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado o no tiene permisos para verlo'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    /**
     * Verificar si existe un ticket
     */
    public function verify($ticket_number)
    {
        $exists = Ticket::where('ticket_number', $ticket_number)->exists();

        return response()->json([
            'success' => true,
            'exists' => $exists
        ]);
    }

    /**
     * Añadir comentario a un ticket
     */
    public function addComment(Request $request, $ticket_number)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar credenciales del usuario
        $user = User::where('email', $request->header('X-User-Email'))->first();

        if (!$user || !Hash::check($request->header('X-User-Password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        // Buscar el ticket
        $ticket = Ticket::where('ticket_number', $ticket_number)
            ->where('user_id', $user->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado o no tiene permisos para modificarlo'
            ], 404);
        }

        try {
            $comment = $ticket->comments()->create([
                'content' => $request->content,
                'user_id' => $user->id,
                'is_internal' => false,
            ]);

            // Si el ticket estaba marcado para cierre por inactividad, quitar marca
            if ($ticket->marked_for_closure_at) {
                $ticket->marked_for_closure_at = null;
                $ticket->save();
            }

            // Si el ticket estaba cerrado o resuelto, reabrirlo
            if (in_array($ticket->status, [StatusTicket::Closed, StatusTicket::Resolved])) {
                $oldStatus = $ticket->status;
                $ticket->status = StatusTicket::Reopened;
                $ticket->save();

                // Disparar evento de cambio de estado
                event(new \App\Events\TicketStatusChanged(
                    ticket: $ticket,
                    oldStatus: $oldStatus,
                    newStatus: StatusTicket::Reopened,
                    changedBy: $user->id,
                    reason: 'Reabierto por comentario del cliente'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Comentario añadido correctamente',
                'data' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al añadir comentario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar un ticket por parte del cliente
     */
    public function closeTicket(Request $request, $ticket_number)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar credenciales del usuario
        $user = User::where('email', $request->header('X-User-Email'))->first();

        if (!$user || !Hash::check($request->header('X-User-Password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        // Buscar el ticket
        $ticket = Ticket::where('ticket_number', $ticket_number)
            ->where('user_id', $user->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado o no tiene permisos para modificarlo'
            ], 404);
        }

        try {
            // Guardar el estado anterior para el evento
            $oldStatus = $ticket->status;

            // Actualizar estado del ticket
            $ticket->status = StatusTicket::Closed;
            $ticket->resolution_at = now();
            $ticket->save();

            // Añadir comentario de cierre
            $closureReason = $request->reason ?: 'Ticket cerrado por el cliente';
            $ticket->comments()->create([
                'content' => $closureReason,
                'user_id' => $ticket->user_id,
                'is_internal' => false,
            ]);

            // Disparar evento de cambio de estado
            event(new \App\Events\TicketStatusChanged(
                ticket: $ticket,
                oldStatus: $oldStatus,
                newStatus: StatusTicket::Closed,
                changedBy: $user->id,
                reason: $closureReason
            ));

            return response()->json([
                'success' => true,
                'message' => 'Ticket cerrado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar el ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tipos de tickets
     */
    public function getTicketTypes()
    {
        $types = [];

        foreach (TicketType::cases() as $type) {
            $types[] = [
                'value' => $type->value,
                'label' => $type->getLabel()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Obtener ID del departamento predeterminado
     */
    private function getDefaultDepartmentId()
    {
        // Buscar departamento de servicio al cliente o el primero disponible
        $department = \App\Models\Department::where('name', 'like', '%servicio%cliente%')
            ->orWhere('code', 'SERVICLI')
            ->first();

        if (!$department) {
            $department = \App\Models\Department::where('status', 'active')->first();
        }

        return $department ? $department->id : null;
    }
}
