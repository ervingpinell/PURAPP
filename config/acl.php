<?php

return [

    // Mapea IDs de roles en DB → slugs estables (no dependas del número)
    'roles_by_id' => [
        1 => 'admin',
        2 => 'supervisor',
        3 => 'customer',      // FIXED: was 'editor', but DB shows 'Customer'
    ],

    // Abilities → lista de roles (slugs) que pueden ejercerlos
    'abilities' => [
        'access-admin'            => ['admin', 'supervisor'],  // FIXED: removed 'editor', added only actual admin roles
        'manage-users'            => ['admin'],

        // Reseñas (contenido)
        'manage-reviews'          => ['admin', 'supervisor'],  // FIXED: removed 'editor'

        // Proveedores / requests
        'manage-review-providers' => ['admin', 'supervisor'],
        'manage-review-requests'  => ['admin', 'supervisor'],

        // Tours / catálogo
        'tours.manage'            => ['admin', 'supervisor'],  // FIXED: removed 'editor', changed to 'supervisor'
    ],
];
