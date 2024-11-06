<div class="faq-form">
    <form action="{{ route('support.store') }}" method="POST">
        @csrf
        <div class="section-title">
            <span>PQR's</span>
            <h2>Preguntas, Peticiones, Quejas y Recursos</h2>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Su nombre">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Su email">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Su teléfono">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Asunto:</label>
                    <input type="text" class="form-control" name="subject" id="subject"
                        placeholder="Escriba un breve resumen">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Message</label>
            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Mensaje completo"></textarea>
        </div>
        <!-- Google Recaptcha -->
        <div class="g-recaptcha mt-4" data-sitekey={{ config('services.recaptcha.key') }}></div>

        <div class="text-center">
            <button type="submit" class="faq-form-btn">Enviar mensaje</button>
        </div>
    </form>
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Cuidado!</strong> Hay un error es una de las
            entradas.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
