<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Illuminate\Validation\Rules\Enum;
use Str;

enum SectionType: string implements HasLabel, HasColor, HasIcon
{
    case HERO = 'hero';
    case REDBUS = 'redbus';
    case CONTACT = 'contact';
    case SERVICES = 'services';
    case TEXT = 'text';
    case FEATURES = 'features';
    case BANNER = 'banner';
    case STATS = 'stats';
    case TESTIMONIALS = 'testimonials';
    case CUSTOM = 'custom';
    case MENU = 'menu';
    case SLIDER = 'slider';
    case CONTENT = 'content';
    case WIDGET = 'widget';

    public function getLabel(): string
    {
        return match($this) {
            self::HERO => 'Hero Banner',
            self::REDBUS => 'Widget Redbus',
            self::CONTACT => 'Contacto',
            self::SERVICES => 'Servicios',
            self::TEXT => 'Texto',
            self::FEATURES => 'Características',
            self::BANNER => 'Banner',
            self::STATS => 'Estadísticas',
            self::TESTIMONIALS => 'Testimonios',
            self::CUSTOM => 'Personalizado',
            self::MENU => 'Menú',
            self::SLIDER => 'Slider',
            self::CONTENT => 'Contenido',
            self::WIDGET => 'Widget',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::HERO => 'primary',
            self::REDBUS => 'success',
            self::CONTACT => 'info',
            self::SERVICES => 'warning',
            self::TEXT => 'gray',
            self::FEATURES => 'danger',
            self::BANNER => 'secondary',
            self::STATS => 'primary',
            self::TESTIMONIALS => 'warning',
            self::CUSTOM => 'custom',
            self::MENU => 'info',
            self::SLIDER => 'danger',
            self::CONTENT => 'gray',
            self::WIDGET => 'success',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::HERO => 'heroicon-o-photo',
            self::REDBUS => 'heroicon-o-ticket',
            self::CONTACT => 'heroicon-o-envelope',
            self::SERVICES => 'heroicon-o-briefcase',
            self::TEXT => 'heroicon-o-document-text',
            self::FEATURES => 'heroicon-o-sparkles',
            self::BANNER => 'heroicon-o-rectangle-stack',
            self::STATS => 'heroicon-o-chart-bar',
            self::TESTIMONIALS => 'heroicon-o-chat-bubble-left-right',
            self::CUSTOM => 'heroicon-o-cog',
            self::MENU => 'heroicon-o-menu',
            self::SLIDER => 'heroicon-o-play',
            self::CONTENT => 'heroicon-o-newspaper',
            self::WIDGET => 'heroicon-o-cube',
        };
    }

    public function getViewComponent(): string
    {
        return 'sections.' . Str::slug($this->value);
    }

    public function getSchema(): array
    {
        return match($this) {
            self::HERO => [
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(100),
                TextInput::make('subtitle')
                    ->label('Subtítulo')
                    ->maxLength(200),
                FileUpload::make('background')
                    ->label('Imagen de Fondo')
                    ->image()
                    ->required()
                    ->directory('heroes'),
                TextInput::make('button_text')
                    ->label('Texto del Botón'),
                TextInput::make('button_url')
                    ->label('URL del Botón')
                    ->url(),
            ],

            self::REDBUS => [
                TextInput::make('widget_id')
                    ->label('ID del Widget')
                    ->required(),
                Select::make('position')
                    ->label('Posición')
                    ->options([
                        'left' => 'Izquierda',
                        'center' => 'Centro',
                        'right' => 'Derecha',
                    ])
                    ->default('center')
                    ->required(),
            ],

            self::CONTACT => [
                TextInput::make('title')
                    ->label('Título')
                    ->required(),
                RichEditor::make('description')
                    ->label('Descripción')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'bulletList',
                        'orderedList',
                    ]),
                Select::make('form_type')
                    ->label('Tipo de Formulario')
                    ->options([
                        'contact' => 'Contacto',
                        'quote' => 'Cotización',
                    ])
                    ->required(),
            ],

            self::SERVICES => [
                TextInput::make('title')
                    ->label('Título de Sección')
                    ->required(),
                RichEditor::make('description')
                    ->label('Descripción'),
                Repeater::make('services')
                    ->label('Servicios')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required(),
                        TextInput::make('icon')
                            ->label('Icono (clase)')
                            ->required(),
                        RichEditor::make('description')
                            ->label('Descripción')
                            ->toolbarButtons(['bold', 'italic', 'bulletList']),
                    ])
                    ->defaultItems(3)
                    ->maxItems(6),
            ],

            self::TEXT => [
                TextInput::make('title')
                    ->label('Título')
                    ->required(),
                RichEditor::make('content')
                    ->label('Contenido')
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'bulletList',
                        'orderedList',
                        'blockquote',
                    ]),
                Select::make('columns')
                    ->label('Columnas')
                    ->options([
                        1 => 'Una columna',
                        2 => 'Dos columnas',
                    ])
                    ->default(1),
            ],

            default => []
        };
    }

    public function getContentRelation(): string
    {
        return match($this) {
            self::REDBUS => 'redbus',
            self::SERVICES => 'services',
            self::TEXT => 'content',
            self::CUSTOM => 'custom',
            self::MENU => 'menu',
            self::SLIDER => 'slides',
            self::CONTENT => 'content',
            default => 'contents'
        };
    }

    public function getDefaultSettings(): array
    {
        return match($this) {
            self::HERO => [
                'full_height' => true,
                'overlay_opacity' => 50,
                'text_color' => 'white',
            ],
            self::REDBUS => [
                'container_class' => 'shadow-lg',
                'padding' => 'lg',
            ],
            self::SERVICES => [
                'columns' => 3,
                'show_icons' => true,
                'card_style' => 'shadow',
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

    public function getValidationRules(): array
    {
        return match($this) {
            self::HERO => [
                'title' => ['required', 'string', 'max:100'],
                'background' => ['required', 'image'],
                'button_url' => ['nullable', 'url'],
            ],
            self::REDBUS => [
                'widget_id' => ['required', 'string'],
                'position' => ['required', 'in:left,center,right'],
            ],
            default => []
        };
    }
}
