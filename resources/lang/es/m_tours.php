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
        'optional' => 'opcional',
        'error_title'   => 'Error',
        'people' => 'personas',
        'hours' => 'horas',
        'success' => 'Éxito',
        'error' => 'Error',
        'cancel' => 'Cancelar',
        'confirm_delete' => 'Sí, eliminar',
        'unspecified' => 'Sin especificar',
        'no_description' => 'Sin descripción',
        'required_fields_title' => 'Campos requeridos',
        'required_fields_text' => 'Por favor completa los campos obligatorios: Nombre y Capacidad Máxima',
        'active' => 'Activo',
        'inactive' => 'Inactivo',
        'notice' => 'Aviso',
        'na'    => 'No configurado',
        'create' => 'Crear',
        'previous' => 'Retroceder',
        'info'               => 'Información',
        'close'              => 'Cerrar',
        'save'              => 'Guardar',
        'required'           => 'Este campo es obligatorio.',
        'add'                => 'Agregar',
        'translating'        => 'Traduciendo...',
        'error_translating'  => 'No se pudo traducir el texto.',
        'confirm' => 'confirmar',
        'yes' => 'Sí',
        'form_errors_title' => 'Por favor corrige los siguientes errores:',
        'delete' => 'Eliminar',
        'delete_all' => 'Eliminar Todo',
        'actions' => 'Acciones',
        'updated_at' => 'Última Actualización',
        'not_set' => 'No especificado',
        'error_deleting' => 'Ocurrió un error al eliminar. Por favor intenta de nuevo.',
        'error_saving' => 'Ocurrió un error al guardar. Por favor intenta de nuevo.',
        'crud_go_to_index' => 'Administrar :element',
        'validation_title' => 'Hay errores de validación',
        'ok'               => 'Aceptar',
        'confirm_delete_title' => '¿Eliminar este item?',
        'confirm_delete_text' => 'Esta acción no se puede deshacer',
        'saving' => 'Guardando...',
        'network_error' => 'Error de red',
        'custom' => 'Personalizado',

    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'singular' => 'amenidad',
        'plural'   => 'amenidades',
        'fields' => [
            'name' => 'Nombre',
            'icon' => 'Ícono (FontAwesome)',

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
            'included_required' => 'Debes seleccionar al menos una amenidad incluida.',
            'name' => [
                'title'    => 'Nombre inválido',
                'required' => 'El :attribute es obligatorio.',
                'string'   => 'El :attribute debe ser una cadena de texto.',
                'max'      => 'El :attribute no puede exceder :max caracteres.',
            ],
        ],

        'hints' => [
            'fontawesome' => 'Usa clases de FontAwesome, por ejemplo: "fas fa-check".',
        ],
        'quick_create' => [
            'button'           => 'Nueva amenidad',
            'title'            => 'Crear amenidad rápida',
            'name_label'       => 'Nombre de la amenidad',
            'icon_label'       => 'Icono (opcional)',
            'icon_placeholder' => 'Ej: fas fa-utensils',
            'icon_help'        => 'Usa una clase de icono de Font Awesome o déjalo en blanco.',
            'save'             => 'Guardar amenidad',
            'cancel'           => 'Cancelar',
            'saving'           => 'Guardando...',
            'error_generic'    => 'No se pudo crear la amenidad. Intenta de nuevo.',
            'go_to_index'         => 'Ver todas',
            'go_to_index_title'   => 'Ir al listado completo de amenidades',
            'success_title'       => 'Amenidad creada',
            'success_text'        => 'La amenidad se agregó a la lista del tour.',
            'error_title'         => 'Error al crear la amenidad',
            'error_duplicate'     => 'Ya existe una amenidad con ese nombre.',
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'plural'       => 'Horarios',
        'singular'       => 'Horario',
        'fields' => [
            'start_time'     => 'Inicio',
            'end_time'       => 'Fin',
            'label'          => 'Etiqueta',
            'label_optional' => 'Etiqueta (opcional)',
            'max_capacity'   => 'Capacidad máx.',
            'active'         => 'Activo',
            'excluded_amenities' => 'Amenidades excluidas', // 🆕


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
            'base_capacity_tour'             => 'Capacidad base del tour:',
            'capacity_not_defined'           => 'No definida',
            'capacity_optional'              => 'Capacidad (opcional)',
            'capacity_placeholder_with_value' => 'Ej: :capacity',
            'capacity_placeholder_generic'   => 'Usar capacidad del tour',
            'capacity_hint_with_value'       => 'Dejar vacío → :capacity',
            'capacity_hint_generic'          => 'Dejar vacío → capacidad del tour',
            'tip_label'                      => 'Tip:',
            'capacity_tip'                   => 'Podés dejar vacía la capacidad para que el sistema use la capacidad general del tour (:capacity).',
            'new_schedule_for_tour'            => 'Nuevo horario',
            'modal_new_for_tour_title'         => 'Crear horario para :tour',
            'modal_save'                       => 'Guardar horario',
            'modal_cancel'                     => 'Cancelar',
            'capacity_modal_info_with_value'   => 'La capacidad base del tour es :capacity. Si dejás vacío el campo de capacidad, se usará este valor.',
            'capacity_modal_info_generic'      => 'Si dejás vacío el campo de capacidad, se usará la capacidad general del tour cuando esté definida.',
            'capacity_override'                => 'Capacidad Override',
            'use_tour_capacity'                => 'Usar capacidad del tour',
            'only_this_schedule'               => 'Solo para este horario en este tour',
            'leave_empty_for_base'             => 'Dejar vacío para usar capacidad base del tour: :capacity',
            'trash_title'                      => 'Papelera de Horarios',
            'trash_list_title'                 => 'Horarios Eliminados',
            'restore'                          => 'Restaurar',
            'empty_trash'                      => 'Papelera vacía',
            'deleted_at'                       => 'Fecha de eliminación',
            'deleted_by'                       => 'Eliminado por',
            'original_start'                   => 'Inicio Original',
            'original_end'                     => 'Fin Original',
            'back_to_list'                     => 'Volver a la lista',
            'success' => [
                'restored'      => 'Horario restaurado correctamente.',
                'force_deleted' => 'Horario eliminado permanentemente.',
            ],
            'error' => [
                'restore'      => 'No se pudo restaurar el horario.',
                'force_delete' => 'No se pudo eliminar permanentemente el horario.',
            ],

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
            'created_and_attached' => 'El horario se creó y asignó correctamente al tour',
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

        'placeholders' => [
            'morning' => 'Ej: Mañana',
        ],
        'validation' => [
            'no_schedule_selected' => 'Debes seleccionar al menos un horario',
            'title' => 'Validación de Horarios',
            'end_after_start' => 'La hora de fin debe ser posterior a la hora de inicio',
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
            'assigned_items'       => 'Ítems asignados al itinerario',
            'drag_to_order'        => 'Arrastra los ítems para definir el orden.',
            'pool_hint'            => 'Marca los ítems disponibles que quieras incluir en este itinerario.',
            'register_item_hint'   => 'Registra nuevos ítems si necesitas pasos adicionales que aún no existen.',
            'translations_updated' => 'Traducción actualizada',
            'toggle_on'  => 'Activar ítem',
            'toggle_off' => 'Desactivar ítem',

            'toggle_confirm_on_title'  => '¿Activar ítem?',
            'toggle_confirm_off_title' => '¿Desactivar ítem?',
            'toggle_confirm_on_html'   => 'El ítem <b>:label</b> quedará <b>activo</b>.',
            'toggle_confirm_off_html'  => 'El ítem <b>:label</b> quedará <b>inactivo</b>.',
            'yes_continue'             => 'Sí, continuar',
            'deactivating'             => 'Desactivando...',
            'activating'               => 'Activando...',

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
        'plural'           => 'Itinerarios',
        'singular'     => 'Itinerario',

        'fields' => [
            'name'                 => 'Nombre del itinerario',
            'description'          => 'Descripción',
            'description_optional' => 'Descripción (opcional)',
            'items'                    => 'Ítems',
            'item_title'           => 'Título del ítem',
            'item_description'     => 'Descripción del ítem',

        ],

        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
        ],

        'ui' => [
            'page_title'    => 'Itinerarios y Ítems',
            'page_heading'  => 'Itinerarios y Gestión de Ítems',
            'new_itinerary' => 'Nuevo Itinerario',
            'translations_updated' => 'Traducción actualizada',
            'select_or_create_hint' => 'Selecciona un itinerario existente o crea uno nuevo para este tour.',
            'save_changes'          => 'Guarda el itinerario para aplicar los cambios al tour.',
            'select_existing' => 'Seleccionar itinerario existente',
            'create_new' => 'Crear nuevo itinerario',
            'add_item' => 'Agregar ítem',
            'min_one_item' => 'Debe haber al menos un ítem en el itinerario',
            'cannot_delete_item' => 'No se puede eliminar',
            'item_added' => 'Item agregado',
            'item_added_success' => 'El item se agregó correctamente al itinerario',
            'error_creating_item' => 'Error de validación al crear el item.',

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
            'go_to_crud' => 'Ir al Módulo',

            'trash_title'      => 'Papelera de Itinerarios',
            'trash_list_title' => 'Itinerarios Eliminados',
            'restore'          => 'Restaurar',
            'empty_trash'      => 'Papelera vacía',
            'deleted_at'       => 'Fecha de eliminación',
            'deleted_by'       => 'Eliminado por',
            'back_to_list'     => 'Volver a la lista',
        ],
        'modal' => [
            'create_itinerary' => 'Crear itinerario',
        ],

        'success' => [
            'created'        => 'Itinerario creado correctamente.',
            'updated'        => 'Itinerario actualizado correctamente.',
            'activated'      => 'Itinerario activado correctamente.',
            'deactivated'    => 'Itinerario desactivado correctamente.',
            'deleted'        => 'Itinerario eliminado definitivamente.',
            'items_assigned' => 'Ítems asignados correctamente.',
            'restored'       => 'Itinerario restaurado correctamente.',
            'force_deleted'  => 'Itinerario eliminado permanentemente.',
        ],

        'error' => [
            'create'  => 'No se pudo crear el itinerario.',
            'update'  => 'No se pudo actualizar el itinerario.',
            'toggle'  => 'No se pudo cambiar el estado del itinerario.',
            'delete'  => 'No se pudo eliminar el itinerario.',
            'assign'  => 'No se pudieron asignar los ítems.',
            'restore'      => 'No se pudo restaurar el itinerario.',
            'force_delete' => 'No se pudo eliminar permanentemente el itinerario.',
        ],

        'validation' => [
            'name_required' => 'Debes indicar un nombre para el itinerario.',
            'must_add_items' => 'Debes agregar al menos un item al nuevo itinerario',
            'title' => 'Validación de Itinerario',
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
                'item' => 'Ítem',
                'required'      => 'Debes seleccionar al menos un ítem.',
                'array'         => 'El formato de los ítems no es válido.',
                'min'           => 'Debes seleccionar al menos un ítem.',
                'order_integer' => 'El orden debe ser un número entero.',
                'order_min'     => 'El orden no puede ser negativo.',
                'order_max'     => 'El orden no puede exceder 9999.',
            ],
        ],
        'item' => 'Ítem',
        'items' => 'Ítems',

    ],

    // =========================================================
    // [06] LANGUAGE
    // =========================================================
    'language' => [
        'fields' => [
            'name' => 'Idioma',
            'code' => 'Código',
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
            'trash_title'      => 'Papelera de Idiomas',
            'trash_list_title' => 'Idiomas Eliminados',
            'restore'          => 'Restaurar',
            'empty_trash'      => 'Papelera vacía',
            'deleted_at'       => 'Fecha de eliminación',
            'deleted_by'       => 'Eliminado por',
            'back_to_list'     => 'Volver a la lista',
            'delete'           => 'Eliminar',
            'alerts' => [
                'delete_title' => '¿Eliminar este idioma?',
                'delete_text'  => 'El idioma se moverá a la papelera.',
            ],
        ],

        'success' => [
            'created'     => 'Idioma creado exitosamente.',
            'updated'     => 'Idioma actualizado correctamente.',
            'activated'   => 'Idioma activado correctamente.',
            'deactivated' => 'Idioma desactivado correctamente.',
            'deleted'     => 'Idioma eliminado correctamente.',
            'restored'      => 'Idioma restaurado correctamente.',
            'force_deleted' => 'Idioma eliminado permanentemente.',
        ],

        'error' => [
            'create' => 'No se pudo crear el idioma.',
            'update' => 'No se pudo actualizar el idioma.',
            'toggle' => 'No se pudo cambiar el estado del idioma.',
            'delete' => 'No se pudo eliminar el idioma.',
            'save'   => 'No se pudo guardar',
            'restore'      => 'No se pudo restaurar el idioma.',
            'force_delete' => 'No se pudo eliminar permanentemente el idioma.',
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
        'hints' => [
            'iso_639_1' => 'Código ISO 639-1, por ejemplo: es, en, fr.',
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [

        'validation' => [
            // Mensajes generales
            'required' => 'Este campo es obligatorio.',
            'min' => 'Este campo debe tener al menos :min caracteres.',
            'max' => 'Este campo no puede exceder :max caracteres.',
            'number' => 'Este campo debe ser un número válido.',
            'slug' => 'El slug solo puede contener letras minúsculas, números y guiones.',
            'color' => 'Por favor selecciona un color válido.',
            'select' => 'Por favor selecciona una opción.',

            // Mensajes específicos de campos
            'length_in_hours' => 'Duración en horas (ej: 2, 2.5, 4)',
            'max_capacity_help' => 'Número máximo de personas por tour',

            // Formularios
            'form_error_title' => '¡Atención!',
            'form_error_message' => 'Por favor corrige los errores en el formulario antes de continuar.',
            'saving' => 'Guardando...',

            // Éxito
            'success' => '¡Éxito!',
            'tour_type_created' => 'Tipo de tour creado exitosamente.',
            'language_created' => 'Idioma creado exitosamente.',

            // Errores
            'tour_type_error' => 'Error al crear el tipo de tour.',
            'languages_hint' => 'Selecciona los idiomas disponibles para este tour.',
        ],

        'wizard' => [
            // Títulos generales
            'create_new_tour' => 'Crear Nuevo Tour',
            'edit_tour' => 'Editar Tour',
            'step_number' => 'Paso :number',
            'edit_step' => 'Editar',
            'leave_warning' => 'Tienes cambios sin guardar en el tour. Si sales ahora, el borrador quedará en la base de datos. ¿Seguro que deseas salir?',
            'cancel_title'   => '¿Cancelar la configuración del tour?',
            'cancel_text'    => 'Si sales de este asistente, podrías perder cambios no guardados en este paso.',
            'cancel_confirm' => 'Sí, descartar cambios',
            'cancel_cancel'  => 'No, seguir editando',
            'details_validation_text' => 'Revisa los campos obligatorios del formulario de detalles antes de continuar.',
            'most_recent'  => 'Más reciente',
            'last_modified'  => 'Última modificación',
            'start_fresh'  => 'Empezar nuevamente',
            'draft_details'  => 'Detalles del borrador',
            'drafts_found'  => 'Se ha encontrado un borrador',
            'basic_info'  => 'Detalles',

            // Pasos del wizard
            'steps' => [
                'details' => 'Detalles Básicos',
                'itinerary' => 'Itinerario',
                'schedules' => 'Horarios',
                'amenities' => 'Amenidades',
                'prices' => 'Precios',
                'summary' => 'Resumen',
            ],

            // Acciones
            'save_and_continue' => 'Guardar y Continuar',
            'publish_tour' => 'Publicar Tour',
            'delete_draft' => 'Eliminar Borrador',
            'ready_to_publish' => '¿Listo para Publicar?',

            // Mensajes
            'details_saved' => 'Detalles guardados correctamente',
            'itinerary_saved' => 'Itinerario guardado correctamente',
            'schedules_saved' => 'Horarios guardados correctamente',
            'amenities_saved' => 'Amenidades guardadas correctamente',
            'prices_saved' => 'Precios guardados correctamente',
            'published_successfully' => '¡Tour publicado exitosamente!',
            'draft_cancelled' => 'Borrador eliminado',

            // Estados
            'draft_mode' => 'Modo Borrador',
            'draft_explanation' => 'Este tour se guardará como borrador hasta que completes todos los pasos y lo publiques.',
            'already_published' => 'Este tour ya ha sido publicado. Usa el editor normal para modificarlo.',
            'cannot_cancel_published' => 'No puedes cancelar un tour ya publicado',

            // Confirmaciones
            'confirm_cancel' => '¿Estás seguro de que deseas cancelar y eliminar este borrador?',

            // Summary
            'publish_explanation' => 'Revisa toda la información antes de publicar. Una vez publicado, el tour estará disponible para reservas.',
            'can_edit_later' => 'Podrás editar el tour después de publicarlo desde el panel de administración.',
            'incomplete_warning' => 'Algunos pasos están incompletos. Puedes publicar de todas formas, pero se recomienda completar toda la información.',

            // Checklist
            'checklist' => 'Lista de Verificación',
            'checklist_details' => 'Detalles básicos completados',
            'checklist_itinerary' => 'Itinerario configurado',
            'checklist_schedules' => 'Horarios agregados',
            'checklist_amenities' => 'Amenidades configuradas',
            'checklist_prices' => 'Precios establecidos',

            // Hints
            'hints' => [
                'status' => 'El estado se puede cambiar después de publicar',
            ],

            // Modal de drafts existentes
            'existing_drafts_title' => '¡Tienes tours en borrador sin terminar!',
            'existing_drafts_message' => 'Encontramos :count tour en borrador que no has completado.',
            'current_step' => 'Paso Actual',
            'step' => 'Paso',

            // Acciones del modal
            'continue_draft' => 'Continuar con este borrador',
            'delete_all_drafts' => 'Eliminar Todos los Borradores',
            'create_new_anyway' => 'Crear Nuevo Tour de Todos Modos',

            // Información adicional
            'drafts_info' => 'Puedes continuar editando un borrador existente, eliminarlo individualmente, eliminar todos los borradores, o crear un nuevo tour ignorando los borradores actuales.',

            // Confirmaciones de eliminación
            'confirm_delete_title' => '¿Eliminar este borrador?',
            'confirm_delete_message' => 'Esta acción no se puede deshacer. Se eliminará permanentemente el borrador:',
            'confirm_delete_all_title' => '¿Eliminar todos los borradores?',
            'confirm_delete_all_message' => 'Se eliminarán permanentemente :count borrador(es). Esta acción no se puede deshacer.',

            // Mensajes de éxito
            'draft_deleted' => 'Borrador eliminado exitosamente.',
            'all_drafts_deleted' => 'Se eliminaron :count borrador(es) exitosamente.',
            'continuing_draft' => 'Continuando con tu borrador...',

            // Mensajes de error
            'not_a_draft' => 'Este tour ya no es un borrador y no puede ser editado mediante el wizard.',
        ],

        'title' => 'Tours',

        'fields' => [
            'id'            => 'ID',
            'name'          => 'Nombre',
            'details'       => 'Detalles',
            'price'         => 'Precios',
            'overview'      => 'Resumen',
            'recommendations' => 'Recomendaciones', // 🆕
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
            'group_size' => 'Tamaño de grupo',

        ],


        'pricing' => [
            'already_added' => 'Esta categoría ya fue agregada',
            'configured_categories' => 'Categorías configuradas',
            'create_category' => 'Crear categoría',
            'note_title'              => 'Nota:',
            'note_text'               => 'Define aquí los precios base para cada categoría de cliente.',
            'manage_detailed_hint'    => ' Para gestión detallada, usa el botón "Gestionar Precios Detallados" arriba.',
            'price_usd'               => 'Precio (USD)',
            'min_quantity'            => 'Cantidad mínima',
            'max_quantity'            => 'Cantidad máxima',
            'status'                  => 'Estado',
            'active'                  => 'Activo',
            'no_categories'           => 'No hay categorías de clientes configuradas.',
            'create_categories_first' => 'Crear categorías primero',
            'page_title'         => 'Precios - :name',
            'header_title'       => 'Precios: :name',
            'back_to_tours'      => 'Volver a los tours',

            'configured_title'   => 'Categorías y precios configurados',
            'empty_title'        => 'No hay categorías configuradas para este tour.',
            'empty_hint'         => 'Usa el formulario a la derecha para agregar categorías.',

            'save_changes'       => 'Guardar cambios',
            'auto_disable_note'  => 'Los precios en $0 se desactivan automáticamente',
            'not_available_for_date' => 'No disponible para esta fecha',

            // Calendar price indicators
            'price_lower' => 'Precio más bajo',
            'price_higher' => 'Precio más alto',
            'price_normal' => 'Precio normal',
            'price_legend' => 'Leyenda de precios',

            'add_category'       => 'Agregar categoría',
            'period_name'        => 'Nombre del Periodo',
            'period_name_placeholder' => 'Ej. Temporada Alta',

            'all_assigned_title' => 'Todas las categorías están asignadas',
            'all_assigned_text'  => 'No hay más categorías disponibles para este tour.',

            'info_title'         => 'Información',
            'tour_label'         => 'Tour',
            'configured_count'   => 'Categorías configuradas',
            'active_count'       => 'Categorías activas',

            'fields_title'       => 'Campos',
            'rules_title'        => 'Reglas',

            'field_price'        => 'Precio',
            'field_min'          => 'Mínimo',
            'field_max'          => 'Máximo',
            'field_status'       => 'Estado',

            'rule_min_le_max'    => 'El mínimo debe ser menor o igual al máximo',
            'rule_zero_disable'  => 'Los precios en $0 se desactivan automáticamente',
            'rule_only_active'   => 'Solo las categorías activas aparecen en el sitio público',

            'status_active'      => 'Activo',
            'add_existing_category'      => 'Agregar categoría existente',
            'choose_category_placeholder' => 'Selecciona una categoría…',
            'add_button'                 => 'Agregar',
            'add_existing_hint'          => 'Añade solo las categorías de cliente necesarias para este tour.',
            'remove_category'            => 'Quitar categoría',
            'category_already_added'     => 'Esta categoría ya está agregada al tour.',
            'no_prices_preview'          => 'Aún no hay precios configurados.',

            // Seasonal pricing
            'valid_from'                  => 'Válido desde',
            'valid_until'                 => 'Válido hasta',
            'default_price'               => 'Precio por defecto',
            'seasonal_price'              => 'Precio temporal',
            'season_label'                => 'Temporada',
            'all_year'                    => 'Todo el año',
            'from'                        => 'Desde',
            'until'                       => 'Hasta',
            'date_overlap_warning'        => 'Las fechas se solapan con otro precio de esta categoría',
            'invalid_date_range'          => 'La fecha de inicio debe ser anterior a la fecha de fin',
            'add_period'                  => 'Agregar Periodo de Precios',
            'no_periods'                  => 'No hay periodos de precios definidos',
            'click_add_period'            => 'Haz clic en "Agregar Periodo de Precios" para comenzar',
            'confirm_remove_period'       => '¿Eliminar este periodo de precios?',
            'select_category_first'       => 'Por favor selecciona una categoría',
            'category_already_in_period'  => 'Esta categoría ya está agregada a este periodo',
            'add_at_least_one_period'     => 'Debes agregar al menos un periodo de precios',
            'add_at_least_one_category'   => 'Debes agregar al menos una categoría a cada periodo',
            'wizard_description'          => 'Define los precios por temporada y categoría de cliente',
            'category'                    => 'Categoría',
            'age_range'                   => 'Edad',
            'active'                      => 'Activo',
            'no_taxes'                    => 'No hay impuestos disponibles',
            'taxes'                       => 'Impuestos',
            'category_removed_success'    => 'Categoría eliminada exitosamente',
            'leave_empty_no_limit'        => 'Dejar vacío para sin límite',
            'category_added_success'      => 'Categoría agregada exitosamente',
            'period_removed_success'      => 'Periodo eliminado exitosamente',
            'period_added_success'        => 'Periodo agregado correctamente',
            'overlap_not_allowed_title'   => 'Rango de fechas no permitido',
            'overlap_not_allowed_text'    => 'Las fechas seleccionadas se solapan con otro periodo de precios. Ajusta el rango para que no se crucen.',
            'overlap_conflict_with'       => 'Conflicto con los siguientes periodos:',
            'duplicate_category_title'    => 'Categoría duplicada',
            'invalid_date_range_title'    => 'Rango de fechas inválido',
            'remove_category_confirm_text' => 'Se eliminará esta categoría del periodo',
            'validation_failed'           => 'Validación fallida',
            'are_you_sure'                => '¿Estás seguro?',
            'yes_delete'                  => 'Sí, eliminar',
            'cancel'                      => 'Cancelar',
            'attention'                   => 'Atención',
        ],
        'modal' => [
            'create_category' => 'Crear categoría',

            'fields' => [
                'name'          => 'Nombre',
                'age_from'      => 'Edad desde',
                'age_to'        => 'Edad hasta',
                'age_range'     => 'Rango de edad',
                'min'           => 'Mínimo',
                'max'           => 'Máximo',
                'order'         => 'Orden',
                'is_active'     => 'Activo',
                'auto_translate' => 'Traducir automáticamente',
            ],

            'placeholders' => [
                'name'              => 'Ej: Adulto, Niño, Infante',
                'age_to_optional'   => 'Dejar vacío para "+"',
            ],

            'hints' => [
                'age_to_empty_means_plus' => 'Si dejas la edad máxima vacía, se interpretará como "+" (por ejemplo 12+).',
                'min_le_max'              => 'El mínimo debe ser menor o igual al máximo.',
            ],

            'errors' => [
                'min_le_max' => 'El mínimo debe ser menor o igual al máximo.',
            ],
        ],

        'schedules_form' => [
            'available_title'        => 'Horarios Disponibles',
            'select_hint'            => 'Selecciona los horarios para este tour',
            'no_schedules'           => 'No hay horarios disponibles.',
            'create_schedules_link'  => 'Crear horarios',

            'create_new_title'       => 'Crear Horario Nuevo',
            'label_placeholder'      => 'Ej: Mañana, Tarde',
            'create_and_assign'      => 'Crear este horario y asignarlo al tour',

            'info_title'             => 'Información',
            'schedules_title'        => 'Horarios',
            'schedules_text'         => 'Selecciona uno o más horarios en los que este tour estará disponible.',
            'create_block_title'     => 'Crear Nuevo',
            'create_block_text'      => 'Si necesitas un horario que no existe, puedes crearlo desde aquí marcando la casilla "Crear este horario y asignarlo al tour".',

            'current_title'          => 'Horarios Actuales',
            'none_assigned'          => 'Sin horarios asignados',
        ],

        'summary' => [
            'preview_title'        => 'Vista Previa del Tour',
            'preview_text_create'  => 'Revisa toda la información antes de crear el tour.',
            'preview_text_update'  => 'Revisa toda la información antes de actualizar el tour.',

            'basic_details_title'  => 'Detalles Básicos',
            'description_title'    => 'Descripción',
            'prices_title'         => 'Precios por Categoría',
            'schedules_title'      => 'Horarios',
            'languages_title'      => 'Idiomas',
            'itinerary_title'      => 'Itinerario',
            'amenities_title'      => 'Amenidades',

            'table' => [
                'category' => 'Categoría',
                'price'    => 'Precio',
                'min_max'  => 'Mín-Máx',
                'status' =>    'Estado'
            ],

            'not_specified'        => 'Sin especificar',
            'slug_autogenerated'   => 'Se generará automáticamente',
            'no_description'       => 'Sin descripción',
            'no_active_prices'     => 'Sin precios activos configurados',
            'no_languages'         => 'Sin idiomas asignados',
            'none_included'        => 'Nada incluido especificado',
            'none_excluded'        => 'Nada excluido especificado',
            'date_range'           => 'Rango de Fechas',

            'units' => [
                'hours'  => 'horas',
                'people' => 'personas',
            ],

            'create_note' => 'Los horarios, precios, idiomas y amenidades se mostrarán aquí después de guardar el tour.',
        ],
        'alerts' => [
            'delete_title' => '¿Eliminar tour?',
            'delete_text'  => 'El tour se moverá a Eliminados. Podrás restaurarlo después.',
            'purge_title'  => '¿Eliminar definitivamente?',
            'purge_text'   => 'Esta acción es irreversible.',
            'purge_text_with_bookings' => 'Este tour tiene :count reserva(s). No se eliminarán; quedarán sin tour asociado.',
            'toggle_question_active'   => '¿Desactivar tour?',
            'toggle_question_inactive' => '¿Activar tour?',
        ],
        'flash' => [
            'created'       => 'Tour creado exitosamente.',
            'updated'       => 'Tour actualizado exitosamente.',
            'deleted_soft'  => 'Tour movido a Eliminados.',
            'restored'      => 'Tour restaurado exitosamente.',
            'purged'        => 'Tour eliminado definitivamente.',
            'toggled_on'    => 'Tour activado.',
            'toggled_off'   => 'Tour desactivado.',
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
            'prices'        => 'Precios',
            'capacity'      => 'Capacidad',
            'group_size'        => 'Max. Grupo'

        ],

        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
            'archived' => 'Archivado',
        ],
        'placeholders' => [
            'group_size' => 'Ej: 10',
        ],
        'hints' => [
            'group_size' => 'Tamaño del grupo por cada guía o general para este tour. (Este dato se muestra en la información del producto',
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
            'add_tour_type' => 'Añadir tipo de tour',
            'back' => 'Regresar',
            'page_title'       => 'Gestión de Tours',
            'page_heading'     => 'Gestión de Tours',
            'create_title'     => 'Registrar Tour',
            'edit_title'       => 'Editar Tour',
            'delete_title'     => 'Eliminar Tour',
            'cancel'           => 'Cancelar',
            'save'             => 'Guardar',
            'save_changes'     => 'Guardar cambios',
            'update'           => 'Actualizar',
            'delete_confirm'   => '¿Eliminar este tour?',
            'toggle_on'        => 'Activar',
            'toggle_off'       => 'Desactivar',
            'toggle_on_title'  => '¿Activar tour?',
            'toggle_off_title' => '¿Desactivar tour?',
            'toggle_on_button' => 'Sí, activar',
            'toggle_off_button' => 'Sí, desactivar',
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
            'delete'                 => 'Eliminar',
            'choose_itinerary'       => 'Elegir itinerario',
            'select_type'            => 'Seleccionar tipo',
            'empty_means_default'    => 'Por defecto',
            'actives'                 => 'Activos',
            'inactives'               => 'Inactivos',
            'archived'                => 'Archivados',
            'all'                     => 'Todos',
            'help_title'              => 'Ayuda',
            'amenities_included_hint' => 'Selecciona lo que está incluido en el tour.',
            'amenities_excluded_hint' => 'Selecciona lo que NO está incluido en el tour.',
            'help_included_title'     => 'Incluido',
            'help_included_text'      => 'Marca todo lo que está incluido en el precio del tour (transporte, comidas, entradas, equipo, guía, etc.).',
            'help_excluded_title'     => 'No Incluido',
            'help_excluded_text'      => 'Marca lo que el cliente debe pagar por separado o traer (propinas, bebidas alcohólicas, souvenirs, etc.).',
            'select_or_create_title' => 'Seleccionar o Crear Itinerario',
            'select_existing_items'  => 'Seleccionar Ítems Existentes',
            'name_hint'              => 'Nombre identificador para este itinerario',
            'click_add_item_hint'    => 'Haz clic en "Agregar Ítem" para crear ítems nuevos',
            'scroll_hint' => 'Desliza horizontalmente para ver más columnas',
            'no_schedules' => 'Sin horarios',

            // Badges de precios
            'prices_by_period' => 'Precios por Periodo',
            'period' => 'periodo',
            'periods' => 'periodos',
            'all_year' => 'Todo el año',
            'from' => 'Desde',
            'until' => 'Hasta',
            'no_prices' => 'Sin precios',

            'edit' => 'Editar',
            'slug_auto' => 'Se generará automáticamente',
            'deactivate' => 'Desactivar',
            'manage_prices' => 'Gestionar precios',
            'manage_images' => 'Gestionar imágenes',
            'manage_delete' => 'Eliminar',
            'added_to_cart' => 'Añadido al carrito',
            'add_language' => 'Añadir idioma',
            'added_to_cart_text' => 'El tour se agregó al carrito correctamente.',
            'amenities_excluded_auto_hint'    => 'Por defecto marcamos como “no incluidas” todas las amenidades que no seleccionaste como incluidas. Podés desmarcar las que no aplican al tour.',
            "quick_create_language_hint" => "Agrega un nuevo idioma rápidamente si no aparece en la lista.",
            "quick_create_type_hint" => "Agrega un nuevo tipo de tour rápidamente si no aparece en la lista.",


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
            // Página de selección de tour
            'page_title_pick'     => 'Imágenes de Tours',
            'page_heading'        => 'Imágenes de Tours',
            'choose_tour'         => 'Elegir tour',
            'search_placeholder'  => 'Buscar por ID o nombre…',
            'search_button'       => 'Buscar',
            'no_results'          => 'No se encontraron tours.',
            'manage_images'       => 'Administrar imágenes',
            'cover_alt'           => 'Portada',
            'images_label'        => 'imágenes',

            // Botones genéricos
            'upload_btn'          => 'Subir',
            'delete_btn'          => 'Eliminar',
            'show_btn'            => 'Mostrar',
            'close_btn'           => 'Cerrar',
            'preview_title'       => 'Vista previa de la imagen',

            // Textos generales de estado
            'error_title'         => 'Error',
            'warning_title'       => 'Atención',
            'success_title'       => 'Éxito',
            'cancel_btn'          => 'Cancelar',

            // Confirmaciones básicas
            'confirm_delete_title' => '¿Eliminar esta imagen?',
            'confirm_delete_text'  => 'Esta acción no se puede deshacer.',

            // Gestión de portada por formulario clásico
            'cover_current_title'    => 'Portada actual',
            'upload_new_cover_title' => 'Subir nueva portada',
            'cover_file_label'       => 'Archivo de portada',
            'file_help_cover'        => 'JPEG/PNG/WebP, 30 MB máx.',
            'id_label'               => 'ID',

            // Navegación / cabecera en vista de un tour
            'back_btn'          => 'Volver a la lista',

            // Stats (barra superior)
            'stats_images'      => 'Imágenes subidas',
            'stats_cover'       => 'Portadas definidas',
            'stats_selected'    => 'Seleccionadas',

            // Zona de subida
            'drag_or_click'     => 'Arrastra y suelta tus imágenes o haz clic para seleccionar.',
            'upload_help'       => 'Formatos permitidos: JPG, PNG, WebP. Tamaño máximo total 100 MB.',
            'select_btn'        => 'Elegir archivos',
            'limit_badge'       => 'Límite de :max imágenes alcanzado',
            'files_word'        => 'archivos',

            // Toolbar de selección múltiple
            'select_all'        => 'Seleccionar todas',
            'delete_selected'   => 'Eliminar seleccionadas',
            'delete_all'        => 'Eliminar todas',

            // Selector por imagen (chip)
            'select_image_title' => 'Seleccionar esta imagen',
            'select_image_aria'  => 'Seleccionar imagen :id',

            // Portada (chip / botón por tarjeta)
            'cover_label'       => 'Portada',
            'cover_btn'         => 'Hacer portada',

            // Estados de guardado / helpers JS
            'caption_placeholder' => 'Leyenda (opcional)',
            'saving_label'        => 'Guardando…',
            'saving_fallback'     => 'Guardando…',
            'none_label'          => 'Sin leyenda',
            'limit_word'          => 'Límite',

            // Confirmaciones avanzadas (JS)
            'confirm_set_cover_title' => '¿Establecer como portada?',
            'confirm_set_cover_text'  => 'Esta imagen será la portada principal del tour.',
            'confirm_btn'             => 'Sí, continuar',

            'confirm_bulk_delete_title' => '¿Eliminar las imágenes seleccionadas?',
            'confirm_bulk_delete_text'  => 'Se eliminarán definitivamente las imágenes seleccionadas.',

            'confirm_delete_all_title'  => '¿Eliminar todas las imágenes?',
            'confirm_delete_all_text'   => 'Se eliminarán todas las imágenes de este tour.',

            // Vista sin imágenes
            'no_images'           => 'Aún no hay imágenes para este tour.',
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

    'prices' => [
        'ui' => [
            'page_title'         => 'Precios - :name',
            'header_title'       => 'Precios: :name',
            'back_to_tours'      => 'Volver a los tours',

            'configured_title'   => 'Categorías y precios configurados',
            'empty_title'        => 'No hay categorías configuradas para este tour.',
            'empty_hint'         => 'Usa el formulario a la derecha para agregar categorías.',

            'save_changes'       => 'Guardar cambios',
            'auto_disable_note'  => 'Los precios en $0 se desactivan automáticamente',

            'add_category'       => 'Agregar categoría',

            'all_assigned_title' => 'Todas las categorías están asignadas',
            'all_assigned_text'  => 'No hay más categorías disponibles para este tour.',

            'info_title'         => 'Información',
            'tour_label'         => 'Tour',
            'configured_count'   => 'Categorías configuradas',
            'active_count'       => 'Categorías activas',

            'fields_title'       => 'Campos',
            'rules_title'        => 'Reglas',

            'field_price'        => 'Precio',
            'field_min'          => 'Mínimo',
            'field_max'          => 'Máximo',
            'field_status'       => 'Estado',

            'rule_min_le_max'    => 'El mínimo debe ser menor o igual al máximo',
            'rule_zero_disable'  => 'Los precios en $0 se desactivan automáticamente',
            'rule_only_active'   => 'Solo las categorías activas aparecen en el sitio público',
        ],

        'table' => [
            'category'   => 'Categoría',
            'age_range'  => 'Rango de edad',
            'price_usd'  => 'Precio (USD)',
            'min'        => 'Mín',
            'max'        => 'Máx',
            'status'     => 'Estado',
            'action'     => 'Acción',
            'active'     => 'Activo',
            'inactive'   => 'Inactivo',
        ],

        'forms' => [
            'select_placeholder'  => '-- Seleccionar --',
            'category'            => 'Categoría',
            'price_usd'           => 'Precio (USD)',
            'min'                 => 'Mínimo',
            'max'                 => 'Máximo',
            'create_disabled_hint' => 'Si el precio es $0, la categoría se creará desactivada',
            'add'                 => 'Agregar',
        ],

        'modal' => [
            'delete_title'   => 'Eliminar categoría',
            'delete_text'    => '¿Eliminar esta categoría de este tour?',
            'cancel'         => 'Cancelar',
            'delete'         => 'Eliminar',
            'delete_tooltip' => 'Eliminar categoría',
        ],

        'flash' => [
            'success' => 'Operación realizada con éxito.',
            'error'   => 'Ocurrió un error.',
        ],
        'alerts' => [
            'price_updated' => 'Precio actualizado correctamente',
            'price_created' => 'Categoría agregada al periodo correctamente',
            'price_deleted' => 'Precio eliminado correctamente',
            'status_updated' => 'Estado actualizado',
            'period_updated' => 'Fechas del periodo actualizadas',
            'period_deleted' => 'Periodo eliminado correctamente',

            'error_title' => 'Error',
            'error_unexpected' => 'Ha ocurrido un error inesperado',
            'error_delete_price' => 'No se pudo eliminar el precio',
            'error_add_category' => 'No se pudo agregar la categoría',
            'error_update_period' => 'No se pudieron actualizar las fechas del periodo',

            'attention' => 'Atención',
            'select_category_first' => 'Selecciona una categoría primero',
            'duplicate_category_title' => 'Categoría duplicada',
            'duplicate_category_text' => 'Esta categoría ya está agregada en este periodo',

            'confirm_delete_price_title' => '¿Eliminar precio?',
            'confirm_delete_price_text' => 'Esta acción no se puede deshacer.',
            'confirm_delete_period_title' => '¿Eliminar este periodo?',
            'confirm_delete_period_text' => 'Se eliminarán todos los precios asociados a este periodo.',
            'confirm_yes_delete' => 'Sí, eliminar',
            'confirm_cancel' => 'Cancelar',

            'no_categories' => 'Este periodo no tiene categorías',
        ],

        'js' => [
            'max_ge_min'            => 'El máximo debe ser mayor o igual al mínimo',
            'auto_disabled_tooltip' => 'Precio en $0 – desactivado automáticamente',
            'fix_errors'            => 'Corrige las cantidades mínimas y máximas',
        ],
        'quick_category' => [
            'title'                 => 'Crear categoría rápida',
            'button'                => 'Nueva categoría',
            'go_to_index'           => 'Ver todas las categorías',
            'go_to_index_title'     => 'Abrir el listado completo de categorías',
            'name_label'            => 'Nombre de la categoría',
            'age_from'              => 'Edad desde',
            'age_to'                => 'Edad hasta',
            'save'                  => 'Guardar categoría',
            'cancel'                => 'Cancelar',
            'saving'                => 'Guardando...',
            'success_title'         => 'Categoría creada',
            'success_text'          => 'La categoría se creó correctamente y se añadió al tour.',
            'error_title'           => 'Error',
            'error_generic'         => 'Ocurrió un problema al crear la categoría.',
            'created_ok'            => 'Categoría creada correctamente.',
            'no_limit'              => 'Dejar vacío para sin límite',
        ],

        'validation' => [
            'title' => 'Validación de Precios',
            'no_categories' => 'Debes agregar al menos una categoría de precio',
            'no_price_greater_zero' => 'Debe haber al menos una categoría con precio mayor a $0.00',
            'price_required' => 'El precio es obligatorio',
            'price_min' => 'El precio debe ser mayor o igual a 0',
            'age_to_greater_equal' => 'La edad hasta debe ser mayor o igual a la edad desde',
        ],
    ],

    'ajax' => [
        'category_created' => 'Categoría creada exitosamente',
        'category_error' => 'Error al crear la categoría',
        'language_created' => 'Idioma creado exitosamente',
        'language_error' => 'Error al crear el idioma.',
        'languages_hint' => 'Selecciona los idiomas disponibles para este tour.',
        'amenity_created' => 'Amenidad creada exitosamente',
        'amenity_error' => 'Error al crear la amenidad',
        'schedule_created' => 'Horario creado exitosamente',
        'schedule_error' => 'Error al crear el horario',
        'itinerary_created' => 'Itinerario creado exitosamente',
        'itinerary_error' => 'Error al crear el itinerario',
        'translation_error' => 'Error al traducir',
    ],

    'modal' => [
        'create_category' => 'Crear Nueva Categoría',
        'create_language' => 'Crear Nuevo Idioma',
        'create_amenity' => 'Crear Nueva Amenidad',
        'create_schedule' => 'Crear Nuevo Horario',
        'create_itinerary' => 'Crear Nuevo Itinerario',
    ],

    'validation' => [
        'slug_taken' => 'Este slug ya está en uso',
        'slug_available' => 'Slug disponible',
    ],
    'tour_type' => [
        'fields' => [
            'name' => 'Nombre',
            'description' => 'Descripción',
            'status' => 'Estado',
            'duration' => 'Duración',
            'duration_hint'        => 'Duración sugerida del tour (opcional)',
            'duration_placeholder' => 'Ej: 4 horas, 6 horas, etc.',
        ],

    ],
];
