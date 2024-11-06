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
                         <li class="nav-item">
                             <a href="{{ route('page', '_home') }}" class="nav-link {{ Request::path() == 'home' ? 'active' : '' }}">Inicio</a>
                         </li>

                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 Torcoroma <i class='bx bx-chevron-down'></i>
                             </a>
                             <ul class="dropdown-menu">
                                 <li class="nav-item">
                                     <a href="{{ route('page', 'about') }}" class="nav-link {{ Request::path() == 'about' ? 'active' : '' }}">Acerca
                                         de</a>
                                 </li>
                                 <li class="nav-item">
                                     <a href="{{ route('page', 'faq') }}" class="nav-link {{ Request::path() == 'faq' ? 'active' : '' }}">Formatos
                                         para transporte
                                         especializado</a>
                                 </li>

                                 <li class="nav-item">
                                     <a href="{{ route('page', 'faq') }}" class="nav-link {{ Request::path() == 'faq' ? 'active' : '' }}">Preguntas
                                         Frecuentes</a>
                                 </li>
                             </ul>
                         </li>

                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 Servicios <i class='bx bx-chevron-down'></i>
                             </a>
                             <ul class="dropdown-menu">
                                 <li class="nav-item {{ Request::path() == 'sevice' ? 'active' : '' }}">
                                     <a href="{{ route('page', 'service') . '#services' }}" class="nav-link">Servicios</a>
                                 </li>
                                 <li class="nav-item">
                                     <a href="{{ route('page', 'service') . '#buy-ticket' }}" class="nav-link {{ Request::path() == 'sevice' ? 'active' : '' }}">Compra
                                         tu
                                         tiquete</a>
                                 </li>
                                 <!--<li class="nav-item">
                                     <a href="{{ route('page', 'service') . '#tracking' }}" class="nav-link {{ Request::path() == 'sevice' ? 'active' : '' }}">Rastrea
                                         tu
                                         ruta</a>
                                 </li>-->
                             </ul>
                         </li>

                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 Transparencia <i class='bx bx-chevron-down'></i>
                             </a>
                             <ul class="dropdown-menu">
                                 <li class="nav-item">
                                     <a href="{{ route('page', 'policy') . '#terms' }}" class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}">Terminos
                                         y
                                         condiciones</a>
                                 </li>
                                 <li class="nav-item">
                                     <a href="{{ route('page', 'policy') . '#security' }}" class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}">Política
                                         integral y de prevención
                                     </a>
                                 </li>
                                 <li class="nav-item">
                                     <a href="{{ route('page', 'policy') . '#rights' }}" class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}">Política
                                         de
                                         equipaje</a>
                                 </li>
                                 <li class="nav-item">
                                     <a href="{{ route('page', 'policy') . '#rights' }}" class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}">Política
                                         de
                                         reembolso</a>
                                 </li>
                                 <li class="nav-item">
                                     <a href="{{ route('page', 'policy') . '#rights' }}" class="nav-link {{ Request::path() == 'policy' ? 'active' : '' }}">Derechos
                                         y
                                         Deberes De Los Pasajeros</a>
                                 </li>


                             </ul>
                         </li>

                         <li class="nav-item">
                             <a href="{{ route('page', 'contact') }}" class="nav-link {{ Request::path() == 'contact' ? 'active' : '' }}">Contacto</a>
                         </li>

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