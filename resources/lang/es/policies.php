<?php

return [

    'page_title' => 'Políticas',
    'no_policies' => 'No hay políticas disponibles por el momento.',
    'no_sections' => 'No hay secciones disponibles por el momento.',
    // =========================================================
    // [01] CAMPOS
    // =========================================================
    'fields' => [
        'title'       => 'Título',
        'description' => 'Descripción',
        'type'        => 'Tipo',
        'is_active'   => 'Activo',
    ],

    // =========================================================
    // [02] TIPOS
    // =========================================================
    'types' => [
        'cancellation' => 'Política de Cancelación',
        'refund'       => 'Política de Reembolso',
        'terms'        => 'Términos y Condiciones',
    ],

    // =========================================================
    // [03] MENSAJES
    // =========================================================
    'success' => [
        'created'   => 'Política creada correctamente.',
        'updated'   => 'Política actualizada correctamente.',
        'deleted'   => 'Política eliminada correctamente.',
    ],

    'error' => [
        'create' => 'No se pudo crear la política.',
        'update' => 'No se pudo actualizar la política.',
        'delete' => 'No se pudo eliminar la política.',
    ],
];
