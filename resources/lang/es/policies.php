<?php

return [

    // =========================================================
    // [00] GENÉRICO
    // =========================================================
    'page_title'  => 'Políticas',
    'no_policies' => 'No hay políticas disponibles por el momento.',
    'no_sections' => 'No hay secciones disponibles por el momento.',
    'propagate_to_all_langs' => 'Propagar este cambio a todos los idiomas (EN, FR, DE, PT)',
    'propagate_hint'         => 'Se traducirá automáticamente desde el texto actual y se sobrescribirán las traducciones existentes en esos idiomas.',
    'update_base_es'         => 'Actualizar también la base (ES)',
    'update_base_hint'       => 'Sobrescribe el nombre y el contenido de la política en la tabla base (español). Úsalo solo si quieres que el texto original también cambie.',

    // =========================================================
    // [01] CHECKOUT
    // =========================================================
    'checkout' => [
        'card_title'  => 'Tu pedido',
        'details'     => 'Detalle',
        'must_accept' => 'Debes leer y aceptar todas las políticas para continuar con el pago.',
        'accept_label_html' =>
            'He leído y acepto los <strong>Términos y Condiciones</strong>, la <strong>Política de Privacidad</strong> y todas las <strong>Políticas de Cancelación, Devoluciones y Garantía</strong>.',
        'back'       => 'Volver',
        'pay'        => 'Procesar pago',
        'order_full' => 'Detalle completo del pedido',

        // Solo títulos genéricos (los contenidos vienen de BD / policies)
        'titles' => [
            'terms'        => 'Términos y Condiciones',
            'privacy'      => 'Política de Privacidad',
            'cancellation' => 'Política de Cancelación',
            'refunds'      => 'Política de Devoluciones',
            'warranty'     => 'Política de Garantía',
            'payments'     => 'Métodos de Pago',
        ],
    ],

    // =========================================================
    // [02] CAMPOS
    // =========================================================
    'fields' => [
        'title'       => 'Título',
        'description' => 'Descripción',
        'type'        => 'Tipo',
        'is_active'   => 'Activo',
    ],

    // =========================================================
    // [03] TIPOS
    // =========================================================
    'types' => [
        'cancellation' => 'Política de Cancelación',
        'refund'       => 'Política de Reembolso',
        'terms'        => 'Términos y Condiciones',
    ],

    // =========================================================
    // [04] MENSAJES
    // =========================================================
    'success' => [
        'created' => 'Política creada con éxito.',
        'updated' => 'Política actualizada con éxito.',
        'deleted' => 'Política eliminada con éxito.',
    ],

    'error' => [
        'create' => 'No se pudo crear la política.',
        'update' => 'No se pudo actualizar la política.',
        'delete' => 'No se pudo eliminar la política.',
    ],
];
