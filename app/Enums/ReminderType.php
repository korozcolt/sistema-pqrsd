<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ReminderType: string implements HasLabel, HasColor, HasIcon {

    case HalfTimeResponse = 'half_time_response';
    case DayBeforeResponse = 'day_before_response';
    case HalfTimeResolution = 'half_time_resolution';
    case DayBeforeResolution = 'day_before_resolution';

    public function getLabel(): string
    {
        return match($this) {
            self::HalfTimeResponse => 'Mitad del tiempo de respuesta',
            self::DayBeforeResponse => '24h antes del vencimiento de respuesta',
            self::HalfTimeResolution => 'Mitad del tiempo de resolución',
            self::DayBeforeResolution => '24h antes del vencimiento de resolución',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::HalfTimeResponse => 'Ha transcurrido la mitad del tiempo para responder el ticket',
            self::DayBeforeResponse => 'Falta un día para que venza el tiempo de respuesta',
            self::HalfTimeResolution => 'Ha transcurrido la mitad del tiempo para resolver el ticket',
            self::DayBeforeResolution => 'Falta un día para que venza el tiempo de resolución',
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::HalfTimeResponse => 'info',
            self::DayBeforeResponse => 'warning',
            self::HalfTimeResolution => 'info',
            self::DayBeforeResolution => 'warning',
        };
    }

    public function getIcon(): string | null
    {
        return match($this) {
            self::HalfTimeResponse => 'heroicon-o-bell-alert', // Campana con alerta para mitad de tiempo
            self::DayBeforeResponse => 'heroicon-o-exclamation-circle', // Exclamación para urgencia
            self::HalfTimeResolution => 'heroicon-o-bell-snooze', // Campana con pausa para mitad de resolución
            self::DayBeforeResolution => 'heroicon-o-exclamation-triangle', // Triángulo para advertencia final
        };
    }

    public function getColorHtml(): string | null
    {
        return match($this) {
            self::HalfTimeResponse => 'bg-blue-100',
            self::DayBeforeResponse => 'bg-yellow-100',
            self::HalfTimeResolution => 'bg-blue-100',
            self::DayBeforeResolution => 'bg-yellow-100',
        };
    }

}
