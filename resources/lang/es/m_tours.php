<?php

/*************************************************************
 *  MÓDULO DE TRADUCCIONES: TOURS
 *  Archivo: resources/lang/es/m_tours.php
 *
 *  Índice (sección y línea de inicio)
 *  [01] COMMON           -> línea 23
 *  [02] AMENITY          -> línea 31
 *  [03] SCHEDULE         -> línea 106
 *  [04] ITINERARY_ITEM   -> línea 218
 *  [05] ITINERARY        -> línea 288
 *  [06] LANGUAGE         -> línea 364
 *  [07] TOUR             -> línea 453
 *  [08] IMAGES           -> línea 579
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title' => 'Éxito',
        'error_title'   => 'Error',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'fields' => [
            'name' => 'Nombre',
        ],

        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
        ],

        'ui' => [
            'page_title'    => 'Amenidades',
            'page_heading'  => 'Gestión de Amenidades',
            'list_title'    => 'Listado de Amenidades',

            'add'            => 'Añadir Amenidad',
            'create_title'   => 'Registrar Amenidad',
            'edit_title'     => 'Editar Amenidad',
            'save'           => 'Guardar',
            'update'         => 'Actualizar',
            'cancel'         => 'Cancelar',
            'close'          => 'Cerrar',
            'state'          => 'Estado',
            'actions'        => 'Acciones',
            'delete_forever' => 'Eliminar definitivamente',

            'processing' => 'Procesando...',
            'applying'   => 'Aplicando...',
            'deleting'   => 'Eliminando...',

            'toggle_on'  => 'Activar amenidad',
            'toggle_off' => 'Desactivar amenidad',

            'toggle_confirm_on_title'  => '¿Activar amenidad?',
            'toggle_confirm_off_title' => '¿Desactivar amenidad?',
            'toggle_confirm_on_html'   => 'La amenidad <b>:label</b> quedará activa.',
            'toggle_confirm_off_html'  => 'La amenidad <b>:label</b> quedará inactiva.',

            'delete_confirm_title' => '¿Eliminar definitivamente?',
            'delete_confirm_html'  => 'Se eliminará <b>:label</b> y no podrás deshacerlo.',

            'yes_continue' => 'Sí, continuar',
            'yes_delete'   => 'Sí, eliminar',

            'item_this' => 'esta amenidad',
        ],

        'success' => [
            'created'     => 'Amenidad creada correctamente.',
            'updated'     => 'Amenidad actualizada correctamente.',
            'activated'   => 'Amenidad activada correctamente.',
            'deactivated' => 'Amenidad desactivada correctamente.',
            'deleted'     => 'Amenidad eliminada definitivamente.',
        ],

        'error' => [
            'create' => 'No se pudo crear la amenidad.',
            'update' => 'No se pudo actualizar la amenidad.',
            'toggle' => 'No se pudo cambiar el estado de la amenidad.',
            'delete' => 'No se pudo eliminar la amenidad.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nombre inválido',
                'required' => 'El :attribute es obligatorio.',
                'string'   => 'El :attribute debe ser una cadena de texto.',
                'max'      => 'El :attribute no puede exceder :max caracteres.',
            ],
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'fields' => [
            'start_time'     => 'Inicio',
            'end_time'       => 'Fin',
            'label'          => 'Etiqueta',
            'label_optional' => 'Etiqueta (opcional)',
            'max_capacity'   => 'Capacidad máx.',
            'active'         => 'Activo',
        ],

        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
        ],

        'ui' => [
            'page_title'        => 'Horarios de Tours',
            'page_heading'      => 'Gestión de Horarios',

            'general_title'     => 'Horarios generales',
            'new_schedule'      => 'Nuevo horario',
            'new_general_title' => 'Nuevo horario general',
            'new'               => 'Nuevo',
            'edit_schedule'     => 'Editar horario',
            'edit_global'       => 'Editar (global)',

            'assign_existing'    => 'Asignar existente',
            'assign_to_tour'     => 'Asignar horario a ":tour"',
            'select_schedule'    => 'Selecciona un horario',
            'choose'             => 'Elige',
            'assign'             => 'Asignar',
            'new_for_tour_title' => 'Nuevo horario para ":tour"',

            'time_range'        => 'Horario',
            'state'             => 'Estado',
            'actions'           => 'Acciones',
            'schedule_state'    => 'Horario',
            'assignment_state'  => 'Asignación',
            'no_general'        => 'No hay horarios generales.',
            'no_tour_schedules' => 'Este tour aún no tiene horarios.',
            'no_label'          => 'Sin etiqueta',
            'assigned_count'    => 'horario(s) asignado(s)',

            'toggle_global_title'     => 'Activar/Desactivar (global)',
            'toggle_global_on_title'  => '¿Activar horario (global)?',
            'toggle_global_off_title' => '¿Desactivar horario (global)?',
            'toggle_global_on_html'   => 'Se activará <b>:label</b> para todos los tours.',
            'toggle_global_off_html'  => 'Se desactivará <b>:label</b> para todos los tours.',

            'toggle_on_tour'          => 'Activar en este tour',
            'toggle_off_tour'         => 'Desactivar en este tour',
            'toggle_assign_on_title'  => '¿Activar en este tour?',
            'toggle_assign_off_title' => '¿Desactivar en este tour?',
            'toggle_assign_on_html'   => 'La asignación quedará <b>activa</b> para <b>:tour</b>.',
            'toggle_assign_off_html'  => 'La asignación quedará <b>inactiva</b> para <b>:tour</b>.',

            'detach_from_tour'     => 'Quitar del tour',
            'detach_confirm_title' => '¿Quitar del tour?',
            'detach_confirm_html'  => 'El horario se <b>desasignará</b> de <b>:tour</b>.',

            'delete_forever'       => 'Eliminar (global)',
            'delete_confirm_title' => '¿Eliminar definitivamente?',
            'delete_confirm_html'  => 'Se eliminará <b>:label</b> (global) y no podrás deshacerlo.',

            'yes_continue' => 'Sí, continuar',
            'yes_delete'   => 'Sí, eliminar',
            'yes_detach'   => 'Sí, quitar',

            'this_schedule' => 'este horario',
            'this_tour'     => 'este tour',

            'processing'     => 'Procesando...',
            'applying'       => 'Aplicando...',
            'deleting'       => 'Eliminando...',
            'removing'       => 'Quitando...',
            'saving_changes' => 'Guardando cambios...',
            'save'           => 'Guardar',
            'save_changes'   => 'Guardar cambios',
            'cancel'         => 'Cancelar',

            'missing_fields_title' => 'Faltan datos',
            'missing_fields_text'  => 'Revisa los campos requeridos (inicio, fin y capacidad).',
            'could_not_save'       => 'No se pudo guardar',
        ],

        'success' => [
            'created'                => 'Horario creado correctamente.',
            'updated'                => 'Horario actualizado correctamente.',
            'activated_global'       => 'Horario activado correctamente (global).',
            'deactivated_global'     => 'Horario desactivado correctamente (global).',
            'attached'               => 'Horario asignado al tour.',
            'detached'               => 'Horario eliminado del tour correctamente.',
            'assignment_activated'   => 'Asignación activada para este tour.',
            'assignment_deactivated' => 'Asignación desactivada para este tour.',
            'deleted'                => 'Horario eliminado correctamente.',
        ],

        'error' => [
            'create'               => 'Hubo un problema al crear el horario.',
            'update'               => 'Hubo un problema al actualizar el horario.',
            'toggle'               => 'No se pudo cambiar el estado global del horario.',
            'attach'               => 'No se pudo asignar el horario al tour.',
            'detach'               => 'No se pudo desasignar el horario del tour.',
            'assignment_toggle'    => 'No se pudo cambiar el estado de la asignación.',
            'not_assigned_to_tour' => 'El horario no está asignado a este tour.',
            'delete'               => 'Hubo un problema al eliminar el horario.',
        ],
    ],

    // =========================================================
    // [04] ITINERARY_ITEM
    // =========================================================
    'itinerary_item' => [
        'fields' => [
            'title'       => 'Título',
            'description' => 'Descripción',
        ],

        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
        ],

        'ui' => [
            'list_title'    => 'Ítems de Itinerario',
            'add_item'      => 'Añadir Ítem',
            'register_item' => 'Registrar Ítem',
            'edit_item'     => 'Editar Ítem',
            'save'          => 'Guardar',
            'update'        => 'Actualizar',
            'cancel'        => 'Cancelar',
            'state'         => 'Estado',
            'actions'       => 'Acciones',
            'see_more'      => 'Ver más',
            'see_less'      => 'Ver menos',

            'toggle_on'  => 'Activar ítem',
            'toggle_off' => 'Desactivar ítem',

            'delete_forever'       => 'Eliminar definitivamente',
            'delete_confirm_title' => '¿Eliminar definitivamente?',
            'delete_confirm_html'  => 'Se eliminará <b>:label</b> y no podrás deshacerlo.',
            'yes_delete'           => 'Sí, eliminar',
            'item_this'            => 'este ítem',

            'processing' => 'Procesando...',
            'applying'   => 'Aplicando...',
            'deleting'   => 'Eliminando...',
        ],

        'success' => [
            'created'     => 'Ítem de itinerario creado correctamente.',
            'updated'     => 'Ítem actualizado correctamente.',
            'activated'   => 'Ítem activado correctamente.',
            'deactivated' => 'Ítem desactivado correctamente.',
            'deleted'     => 'Ítem eliminado definitivamente.',
        ],

        'error' => [
            'create' => 'No se pudo crear el ítem.',
            'update' => 'No se pudo actualizar el ítem.',
            'toggle' => 'No se pudo cambiar el estado del ítem.',
            'delete' => 'No se pudo eliminar el ítem.',
        ],

        'validation' => [
            'title' => [
                'required' => 'El :attribute es obligatorio.',
                'string'   => 'El :attribute debe ser una cadena de texto.',
                'max'      => 'El :attribute no puede exceder :max caracteres.',
            ],
            'description' => [
                'required' => 'La :attribute es obligatoria.',
                'string'   => 'La :attribute debe ser una cadena de texto.',
                'max'      => 'La :attribute no puede exceder :max caracteres.',
            ],
        ],
    ],

    // =========================================================
    // [05] ITINERARY
    // =========================================================
    'itinerary' => [
        'fields' => [
            'name'                 => 'Nombre del itinerario',
            'description'          => 'Descripción',
            'description_optional' => 'Descripción (opcional)',
        ],

        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
        ],

        'ui' => [
            'page_title'    => 'Itinerarios y Ítems',
            'page_heading'  => 'Itinerarios y Gestión de Ítems',
            'new_itinerary' => 'Nuevo Itinerario',

            'assign'        => 'Asignar',
            'edit'          => 'Editar',
            'save'          => 'Guardar',
            'cancel'        => 'Cancelar',
            'close'         => 'Cerrar',
            'create_title'  => 'Crear nuevo itinerario',
            'create_button' => 'Crear',

            'toggle_on'  => 'Activar itinerario',
            'toggle_off' => 'Desactivar itinerario',
            'toggle_confirm_on_title'  => '¿Activar itinerario?',
            'toggle_confirm_off_title' => '¿Desactivar itinerario?',
            'toggle_confirm_on_html'   => 'El itinerario <b>:label</b> quedará <b>activo</b>.',
            'toggle_confirm_off_html'  => 'El itinerario <b>:label</b> quedará <b>inactivo</b>.',
            'yes_continue' => 'Sí, continuar',

            'assign_title'          => 'Asignar ítems a :name',
            'drag_hint'             => 'Arrastra y suelta los ítems para definir el orden.',
            'drag_handle'           => 'Arrastrar para reordenar',
            'select_one_title'      => 'Debes seleccionar al menos un ítem',
            'select_one_text'       => 'Por favor, selecciona al menos un ítem para continuar.',
            'assign_confirm_title'  => '¿Asignar ítems seleccionados?',
            'assign_confirm_button' => 'Sí, asignar',
            'assigning'             => 'Asignando...',

            'no_items_assigned'       => 'No hay ítems asignados a este itinerario.',
            'itinerary_this'          => 'este itinerario',
            'processing'              => 'Procesando...',
            'saving'                  => 'Guardando...',
            'activating'              => 'Activando...',
            'deactivating'            => 'Desactivando...',
            'applying'                => 'Aplicando...',
            'deleting'                => 'Eliminando...',
            'flash_success_title'     => 'Éxito',
            'flash_error_title'       => 'Error',
            'validation_failed_title' => 'No se pudo procesar',
        ],

        'success' => [
            'created'        => 'Itinerario creado correctamente.',
            'updated'        => 'Itinerario actualizado correctamente.',
            'activated'      => 'Itinerario activado correctamente.',
            'deactivated'    => 'Itinerario desactivado correctamente.',
            'deleted'        => 'Itinerario eliminado definitivamente.',
            'items_assigned' => 'Ítems asignados correctamente.',
        ],

        'error' => [
            'create'  => 'No se pudo crear el itinerario.',
            'update'  => 'No se pudo actualizar el itinerario.',
            'toggle'  => 'No se pudo cambiar el estado del itinerario.',
            'delete'  => 'No se pudo eliminar el itinerario.',
            'assign'  => 'No se pudieron asignar los ítems.',
        ],

        'validation' => [
            'name' => [
                'required' => 'El nombre del itinerario es obligatorio.',
                'string'   => 'El nombre debe ser texto.',
                'max'      => 'El nombre no puede exceder 255 caracteres.',
                'unique'   => 'Ya existe un itinerario con ese nombre.',
            ],
            'description' => [
                'string' => 'La descripción debe ser texto.',
                'max'    => 'La descripción no puede exceder 1000 caracteres.',
            ],
            'items' => [
                'required'      => 'Debes seleccionar al menos un ítem.',
                'array'         => 'El formato de los ítems no es válido.',
                'min'           => 'Debes seleccionar al menos un ítem.',
                'order_integer' => 'El orden debe ser un número entero.',
                'order_min'     => 'El orden no puede ser negativo.',
                'order_max'     => 'El orden no puede exceder 9999.',
            ],
        ],
    ],

    // =========================================================
    // [06] LANGUAGE
    // =========================================================
    'language' => [
        'fields' => [
            'name' => 'Idioma',
        ],

        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
        ],

        'ui' => [
            'page_title'   => 'Idiomas de Tours',
            'page_heading' => 'Gestión de Idiomas',
            'list_title'   => 'Listado de Idiomas',

            'table' => [
                'id'      => 'ID',
                'name'    => 'Idioma',
                'state'   => 'Estado',
                'actions' => 'Acciones',
            ],

            'add'            => 'Añadir Idioma',
            'create_title'   => 'Registrar Idioma',
            'edit_title'     => 'Editar Idioma',
            'save'           => 'Guardar',
            'update'         => 'Actualizar',
            'cancel'         => 'Cancelar',
            'close'          => 'Cerrar',
            'actions'        => 'Acciones',
            'delete_forever' => 'Eliminar definitivamente',

            'processing'   => 'Procesando...',
            'saving'       => 'Guardando...',
            'activating'   => 'Activando...',
            'deactivating' => 'Desactivando...',
            'deleting'     => 'Eliminando...',

            'toggle_on'  => 'Activar idioma',
            'toggle_off' => 'Desactivar idioma',
            'toggle_confirm_on_title'  => '¿Activar idioma?',
            'toggle_confirm_off_title' => '¿Desactivar idioma?',
            'toggle_confirm_on_html'   => 'El idioma <b>:label</b> quedará <b>activo</b>.',
            'toggle_confirm_off_html'  => 'El idioma <b>:label</b> quedará <b>inactivo</b>.',
            'edit_confirm_title'       => '¿Guardar cambios?',
            'edit_confirm_button'      => 'Sí, guardar',

            'yes_continue' => 'Sí, continuar',
            'yes_delete'   => 'Sí, eliminar',
            'item_this'    => 'este idioma',

            'flash' => [
                'activated_title'   => 'Idioma Activado',
                'deactivated_title' => 'Idioma Desactivado',
                'updated_title'     => 'Idioma Actualizado',
                'created_title'     => 'Idioma Registrado',
                'deleted_title'     => 'Idioma Eliminado',
            ],
        ],

        'success' => [
            'created'     => 'Idioma creado exitosamente.',
            'updated'     => 'Idioma actualizado correctamente.',
            'activated'   => 'Idioma activado correctamente.',
            'deactivated' => 'Idioma desactivado correctamente.',
            'deleted'     => 'Idioma eliminado correctamente.',
        ],

        'error' => [
            'create' => 'No se pudo crear el idioma.',
            'update' => 'No se pudo actualizar el idioma.',
            'toggle' => 'No se pudo cambiar el estado del idioma.',
            'delete' => 'No se pudo eliminar el idioma.',
            'save'   => 'No se pudo guardar',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nombre inválido',
                'required' => 'El nombre del idioma es obligatorio.',
                'string'   => 'El :attribute debe ser una cadena de texto.',
                'max'      => 'El :attribute no puede exceder :max caracteres.',
                'unique'   => 'Ya existe un idioma con ese nombre.',
            ],
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'title' => 'Tours',

        'fields' => [
            'id'            => 'ID',
            'name'          => 'Nombre',
            'overview'      => 'Resumen',
            'amenities'     => 'Amenidades',
            'exclusions'    => 'Exclusiones',
            'itinerary'     => 'Itinerario',
            'languages'     => 'Idiomas',
            'schedules'     => 'Horarios',
            'adult_price'   => 'Precio Adulto',
            'kid_price'     => 'Precio Niño',
            'length_hours'  => 'Duración (horas)',
            'max_capacity'  => 'Cupo máximo',
            'type'          => 'Tipo de Tour',
            'viator_code'   => 'Código Viator',
            'status'        => 'Estado',
            'actions'       => 'Acciones',
        ],

        'table' => [
            'id'            => 'ID',
            'name'          => 'Nombre',
            'overview'      => 'Resumen',
            'amenities'     => 'Amenidades',
            'exclusions'    => 'Exclusiones',
            'itinerary'     => 'Itinerario',
            'languages'     => 'Idiomas',
            'schedules'     => 'Horarios',
            'adult_price'   => 'Precio Adulto',
            'kid_price'     => 'Precio Niño',
            'length_hours'  => 'Duración (h)',
            'max_capacity'  => 'Cupo Máx.',
            'type'          => 'Tipo',
            'viator_code'   => 'Código Viator',
            'status'        => 'Estado',
            'actions'       => 'Acciones',
            'slug'          => 'URL',
        ],

        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
            'archived' => 'Archivado',
        ],

        'success' => [
            'created'     => 'El tour fue creado correctamente.',
            'updated'     => 'El tour fue actualizado correctamente.',
            'deleted'     => 'El tour fue eliminado.',
            'toggled'     => 'El estado del tour fue actualizado.',
            'activated'   => 'Tour activado correctamente.',
            'deactivated' => 'Tour desactivado correctamente.',
            // nuevos
            'archived'    => 'Tour archivado correctamente.',
            'restored'    => 'Tour restaurado correctamente.',
            'purged'      => 'Tour eliminado permanentemente.',
        ],

        'error' => [
            'create'    => 'No se pudo crear el tour.',
            'update'    => 'No se pudo actualizar el tour.',
            'delete'    => 'No se pudo eliminar el tour.',
            'toggle'    => 'No se pudo cambiar el estado del tour.',
            'not_found' => 'El tour no existe.',
            // nuevos
            'restore'            => 'No se pudo restaurar el tour.',
            'purge'              => 'No se pudo eliminar permanentemente el tour.',
            'purge_has_bookings' => 'No se puede eliminar permanentemente: el tour tiene reservas.',
        ],

        'ui' => [
            'page_title'       => 'Gestión de Tours',
            'page_heading'     => 'Gestión de Tours',
            'create_title'     => 'Registrar Tour',
            'edit_title'       => 'Editar Tour',
            'delete_title'     => 'Eliminar Tour',
            'cancel'           => 'Cancelar',
            'save'             => 'Guardar',
            'update'           => 'Actualizar',
            'delete_confirm'   => '¿Eliminar este tour?',
            'toggle_on'        => 'Activar',
            'toggle_off'       => 'Desactivar',
            'toggle_on_title'  => '¿Activar tour?',
            'toggle_off_title' => '¿Desactivar tour?',
            'toggle_on_button' => 'Sí, activar',
            'toggle_off_button'=> 'Sí, desactivar',
            'see_more'         => 'Ver más',
            'see_less'         => 'Ocultar',
            'load_more'        => 'Cargar más',
            'loading'          => 'Cargando...',
            'load_more_error'  => 'No se pudieron cargar más tours.',
            'confirm_title'    => 'Confirmación',
            'confirm_text'     => '¿Deseas confirmar esta acción?',
            'yes_confirm'      => 'Sí, confirmar',
            'no_confirm'       => 'No, cancelar',
            'add_tour'         => 'Añadir Tour',
            'edit_tour'        => 'Editar Tour',
            'delete_tour'      => 'Eliminar Tour',
            'toggle_tour'      => 'Activar/Desactivar Tour',
            'view_cart'        => 'Ver Carrito',
            'add_to_cart'      => 'Añadir al Carrito',
            'slug_help'        => 'Identificador del tour en la URL (sin espacios ni tildes)',
             'generate_auto'       => 'Generar automáticamente',
            'slug_preview_label'  => 'Vista previa',
            'saved'               => 'Guardado',
            // claves extra de UI (ya usadas en el Blade)
            'available_languages'    => 'Idiomas disponibles',
            'default_capacity'       => 'Cupo por defecto',
            'create_new_schedules'   => 'Crear horarios nuevos',
            'multiple_hint_ctrl_cmd' => 'Mantén CTRL/CMD para seleccionar varios',
            'use_existing_schedules' => 'Usar horarios existentes',
            'add_schedule'           => 'Añadir horario',
            'schedules_title'        => 'Horarios del Tour',
            'amenities_included'     => 'Amenidades incluidas',
            'amenities_excluded'     => 'Amenidades no incluidas',
            'color'                  => 'Color del Tour',
            'remove'                 => 'Eliminar',
            'choose_itinerary'       => 'Elegir itinerario',
            'select_type'            => 'Seleccionar tipo',
            'empty_means_default'    => 'Por defecto',
            'actives'                 => 'Activos',
            'inactives'               => 'Inactivos',
            'archived'                => 'Archivados',
            'all'                     => 'Todos',

            'none' => [
                'amenities'       => 'Sin amenidades',
                'exclusions'      => 'Sin exclusiones',
                'itinerary'       => 'Sin itinerario',
                'itinerary_items' => 'Sin ítems',
                'languages'       => 'Sin idiomas',
                'schedules'       => 'Sin horarios',
            ],

            // NUEVO: acciones de archivado/restauración/purga
            'archive' => 'Archivar',
            'restore' => 'Restaurar',
            'purge'   => 'Eliminar definitivamente',

            'confirm_archive_title' => '¿Archivar tour?',
            'confirm_archive_text'  => 'El tour quedará inhabilitado para nuevas reservas, pero las reservas existentes se conservan.',
            'confirm_purge_title'   => 'Eliminar definitivamente',
            'confirm_purge_text'    => 'Esta acción es irreversible y solo se permite si el tour nunca tuvo reservas.',

            // Filtros de estado
            'filters' => [
                'active'   => 'Activos',
                'inactive' => 'Inactivos',
                'archived' => 'Archivados',
                'all'      => 'Todos',
            ],

            // Toolbar de fuente (usado en tourlist.blade.php)
            'font_decrease_title' => 'Disminuir tamaño de fuente',
            'font_increase_title' => 'Aumentar tamaño de fuente',
        ],
    ],

    // =========================================================
    // [08] IMAGES
    // =========================================================
    'image' => [

        'limit_reached_title' => 'Límite alcanzado',
        'limit_reached_text'  => 'Se alcanzó el límite de imágenes para este tour.',
        'upload_success'      => 'Imágenes subidas correctamente.',
        'upload_none'         => 'No se subieron imágenes.',
        'upload_truncated'    => 'Algunos archivos se omitieron por el límite por tour.',
        'done'                => 'Listo',
        'notice'              => 'Aviso',
        'saved'               => 'Guardar',
        'caption_updated'     => 'Leyenda actualizada correctamente.',
        'deleted'             => 'Eliminado',
        'image_removed'       => 'Imagen eliminada correctamente.',
        'invalid_order'       => 'Carga de orden inválida.',
        'nothing_to_reorder'  => 'Nada que reordenar.',
        'order_saved'         => 'Orden guardado.',
        'cover_updated_title' => 'Actualizar portada',
        'cover_updated_text'  => 'Esta imagen ahora es la portada.',
        'deleting'            => 'Eliminando...',

        'ui' => [
            'page_title_pick'     => 'Imágenes de Tours',
            'page_heading'        => 'Imágenes de Tours',
            'choose_tour'         => 'Elegir tour',
            'search_placeholder'  => 'Buscar por ID o nombre…',
            'search_button'       => 'Buscar',
            'no_results'          => 'No se encontraron tours.',
            'manage_images'       => 'Administrar imágenes',
            'cover_alt'           => 'Portada',
            'images_label'        => 'imágenes',
            'upload_btn'          => 'Subir',
            'caption_placeholder' => 'Leyenda (opcional)',
            'set_cover_btn'       => 'Elige la imagen que quieres como portada',
            'no_images'           => 'Aún no hay imágenes para este tour.',
            'delete_btn'          => 'Eliminar',
            'show_btn'            => 'Mostrar',
            'close_btn'           => 'Cerrar',
            'preview_title'       => 'Vista previa de la imagen',

            'error_title'         => 'Error',
            'warning_title'       => 'Atención',
            'success_title'       => 'Éxito',
            'cancel_btn'          => 'Cancelar',
            'confirm_delete_title'=> '¿Eliminar esta imagen?',
            'confirm_delete_text' => 'Esta acción no se puede deshacer.',
        ],

        'errors' => [
            'validation'     => 'Los datos enviados no son válidos.',
            'upload_generic' => 'No se pudieron subir algunas imágenes.',
            'update_caption' => 'No se pudo actualizar la leyenda.',
            'delete'         => 'No se pudo eliminar la imagen.',
            'reorder'        => 'No se pudo guardar el orden.',
            'set_cover'      => 'No se pudo establecer la portada.',
            'load_list'      => 'No se pudo cargar el listado.',
            'too_large'      => 'El archivo supera el tamaño máximo permitido. Intenta con una imagen más liviana.',
        ],
    ],

];
