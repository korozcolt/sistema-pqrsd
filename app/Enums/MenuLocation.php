<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MenuLocation: string implements HasLabel, HasColor, HasIcon
{
    case HEADER = 'header';
    case FOOTER = 'footer';
    case SIDEBAR = 'sidebar';
    case MOBILE = 'mobile';

    public function getLabel(): string
    {
        return match($this) {
            self::HEADER => 'Encabezado',
            self::FOOTER => 'Pie de página',
            self::SIDEBAR => 'Barra lateral',
            self::MOBILE => 'Menú móvil'
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::HEADER => 'primary',
            self::FOOTER => 'info',
            self::SIDEBAR => 'success',
            self::MOBILE => 'warning'
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::HEADER => 'heroicon-o-bars-3',
            self::FOOTER => 'heroicon-o-bars-3-bottom',
            self::SIDEBAR => 'heroicon-o-bars-3-center-left',
            self::MOBILE => 'heroicon-o-device-phone-mobile'
        };
    }
}
