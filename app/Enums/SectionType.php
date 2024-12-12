<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Validation\Rules\Enum;

enum SectionType: string implements HasLabel, HasColor, HasIcon
{
    case MENU = 'menu';
    case HEADER = 'header';
    case SLIDER = 'slider';
    case CONTENT = 'content';
    case WIDGET = 'widget';

    public function getLabel(): string
    {
        return match($this) {
            self::MENU => 'Menú',
            self::HEADER => 'Encabezado',
            self::SLIDER => 'Carrusel',
            self::CONTENT => 'Contenido',
            self::WIDGET => 'Widget'
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::MENU => 'primary',
            self::HEADER => 'warning',
            self::SLIDER => 'info',
            self::CONTENT => 'success',
            self::WIDGET => 'secondary'
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::MENU => 'heroicon-o-bars-3',
            self::HEADER => 'heroicon-o-rectangle-group',
            self::SLIDER => 'heroicon-o-photo',
            self::CONTENT => 'heroicon-o-document-text',
            self::WIDGET => 'heroicon-o-puzzle-piece'
        };
    }

    public function getViewComponent(): string
    {
        return match($this) {
            self::MENU => 'sections.menu',
            self::HEADER => 'sections.header',
            self::SLIDER => 'sections.slider',
            self::CONTENT => 'sections.content',
            self::WIDGET => 'sections.widget'
        };
    }

    public function getContentRelation(): string
    {
        return match($this) {
            self::MENU => 'menu',
            self::SLIDER => 'slider',
            self::CONTENT => 'contents',
            self::HEADER => 'contents',
            self::WIDGET => 'contents'
        };
    }

    public function getDefaultSettings(): array
    {
        return match($this) {
            self::MENU => [
                'sticky' => false,
                'transparent' => false,
                'alignment' => 'left',
                'show_logo' => true
            ],
            self::SLIDER => [
                'autoplay' => true,
                'interval' => 5000,
                'arrows' => true,
                'dots' => true,
                'effect' => 'fade'
            ],
            self::WIDGET => [
                'cache' => true,
                'cache_time' => 3600,
                'wrapper_class' => 'widget-container'
            ],
            default => []
        };
    }

    public function getValidationRules(): array
    {
        return match($this) {
            self::MENU => [
                'config.menu_id' => ['required', 'exists:menus,id'],
                'config.sticky' => ['boolean'],
                'config.transparent' => ['boolean']
            ],
            self::SLIDER => [
                'config.interval' => ['integer', 'min:1000'],
                'config.autoplay' => ['boolean'],
                'content.*.title' => ['required', 'string']
            ],
            self::WIDGET => [
                'config.widget_id' => ['required', 'string'],
                'config.cache_time' => ['integer', 'min:0']
            ],
            default => []
        };
    }

    public function validateConfig(array $config): bool
    {
        $rules = $this->getValidationRules();
        $validator = validator($config, $rules);
        return !$validator->fails();
    }
}
