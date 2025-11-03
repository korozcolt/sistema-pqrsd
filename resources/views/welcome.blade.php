<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('site.seo.default.title') }}</title>
    <meta name="description" content="{{ config('site.seo.default.description') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .feature-card {
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <!-- Hero Section -->
    <section class="gradient-bg min-h-screen flex items-center justify-center relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center fade-in-up">
                <!-- Icon -->
                <div class="inline-flex items-center justify-center w-32 h-32 mb-8 bg-white/20 backdrop-blur-lg rounded-3xl shadow-2xl float-animation">
                    <i class="fas fa-clipboard-list text-6xl text-white"></i>
                </div>

                <!-- Title -->
                <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 leading-tight">
                    Sistema PQRSD
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto font-light">
                    Plataforma Integral de Gestión de <span class="font-semibold">Peticiones, Quejas, Reclamos, Sugerencias y Denuncias</span>
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('support.store') }}" class="px-8 py-4 bg-white text-purple-700 rounded-full font-bold text-lg shadow-2xl hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        Crear Solicitud
                    </a>
                    <a href="{{ route('tracking') }}" class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-full font-bold text-lg hover:bg-white hover:text-purple-700 transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-search"></i>
                        Consultar Estado
                    </a>
                    <a href="/admin" class="px-8 py-4 bg-purple-900 text-white rounded-full font-bold text-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-user-shield"></i>
                        Panel Admin
                    </a>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
            <i class="fas fa-chevron-down text-3xl"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Características del Sistema
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Una solución completa para gestionar todas las solicitudes de tus usuarios
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-ticket-alt text-3xl text-purple-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Sistema de Tickets</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Gestión completa del ciclo de vida de tickets PQRSD con seguimiento en tiempo real y asignación automática.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-clock text-3xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Gestión de SLA</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Configuración de tiempos de respuesta y resolución según tipos de ticket con alertas automáticas.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-pink-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-bell text-3xl text-pink-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Notificaciones</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Sistema de notificaciones multi-canal por correo electrónico y notificaciones internas en tiempo real.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-bar text-3xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Análisis y Reportes</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Paneles analíticos con estadísticas detalladas sobre tickets, tiempos de respuesta y resolución.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-yellow-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-users text-3xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Control de Roles</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Jerarquía de usuarios con roles específicos: SuperAdmin, Admin, Recepcionista y Usuario Web.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-code text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">API RESTful</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Interfaz de programación completa para integración con aplicaciones externas y terceros.
                    </p>
                </div>

                <!-- Feature 7 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-red-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-building text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Departamentos</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Organización por áreas administrativas con asignación inteligente y distribución de carga.
                    </p>
                </div>

                <!-- Feature 8 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-teal-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-tags text-3xl text-teal-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Sistema de Etiquetas</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Categorización flexible de tickets para mejor organización y búsqueda eficiente.
                    </p>
                </div>

                <!-- Feature 9 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-3xl text-orange-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">100% Responsive</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Diseño adaptado para todos los dispositivos: móviles, tablets y computadoras de escritorio.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Types Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Tipos de Solicitudes
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Gestiona diferentes tipos de solicitudes con procesos específicos
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="bg-white rounded-xl p-6 text-center shadow-md hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Peticiones</h3>
                    <p class="text-gray-600 text-sm">Solicitudes de información o documentación</p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-md hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-frown text-2xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Quejas</h3>
                    <p class="text-gray-600 text-sm">Insatisfacción sobre productos o servicios</p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-md hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Reclamos</h3>
                    <p class="text-gray-600 text-sm">Expresión de inconformidad con justificación</p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-md hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lightbulb text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Sugerencias</h3>
                    <p class="text-gray-600 text-sm">Ideas de mejora para procesos o servicios</p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-md hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Denuncias</h3>
                    <p class="text-gray-600 text-sm">Reportes de irregularidades o incumplimientos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center text-white">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    ¿Listo para empezar?
                </h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto opacity-90">
                    Crea tu primera solicitud o consulta el estado de una existente. Nuestro equipo está listo para atenderte.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('support.store') }}" class="px-8 py-4 bg-white text-purple-700 rounded-full font-bold text-lg shadow-2xl hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-rocket"></i>
                        Comenzar Ahora
                    </a>
                    <a href="{{ route('tracking') }}" class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-full font-bold text-lg hover:bg-white hover:text-purple-700 transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-search-plus"></i>
                        Rastrear Solicitud
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-clipboard-list text-3xl text-purple-400"></i>
                        <h3 class="text-2xl font-bold">Sistema PQRSD</h3>
                    </div>
                    <p class="text-gray-400">
                        {{ config('site.company.description') }}
                    </p>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('support.store') }}" class="hover:text-purple-400 transition-colors">Crear Solicitud</a></li>
                        <li><a href="{{ route('tracking') }}" class="hover:text-purple-400 transition-colors">Consultar Estado</a></li>
                        <li><a href="/admin" class="hover:text-purple-400 transition-colors">Panel Administrativo</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-envelope text-purple-400"></i>
                            {{ config('site.company.contact.emails.pqrs') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-phone text-purple-400"></i>
                            {{ config('site.company.contact.phones.main') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-purple-400"></i>
                            {{ config('site.company.contact.address') }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ config('site.company.since') }} {{ config('site.company.name') }}. Todos los derechos reservados.</p>
                <p class="mt-2 text-sm">
                    Licenciado bajo Apache 2.0 | Versión 1.1.0
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card').forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html>
