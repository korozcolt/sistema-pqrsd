<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum TicketType: string implements HasLabel, HasColor, HasIcon {
    case Petition = 'petition';
    case Complaint = 'complaint';
    case Claim = 'claim';
    case Suggestion = 'suggestion';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Petition => 'Petition',
            self::Complaint => 'Complaint',
            self::Claim => 'Claim',
            self::Suggestion => 'Suggestion',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Petition => 'info',
            self::Complaint => 'warning',
            self::Claim => 'danger',
            self::Suggestion => 'success',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Petition => 'heroicon-o-document',
            self::Complaint => 'heroicon-o-exclamation-circle',
            self::Claim => 'heroicon-o-shield-exclamation',
            self::Suggestion => 'heroicon-o-light-bulb',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Petition => 'bg-blue-100',
            self::Complaint => 'bg-yellow-100',
            self::Claim => 'bg-red-100',
            self::Suggestion => 'bg-green-100',
        };
    }

    public function getLabelText(): ?string
    {
        return match ($this) {
            self::Petition => 'Petición',
            self::Complaint => 'Queja',
            self::Claim => 'Reclamo',
            self::Suggestion => 'Sugerencia',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded '.$this->getColorHtml().'">'.$this->getLabelText().'</span>';
    }
}
