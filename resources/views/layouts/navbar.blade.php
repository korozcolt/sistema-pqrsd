<!-- Start Navbar Area -->
<div class="navbar-area">
    <div class="ferry-responsive-nav">
        <div class="container">
            <div class="ferry-responsive-menu">
                <div class="logo">
                    <a href="{{ route('page', '_home') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="TORCOROMA LOGO">
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="ferry-nav">
        <div class="container">
            <nav class="navbar navbar-expand-md navbar-light">
                <a class="navbar-brand" href="/">
                    <img src="{{ asset('images/logo.png') }}" width="150" alt="logo torcoroma">
                </a>

                <div class="collapse navbar-collapse mean-menu">
                    <ul class="navbar-nav ms-auto">
                        <!-- Inicio -->
                        <li class="nav-item">
                            <a href="{{ route('page', '_home') }}"
                                class="nav-link {{ Request::path() == 'home' ? 'active' : '' }}">Inicio</a>
                        </li>

                        <!-- Torcoroma -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                Torcoroma <i class='bx bx-chevron-down'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a href="{{ route('page', 'about') }}"
                                        class="nav-link {{ Request::path() == 'about' ? 'active' : '' }}">Acerca de</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('page', 'faq') }}"
                                        class="nav-link {{ Request::path() == 'faq' ? 'active' : '' }}">Preguntas Frecuentes</a>
                                </li>
                            </ul>
                        </li>

                        <!-- Servicios -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                Servicios <i class='bx bx-chevron-down'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a href="{{ route('page', 'service') }}"
                                        class="nav-link {{ Request::path() == 'service' ? 'active' : '' }}" data-target="#service">Servicios</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('tickets') }}"
                                        class="nav-link {{ Request::routeIs('tickets') ? 'active' : '' }}">PQRS</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('page', 'service') }}"
                                        class="nav-link {{ Request::path() == 'service' ? 'active' : '' }}" data-target="#buy-tickets">Compra tu tiquete</a>
                                </li>
                            </ul>
                        </li>

                        <!-- Transparencia -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                Transparencia <i class='bx bx-chevron-down'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a href="{{ route('page', 'policy') }}"
                                        class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}" data-target="#terms">Términos y Condiciones</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('page', 'policy') }}"
                                        class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}" data-target="#security">Política Integral y de Prevención</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('page', 'policy') }}"
                                        class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}" data-target="#policies">Políticas de Transporte
                                        <i class='bx bx-chevron-right'></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="nav-item">
                                            <a href="{{ route('page', 'policy') }}"
                                                class="nav-link" data-target="#luggage">Política de Equipaje</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('page', 'policy') }}"
                                                class="nav-link" data-target="#refund">Política de Reembolso</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('page', 'policy') }}"
                                                class="nav-link" data-target="#minors">Política de Transporte de Menores</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('page', 'policy') }}"
                                                class="nav-link" data-target="#pets">Política de Transporte de Mascotas</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('page', 'policy') . '#rights' }}"
                                        class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}">Derechos y Deberes</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('page', 'policy') . '#formats' }}"
                                        class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}">Formatos para Transporte</a>
                                </li>
                            </ul>
                        </li>

                        <!-- Contacto -->
                        <li class="nav-item">
                            <a href="{{ route('page', 'contact') }}"
                                class="nav-link {{ Request::path() == 'contact' ? 'active' : '' }}">Contacto</a>
                        </li>

                        <!-- Búsqueda -->
                        <li class="nav-item">
                            <a href="javascript:void(0)" class="nav-link search-box">
                                <i class='bx bx-search'></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</div>
<!-- End Navbar Area -->
