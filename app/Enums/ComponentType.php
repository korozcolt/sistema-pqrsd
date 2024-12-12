<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ComponentType: string implements HasLabel, HasColor, HasIcon {
    case Modal = 'modal';
    case Banner = 'banner';
    case Slider = 'slider';
    case Form = 'form';
    case Widget = 'widget';

    public function getLabel(): string {
        return match($this) {
            self::Modal => 'Modal',
            self::Banner => 'Banner',
            self::Slider => 'Carrusel',
            self::Form => 'Formulario',
            self::Widget => 'Widget'
        };
    }

    public function getColor(): string {
        return match($this) {
            self::Modal => 'primary',
            self::Banner => 'success',
            self::Slider => 'info',
            self::Form => 'warning',
            self::Widget => 'secondary'
        };
    }

    public function getIcon(): string {
        return match($this) {
            self::Modal => 'heroicon-o-square-2-stack',
            self::Banner => 'heroicon-o-rectangle-stack',
            self::Slider => 'heroicon-o-photo',
            self::Form => 'heroicon-o-clipboard-document',
            self::Widget => 'heroicon-o-puzzle-piece'
        };
    }
}
