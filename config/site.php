<?php

return [
    // Company Information
    'company' => [
        'name' => 'Sistema PQRSD',
        'since' => date('Y'),
        'nit' => '000.000.000-0',
        'contact' => [
            'phones' => [
                'main' => '+57 300 000 0000',
                'secondary' => '+57 300 000 0000',
                'whatsapp' => '+573000000000'
            ],
            'emails' => [
                'main' => 'contacto@ejemplo.com',
                'secondary' => 'info@ejemplo.com',
                'pqrs' => 'pqrsd@ejemplo.com'
            ],
            'address' => 'Dirección de su empresa'
        ],
        'description' => 'Sistema de gestión de Peticiones, Quejas, Reclamos, Sugerencias y Denuncias.',
    ],

    // Social Media
    'social' => [
        'facebook' => null,
        'twitter' => null,
        'instagram' => null,
        'linkedin' => null
    ],

    // Business Settings
    'settings' => [
        'taxes' => [
            'active' => true,
            'rate' => 19.00
        ]
    ],

    // Assets
    'assets' => [
        'logo' => 'path/to/your/logo.png',
        'favicon' => 'path/to/your/favicon.ico'
    ],

    // SEO
    'seo' => [
        'default' => [
            'title' => 'Sistema PQRSD - Gestión de Peticiones, Quejas, Reclamos, Sugerencias y Denuncias',
            'description' => 'Sistema integral para la gestión de PQRSD. Gestiona eficientemente las solicitudes de tus usuarios.',
            'keywords' => 'pqrsd, peticiones, quejas, reclamos, sugerencias, denuncias, sistema de tickets',
            'image' => '/images/logo.png'
        ],
        'pages' => [
            '_home' => [
                'title' => 'Inicio | Sistema PQRSD',
                'description' => 'Sistema de gestión de Peticiones, Quejas, Reclamos, Sugerencias y Denuncias.'
            ],
            'about' => [
                'title' => 'Acerca de | Sistema PQRSD',
                'description' => 'Conoce más sobre nuestro sistema de gestión PQRSD.'
            ],
            'service' => [
                'title' => 'Servicios | Sistema PQRSD',
                'description' => 'Servicios de gestión y seguimiento de solicitudes PQRSD.'
            ],
            'contact' => [
                'title' => 'Contacto | Sistema PQRSD',
                'description' => 'Contáctenos para más información sobre el sistema.'
            ],
            'faq' => [
                'title' => 'Preguntas Frecuentes | Sistema PQRSD',
                'description' => 'Respuestas a preguntas comunes sobre el sistema PQRSD.'
            ]
        ]
    ],

    // Legal Content
    'legal' => [
        'about' => 'Texto sobre tu empresa o negocio',
        'terms' => 'Términos y condiciones de uso',
        'privacy' => 'Política de privacidad'
    ],

    // Mail Settings
    'mail' => [
        'default_subject' => 'Asunto del correo'
    ]
];
