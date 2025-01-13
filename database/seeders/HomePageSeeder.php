<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Section;
use App\Enums\{StatusGlobal, SectionType};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        // Crear página de inicio
        $page = Page::create([
            'title' => 'Inicio',
            'slug' => '_home',
            'meta_description' => 'Bienvenido a Torcoroma S.A. - Empresa líder en transporte terrestre',
            'meta_keywords' => 'transporte, buses, colombia, costa caribe, torcoroma',
            'layout' => 'default',
            'settings' => [
                'show_header' => true,
                'show_footer' => true,
            ],
            'og_data' => [
                'title' => 'Inicio - Torcoroma S.A.',
                'description' => 'Líderes en transporte terrestre en la costa caribe colombiana desde 1953.',
            ],
            'searchable' => true,
            'status' => StatusGlobal::Active,
            'order' => 1,
            'theme_id' => null
        ]);

        // Crear todas las secciones
        $sections = [
            // Hero Slider
            [
                'name' => 'Hero Slider',
                'identifier' => 'hero-slider',
                'slug' => Str::slug('hero-slider'),
                'type' => SectionType::HERO,
                'position' => 'header',
                'order' => 1,
                'is_active' => true,
                'config' => json_encode([
                    'slides' => [
                        [
                            'title' => 'Registra tu destino',
                            'subtitle' => 'Aquí puedes viajar con nosotros',
                            'background' => 'item-bg1'
                        ],
                        [
                            'title' => 'Servicio de Transporte',
                            'subtitle' => 'Desde 1953',
                            'description' => 'Contamos con el personal logístico e idoneo para las tareas de transporte en toda la región caribe.',
                            'button_text' => 'Contactanos',
                            'button_url' => '/contact',
                            'background' => 'item-bg2'
                        ],
                        [
                            'title' => 'Nuestro Equipo de Trabajo',
                            'subtitle' => 'Desde 1953',
                            'description' => 'Contando con el personal idoneo para entregale el mejor de los servicios.',
                            'background' => 'item-bg3'
                        ]
                    ]
                ]),
            ],

            // Información de Contacto
            [
                'name' => 'Información de Contacto',
                'identifier' => 'contact-info',
                'slug' => Str::slug('contact-info'),
                'type' => SectionType::FEATURES,
                'position' => 'content',
                'order' => 2,
                'is_active' => true,
                'config' => json_encode([
                    'items' => [
                        [
                            'title' => 'Numeros de Contacto',
                            'icon' => 'bxs-phone',
                            'links' => [
                                ['text' => config('site.company.contact.phones.main'), 'url' => 'tel:' . config('site.company.contact.phones.main')],
                                ['text' => config('site.company.contact.phones.secondary'), 'url' => 'tel:' . config('site.company.contact.phones.secondary')]
                            ]
                        ],
                        [
                            'title' => 'Oficinas Principales',
                            'icon' => 'bxs-location-plus',
                            'description' => config('site.company.contact.address')
                        ],
                        [
                            'title' => 'Abiertos desde',
                            'icon' => 'bx-show',
                            'description' => config('site.company.schedules')
                        ],
                        [
                            'title' => 'Nuestro Email',
                            'icon' => 'bxs-envelope',
                            'links' => [
                                ['text' => config('site.company.contact.emails.main'), 'url' => 'mailto:' . config('site.company.contact.emails.main')],
                                ['text' => config('site.company.contact.emails.secondary'), 'url' => 'mailto:' . config('site.company.contact.emails.secondary')]
                            ]
                        ]
                    ]
                ]),
            ],

            // Sección About
            [
                'name' => 'Acerca de',
                'identifier' => 'about-area',
                'slug' => Str::slug('about-area'),
                'type' => SectionType::TEXT,
                'position' => 'content',
                'order' => 3,
                'is_active' => true,
                'config' => json_encode([
                    'title' => 'Un servicio de transporte seguro y rápido para usted',
                    'subtitle' => 'Acerca de Nosotros',
                    'content' => 'Somos una empresa dedicada al transporte terrestre de pasajeros, con servicios especializados y en distintas modalidades.',
                    'description' => 'Como empresa socialmente responsable, promueve entre sus grupos de interés el respeto y fomento de los Derechos Humanos. Es así como se difunde información a través de medios internos y externos.',
                    'button_text' => 'Aprende más',
                    'button_url' => '/about',
                    'tabs' => [
                        [
                            'title' => 'Visión',
                            'content' => [
                                'Garantizar un optimo servicio de transporte terrestre de pasajeros en la modalidad basica ccoriente y especial.',
                                'Enfocado a la satisfacción de las necesidades y expectativas de sus asociados, clientes y usuarios.',
                                'Contribuir de manera activa al desarrollo sostenible de nuestra región con un servicio de maxima calidad.',
                                'Entregar todo a nuestro alcance para que nuestros usuarios y clientes se sientan satisfechos.'
                            ]
                        ],
                        [
                            'title' => 'Misión',
                            'content' => [
                                'Para 2022 ser una empresa que brinde a sus clientes y usuarios la mejor opción de tansporte terrestre.',
                                'Ademas, ser la mejor opción en modalidad de pasajeros basica, corriente y especial.',
                                'Enfocados en la aplicación de estandares de calidad y seguridad que garantize oportunidad y confort.'
                            ]
                        ],
                        [
                            'title' => 'Valores',
                            'content' => [
                                'Somos personas honestas.',
                                'Inspiramos confianza.',
                                'Somos creativos.',
                                'Nos relacionamos con respeto.',
                                'Estamos comprometidos con el servicio.'
                            ]
                        ]
                    ]
                ]),
            ],

            // Servicios
            [
                'name' => 'Servicios',
                'identifier' => 'services-area',
                'slug' => Str::slug('services-area'),
                'type' => SectionType::FEATURES,
                'position' => 'content',
                'order' => 4,
                'is_active' => true,
                'config' => json_encode([
                    'title' => 'Nuestros Servicios',
                    'subtitle' => 'Servicios logísticos seguros y rápidos',
                    'items' => [
                        [
                            'title' => 'Equipaje',
                            'icon' => 'bx-briefcase-alt',
                            'description' => 'Contamos con compartimentos especiales para que sus pertenencias viajen seguras y lleguen en perfectas condiciones a su lugar de destino.'
                        ],
                        [
                            'title' => 'Cobertura',
                            'icon' => 'bx-shape-circle',
                            'description' => 'Operamos en la costa caribe Colombiana con más de 30 rutas cumpliendo los sueños e ilusiones de todos nuestros clientes.'
                        ]
                    ]
                ]),
            ],

            // Widget RedBus
            [
                'name' => 'Reserva de Viajes',
                'identifier' => 'booking-widget',
                'slug' => Str::slug('booking-widget'),
                'type' => SectionType::REDBUS,
                'position' => 'content',
                'order' => 5,
                'is_active' => true,
                'config' => json_encode([
                    'widget_id' => 'TORCOROMA'
                ]),
            ],

            // Choose Area
            [
                'name' => '¿Por qué escogernos?',
                'identifier' => 'choose-area',
                'slug' => Str::slug('choose-area'),
                'type' => SectionType::FEATURES,
                'position' => 'content',
                'order' => 6,
                'is_active' => true,
                'config' => json_encode([
                    'title' => '¿Por qué escogernos?',
                    'subtitle' => 'Un servicio logístico seguro y rápido para usted',
                    'description' => [
                        'En nuestra empresa de transporte terrestre, nos enorgullece ofrecer un servicio de entrega rápido y seguro para nuestros clientes en la costa caribe colombiana. Nuestro equipo altamente capacitado y nuestra flota de vehículos moderna nos permiten garantizar que tus productos lleguen a su destino de manera eficiente y sin contratiempos.',
                        'Nuestro compromiso con la excelencia nos distingue de la competencia. Trabajamos arduamente para asegurarnos de que cada entrega se realice a tiempo y en perfectas condiciones.'
                    ],
                    'features' => [
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
                    ],
                    'button_text' => 'Contactanos',
                    'button_url' => '#'
                ]),
            ],
        ];

        foreach ($sections as $sectionData) {
            $page->sections()->create($sectionData);
        }
    }
}
