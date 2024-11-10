<?php

return [
    'tickets' => [
        'label' => 'Ticket',
        'plural' => 'Tickets',
        'navigation_group' => 'Gestión',
        'form' => [
            'ticket_number' => [
                'label' => 'Número de Ticket',
            ],
            'title' => [
                'label' => 'Título',
            ],
            'description' => [
                'label' => 'Descripción',
            ],
            'status' => [
                'label' => 'Estado',
                'options' => [
                    'pending' => 'Pendiente',
                    'in_progress' => 'En Progreso',
                    'resolved' => 'Resuelto',
                    'closed' => 'Cerrado',
                    'rejected' => 'Rechazado',
                    'reopened' => 'Reabierto',
                ],
            ],
            'priority' => [
                'label' => 'Prioridad',
                'options' => [
                    'low' => 'Baja',
                    'medium' => 'Media',
                    'high' => 'Alta',
                    'urgent' => 'Urgente',
                ],
            ],
            'type' => [
                'label' => 'Tipo',
                'options' => [
                    'petition' => 'Petición',
                    'complaint' => 'Queja',
                    'claim' => 'Reclamo',
                    'suggestion' => 'Sugerencia',
                ],
            ],
        ],
    ],
    'users' => [
        'label' => 'Usuario',
        'plural' => 'Usuarios',
        'navigation_group' => 'Administración',
        'form' => [
            'name' => [
                'label' => 'Nombre',
            ],
            'email' => [
                'label' => 'Correo electrónico',
            ],
            'password' => [
                'label' => 'Contraseña',
            ],
            'role' => [
                'label' => 'Rol',
                'options' => [
                    'superadmin' => 'Super Administrador',
                    'admin' => 'Administrador',
                    'receptionist' => 'Recepcionista',
                    'user_web' => 'Usuario Web',
                ],
            ],
        ],
    ],
];
