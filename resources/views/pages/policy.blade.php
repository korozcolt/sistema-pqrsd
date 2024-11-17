@extends('layouts.page')
@section('content-page')
    @php
        // Definición manual de términos y condiciones
        $terms = [
            [
                'number' => '1',
                'title' => 'Validez del Tiquete',
                'content' => 'Tiquete es válido para viajar en la fecha, hora, puesto y destino indicado.',
            ],
            [
                'number' => '2',
                'title' => 'Presentación',
                'content' =>
                    'El pasajero deberá presentarse 40 minutos antes de la hora de salida indicada en el tiquete de viaje adquirido en el punto de venta autorizado.',
            ],
            [
                'number' => '3',
                'title' => 'Modificaciones',
                'content' =>
                    'Para modificar la hora o fecha del tiquete, es requisito indispensable presentarlo en la oficina expendedora por lo menos 3 horas antes de la iniciación del viaje.',
            ],
            [
                'number' => '4',
                'title' => 'Equipaje Permitido',
                'content' =>
                    'Pasajero tiene derecho a llevar en bodega hasta 25 kg sin costo adicional. Equipaje de mano hasta 10 kg y una pieza personal hasta 5 kg.',
            ],
            [
                'number' => '5',
                'title' => 'No Presentación',
                'content' =>
                    'Pasajero que no se presente ante la iniciación del viaje, no tendrá derecho a la devolución del valor pagado.',
            ],
            [
                'number' => '6',
                'title' => 'Identificación de Equipaje',
                'content' =>
                    'Al registrar el equipaje de bodega, el pasajero debe reclamar al conductor o auxiliar la ficha de identificación. La empresa no responderá por equipaje sin ficha.',
            ],
            [
                'number' => '7',
                'title' => 'Restricciones de Viaje',
                'content' =>
                    'Empresa no transportará a personas en estado de embriaguez, bajo efecto de estupefacientes o en notorio estado de desaseo.',
            ],
            [
                'number' => '8',
                'title' => 'Material Prohibido',
                'content' =>
                    'Prohibido llevar material inflamable o peligroso. No se permite transportar cajas de cartón. La empresa no responderá por deterioro o pérdida.',
            ],
            [
                'number' => '9',
                'title' => 'Responsabilidad por Equipaje',
                'content' =>
                    'La empresa responderá por pérdida o daño del equipaje hasta la suma de 5 Salarios Diarios Mínimos Legales Vigentes (SDMLV).',
            ],
        ];
    @endphp
    <!-- Page banner Area -->
    <div class="page-banner bg-2">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="page-content">
                        <h2>Políticas y Documentos</h2>
                        <ul>
                            <li><a href="{{ url('/') }}">Inicio</a></li>
                            <li>Transparencia</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación de Políticas -->
    <div class="bg-light shadow-sm py-4 sticky-top">
        <div class="container">
            <nav class="nav nav-pills justify-content-center flex-nowrap overflow-auto">
                <a class="nav-link px-4 mx-2" href="#terms">Términos y Condiciones</a>
                <a class="nav-link px-4 mx-2" href="#security">Política Integral</a>
                <a class="nav-link px-4 mx-2" href="#prevention">Política de Prevención</a>
                <a class="nav-link px-4 mx-2" href="#transport">Políticas de Transporte</a>
                <a class="nav-link px-4 mx-2" href="#rights">Derechos y Deberes</a>
            </nav>
        </div>
    </div>

    <!-- Área Principal de Contenido -->
    <div class="py-5">
        <div class="container">
            <!-- Términos y Condiciones -->
            <section id="terms" class="mb-5 pb-5">
                <div class="text-center mb-5">
                    <h2 class="mb-3">Términos y Condiciones</h2>
                    <p class="text-muted">Información importante para nuestros usuarios</p>
                </div>

                <div class="row g-4">
                    @foreach ($terms as $term)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge bg-primary rounded-circle p-3">{{ $term['number'] }}</span>
                                        <h5 class="card-title ms-3 mb-0">{{ $term['title'] }}</h5>
                                    </div>
                                    <p class="card-text text-muted">{{ $term['content'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Política Integral -->
            <section id="security" class="py-5 my-5 bg-light rounded-3">
                <div class="container">
                    <div class="text-center mb-5">
                        <h2 class="mb-3">Política Integral</h2>
                        <p class="text-muted">Nuestro compromiso con la calidad del servicio</p>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-4">Compromisos de Calidad</h5>
                                    <div class="d-flex align-items-start mb-3">
                                        <i class='bx bx-check-circle text-primary fs-4 me-3'></i>
                                        <div>
                                            <h6>Excelencia en el Servicio</h6>
                                            <p class="text-muted">Nos comprometemos a brindar un servicio de la más alta
                                                calidad, centrado en la satisfacción del cliente.</p>
                                        </div>
                                    </div>
                                    <!-- Agregar más compromisos según sea necesario -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Políticas de Transporte -->
            <section id="transport" class="py-5 my-5">
                <div class="text-center mb-5">
                    <h2 class="mb-3">Políticas de Transporte</h2>
                    <p class="text-muted">Documentos importantes para nuestros usuarios</p>
                </div>

                <div class="row g-4 justify-content-center">
                    <!-- Política de Equipaje -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-4"
                                    style="width: fit-content;">
                                    <i class='bx bxs-backpack'></i>
                                </div>
                                <h4 class="mb-3">Política de Equipaje</h4>
                                <p class="text-muted mb-4">Lineamientos para el manejo y transporte de equipaje</p>
                                <a href="{{ asset('docs/Politica Transporte Equipaje.pdf') }}"
                                    class="btn btn-outline-primary" target="_blank">
                                    <i class='bx bx-download me-2'></i>Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Política de Reembolso -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-4"
                                    style="width: fit-content;">
                                    <i class='bx bx-refresh'></i>
                                </div>
                                <h4 class="mb-3">Política de Reembolso</h4>
                                <p class="text-muted mb-4">Condiciones y procedimientos para reembolsos</p>
                                <a href="{{ asset('docs/01 - P-GT-04 REEMBOLSO.pdf') }}" class="btn btn-outline-primary"
                                    target="_blank">
                                    <i class='bx bx-download me-2'></i>Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Política de Transporte de Menores -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-4"
                                    style="width: fit-content;">
                                    <i class='bx bxs-baby-carriage'></i>
                                </div>
                                <h4 class="mb-3">Transporte de Menores</h4>
                                <p class="text-muted mb-4">Requisitos para el transporte de menores de edad</p>
                                <a href="{{ asset('docs/01 - D-GT-02 Politica Transporte Menor de Edad.pdf') }}"
                                    class="btn btn-outline-primary" target="_blank">
                                    <i class='bx bx-download me-2'></i>Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Política de Transporte de Mascotas -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-4"
                                    style="width: fit-content;">
                                    <i class='bx bxs-dog'></i>
                                </div>
                                <h4 class="mb-3">Transporte de Mascotas</h4>
                                <p class="text-muted mb-4">Requisitos para viajar con animales de compañía</p>
                                <a href="{{ asset('docs/Politica-Transporte-Mascotas.pdf') }}"
                                    class="btn btn-outline-primary" target="_blank">
                                    <i class='bx bx-download me-2'></i>Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Política de Transporte Especializado -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-4"
                                    style="width: fit-content;">
                                    <i class='bx bxs-bus'></i>
                                </div>
                                <h4 class="mb-3">Transporte Especializado</h4>
                                <p class="text-muted mb-4">Normativas para servicios especiales</p>
                                <a href="{{ asset('docs/Politica-Transporte-Especializado.pdf') }}"
                                    class="btn btn-outline-primary" target="_blank">
                                    <i class='bx bx-download me-2'></i>Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Política de Seguridad -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-4"
                                    style="width: fit-content;">
                                    <i class='bx bxs-shield-alt-2'></i>
                                </div>
                                <h4 class="mb-3">Política de Seguridad</h4>
                                <p class="text-muted mb-4">Protocolos de seguridad en el transporte</p>
                                <a href="{{ asset('docs/Politica-Seguridad-Transporte.pdf') }}"
                                    class="btn btn-outline-primary" target="_blank">
                                    <i class='bx bx-download me-2'></i>Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Política de Accesibilidad -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-4"
                                    style="width: fit-content;">
                                    <i class='bx bx-accessibility' style='color:#ffffff' ></i>
                                </div>
                                <h4 class="mb-3">Accesibilidad</h4>
                                <p class="text-muted mb-4">Servicios para personas con movilidad reducida</p>
                                <a href="{{ asset('docs/Politica-Accesibilidad.pdf') }}" class="btn btn-outline-primary"
                                    target="_blank">
                                    <i class='bx bx-download me-2'></i>Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Política de Emergencias -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-4"
                                    style="width: fit-content;">
                                    <i class='bx bxs-ambulance'></i>
                                </div>
                                <h4 class="mb-3">Protocolo de Emergencias</h4>
                                <p class="text-muted mb-4">Procedimientos en casos de emergencia</p>
                                <a href="{{ asset('docs/Protocolo-Emergencias.pdf') }}" class="btn btn-outline-primary"
                                    target="_blank">
                                    <i class='bx bx-download me-2'></i>Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Derechos y Deberes -->
            <section id="rights" class="py-5 my-5 bg-light rounded-3">
                <div class="container text-center">
                    <h2 class="mb-4">Derechos y Deberes de los Pasajeros</h2>
                    <p class="text-muted mb-5">Conoce tus derechos y responsabilidades como pasajero</p>
                    <a href="{{ asset('docs/Cartilla-de-derechos-y-deberes-de-transporte-terrestre.pdf') }}"
                        class="btn btn-primary btn-lg" target="_blank">
                        <i class='bx bx-download me-2'></i>
                        Descargar Manual Completo
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection
