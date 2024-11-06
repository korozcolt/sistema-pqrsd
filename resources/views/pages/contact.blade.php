@extends('layouts.page')
@section('content-page')
    <!-- Page banner Area -->
    <div class="page-banner bg-1">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="page-content">
                        <h2>Contactanos</h2>
                        <ul>
                            <li><a href="{{ asset('/') }}">Inicio</a></li>
                            <li>Contacto</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page banner Area -->

    <!-- Conatct Info -->
    <div class="pt-100 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="contact-info">
                        <i class='bx bxs-phone'></i>
                        <h4>Número de Contacto</h4>
                        <p> <a href="tel:{{ $info->phone }}">{{ $info->phone }}</a></p>
                        <p><a href="tel:{{ $info->phone2 }}">{{ $info->phone2 }}</a></p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="contact-info">
                        <i class='bx bxs-location-plus'></i>
                        <h4>Nuestra Localización</h4>
                        <p>{{ $info->address }}</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 offset-md-3 offset-lg-0">
                    <div class="contact-info">
                        <i class='bx bxs-envelope'></i>
                        <h4>Numero de Contacto</h4>
                        <p><a href="mailto:{{ $info->email }}">{{ $info->email }}</a></p>
                        <p><a href="mailto:{{ $info->email2 }}">{{ $info->email2 }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Conatct Info -->

    <!-- Contact Area -->
    <div class="contact-form-area pb-100">
        <div class="container">
            <div class="section-title">
                <span>Contactate con nosotros</span>
                <h2>Ponte en contacto con nosotros</h2>
            </div>

            <div class="contact-form">
                @if (Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                    </div>
                @endif
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
                <form method="POST" action="{{ route('support.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="name" class="form-control" id="name" required
                                    data-error="Por favor ingrese su Nombre completo" placeholder="Su nombre">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" id="email" required
                                    data-error="Por favor, ingrese su correo" placeholder="Su correo">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="subject" id="msg_subject" class="form-control" required
                                    data-error="Por favor ingrese el Asunto" placeholder="Su asunto">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="phone" class="form-control" id="phone" required
                                    data-error="Por favor ingrese su numero de telefono"
                                    placeholder="Su numero de Telefono">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <textarea name="message" id="message" class="form-control" cols="30" rows="6" required
                                    data-error="Por favor ingrese su mensaje" placeholder="Escriba su mensaje..."></textarea>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 text-center">
                            <button type="submit" class="default-btn-one">Enviar Mensaje</button>
                            <div id="msgSubmit" class="h3 text-center hidden"></div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Contact Area -->

@endsection
