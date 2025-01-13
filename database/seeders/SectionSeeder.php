<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\Section;
use App\Models\Theme;
use App\Enums\SectionType;
use App\Enums\StatusGlobal;
use Illuminate\Support\Str;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $homePage = Page::where('slug', '_home')->firstOrFail();

        $sections = [
            [
                'name' => 'Hero Slider',
                'slug' => 'hero-slider',
                'identifier' => 'hero_slider',
                'type' => SectionType::SLIDER,
                'position' => 'header',
                'order' => 1,
                'is_active' => true,
                'content' => $this->getHeroSliderContent(),
                'config' => [
                    'autoplay' => true,
                    'interval' => 5000,
                    'slides' => [
                        [
                            'title' => 'Registra tu destino',
                            'background' => 'item-bg1'
                        ],
                        [
                            'title' => 'Servicio de Transporte',
                            'subtitle' => 'Desde 1953',
                            'description' => 'Contamos con el personal logístico e idoneo para las tareas de transporte en toda la región caribe.',
                            'background' => 'item-bg2'
                        ],
                        [
                            'title' => 'Nuestro Equipo de Trabajo',
                            'subtitle' => 'Desde 1953',
                            'description' => 'Contando con el personal idoneo para entregale el mejor de los servicios.',
                            'background' => 'item-bg3'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Contact Info',
                'slug' => 'contact-info',
                'identifier' => 'contact_info',
                'type' => SectionType::CONTENT,
                'position' => 'content',
                'order' => 2,
                'is_active' => true,
                'content' => $this->getContactInfoContent()
            ],
            [
                'name' => 'About Section',
                'slug' => 'about-section',
                'identifier' => 'about_section',
                'type' => SectionType::CONTENT,
                'position' => 'content',
                'order' => 3,
                'is_active' => true,
                'content' => $this->getAboutContent()
            ],
            [
                'name' => 'Services Section',
                'slug' => 'services-section',
                'identifier' => 'services_section',
                'type' => SectionType::CONTENT,
                'position' => 'content',
                'order' => 4,
                'is_active' => true,
                'content' => $this->getServicesContent()
            ],
            [
                'name' => 'Book Tickets',
                'slug' => 'book-tickets',
                'identifier' => 'book_tickets',
                'type' => SectionType::WIDGET,
                'position' => 'content',
                'order' => 5,
                'is_active' => true,
                'content' => '',
                'config' => [
                    'widget_id' => 'TORCOROMA',
                    'position' => 'center',
                    'width' => '80%'
                ]
            ]
        ];

        foreach ($sections as $sectionData) {
            Section::updateOrCreate(
                [
                    'page_id' => $homePage->id,
                    'identifier' => $sectionData['identifier']
                ],
                $sectionData
            );
        }
    }

    private function getHeroSliderContent(): string
    {
        return <<<'HTML'
<div class="hero-slider owl-carousel owl-theme">
    <div class="hero-slider-item item-bg1">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="container">
                            <div class="banner-content">
                                <h1>Registra tu destino</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="hero-slider-item item-bg2">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="banner-content">
                                <span>Desde 1953</span>
                                <h1>Servicio de Transporte</h1>
                                <p>Contamos con el personal logístico e idoneo para las tareas de transporte en toda la región caribe.</p>
                                <a href="{{ route('page', 'contact') }}" class="default-btn-one me-3">Contactanos</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="hero-slider-item item-bg3">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="banner-content">
                        <span>Desde 1953</span>
                        <h1>Nuestro<br> Equipo de Trabajo</h1>
                        <p>Contando con el personal idoneo para entregale el mejor de los servicios.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;
    }

    private function getContactInfoContent(): string
    {
        return <<<'HTML'
<div class="contact-area mb-85">
    <div class="contact-content">
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="contact-card">
                    <i class='bx bxs-phone'></i>
                    <h4>Numeros de Contacto</h4>
                    <p><a href="tel:{{ $info->phone ?? '' }}">{{ $info->phone ?? '' }}</a></p>
                    <p><a href="tel:{{ $info->phone2 ?? '' }}">{{ $info->phone2 ?? '' }}</a></p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="contact-card">
                    <i class='bx bxs-location-plus'></i>
                    <h4>Oficinas Principales</h4>
                    <p>{{ $info->address ?? '' }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="contact-card">
                    <i class='bx bx-show'></i>
                    <h4>Abiertos desde</h4>
                    <p>{{ $info->schedules ?? '' }}</p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="contact-card">
                    <i class='bx bxs-envelope'></i>
                    <h4>Nuestro Email</h4>
                    <p><a href="mailto:{{ $info->email ?? '' }}">{{ $info->email ?? '' }}</a></p>
                    <p><a href="mailto:{{ $info->email2 ?? '' }}">{{ $info->email2 ?? '' }}</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;
    }

    private function getAboutContent(): string
    {
        return <<<'HTML'
<div class="about-area pb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="about-contant">
                    <div class="section-title">
                        <span>Acerca de Nosotros</span>
                        <h2>Un servicio de transporte seguro y rápido para usted</h2>
                    </div>

                    <div class="about-text">
                        <p>Somos una empresa dedicada al transporte terrestre de pasajeros, con servicios especializados y en distintas modalidades.</p>
                        <p>Como empresa socialmente responsable, promueve entre sus grupos de interés el respeto y fomento de los Derechos Humanos. Es así como se difunde información a través de medios internos y externos.</p>
                        <a href="{{ route('page', 'about') }}" class="default-btn-one btn-bs">Aprende más</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="about-image">
                    <img src="{{ asset('images/about-page.webp') }}" alt="about-image">
                </div>
            </div>

            <div class="col-lg-3">
                <div class="about-tabs">
                    <div class="tab-contant">
                        <h2 class="title">¡Torcoroma somos todos!</h2>
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-link active" id="nav-vision-tab" data-bs-toggle="tab" href="#nav-vision" role="tab" aria-controls="nav-vision" aria-selected="true">Visión</a>
                                <a class="nav-link nav-link-two" id="nav-mission-tab" data-bs-toggle="tab" href="#nav-mission" role="tab" aria-controls="nav-mission" aria-selected="false">Misión</a>
                                <a class="nav-link nav-link-two" id="nav-value-tab" data-bs-toggle="tab" href="#nav-value" role="tab" aria-controls="nav-value" aria-selected="false">Valores</a>
                            </div>
                        </nav>

                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-vision" role="tabpanel" aria-labelledby="nav-vision-tab">
                                <div class="vision">
                                    <ul>
                                        <li><i class='bx bx-check'></i>Garantizar un optimo servicio de transporte terrestre de pasajeros en la modalidad basica ccoriente y especial.</li>
                                        <li><i class='bx bx-check'></i>Enfocado a la satisfacción de las necesidades y expectativas de sus asociados, clientes y usuarios.</li>
                                        <li><i class='bx bx-check'></i>Contribuir de manera activa al desarrollo sostenible de nuestra región con un servicio de maxima calidad.</li>
                                        <li><i class='bx bx-check'></i>Entregar todo a nuestro alcance para que nuestros usuarios y clientes se sientan satisfechos.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;
    }

    private function getServicesContent(): string
    {
        return <<<'HTML'
<div class="services-area ptb-100">
    <div class="container">
        <div class="section-title">
            <span>Nuestros Servicios</span>
            <h2>Servicios logísticos seguros y rápidos</h2>
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="service-card">
                    <i class='bx bx-briefcase-alt'></i>
                    <h3>Equipaje</h3>
                    <p>Contamos con compartimentos especiales para que sus pertenencias viajen seguras y lleguen en perfectas condiciones a su lugar de destino.</p>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="service-card">
                    <i class='bx bx-shape-circle'></i>
                    <h3>Cobertura</h3>
                    <p>Operamos en la costa caribe Colombiana con más de 30 rutas cumpliendo los sueños e ilusiones de todos nuestros clientes.</p>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;
    }
}
