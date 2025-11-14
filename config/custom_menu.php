<?php

return [
    'menu' => [
        // Navbar items:
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'text' => 'Dashboard',
            'url' => '/dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
            'active' => ['dashboard*']
        ],
        
        // Menú de Almacenes
        [
            'text' => 'Almacenes',
            'url' => '/almacenes',
            'icon' => 'fas fa-fw fa-warehouse',
            'active' => ['almacenes*']
        ],
        
        // Separador
        ['header' => 'ADMINISTRACIÓN'],
        
        // Menú de Perfil
        [
            'text' => 'Perfil',
            'url' => '/profile',
            'icon' => 'fas fa-fw fa-user',
        ]
    ]
];
