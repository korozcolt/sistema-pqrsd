@extends('layouts.page')

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar SweetAlert si existe un mensaje
            @if (session('swal'))
                Swal.fire(@json(session('swal')));
            @endif

            // Manejar errores de validación
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: '¡Hay errores en el formulario!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonText: 'Entendido'
                });
            @endif

            // Configurar el formulario para mostrar indicador de carga
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                Swal.fire({
                    title: 'Enviando mensaje...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        });
    </script>
@endpush

@section('content-page')
    <!-- Page banner Area -->
    <div class="page-banner bg-1">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="page-content">
                        <h2>Contactanos</h2>
                        <ul>
                            <li><a href="{{ url('/') }}">Inicio</a></li>
                            <li>Contacto</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Info -->
    <section class="pt-100 pb-70">
        <div class="container">
            <div class="row">
                <!-- Teléfono -->
                <div class="col-lg-4 col-md-6">
                    <div class="contact-info h-100 shadow-sm">
                        <div class="text-center mb-4">
                            <i class='bx bxs-phone display-5 text-primary'></i>
                        </div>
                        <h4 class="text-center mb-4">Líneas de Atención</h4>
                        <div class="text-center">
                            @if (isset($info->phone) && $info->phone)
                                <p class="mb-2">
                                    <a href="tel:{{ $info->phone }}" class="text-body text-decoration-none">
                                        {{ $info->phone }}
                                    </a>
                                </p>
                            @endif
                            @if (isset($info->phone2) && $info->phone2)
                                <p>
                                    <a href="tel:{{ $info->phone2 }}" class="text-body text-decoration-none">
                                        {{ $info->phone2 }}
                                    </a>
                                </p>
                            @endif
                            @if (!isset($info->phone) && !isset($info->phone2))
                                <p class="text-muted">Teléfonos en actualización</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Localización -->
                <div class="col-lg-4 col-md-6">
                    <div class="contact-info h-100 shadow-sm">
                        <div class="text-center mb-4">
                            <i class='bx bxs-location-plus display-5 text-primary'></i>
                        </div>
                        <h4 class="text-center mb-4">Nuestra Ubicación</h4>
                        <p class="text-center">
                            {{ isset($info->address) ? $info->address : 'Dirección en actualización' }}
                        </p>
                    </div>
                </div>

                <!-- Email -->
                <div class="col-lg-4 col-md-6 offset-md-3 offset-lg-0">
                    <div class="contact-info h-100 shadow-sm">
                        <div class="text-center mb-4">
                            <i class='bx bxs-envelope display-5 text-primary'></i>
                        </div>
                        <h4 class="text-center mb-4">Correo Electrónico</h4>
                        <div class="text-center">
                            @if (isset($info->email) && $info->email)
                                <p class="mb-2">
                                    <a href="mailto:{{ $info->email }}" class="text-body text-decoration-none">
                                        {{ $info->email }}
                                    </a>
                                </p>
                            @endif
                            @if (isset($info->email2) && $info->email2)
                                <p>
                                    <a href="mailto:{{ $info->email2 }}" class="text-body text-decoration-none">
                                        {{ $info->email2 }}
                                    </a>
                                </p>
                            @endif
                            @if (!isset($info->email) && !isset($info->email2))
                                <p class="text-muted">Correos en actualización</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Area -->
    <section class="contact-form-area pb-100">
        <div class="container">
            <!-- Título de la sección -->
            <div class="section-title text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">Contacto
                    Directo</span>
                <h2>Ponte en contacto con nosotros</h2>
                <p class="text-muted mt-3">Completa el formulario y nos pondremos en contacto contigo lo antes posible</p>
            </div>

            <!-- Formulario -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            @if (Session::has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ Session::get('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>¡Por favor corrija los siguientes errores!</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('contact.send') }}" class="row g-3">
                                @csrf
                                <!-- Nombre -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nombre completo</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}"
                                        placeholder="Ingrese su nombre completo" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}"
                                        placeholder="Ingrese su correo electrónico" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Asunto -->
                                <div class="col-md-6">
                                    <label for="subject" class="form-label">Asunto</label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                        id="subject" name="subject" value="{{ old('subject') }}"
                                        placeholder="Ingrese el asunto" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Teléfono -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone') }}"
                                        placeholder="Ingrese su número de teléfono" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Mensaje -->
                                <div class="col-12">
                                    <label for="message" class="form-label">Mensaje</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5"
                                        placeholder="Escriba su mensaje..." required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- reCAPTCHA -->
                                <div class="col-12">
                                    <div class="g-recaptcha @error('g-recaptcha-response') is-invalid @enderror"
                                        data-sitekey="{{ config('services.recaptcha.site_key') }}">
                                    </div>
                                    @error('g-recaptcha-response')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Botón Submit -->
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary px-5 py-2">
                                        <i class='bx bx-send me-2'></i>Enviar Mensaje
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Horarios y Redes Sociales -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <!-- Horarios -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="mb-4">Horario de Atención</h4>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <i class='bx bx-time me-2 text-primary'></i>
                                    Lunes a Viernes: 8:00 AM - 6:00 PM
                                </li>
                                <li class="mb-3">
                                    <i class='bx bx-time me-2 text-primary'></i>
                                    Sábados: 8:00 AM - 2:00 PM
                                </li>
                                <li>
                                    <i class='bx bx-time me-2 text-primary'></i>
                                    Domingos y Festivos: Cerrado
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Redes Sociales -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="mb-4">Síguenos en Redes Sociales</h4>
                            <div class="d-flex gap-3">
                                @if (isset($info->facebook) && $info->facebook)
                                    <a href="{{ $info->facebook }}" class="btn btn-outline-primary" target="_blank"
                                        rel="noopener noreferrer">
                                        <i class='bx bxl-facebook'></i>
                                    </a>
                                @endif

                                @if (isset($info->twitter) && $info->twitter)
                                    <a href="{{ $info->twitter }}" class="btn btn-outline-primary" target="_blank"
                                        rel="noopener noreferrer">
                                        <i class='bx bxl-twitter'></i>
                                    </a>
                                @endif

                                @if (isset($info->instagram) && $info->instagram)
                                    <a href="{{ $info->instagram }}" class="btn btn-outline-primary" target="_blank"
                                        rel="noopener noreferrer">
                                        <i class='bx bxl-instagram'></i>
                                    </a>
                                @endif

                                @if (isset($info->linkedin) && $info->linkedin)
                                    <a href="{{ $info->linkedin }}" class="btn btn-outline-primary" target="_blank"
                                        rel="noopener noreferrer">
                                        <i class='bx bxl-linkedin'></i>
                                    </a>
                                @endif
                            </div>

                            @if (!isset($info->facebook) && !isset($info->twitter) && !isset($info->instagram) && !isset($info->linkedin))
                                <p class="text-muted text-center mb-0">
                                    Próximamente nuestras redes sociales
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
