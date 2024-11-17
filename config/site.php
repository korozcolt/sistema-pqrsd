<?php

return [
    // Company Information
    'company' => [
        'name' => 'COOPERATIVA | TORCOROMA',
        'since' => '1953',
        'nit' => '890.400.565-5',
        'contact' => [
            'phones' => [
                'main' => '+57 300 123 4567',
                'secondary' => '+57 300 123 4567',
                'whatsapp' => '+573001234567'
            ],
            'emails' => [
                'main' => 'contacto@tusitio.com',
                'secondary' => 'email2@tusitio.com',
                'pqrs' => 'ing.korozco@gmail.com'
            ],
            'address' => 'Tu dirección completa'
        ]
    ],

    // Social Media
    'social' => [
        'facebook' => 'https://www.facebook.com/tusitio',
        'twitter' => 'https://twitter.com/tusitio',
        'instagram' => 'https://www.instagram.com/tusitio',
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
            'title' => 'Torcoroma S.A - Transporte Terrestre desde 1953',
            'description' => 'Empresa líder en transporte terrestre de pasajeros en la costa caribe colombiana desde 1953.',
            'keywords' => 'transporte, buses, colombia, costa caribe, pasajeros',
            'image' => '/images/logo.png'
        ],
        'pages' => [
            '_home' => [
                'title' => 'Inicio | Torcoroma S.A',
                'description' => 'Servicios de transporte terrestre seguros y confiables en la costa caribe colombiana.'
            ],
            'about' => [
                'title' => 'Sobre Nosotros | Torcoroma S.A',
                'description' => 'Conozca nuestra historia, misión, visión y valores. Más de 60 años de experiencia.'
            ],
            'service' => [
                'title' => 'Servicios | Torcoroma S.A',
                'description' => 'Servicios de transporte de pasajeros y encomiendas. Cobertura en toda la costa caribe.'
            ],
            'contact' => [
                'title' => 'Contacto | Torcoroma S.A',
                'description' => 'Contáctenos para información sobre nuestros servicios de transporte.'
            ],
            'faq' => [
                'title' => 'Preguntas Frecuentes | Torcoroma S.A',
                'description' => 'Respuestas a preguntas comunes sobre nuestros servicios.'
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
