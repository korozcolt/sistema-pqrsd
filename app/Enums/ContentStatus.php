<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ContentStatus: string implements HasLabel, HasColor, HasIcon {
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function getLabel(): string {
        return match($this) {
            self::Draft => 'Borrador',
            self::Published => 'Publicado',
            self::Archived => 'Archivado'
        };
    }

    public function getColor(): string {
        return match($this) {
            self::Draft => 'warning',
            self::Published => 'success',
            self::Archived => 'danger'
        };
    }

    public function getIcon(): string {
        return match($this) {
            self::Draft => 'heroicon-o-pencil',
            self::Published => 'heroicon-o-check-circle',
            self::Archived => 'heroicon-o-archive-box'
        };
    }
}
