<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum StatusGlobal: string implements HasLabel, HasColor, HasIcon {
    case Active = 'active'; // Activo
    case Inactive = 'inactive'; // Inactivo
    case Deleted = 'deleted'; // Eliminado

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Inactive => 'Inactivo',
            self::Deleted => 'Eliminado',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'danger',
            self::Deleted => 'danger',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Active => 'heroicon-o-check-circle',
            self::Inactive => 'heroicon-o-x-circle',
            self::Deleted => 'heroicon-o-trash',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Active => 'bg-green-100',
            self::Inactive => 'bg-orange-100',
            self::Deleted => 'bg-red-100',
        };
    }

    public function getLabelText(): ?string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Inactive => 'Inactivo',
            self::Deleted => 'Eliminado',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded '.$this->getColorHtml().'">'.$this->getLabelText().'</span>';
    }
}
