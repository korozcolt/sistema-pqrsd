<?php

namespace App\Services;

use App\Enums\StatusTicket;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

/**
 * State Machine para gestión de transiciones de estado de tickets
 *
 * Define las transiciones permitidas entre estados y valida
 * que los cambios de estado sigan las reglas del negocio.
 */
class TicketStateMachine
{
    /**
     * Definición de transiciones permitidas
     *
     * @var array<string, array<string>>
     */
    private const ALLOWED_TRANSITIONS = [
        // Desde Pending (pendiente) puede ir a:
        StatusTicket::Pending->value => [
            StatusTicket::In_Progress->value,  // Cuando se empieza a trabajar
            StatusTicket::Rejected->value,     // Si se rechaza directamente
        ],

        // Desde In_Progress (en progreso) puede ir a:
        StatusTicket::In_Progress->value => [
            StatusTicket::Resolved->value,     // Cuando se resuelve
            StatusTicket::Rejected->value,     // Si se determina que no es válido
            StatusTicket::Pending->value,      // Si necesita volver a revisión
        ],

        // Desde Resolved (resuelto) puede ir a:
        StatusTicket::Resolved->value => [
            StatusTicket::Closed->value,       // Cuando el usuario confirma la resolución
            StatusTicket::Reopened->value,     // Si el usuario no está satisfecho
        ],

        // Desde Rejected (rechazado) puede ir a:
        StatusTicket::Rejected->value => [
            StatusTicket::Reopened->value,     // Si se reconsideró la decisión
            StatusTicket::Pending->value,      // Si se vuelve a revisar
        ],

        // Desde Reopened (reabierto) puede ir a:
        StatusTicket::Reopened->value => [
            StatusTicket::In_Progress->value,  // Cuando se empieza a trabajar de nuevo
            StatusTicket::Rejected->value,     // Si se rechaza tras reevaluación
            StatusTicket::Resolved->value,     // Si se resuelve directamente
        ],

        // Desde Closed (cerrado) puede ir a:
        StatusTicket::Closed->value => [
            StatusTicket::Reopened->value,     // Solo en casos excepcionales
        ],
    ];

    /**
     * Estados que se consideran terminales (finales)
     *
     * @var array<string>
     */
    private const TERMINAL_STATES = [
        StatusTicket::Closed->value,
    ];

    /**
     * Estados que requieren aprobación especial para cambiar
     *
     * @var array<string>
     */
    private const RESTRICTED_STATES = [
        StatusTicket::Closed->value,
        StatusTicket::Rejected->value,
    ];

    /**
     * Validar si una transición de estado es permitida
     */
    public function canTransition(StatusTicket|string $from, StatusTicket|string $to): bool
    {
        $fromValue = $from instanceof StatusTicket ? $from->value : $from;
        $toValue = $to instanceof StatusTicket ? $to->value : $to;

        // Verificar si la transición está definida
        if (! isset(self::ALLOWED_TRANSITIONS[$fromValue])) {
            return false;
        }

        return in_array($toValue, self::ALLOWED_TRANSITIONS[$fromValue]);
    }

    /**
     * Aplicar una transición de estado a un ticket
     */
    public function transition(Ticket $ticket, StatusTicket $newStatus, ?string $reason = null): bool
    {
        $oldStatus = $ticket->status;

        // Verificar si la transición es válida
        if (! $this->canTransition($oldStatus, $newStatus)) {
            Log::warning('Invalid ticket state transition attempted', [
                'ticket_id' => $ticket->id,
                'from' => $oldStatus->value,
                'to' => $newStatus->value,
                'reason' => $reason,
            ]);

            return false;
        }

        // Aplicar la transición
        $ticket->status = $newStatus;

        // Guardar cambios
        $saved = $ticket->save();

        if ($saved) {
            Log::info('Ticket state transition completed', [
                'ticket_id' => $ticket->id,
                'from' => $oldStatus->value,
                'to' => $newStatus->value,
                'reason' => $reason,
            ]);
        }

        return $saved;
    }

    /**
     * Obtener los estados permitidos desde un estado actual
     *
     * @return array<StatusTicket>
     */
    public function getAllowedTransitions(StatusTicket|string $currentStatus): array
    {
        $currentValue = $currentStatus instanceof StatusTicket ? $currentStatus->value : $currentStatus;

        if (! isset(self::ALLOWED_TRANSITIONS[$currentValue])) {
            return [];
        }

        return array_map(
            fn (string $status) => StatusTicket::from($status),
            self::ALLOWED_TRANSITIONS[$currentValue]
        );
    }

    /**
     * Verificar si un estado es terminal (no puede cambiar más)
     */
    public function isTerminalState(StatusTicket|string $status): bool
    {
        $statusValue = $status instanceof StatusTicket ? $status->value : $status;

        // Un estado es terminal si tiene transiciones vacías o está en la lista de terminales
        $hasNoTransitions = empty(self::ALLOWED_TRANSITIONS[$statusValue] ?? []);
        $isInTerminalList = in_array($statusValue, self::TERMINAL_STATES);

        return $hasNoTransitions || $isInTerminalList;
    }

    /**
     * Verificar si un estado requiere aprobación especial
     */
    public function isRestrictedState(StatusTicket|string $status): bool
    {
        $statusValue = $status instanceof StatusTicket ? $status->value : $status;

        return in_array($statusValue, self::RESTRICTED_STATES);
    }

    /**
     * Obtener el mensaje de error para una transición inválida
     */
    public function getTransitionErrorMessage(StatusTicket|string $from, StatusTicket|string $to): string
    {
        $fromEnum = $from instanceof StatusTicket ? $from : StatusTicket::from($from);
        $toEnum = $to instanceof StatusTicket ? $to : StatusTicket::from($to);

        $allowedTransitions = $this->getAllowedTransitions($fromEnum);
        $allowedLabels = array_map(fn (StatusTicket $status) => $status->getLabel(), $allowedTransitions);

        return sprintf(
            'No se puede cambiar de "%s" a "%s". Transiciones permitidas: %s',
            $fromEnum->getLabel(),
            $toEnum->getLabel(),
            empty($allowedLabels) ? 'ninguna' : implode(', ', $allowedLabels)
        );
    }

    /**
     * Obtener todas las transiciones definidas (útil para debugging)
     *
     * @return array<string, array<string>>
     */
    public function getAllTransitions(): array
    {
        return self::ALLOWED_TRANSITIONS;
    }

    /**
     * Validar la integridad de las transiciones definidas
     * Útil para tests
     */
    public function validateTransitions(): array
    {
        $errors = [];

        // Verificar que todos los estados origen son válidos
        foreach (array_keys(self::ALLOWED_TRANSITIONS) as $fromStatus) {
            try {
                StatusTicket::from($fromStatus);
            } catch (\ValueError $e) {
                $errors[] = "Estado origen inválido: {$fromStatus}";
            }
        }

        // Verificar que todos los estados destino son válidos
        foreach (self::ALLOWED_TRANSITIONS as $from => $toStates) {
            foreach ($toStates as $toStatus) {
                try {
                    StatusTicket::from($toStatus);
                } catch (\ValueError $e) {
                    $errors[] = "Estado destino inválido: {$toStatus} (desde {$from})";
                }
            }
        }

        return $errors;
    }
}
