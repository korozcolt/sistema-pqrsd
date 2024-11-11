<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum UserRole: string implements HasLabel, HasColor, HasIcon {
    case SuperAdmin = 'superadmin';
    case Admin = 'admin';
    case Receptionist = 'receptionist';
    case UserWeb = 'user_web';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrador',
            self::Admin => 'Administrador',
            self::Receptionist => 'Recepcionista',
            self::UserWeb => 'Usuario Web',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::SuperAdmin => 'primary',
            self::Admin => 'success',
            self::Receptionist => 'warning',
            self::UserWeb => 'info',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::SuperAdmin => 'heroicon-o-shield-check',
            self::Admin => 'heroicon-o-user-circle',
            self::Receptionist => 'heroicon-o-phone',
            self::UserWeb => 'heroicon-o-globe-alt',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::SuperAdmin => 'bg-blue-100',
            self::Admin => 'bg-green-100',
            self::Receptionist => 'bg-yellow-100',
            self::UserWeb => 'bg-blue-100',
        };
    }

    public function getLabelText(): ?string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrador',
            self::Admin => 'Administrador',
            self::Receptionist => 'Recepcionista',
            self::UserWeb => 'Usuario Web',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded '.$this->getColorHtml().'">'.$this->getLabelText().'</span>';
    }
}
