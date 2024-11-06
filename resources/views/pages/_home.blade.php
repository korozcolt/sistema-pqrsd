@extends('layouts.page')
@section('content-page')

    <!-- Hero Slider -->
    <div class="hero-slider owl-carousel owl-theme">
        <div class="hero-slider-item item-bg1">
            <div class="d-table">
                <div class="d-table-cell">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="">
                                <div class="container">
                                    <div class="banner-content">
                                        <h1>Registra tu destino</h1>
                                        <!--<h2>Aquí puedes viajar con nosotros</h2>-->

                                    </div>
                                </div>
                            </div>
                            <div class="widget" data-widgetid="TORCOROMA"></div>
                            <!--<div class="col-lg-6">
                                                                                                                                            <div class="order-form">
                                                                                                                                                <h2>REVISA TU RUTA</h2>
                                                                                                                                                <form>
                                                                                                                                                    <div class="form-group">
                                                                                                                                                        <input type="text" class="form-control" placeholder="Origen">
                                                                                                                                                    </div>

                                                                                                                                                    <div class="form-group">
                                                                                                                                                        <input type="text" class="form-control" placeholder="Destino">
                                                                                                                                                    </div>

                                                                                                                                                    <div class="text-center">
                                                                                                                                                        <button type="submit" class="default-btn-one">Revisar</button>
                                                                                                                                                    </div>
                                                                                                                                                </form>
                                                                                                                                            </div>
                                                                                                                                        </div>-->

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-slider-item item-bg2">

            <div class="d-table">
                <div class="d-table-cell">
                    <div class="d-table">
                        <div class="d-table-cell">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-lg-12">
                                        <div class="banner-content">
                                            <span>Desde 1953</span>
                                            <h1> Servicio de Transporte </h1>
                                            <p>Contamos con el personal logístico e idoneo para las tareas de transporte en
                                                toda la región caribe.</p>
                                            <a href="{{ route('page', 'contact') }}"
                                                class="default-btn-one me-3">Contactanos</a>
                                        </div>
                                    </div>
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
                            <h1>Nuestro<br> Equipo de Trabajo</h1->
                                <p>Contando con el personal idoneo para entregale el mejor de los servicios.</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Slider -->

    <!-- Contact Area -->
    <div class="container">
        <div class="contact-area mb-85">
            <div class="contact-content">
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="contact-card">
                            <i class='bx bxs-phone'></i>
                            <h4>Numeros de Contacto</h4>
                            <p>
                                <a href="tel:{{ $info->phone ?? '' }}">{{ $info->phone ?? '' }}</a>
                            </p>
                            <p>
                                <a href="tel:{{ $info->phone2 ?? '' }}"> {{ $info->phone2 ?? '' }}</a>
                            </p>
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
                            <p>
                                <a href="mailto:{{ $info->email ?? '' }}">{{ $info->email ?? '' }}</a>
                            </p>
                            <p>
                                <a href="mailto:{{ $info->email ?? '' }}">{{ $info->email2 ?? '' }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Contact Area -->

    <!-- About Area -->
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
                            <p>Somos una empresa dedicada al transporte terrestre de pasajeros, con servicios especializados
                                y en distintas modalidades.</p>

                            <p>Como empresa socialmente responsable, promueve entre sus grupos de interés el respeto y
                                fomento de los Derechos Humanos. Es así como se difunde información a través de medios
                                internos y externos..</p>

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
                                    <a class="nav-link active" id="nav-vision-tab" data-bs-toggle="tab" href="#nav-vision"
                                        role="tab" aria-controls="nav-vision" aria-selected="true">Visión</a>
                                    <a class="nav-link nav-link-two" id="nav-mission-tab" data-bs-toggle="tab"
                                        href="#nav-mission" role="tab" aria-controls="nav-mission"
                                        aria-selected="false">Misión</a>
                                    <a class="nav-link nav-link-two" id="nav-value-tab" data-bs-toggle="tab"
                                        href="#nav-value" role="tab" aria-controls="nav-value"
                                        aria-selected="false">Valores</a>
                                </div>
                            </nav>

                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-vision" role="tabpanel"
                                    aria-labelledby="nav-vision-tab">
                                    <div class="vision">
                                        <ul>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Garantizar un optimo servicio de transporte terrestre de pasajeros en la
                                                modalidad basica ccoriente y especial.
                                            </li>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Enfocado a la satisfacción de las necesidades y expectativas de sus
                                                asociados, clientes y usuarios.
                                            </li>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Contribuir de manera activa al desarrollo sostenible de nuestra región con
                                                un servicio de maxima calidad.
                                            </li>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Entregar todo a nuestro alcance para que nuestros usuarios y clientes se
                                                sientan satisfechos.
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="nav-mission" role="tabpanel"
                                    aria-labelledby="nav-mission-tab">
                                    <div class="vision">
                                        <ul>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Para 2022 ser una empresa que brinde a sus clientes y usuarios la mejor
                                                opción de tansporte terrestre.
                                            </li>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Ademas, ser la mejor opción en modalidad de pasajeros basica, corriente y
                                                especial.
                                            </li>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Enfocados en la aplicación de estandares de calidad y seguridad que
                                                garantize oportunidad y confort.
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="nav-value" role="tabpanel"
                                    aria-labelledby="nav-value-tab">
                                    <div class="vision">
                                        <ul>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Somos personas honestas.
                                            </li>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Inspiramos confianza.
                                            </li>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Somos creativos.
                                            </li>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Nos relacionamos con respeto.
                                            </li>
                                            <li>
                                                <i class='bx bx-check'></i>
                                                Estamos comprometidos con el servicio.
                                            </li>
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
    <!-- End About Area -->

    <!-- Services Area -->
    <div class="services-area ptb-100">
        <div class="container">
            <div class="section-title">
                <span>Nuestros Servicios</span>
                <h2>Servicios logísticos seguros y rápidos</h2>
            </div>
            {{--        <div class="widget" data-widgetid="TORCOROMA" style="position: relative;width: 80%;margin:auto;"></div> --}}
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="service-card">
                        <i class='bx bx-briefcase-alt'></i>
                        <h3>Equipaje</h3>
                        <p>Contamos con compartimentos especiales para que sus pertenencias viajen seguras y lleguen en perfectas condiciones a su lugar de destino.</p>
                        {{--                    <a href="service-details.html" class="default-btn-two">Read More</a> --}}
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="service-card">
                        <i class='bx bx-shape-circle'></i>
                        <h3>Cobertura</h3>
                        <p>Operamos en la costa caribe Colombiana con más de 30 rutas cumpliendo los sueños e ilusiones de todos nuestros clientes.</p>
                        {{--                    <a href="service-details.html" class="default-btn-two">Read More</a> --}}
                    </div>
                </div>
            </div>

{{--            <div class="view-btn">--}}
{{--                <a href="{{ url('/') }}" class="default-btn">Verlos completo</a>--}}
{{--            </div>--}}
        </div>

        <div class="services-shape">

        </div>
    </div>
    <!-- End Services Area -->
    {{-- <div class="blog-area pt-100 pb-70">
    <div class="container">
        <div class="section-title ">
            <span>Registra tu destino</span>
            <h2>Aquí puedes viajar con nosotros</h2>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="widget" data-widgetid="TORCOROMA" style="position: relative;width: 80%;margin:auto;"></div>
            </div>
        </div>
    </div>
</div> --}}
    <!-- Choose Area -->
    <div class="choose-area ptb-100">
        <div class="shape-one">
            <img src="img/shape/shape5.png" alt="Shape">
        </div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="choose-contant">
                        <div class="choose-card">
                            <i class='bx bx-world'></i>
                            <h3>Servicio al instante</h3>
                            <p>Toda una región cubierta y brindando garantías de un servicio impecable.</p>
                        </div>
                    </div>

                    <div class="choose-contant">
                        <div class="choose-card">
                            <i class='bx bxs-paper-plane'></i>
                            <h3>Servicio de rastreo</h3>
                            <p>Podemos rastrear en tiempo real donde se encuentra su mercancía.</p>
                        </div>
                    </div>

                    <div class="choose-contant">
                        <div class="choose-card">
                            <i class='bx bxs-truck'></i>
                            <h3>Rapido y totalmente seguro</h3>
                            <p>No tenemos demoras prestando nuestros servicios de transporte.</p>
                        </div>
                    </div>

                    <div class="choose-contant">
                        <div class="choose-card">
                            <i class='bx bx-support'></i>
                            <h3>Soporte 24/7</h3>
                            <p>Por medio de nuestra web podemos brindarle servicio 24/7.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="choose-text">
                        <div class="section-title">
                            <span>¿Por qué escogernos?</span>
                            <h2>Un servicio logístico seguro y rápido para usted</h2>
                        </div>
                        <p>En nuestra empresa de transporte terrestre, nos enorgullece ofrecer un servicio de entrega rápido y seguro para nuestros clientes en la costa caribe colombiana. Nuestro equipo altamente capacitado y nuestra flota de vehículos moderna nos permiten garantizar que tus productos lleguen a su destino de manera eficiente y sin contratiempos. Ya sea que necesites enviar mercancías a nivel local o nacional, puedes confiar en nosotros para brindarte un servicio logístico confiable y de calidad.</p>

                        <p>Nuestro compromiso con la excelencia nos distingue de la competencia. Trabajamos arduamente para asegurarnos de que cada entrega se realice a tiempo y en perfectas condiciones. Nuestros conductores profesionales están capacitados para manejar cualquier desafío que se les presente en la carretera, lo que garantiza que tus productos estén en buenas manos durante todo el proceso de transporte.</p>

                        <a href="#" class="default-btn-one">Contactanos</a>
                        <div class="shape-image">
                            <img src="{{ asset('images/logo-iso.png') }}" alt="icon">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="best-logistic-area ptb-100">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="logistic-text">
                            <h2>Conoce nuestra flota de buses</h2>
                            <p>En nuestra empresa de transporte terrestre, nos enorgullece contar con una flota de buses moderna y segura que cumple con todas las medidas de seguridad. Nuestros buses están equipados con dispositivos tecnológicos avanzados que garantizan un viaje cómodo y seguro para nuestros pasajeros.</p>
                        </div>
{{--                        <a href="#" class="default-btn-one">Read More</a>--}}
                    </div>
                    <div class="col-lg-6">
                        <div class="logistic-image">
                            <img src="{{ asset('images/buses_1.webp') }}" alt="image">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="best-logistic-area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="logistic-image">
                            <img src="{{ asset('images/buses_2.webp') }}" alt="image">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="logistic-text">
                            <p>Nuestros conductores altamente capacitados están comprometidos con la seguridad y el bienestar de nuestros pasajeros. Trabajamos arduamente para asegurarnos de que cada viaje sea una experiencia agradable y sin contratiempos.

                                En nuestra flota, contamos con buses de diferentes tamaños y capacidades para satisfacer las necesidades específicas de nuestros clientes. Ya sea que necesites transportar a un grupo pequeño o grande, podemos ayudarte a encontrar la mejor opción para tus necesidades.</p>
                        </div>
                        {{--                        <a href="#" class="default-btn-one">Read More</a>--}}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- End Choose Area -->

    <!-- partner Slider Area -->
    {{-- <div class="partner-area pb-100 mt-10">
    <div class="container">
        <div class="section-title">
            <span>Socios comerciales</span>
            <h2>Más de 20 empresas que confiaron en nosotros</h2>
        </div>

        <div class="partner-slider owl-carousel owl-theme">
            <div class="partner-slider-item">
                <a href="#">
                    <img src="img/partner/partner1.png" alt="logo">
                </a>
            </div>

            <div class="partner-slider-item">
                <a href="#">
                    <img src="img/partner/partner2.png" alt="logo">
                </a>
            </div>

            <div class="partner-slider-item">
                <a href="#">
                    <img src="img/partner/partner3.png" alt="logo">
                </a>
            </div>

            <div class="partner-slider-item">
                <a href="#">
                    <img src="img/partner/partner4.png" alt="logo">
                </a>
            </div>

            <div class="partner-slider-item">
                <a href="#">
                    <img src="img/partner/partner5.png" alt="logo">
                </a>
            </div>

            <div class="partner-slider-item">
                <a href="#">
                    <img src="img/partner/partner6.png" alt="logo">
                </a>
            </div>
        </div>
    </div>
</div> --}}
    <!-- End partner Slider Area -->
@endsection
