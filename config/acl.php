<?php

return [

    // Mapea IDs de roles en DB → slugs estables (no dependas del número)
    'roles_by_id' => [
        1 => 'admin',
        2 => 'supervisor',   // o 'staff'
        3 => 'editor',
        4 => 'agent',
        5 => 'viewer',
    ],

    // Abilities → lista de roles (slugs) que pueden ejercerlos
    'abilities' => [
        'access-admin'            => ['admin', 'supervisor', 'editor'],
        'manage-users'            => ['admin'],

        // Reseñas (contenido)
        'manage-reviews'          => ['admin', 'supervisor', 'editor'],

        // Proveedores / requests
        'manage-review-providers' => ['admin', 'supervisor'],
        'manage-review-requests'  => ['admin', 'supervisor'],

        // Tours / catálogo
        'tours.manage'            => ['admin', 'editor'],
    ],
];
