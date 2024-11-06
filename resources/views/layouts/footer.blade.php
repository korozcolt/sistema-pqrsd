<footer class="footer-area pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="footer-widget">
                    <div class="logo">
                        <img src="{{ asset('images/logo.png') }}" width="220" alt="logo torcoroma">
                    </div>
                    <p>Conoce todo de nosotros, aprende un poco de nuestra historia, nuestros clientes y nuestra
                        forma de ver el futuro.</p>

                    <ul class="footer-socials">
                        <li>
                            <a href="{{ $info->facebook ?? '#' }}" target="_blank">
                                <i class='bx bxl-facebook'></i>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $info->twitter ?? '#' }}" target="_blank">
                                <i class='bx bxl-twitter'></i>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $info->linkedin ?? '#' }}" target="_blank">
                                <i class='bx bxl-linkedin'></i>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $info->instagram ?? '#' }}" target="_blank">
                                <i class='bx bxl-instagram'></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="footer-widget pl-80">
                    <h3>Nosotros</h3>

                    <ul class="footer-text">
                        <li>
                            <a href="{{ url('/team') }}">Equipo</a>
                        </li>
                        <li>
                            <a href="{{ url('/services') }}">Servicios</a>
                        </li>
                        <li>
                            <a href="{{ url('/about') }}">Acerca de</a>
                        </li>
                        <li>
                            <a href="{{ url('/#quotation') }}">Cotización</a>
                        </li>
                        <li>
                            <a href="{{ url('/blog') }}">Últimas noticias</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="footer-widget pl-50">
                    <h3>Soporte</h3>

                    <ul class="footer-text">
                        <li>
                            <a href="{{ url('/faq') }}">Preguntas frecuentes</a>
                        </li>
                        <li>
                            <a href="{{ url('/policy-and-term') }}">Políticas de bioseguridad</a>
                        </li>
                        <li>
                            <a href="{{ url('/policy-and-term') }}">Terminos & condiciones</a>
                        </li>
                        <li>
                            <a href="{{ url('/faq') }}">Formatos para transporte especializado</a>
                        </li>
                        <li>
                            <a href="{{ url('/contact') }}">Contactanos</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="footer-widget">
                    <h3>Información de Contacto</h3>
                    <ul class="info-list">
                        <li>
                            <i class='bx bxs-location-plus'></i>
                            {{ $info->address ?? '' }}
                        </li>
                        <li>
                            <i class='bx bxs-envelope'></i>
                            <a href="mailto:{{ $info->email ?? '' }}">{{ $info->email ?? '' }}</a>
                        </li>

                        <li>
                            <i class='bx bxs-phone'></i>
                            <a href="tel:{{ $info->phone ?? '' }}">{{ $info->phone ?? '' }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="partner-area pb-100">
        <div class="container">
            <div class="section-title">
                <span>Entidades Reguladoras</span>
                <h2>Regulado por:</h2>
            </div>
            <div class="partner-slider owl-carousel owl-theme owl-loaded owl-drag">
                <div class="owl-stage-outer">
                    <div class="owl-stage"
                         style="width: 616px;">
                        <div class="owl-item cloned" style="width: 205.2px; margin-right: 30px;">
                            <div class="partner-slider-item">
                                <a href="https://www.supertransporte.gov.co/" target="_blank">
                                    <img src="{{ asset('images/supertransporte_logo.webp') }}" alt="supertransporte_logo">
                                </a>
                            </div>
                        </div>
                        <div class="owl-item cloned" style="width: 205.2px; margin-right: 30px;">
                            <div class="partner-slider-item">
                                <a href="https://www.mintransporte.gov.co/" target="_blank">
                                    <img src="{{ asset('images/sipor_logo.webp') }}" alt="sipor_logo">
                                </a>
                            </div>
                        </div>
                        <div class="owl-item cloned" style="width: 205.2px; margin-right: 30px;">
                            <div class="partner-slider-item">
                                <a href="https://www.supersolidaria.gov.co/" target="_blank">
                                    <img src="{{ asset('images/logosupersolidaria.webp') }}" alt="supersolidaria_logo">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="owl-nav disabled">
                    <button type="button" role="presentation" class="owl-prev"><span aria-label="Previous">‹</span>
                    </button>
                    <button type="button" role="presentation" class="owl-next"><span aria-label="Next">›</span></button>
                </div>
                <div class="owl-dots disabled"></div>
            </div>
        </div>
    </div>
</footer>
