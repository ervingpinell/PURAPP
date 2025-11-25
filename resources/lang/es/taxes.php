<?php

return [
    'title' => 'Gestión de Impuestos',
    'create' => 'Crear Impuesto',
    'edit' => 'Editar Impuesto',
    'included' => 'Incluido',
    'not_included' => 'No Incluido',
    'mixed' => 'Mixto',
    'fields' => [
        'name' => 'Nombre',
        'code' => 'Código',
        'rate' => 'Tasa/Monto',
        'type' => 'Tipo',
        'apply_to' => 'Aplicar a',
        'is_inclusive' => 'Incluido en precio',
        'is_active' => 'Activo',
        'sort_order' => 'Orden',
    ],
    'types' => [
        'percentage' => 'Porcentaje (%)',
        'fixed' => 'Monto Fijo ($)',
    ],
    'apply_to_options' => [
        'subtotal' => 'Subtotal',
        'total' => 'Total (Cascada)',
        'per_person' => 'Por Persona',
    ],
    'messages' => [
        'created' => 'Impuesto creado exitosamente.',
        'updated' => 'Impuesto actualizado exitosamente.',
        'deleted' => 'Impuesto eliminado exitosamente.',
        'toggled' => 'Estado del impuesto actualizado.',
        'select_taxes' => 'Seleccione los impuestos que aplican a este tour.',
    ],
    'breakdown' => [
        'title' => 'Desglose de Precio',
        'subtotal' => 'Subtotal',
        'tax' => 'Impuesto',
        'total' => 'Total',
    ],
];
