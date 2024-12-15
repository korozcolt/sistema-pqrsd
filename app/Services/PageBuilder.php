<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Section;
use App\Models\Content;

class PageBuilder
{
    public function getAvailableComponents(): array
    {
        return [
            'hero' => [
                'name' => 'Hero Section',
                'icon' => 'heroicon-o-photograph',
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Título'],
                    'subtitle' => ['type' => 'text', 'label' => 'Subtítulo'],
                    'background' => ['type' => 'image', 'label' => 'Imagen de fondo'],
                    'cta_text' => ['type' => 'text', 'label' => 'Texto del botón'],
                    'cta_url' => ['type' => 'text', 'label' => 'URL del botón']
                ]
            ],
            'text' => [
                'name' => 'Text Content',
                'icon' => 'heroicon-o-document-text',
                'fields' => [
                    'content' => ['type' => 'richtext', 'label' => 'Contenido'],
                    'columns' => ['type' => 'select', 'label' => 'Columnas', 'options' => [1, 2, 3]]
                ]
            ],
            'services' => [
                'name' => 'Services Grid',
                'icon' => 'heroicon-o-collection',
                'fields' => [
                    'services' => ['type' => 'repeater', 'label' => 'Servicios', 'fields' => [
                        'title' => ['type' => 'text', 'label' => 'Título'],
                        'description' => ['type' => 'textarea', 'label' => 'Descripción'],
                        'icon' => ['type' => 'icon', 'label' => 'Icono']
                    ]]
                ]
            ],
            'redbus_widget' => [
                'name' => 'RedBus Widget',
                'icon' => 'heroicon-o-ticket',
                'fields' => [
                    'widget_id' => ['type' => 'text', 'label' => 'ID del Widget'],
                    'position' => ['type' => 'select', 'label' => 'Posición', 'options' => ['left', 'center', 'right']]
                ]
            ]
        ];
    }

    public function renderSection(Section $section): string
    {
        // Renderizar la sección basada en su tipo y configuración
        $component = $section->type->getViewComponent();
        $content = $section->getContent();

        return view($component, [
            'content' => $content,
            'config' => $section->config,
            'styles' => $section->styles
        ])->render();
    }

    public function createSection(Page $page, array $data): Section
    {
        return $page->sections()->create([
            'name' => $data['name'],
            'type' => $data['type'],
            'config' => $data['config'] ?? [],
            'styles' => $data['styles'] ?? [],
            'order' => $data['order'] ?? 0
        ]);
    }
}
