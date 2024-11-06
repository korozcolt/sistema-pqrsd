<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum StatusTicket: string implements HasLabel, HasColor, HasIcon {
    case Pending = 'pending';        // Ticket recién creado, esperando procesamiento
    case In_Progress = 'in_progress'; // Operador o responsable está trabajando en la PQR
    case Closed = 'closed';          // Ticket cerrado por el operador
    case Resolved = 'resolved';      // Resuelto, pero el cliente aún puede re-abrir si no está satisfecho
    case Rejected = 'rejected';      // Rechazado por alguna razón (incompleto, no válido)
    case Reopened = 'reopened';      // Ticket que se reabre después de haberse resuelto o cerrado

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::In_Progress => 'In Progress',
            self::Closed => 'Closed',
            self::Resolved => 'Resolved',
            self::Rejected => 'Rejected',
            self::Reopened => 'Reopened',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::In_Progress => 'info',
            self::Closed => 'default',
            self::Resolved => 'success',
            self::Rejected => 'danger',
            self::Reopened => 'warning',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::In_Progress => 'heroicon-o-cog',
            self::Closed => 'heroicon-o-check-circle',
            self::Resolved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::Reopened => 'heroicon-o-refresh',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Pending => 'bg-yellow-100',
            self::In_Progress => 'bg-blue-100',
            self::Closed => 'bg-gray-100',
            self::Resolved => 'bg-green-100',
            self::Rejected => 'bg-red-100',
            self::Reopened => 'bg-yellow-100',
        };
    }

    public function getLabelText(): ?string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::In_Progress => 'En Progreso',
            self::Closed => 'Cerrado',
            self::Resolved => 'Resuelto',
            self::Rejected => 'Rechazado',
            self::Reopened => 'Reabierto',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded '.$this->getColorHtml().'">'.$this->getLabelText().'</span>';
    }
}
