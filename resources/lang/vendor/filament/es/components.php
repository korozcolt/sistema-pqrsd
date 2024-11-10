<?php
return [
    'pagination' => [
        'label' => 'Navegación de paginación',
        'overview' => 'Mostrando :first a :last de :total resultados',
        'fields' => [
            'records_per_page' => [
                'label' => 'por página',
            ],
        ],
        'buttons' => [
            'go_to_page' => [
                'label' => 'Ir a la página :page',
            ],
            'next' => [
                'label' => 'Siguiente',
            ],
            'previous' => [
                'label' => 'Anterior',
            ],
        ],
    ],
    'buttons' => [
        'create' => [
            'label' => 'Crear :label',
        ],
        'delete' => [
            'label' => 'Eliminar',
        ],
        'edit' => [
            'label' => 'Editar',
        ],
        'save' => [
            'label' => 'Guardar',
        ],
        'cancel' => [
            'label' => 'Cancelar',
        ],
    ],
    'fields' => [
        'search' => [
            'placeholder' => 'Buscar',
        ],
    ],
    'modal' => [
        'confirmation' => [
            'buttons' => [
                'cancel' => [
                    'label' => 'Cancelar',
                ],
                'confirm' => [
                    'label' => 'Confirmar',
                ],
            ],
            'title' => '¿Está seguro?',
        ],
    ],
];
