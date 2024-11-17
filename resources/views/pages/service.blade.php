@extends('layouts.page')
@section('content-page')
    <!-- Page banner Area -->
    <div class="page-banner bg-2">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="page-content">
                        <h2>Servicios de Transporte</h2>
                        <ul>
                            <li><a href="{{ url('/') }}">Inicio</a></li>
                            <li>Servicios</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios - Navegación Interna -->
    <div class="services-navigation bg-light shadow-sm py-3 mb-5">
        <div class="container">
            <nav class="nav justify-content-center">
                <a class="nav-link px-4" href="#service">Servicios</a>
                <a class="nav-link px-4" href="#buy-tickets">Compra de Tiquetes</a>
                <a class="nav-link px-4" href="#coverage">Cobertura</a>
            </nav>
        </div>
    </div>

    <!-- Sección de Servicios -->
    <section id="service" class="service-section py-5 my-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title text-center mb-5">
                        <span>Nuestros Servicios</span>
                        <h2>Servicios de Transporte Terrestre</h2>
                        <p class="mt-3">Brindamos soluciones de transporte seguras y eficientes</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card shadow-sm p-4">
                        <i class='bx bxs-bus mb-4'></i>
                        <h4>Transporte de Pasajeros</h4>
                        <p>Servicio regular entre las principales ciudades de la costa caribe</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card shadow-sm p-4">
                        <i class='bx bxs-car mb-4'></i>
                        <h4>Servicios Especiales</h4>
                        <p>Transporte para empresas y grupos con rutas personalizadas</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card shadow-sm p-4">
                        <i class='bx bxs-package mb-4'></i>
                        <h4>Envío de Paquetes</h4>
                        <p>Servicio de envío de paquetes y documentos entre nuestras rutas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Compra de Tiquetes -->
    <section id="buy-tickets" class="service-section bg-light py-5 my-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <span>Reserva tu viaje</span>
                <h2>Compra Tus Tiquetes Online</h2>
                <p class="mt-3">Sistema de reservas y compra de tiquetes en línea</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <div class="widget-container">
                                <div class="widget" data-widgetid="TORCOROMA"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Cobertura -->
    <section id="coverage" class="service-section py-5 my-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <span>Cobertura</span>
                <h2>Nuestras Rutas y Destinos</h2>
                <p class="mt-3">Conectando la región caribe colombiana</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="route-card shadow-sm p-4">
                        <div class="icon text-center">
                            <i class='bx bxs-map'></i>
                        </div>
                        <h4 class="text-center mb-4">Rutas Principales</h4>
                        <ul class="route-list">
                            <li>Sincelejo - Cartagena</li>
                            <li>Sincelejo - Barranquilla</li>
                            <li>Sincelejo - Santa Marta</li>
                            <li>Sincelejo - Montería</li>
                            <li>Sincelejo - Valledupar</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="route-card shadow-sm p-4">
                        <div class="icon text-center">
                            <i class='bx bx-time-five'></i>
                        </div>
                        <h4 class="text-center mb-4">Horarios de Servicio</h4>
                        <ul class="route-list">
                            <li>Servicio 24 horas</li>
                            <li>Salidas cada hora</li>
                            <li>Todos los días del año</li>
                            <li>Servicios expresos disponibles</li>
                            <li>Horarios flexibles</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="route-card shadow-sm p-4">
                        <div class="icon text-center">
                            <i class='bx bx-info-circle'></i>
                        </div>
                        <h4 class="text-center mb-4">Información Adicional</h4>
                        <ul class="route-list">
                            <li>Wifi gratuito en terminales</li>
                            <li>Salas VIP disponibles</li>
                            <li>Estacionamiento seguro</li>
                            <li>Atención preferencial</li>
                            <li>Servicios especiales</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Características Adicionales -->
    <section class="service-section bg-light py-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <span>Características</span>
                <h2>¿Por qué Elegirnos?</h2>
                <p class="mt-3">Nos destacamos por nuestro compromiso con la calidad y seguridad</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="text-center p-4 h-100 bg-white rounded shadow-sm">
                        <div class="mb-4">
                            <i class='bx bxs-shield-plus display-4 text-primary'></i>
                        </div>
                        <h4>Seguridad</h4>
                        <p class="mb-0">Flota moderna con GPS y monitoreo permanente</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="text-center p-4 h-100 bg-white rounded shadow-sm">
                        <div class="mb-4">
                            <i class='bx bxs-time display-4 text-primary'></i>
                        </div>
                        <h4>Puntualidad</h4>
                        <p class="mb-0">Cumplimiento estricto de horarios programados</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="text-center p-4 h-100 bg-white rounded shadow-sm">
                        <div class="mb-4">
                            <i class='bx bxs-car-crash display-4 text-primary'></i>
                        </div>
                        <h4>Cobertura Total</h4>
                        <p class="mb-0">Seguro de viaje incluido para todos los pasajeros</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="text-center p-4 h-100 bg-white rounded shadow-sm">
                        <div class="mb-4">
                            <i class='bx bxs-phone-call display-4 text-primary'></i>
                        </div>
                        <h4>Soporte 24/7</h4>
                        <p class="mb-0">Atención al cliente permanente para tu tranquilidad</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
