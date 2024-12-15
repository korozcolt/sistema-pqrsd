<?php

namespace App\Services\Templates;

use App\Models\Page;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

class PageTemplate
{
    protected $name;
    protected $description;
    protected $thumbnail;
    protected $sections = [];

    public function __construct(string $name, string $description, string $thumbnail)
    {
        $this->name = $name;
        $this->description = $description;
        $this->thumbnail = $thumbnail;
    }

    public function addSection(array $sectionData): self
    {
        $this->sections[] = $sectionData;
        return $this;
    }

    public function apply(Page $page): void
    {
        DB::transaction(function() use ($page) {
            // Limpiar secciones existentes si las hay
            $page->sections()->delete();

            // Crear nuevas secciones basadas en la plantilla
            foreach ($this->sections as $index => $sectionData) {
                $page->sections()->create([
                    'name' => $sectionData['name'],
                    'type' => $sectionData['type'],
                    'content' => $sectionData['content'] ?? [],
                    'config' => $sectionData['config'] ?? [],
                    'order' => $index * 10
                ]);
            }
        });
    }
}

class TemplateManager
{
    protected $templates = [];

    public function __construct()
    {
        $this->registerDefaultTemplates();
    }

    protected function registerDefaultTemplates(): void
    {
        // Plantilla Homepage
        $homepageTemplate = new PageTemplate(
            'Homepage',
            'Plantilla para página de inicio con hero, servicios y widget de Redbus',
            'templates/homepage.jpg'
        );

        $homepageTemplate
            ->addSection([
                'name' => 'Hero Principal',
                'type' => 'hero',
                'content' => [
                    'title' => 'Bienvenidos a Torcoroma',
                    'subtitle' => 'Tu mejor opción en transporte terrestre',
                    'background_image' => '/images/default-hero.jpg'
                ],
                'config' => [
                    'full_height' => true,
                    'overlay_opacity' => 50
                ]
            ])
            ->addSection([
                'name' => 'Redbus Widget',
                'type' => 'redbus_widget',
                'config' => [
                    'container_class' => 'shadow'
                ]
            ])
            ->addSection([
                'name' => 'Servicios Destacados',
                'type' => 'services_grid',
                'content' => [
                    'title' => 'Nuestros Servicios',
                    'services' => [
                        [
                            'title' => 'Transporte de Pasajeros',
                            'description' => 'Servicio seguro y confortable',
                            'icon' => 'bus'
                        ],
                        [
                            'title' => 'Equipaje Seguro',
                            'description' => 'Control y seguridad de su equipaje',
                            'icon' => 'luggage'
                        ]
                    ]
                ]
            ]);

        $this->templates['homepage'] = $homepageTemplate;

        // Plantilla Página de Contacto
        $contactTemplate = new PageTemplate(
            'Contacto',
            'Plantilla para página de contacto con formulario y mapa',
            'templates/contact.jpg'
        );

        $contactTemplate
            ->addSection([
                'name' => 'Información de Contacto',
                'type' => 'contact_info',
                'content' => [
                    'title' => 'Contáctanos',
                    'description' => 'Estamos aquí para ayudarte'
                ]
            ])
            ->addSection([
                'name' => 'Formulario de Contacto',
                'type' => 'contact_form',
                'config' => [
                    'show_recaptcha' => true,
                    'email_to' => 'contacto@torcoroma.com'
                ]
            ]);

        $this->templates['contact'] = $contactTemplate;
    }

    public function getTemplate(string $name): ?PageTemplate
    {
        return $this->templates[$name] ?? null;
    }

    public function getAllTemplates(): array
    {
        return $this->templates;
    }
}
