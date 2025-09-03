<?php

return [

    // =========================================================
    // [01] AVAILABILITY
    // =========================================================
    'availability' => [
    'fields' => [
        'tour'        => 'Tour',
        'date'        => 'Fecha',
        'start_time'  => 'Hora de inicio',
        'end_time'    => 'Hora de fin',
        'available'   => 'Disponible',
        'is_active'   => 'Activo',
    ],

    'success' => [
        'created'     => 'Disponibilidad creada correctamente.',
        'updated'     => 'Disponibilidad actualizada correctamente.',
        'deactivated' => 'Disponibilidad desactivada correctamente.',
    ],

    'error' => [
        'create'     => 'No se pudo crear la disponibilidad.',
        'update'     => 'No se pudo actualizar la disponibilidad.',
        'deactivate' => 'No se pudo desactivar la disponibilidad.',
    ],

    'validation' => [
        'tour_id' => [
            'required' => 'El :attribute es obligatorio.',
            'integer'  => 'El :attribute debe ser un número entero.',
            'exists'   => 'El :attribute seleccionado no existe.',
        ],
        'date' => [
            'required'    => 'La :attribute es obligatoria.',
            'date_format' => 'La :attribute debe tener el formato YYYY-MM-DD.',
        ],
        'start_time' => [
            'date_format'   => 'La :attribute debe tener el formato HH:MM (24h).',
            'required_with' => 'La :attribute es obligatoria cuando se especifica la hora de fin.',
        ],
        'end_time' => [
            'date_format'    => 'La :attribute debe tener el formato HH:MM (24h).',
            'after_or_equal' => 'La :attribute debe ser mayor o igual a la hora de inicio.',
        ],
        'available' => [
            'boolean' => 'El campo :attribute es inválido.',
        ],
        'is_active' => [
            'boolean' => 'El :attribute es inválido.',
        ],
    ],

    'ui' => [
        'page_title'           => 'Disponibilidades',
        'page_heading'         => 'Disponibilidades',
        'blocked_page_title'   => 'Tours bloqueados',
        'blocked_page_heading' => 'Tours bloqueados',
        'tours_count'          => '( :count tours )',
        'blocked_count'        => '( :count bloqueados )',
    ],

    'filters' => [
        'date'               => 'Fecha',
        'days'               => 'Días',
        'product'            => 'Producto',
        'search_placeholder' => 'Buscar tour...',
        'update_state'       => 'Actualizar estado',
        'view_blocked'       => 'Ver bloqueados',
        'tip'                => 'Tip: marca filas y usa una acción del menú.',
    ],

    'blocks' => [
        'am_tours'    => 'AM tours (todos los tours que inicien antes de las 12:00pm)',
        'pm_tours'    => 'PM tours (todos los tours que inicien después de las 12:00pm)',
        'am_blocked'  => 'AM bloqueados',
        'pm_blocked'  => 'PM bloqueados',
        'empty_block' => 'Sin tours en este bloque.',
        'empty_am'    => 'Sin bloqueados en AM.',
        'empty_pm'    => 'Sin bloqueados en PM.',
        'no_data'     => 'No hay datos para los filtros seleccionados.',
        'no_blocked'  => 'No hay tours bloqueados en el rango seleccionado.',
    ],

    'states' => [
        'available' => 'Disponible',
        'blocked'   => 'Bloqueado',
    ],

    'buttons' => [
        'mark_all'         => 'Marcar todos',
        'unmark_all'       => 'Desmarcar todos',
        'block_all'        => 'Bloquear todos',
        'unblock_all'      => 'Desbloquear todos',
        'block_selected'   => 'Bloquear seleccionados',
        'unblock_selected' => 'Desbloquear seleccionados',
        'back'             => 'Volver',
        'open'             => 'Abrir',
        'cancel'           => 'Cancelar',
        'block'            => 'Bloquear',
        'unblock'          => 'Desbloquear',
    ],

    'confirm' => [
        'view_blocked_title'    => 'Ver tours bloqueados',
        'view_blocked_text'     => 'Se abrirá la vista con los tours bloqueados para desbloquearlos.',
        'block_title'           => '¿Bloquear tour?',
        'block_html'            => 'Se bloqueará <b>:label</b> para la fecha <b>:day</b>.',
        'block_btn'             => 'Sí, bloquear',
        'unblock_title'         => '¿Desbloquear tour?',
        'unblock_html'          => 'Se desbloqueará <b>:label</b> para la fecha <b>:day</b>.',
        'unblock_btn'           => 'Sí, desbloquear',
        'bulk_title'            => 'Confirmar acción',
        'bulk_items_html'       => 'Ítems a afectar: <b>:count</b>.',
        'bulk_block_day_html'   => 'Bloquear todos los disponibles del día <b>:day</b>',
        'bulk_block_block_html' => 'Bloquear todos los disponibles del bloque <b>:block</b> del <b>:day</b>',
    ],

    'toasts' => [
        'applying_filters'   => 'Aplicando filtros...',
        'searching'          => 'Buscando...',
        'updating_range'     => 'Actualizando rango...',
        'invalid_date_title' => 'Fecha inválida',
        'invalid_date_text'  => 'No se permiten fechas pasadas.',
        'marked_n'           => 'Marcados :n',
        'unmarked_n'         => 'Desmarcados :n',
        'updated'            => 'Cambio aplicado',
        'updated_count'      => 'Actualizados: :count',
        'unblocked_count'    => 'Desbloqueados: :count',
        'no_selection_title' => 'Sin selección',
        'no_selection_text'  => 'Marca al menos un tour.',
        'no_changes_title'   => 'Sin cambios',
        'no_changes_text'    => 'No hay ítems aplicables.',
        'error_generic'      => 'No se pudo completar la actualización.',
        'error_update'       => 'No se pudo actualizar.',
    ],
],

];
