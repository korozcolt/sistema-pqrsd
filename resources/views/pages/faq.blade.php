@extends('layouts.page')
@section('content-page')
    <!-- Page banner Area -->
    <div class="page-banner bg-2">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="page-content">
                        <h2>PQR's</h2>
                        <ul>
                            <li><a href="{{ url('/') }}">Inicio</a></li>
                            <li>PQR's</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page banner Area -->

    <!-- Star Faq Area -->
    <div class="faq-area ptb-100">
        <div class="container">
            <div class="section-title">
                <span>PQR's</span>
                <h2>Preguntas, Peticiones, Quejas y Recursos</h2>
            </div>
            <div class="faq-contant">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <ul class="accordion">
                            <li>
                                <h3 class="title">¿Cómo saber si hay rutas disponibles?</h3>
                                <div class="accordion-content">
                                    <p>Puedes ingresar en la pagina principal y encontraras un formulario para poder buscar
                                        directamente la ruta que desees viajar con Torcoroma. Ahí podrás ver si está
                                        disponible o no.</p>
                                </div>
                            </li>
                            <li>
                                <h3 class="title">¿Cómo saber los horarios de las diferentes rutas?</h3>
                                <div class="accordion-content">
                                    <p>Puedes ingresar en la pagina principal y encontraras un formulario para poder buscar
                                        directamente la ruta que desees viajar con Torcoroma. Ahí podrás ver los horarios y
                                        la disponibilidad de las mismas.</p>
                                </div>
                            </li>
                            <li>
                                <h3 class="title">¿Qué debo hacer si debo enviar un menor de edad sin acompañante?</h3>
                                <div class="accordion-content">
                                    <p>Para que un menor pueda viajar con nosotros sin un acompañante deberás descargar el
                                        formato F-GT-12
                                        <a target="_blank"
                                            href="{{ asset('docs/F-GT-12_Autorizacion_Para_Transporte_Menores_de_Edad_Sin_Acompañante.pdf') }}">(Descargar
                                            Documento)</a>, luego imprimirlo y llenarlo con toda la información que allí es
                                        requerida.
                                        <strong>¡Recuerda!</strong> es indispensable que toda la información sea legible, sin tachones, ni
                                        borrones, adicional puede descargar la politica de transporte de menores de edad en el siguiente enlace
                                        <a target="_blank"
                                            href="{{ asset('docs/01 - D-GT-02 Politica Transporte Menor de Edad.pdf') }}">(Descargar
                                            Documento)</a>.
                                    </p>
                                </div>
                            </li>
                            <li>
                                <h3 class="title">¿Qué debo hacer para comprar un tiquete en linea?</h3>
                                <div class="accordion-content">
                                    <p>En la pagina tenemos la opción para que puedas reservar y comprar tus pasajes.</p>
                                </div>
                            </li>
                            <li>
                                <h3 class="title">Luego de comprar los tiquetes, ¿Qué debo hacer?</h3>
                                <div class="accordion-content">
                                    <p>Solo acercate a las instalaciones de TORCOROMA y lleva tu documento de identidad para
                                        proceder al proceso de CkeckIn.</p>
                                </div>
                            </li>
                            <li>
                                <h3 class="title">¿Hay envío y recepción de paquetería? </h3>
                                <div class="accordion-content">
                                    <p>Estamos trabajando en ello, no desesperes, pronto estará disponible este servicio.
                                    </p>
                                </div>
                            </li>
                            <li>
                                <h3 class="title">¿En que condiciones se encuentran los Buses de TORCOROMA?</h3>
                                <div class="accordion-content">
                                    <p>Contamos con una flota nueva, y lista para que nuestros clientes puedan disfrutar del
                                        mejor servicio posible.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-6">
                        <div class="faq-image">
                            <img src="{{ asset('images/about-page.webp') }}" alt="image">
                        </div>
                    </div>
                </div>
            </div>

            @include('pages.layouts.form-pqrs')
            <hr />
            @include('pages.layouts.search-pqrs')
        </div>
    </div>
    <!-- End Faq Area -->
@endsection
