<?php

namespace App\Services\Components;

use App\Models\Section;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

abstract class BaseComponent
{
    protected $name;
    protected $icon;
    protected $description;
    protected $category;
    protected $fields = [];
    protected $defaultValues = [];

    abstract public function render(array $data): string;

    public function getName(): string
    {
        return $this->name;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }

    public function validate(array $data): bool
    {
        // Implementar validación basada en los campos definidos
        return true;
    }
}

class HeroComponent extends BaseComponent
{
    protected $name = 'Hero Banner';
    protected $icon = 'heroicon-o-photograph';
    protected $description = 'Sección principal con imagen de fondo y llamado a la acción';
    protected $category = 'header';

    protected $fields = [
        'title' => [
            'type' => 'text',
            'label' => 'Título',
            'required' => true
        ],
        'subtitle' => [
            'type' => 'text',
            'label' => 'Subtítulo'
        ],
        'background_image' => [
            'type' => 'image',
            'label' => 'Imagen de Fondo',
            'required' => true,
            'help' => 'Tamaño recomendado: 1920x800px'
        ],
        'cta_text' => [
            'type' => 'text',
            'label' => 'Texto del Botón'
        ],
        'cta_url' => [
            'type' => 'text',
            'label' => 'URL del Botón'
        ],
        'overlay_opacity' => [
            'type' => 'range',
            'label' => 'Opacidad del Overlay',
            'min' => 0,
            'max' => 100,
            'step' => 5,
            'default' => 50
        ]
    ];

    public function render(array $data): string
    {
        return view('components.hero', [
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? '',
            'background' => $data['background_image'],
            'ctaText' => $data['cta_text'] ?? '',
            'ctaUrl' => $data['cta_url'] ?? '#',
            'opacity' => $data['overlay_opacity'] ?? 50
        ])->render();
    }
}

class RedbusWidgetComponent extends BaseComponent
{
    protected $name = 'Redbus Widget';
    protected $icon = 'heroicon-o-ticket';
    protected $description = 'Widget de reservas de Redbus';
    protected $category = 'widgets';

    protected $fields = [
        'widget_id' => [
            'type' => 'text',
            'label' => 'ID del Widget',
            'required' => true
        ],
        'container_class' => [
            'type' => 'select',
            'label' => 'Estilo del Contenedor',
            'options' => [
                'default' => 'Por defecto',
                'shadow' => 'Con sombra',
                'border' => 'Con borde'
            ]
        ]
    ];

    protected $defaultValues = [
        'container_class' => 'default'
    ];

    public function render(array $data): string
    {
        return view('components.redbus-widget', [
            'widgetId' => $data['widget_id'],
            'containerClass' => $data['container_class'] ?? 'default'
        ])->render();
    }
}

// Servicio para gestionar componentes
class ComponentManager
{
    protected $components = [];

    public function __construct()
    {
        $this->registerCoreComponents();
    }

    protected function registerCoreComponents(): void
    {
        $this->register(new HeroComponent());
        $this->register(new RedbusWidgetComponent());
        // Registrar otros componentes core
    }

    public function register(BaseComponent $component): void
    {
        $this->components[get_class($component)] = $component;
    }

    public function getComponent(string $class): ?BaseComponent
    {
        return $this->components[$class] ?? null;
    }

    public function getAllComponents(): Collection
    {
        return collect($this->components);
    }

    public function getComponentsByCategory(string $category): Collection
    {
        return collect($this->components)->filter(function($component) use ($category) {
            return $component->getCategory() === $category;
        });
    }
}
