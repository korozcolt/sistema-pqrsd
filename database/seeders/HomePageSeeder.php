<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Page, Section, Content, ContentType, Theme, Widget};
use App\Enums\{ContentStatus, ComponentType, StatusGlobal};
use Carbon\Carbon;
use Illuminate\Support\Str;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $theme = Theme::create([
            'name' => 'Tema Principal',
            'slug' => 'tema-principal',
            'settings' => json_encode([
                'primary_color' => '#007bff',
                'secondary_color' => '#6c757d',
                'header_style' => 'default',
                'footer_style' => 'default',
                'font_family' => 'Inter'
            ]),
            'status' => StatusGlobal::Active,
            'is_default' => true
        ]);

        // 2. Crear la página de inicio
        $homePage = Page::create([
            'title' => 'Inicio',
            'slug' => '_home',
            'meta_description' => 'Empresa de transporte terrestre con más de 70 años de experiencia brindando servicios de calidad en la costa caribe colombiana',
            'meta_keywords' => 'transporte, buses, costa caribe, colombia, viajes, pasajes, turismo',
            'layout' => 'default',
            'searchable' => true,
            'status' => StatusGlobal::Active,
            'order' => 1,
            'theme_id' => $theme->id,
            'og_data' => json_encode([
                'og:title' => 'Torcoroma - Transporte Terrestre',
                'og:description' => 'Servicios de transporte terrestre en la costa caribe colombiana',
                'og:type' => 'website',
                'og:image' => asset('images/og-image.jpg'),
            ]),
            'settings' => json_encode([
                'show_breadcrumbs' => false,
                'show_title' => false,
                'header_transparent' => true,
            ])
        ]);

        // 3. Crear tipo de contenido para el slider
        $sliderType = ContentType::create([
            'name' => 'Hero Slider',
            'slug' => 'hero-slider',
            'component_type' => ComponentType::Slider,
            'schema' => json_encode([
                'title' => 'string',
                'subtitle' => 'string',
                'background_image' => 'string',
                'button_text' => 'string',
                'button_url' => 'string'
            ]),
            'status' => StatusGlobal::Active
        ]);

        // 4. Crear sección del Hero Slider
        $heroSection = Section::create([
            'name' => 'Hero Slider',
            'slug' => Str::slug('Hero Slider'),
            'identifier' => 'hero-slider',
            'position' => 'main',
            'page_id' => $homePage->id,
            'settings' => json_encode([
                'full_width' => true,
                'height' => 'full',
                'animation' => 'fade'
            ]),
            'order' => 1,
            'is_active' => true
        ]);

        // 5. Crear los slides
        $slides = [
            [
                'title' => 'Registra tu destino',
                'subtitle' => 'Aquí puedes viajar con nosotros',
                'background_image' => asset('images/slider/slide1.jpg'),
            ],
            [
                'title' => 'Servicio de Transporte',
                'subtitle' => 'Desde 1953',
                'content' => 'Contamos con el personal logístico e idoneo para las tareas de transporte en toda la región caribe.',
                'background_image' => asset('images/slider/slide2.jpg'),
                'button_text' => 'Contactanos',
                'button_url' => route('page', 'contact')
            ],
            [
                'title' => 'Nuestro Equipo de Trabajo',
                'subtitle' => 'Desde 1953',
                'content' => 'Contando con el personal idoneo para entregale el mejor de los servicios.',
                'background_image' => asset('images/slider/slide3.jpg'),
            ]
        ];

        // 6. Crear el contenido de los slides
        foreach ($slides as $index => $slide) {
            Content::create([
                'content_type_id' => $sliderType->id,
                'section_id' => $heroSection->id,
                'content' => json_encode($slide),
                'order' => $index + 1,
                'status' => ContentStatus::Published,
                'published_at' => Carbon::now(),
                'is_active' => true
            ]);
        }

        // 7. Crear tipo de contenido para las Info Cards
        $infoCardType = ContentType::create([
            'name' => 'Info Card',
            'slug' => 'info-card',
            'component_type' => ComponentType::Widget,
            'schema' => json_encode([
                'icon' => 'string',
                'title' => 'string',
                'content' => 'string'
            ]),
            'status' => StatusGlobal::Active
        ]);

        // 8. Crear sección de contacto
        $contactSection = Section::create([
            'name' => 'Información de Contacto',
            'slug' => Str::slug('Información de Contacto'),
            'identifier' => 'contact-info',
            'position' => 'main',
            'page_id' => $homePage->id,
            'settings' => json_encode([
                'background' => 'white',
                'spacing' => 'medium',
                'container' => true
            ]),
            'order' => 2,
            'is_active' => true
        ]);

        // 9. Crear tarjetas de información
        $infoCards = [
            [
                'icon' => 'bxs-phone',
                'title' => 'Números de Contacto',
                'content' => config('site.company.contact.phones.main') . "\n" . config('site.company.contact.phones.secondary'),
            ],
            [
                'icon' => 'bxs-location-plus',
                'title' => 'Oficinas Principales',
                'content' => config('site.company.contact.address'),
            ],
            [
                'icon' => 'bx-show',
                'title' => 'Abiertos desde',
                'content' => config('site.company.schedules'),
            ],
            [
                'icon' => 'bxs-envelope',
                'title' => 'Nuestro Email',
                'content' => config('site.company.contact.emails.main') . "\n" . config('site.company.contact.emails.secondary'),
            ]
        ];

        // 10. Crear el contenido de las tarjetas
        foreach ($infoCards as $index => $card) {
            Content::create([
                'content_type_id' => $infoCardType->id,
                'section_id' => $contactSection->id,
                'content' => json_encode($card),
                'order' => $index + 1,
                'status' => ContentStatus::Published,
                'published_at' => Carbon::now(),
                'is_active' => true
            ]);
        }

        // 11. Crear tipo de contenido para About con tabs
        $aboutType = ContentType::create([
            'name' => 'About With Tabs',
            'slug' => 'about-with-tabs',
            'component_type' => ComponentType::Widget,
            'schema' => json_encode([
                'title' => 'string',
                'description' => 'string',
                'image' => 'string',
                'tabs' => 'array'
            ]),
            'status' => StatusGlobal::Active
        ]);

        // 12. Crear sección About
        $aboutSection = Section::create([
            'name' => 'Acerca de Nosotros',
            'slug' => Str::slug('Acerca de Nosotros'),
            'identifier' => 'about',
            'position' => 'main',
            'page_id' => $homePage->id,
            'settings' => json_encode([
                'background' => 'light',
                'spacing' => 'large',
                'container' => true
            ]),
            'order' => 3,
            'is_active' => true
        ]);

        // 13. Crear contenido de About
        Content::create([
            'content_type_id' => $aboutType->id,
            'section_id' => $aboutSection->id,
            'content' => json_encode([
                'title' => 'Un servicio de transporte seguro y rápido para usted',
                'description' => 'Somos una empresa dedicada al transporte terrestre de pasajeros, con servicios especializados y en distintas modalidades.',
                'image' => asset('images/about-page.webp'),
                'tabs' => [
                    'vision' => [
                        'title' => 'Visión',
                        'items' => [
                            'Garantizar un optimo servicio de transporte terrestre de pasajeros',
                            'Enfocado a la satisfacción de las necesidades',
                            'Contribuir de manera activa al desarrollo sostenible',
                            'Entregar todo a nuestro alcance para la satisfacción'
                        ]
                    ],
                    'mission' => [
                        'title' => 'Misión',
                        'items' => [
                            'Ser la mejor empresa de transporte terrestre',
                            'Ser la mejor opción en modalidad de pasajeros',
                            'Enfocados en la aplicación de estándares de calidad'
                        ]
                    ],
                    'values' => [
                        'title' => 'Valores',
                        'items' => [
                            'Somos personas honestas',
                            'Inspiramos confianza',
                            'Somos creativos',
                            'Nos relacionamos con respeto',
                            'Estamos comprometidos con el servicio'
                        ]
                    ]
                ]
            ]),
            'order' => 1,
            'status' => ContentStatus::Published,
            'published_at' => Carbon::now(),
            'is_active' => true
        ]);

        // 14. Crear Widget de RedBus
        Widget::create([
            'name' => 'RedBus Booking',
            'slug' => 'redbus-booking',
            'type' => ComponentType::Widget,
            'content' => json_encode([
                'widget_id' => 'TORCOROMA',
                'position' => 'hero',
            ]),
            'settings' => json_encode([
                'load_external_script' => true,
                'script_url' => 'https://wl.redbus.com/javascripts/widget.min.js',
            ]),
            'status' => StatusGlobal::Active,
            'display_rules' => json_encode([
                'pages' => ['_home'],
                'position' => 'after_hero'
            ]),
            'order' => 1,
            'is_active' => true
        ]);

        // 15. Crear tipo de contenido para los servicios
        $serviceType = ContentType::create([
            'name' => 'Service Card',
            'slug' => 'service-card',
            'component_type' => ComponentType::Widget,
            'schema' => json_encode([
                'icon' => 'string',
                'title' => 'string',
                'description' => 'string'
            ]),
            'status' => StatusGlobal::Active
        ]);

        // 16. Crear sección de servicios
        $servicesSection = Section::create([
            'name' => 'Nuestros Servicios',
            'slug' => Str::slug('Nuestros Servicios'),
            'identifier' => 'services',
            'position' => 'main',
            'page_id' => $homePage->id,
            'settings' => json_encode([
                'background' => 'white',
                'spacing' => 'large',
                'container' => true,
                'columns' => 2
            ]),
            'order' => 4,
            'is_active' => true
        ]);

        // 17. Crear contenido de servicios
        $services = [
            [
                'icon' => 'bx-briefcase-alt',
                'title' => 'Equipaje',
                'description' => 'Contamos con compartimentos especiales para que sus pertenencias viajen seguras.'
            ],
            [
                'icon' => 'bx-shape-circle',
                'title' => 'Cobertura',
                'description' => 'Operamos en la costa caribe Colombiana con más de 30 rutas cumpliendo los sueños e ilusiones.'
            ]
        ];

        foreach ($services as $index => $service) {
            Content::create([
                'content_type_id' => $serviceType->id,
                'section_id' => $servicesSection->id,
                'content' => json_encode($service),
                'order' => $index + 1,
                'status' => ContentStatus::Published,
                'published_at' => Carbon::now(),
                'is_active' => true
            ]);
        }

        // 18. Crear tipo de contenido para la flota
        $fleetType = ContentType::create([
            'name' => 'Fleet Showcase',
            'slug' => 'fleet-showcase',
            'component_type' => ComponentType::Widget,
            'schema' => json_encode([
                'title' => 'string',
                'description' => 'string',
                'images' => 'array',
                'additional_info' => 'string'
            ]),
            'status' => StatusGlobal::Active
        ]);

        // 19. Crear sección de flota
        $fleetSection = Section::create([
            'name' => 'Nuestra Flota',
            'slug' => Str::slug('Nuestra Flota'),
            'identifier' => 'fleet',
            'position' => 'main',
            'page_id' => $homePage->id,
            'settings' => json_encode([
                'background' => 'white',
                'spacing' => 'large',
                'container' => true,
                'layout' => 'alternating'
            ]),
            'order' => 6,
            'is_active' => true
        ]);

        // 20. Crear contenido de flota
        Content::create([
            'content_type_id' => $fleetType->id,
            'section_id' => $fleetSection->id,
            'content' => json_encode([
                'title' => 'Conoce nuestra flota de buses',
                'description' => 'En nuestra empresa de transporte terrestre, nos enorgullece contar con una flota de buses moderna y segura que cumple con todas las medidas de seguridad. Nuestros buses están equipados con dispositivos tecnológicos avanzados que garantizan un viaje cómodo y seguro para nuestros pasajeros.',
                'images' => [
                    asset('images/buses_1.webp'),
                    asset('images/buses_2.webp')
                ],
                'additional_info' => 'Nuestros conductores altamente capacitados están comprometidos con la seguridad y el bienestar de nuestros pasajeros. Trabajamos arduamente para asegurarnos de que cada viaje sea una experiencia agradable y sin contratiempos.'
            ]),
            'order' => 1,
            'status' => ContentStatus::Published,
            'published_at' => Carbon::now(),
            'is_active' => true
        ]);

        // 21. Crear sección "Por qué escogernos"
        $chooseSection = Section::create([
            'name' => '¿Por qué escogernos?',
            'slug' => Str::slug('Por qué escogernos'),
            'identifier' => 'why-choose-us',
            'position' => 'main',
            'page_id' => $homePage->id,
            'settings' => json_encode([
                'background' => 'light',
                'spacing' => 'large',
                'container' => true
            ]),
            'order' => 5,
            'is_active' => true
        ]);

        // 22. Crear contenido de "Por qué escogernos"
        $reasons = [
            [
                'icon' => 'bx-world',
                'title' => 'Servicio al instante',
                'description' => 'Toda una región cubierta y brindando garantías de un servicio impecable.'
            ],
            [
                'icon' => 'bxs-paper-plane',
                'title' => 'Servicio de rastreo',
                'description' => 'Podemos rastrear en tiempo real donde se encuentra su mercancía.'
            ],
            [
                'icon' => 'bxs-truck',
                'title' => 'Rapido y totalmente seguro',
                'description' => 'No tenemos demoras prestando nuestros servicios de transporte.'
            ],
            [
                'icon' => 'bx-support',
                'title' => 'Soporte 24/7',
                'description' => 'Por medio de nuestra web podemos brindarle servicio 24/7.'
            ]
        ];

        foreach ($reasons as $index => $reason) {
            Content::create([
                'content_type_id' => $serviceType->id, // Reutilizamos el tipo de servicio ya que tiene la misma estructura
                'section_id' => $chooseSection->id,
                'content' => json_encode($reason),
                'order' => $index + 1,
                'status' => ContentStatus::Published,
                'published_at' => Carbon::now(),
                'is_active' => true
            ]);
        }
    }
}
