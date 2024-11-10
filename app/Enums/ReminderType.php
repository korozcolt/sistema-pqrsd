<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ReminderType: string implements HasLabel, HasColor, HasIcon {

    case ResponseDue = 'response_due';
    case ResolutionDue = 'resolution_due';

    public function getLabel(): string
    {
        return match($this) {
            self::ResponseDue => 'Recordatorio de Respuesta',
            self::ResolutionDue => 'Recordatorio de Resolución',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::ResponseDue => 'El tiempo de respuesta está por vencer',
            self::ResolutionDue => 'El tiempo de resolución está por vencer',
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::ResponseDue => 'info',
            self::ResolutionDue => 'warning',
        };
    }

    public function getIcon(): string | null
    {
        return match($this) {
            self::ResponseDue => 'heroicon-o-clock',
            self::ResolutionDue => 'heroicon-o-clock',
        };
    }

    public function getColorHtml(): string | null
    {
        return match($this) {
            self::ResponseDue => 'bg-blue-100',
            self::ResolutionDue => 'bg-yellow-100',
        };
    }

}
