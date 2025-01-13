<section class="contact-form-area pb-100">
    <div class="container">
        <!-- Título de la sección -->
        <div class="section-title text-center mb-5">
            @if(isset($section->config['title']))
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                    Contacto Directo
                </span>
                <h2>{{ $section->config['title'] }}</h2>
            @endif

            @if(isset($section->config['description']))
                <p class="text-muted mt-3">{{ $section->config['description'] }}</p>
            @endif
        </div>

        <!-- Formulario -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
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
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                    id="message" name="message" rows="5"
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
