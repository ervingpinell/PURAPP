<?php

/*************************************************************
 *  MÓDULO DE TRADUCCIONES: TOURS
 *  Archivo: resources/lang/es/m_tours.php
 *
 *  Índice (sección y línea de inicio)
 *  [01] COMMON           -> línea 19
 *  [02] AMENITY          -> línea 27
 *  [03] SCHEDULE         -> línea 90
 *  [04] ITINERARY_ITEM   -> línea 176
 *  [05] ITINERARY        -> línea 239
 *  [06] LANGUAGE         -> línea 302
 *  [07] TOUR             -> línea 386
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
        'fields' => [
            'id'           => 'ID',
            'name'         => 'Nombre',
            'overview'     => 'Resumen',
            'amenities'    => 'Amenidades',
            'exclusions'   => 'Exclusiones',
            'itinerary'    => 'Itinerario',
            'languages'    => 'Idiomas',
            'schedules'    => 'Horarios',
            'adult_price'  => 'Precio adulto',
            'kid_price'    => 'Precio niño',
            'length_hours' => 'Duración (h)',
            'max_capacity' => 'Cupo máx.',
            'type'         => 'Tipo',
            'viator_code'  => 'Código Viator',
            'status'       => 'Estado',
            'actions'      => 'Acciones',
        ],
        'table' => [
            'id'           => 'ID',
            'name'         => 'Nombre',
            'overview'     => 'Resumen',
            'amenities'    => 'Amenidades',
            'exclusions'   => 'Exclusiones',
            'itinerary'    => 'Itinerario',
            'languages'    => 'Idiomas',
            'schedules'    => 'Horarios',
            'adult_price'  => 'Precio adulto',
            'kid_price'    => 'Precio niño',
            'length_hours' => 'Duración (h)',
            'max_capacity' => 'Cupo máx.',
            'type'         => 'Tipo',
            'viator_code'  => 'Código Viator',
            'status'       => 'Estado',
            'actions'      => 'Acciones',
        ],
        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
        ],
        'ui' => [
            'page_title'   => 'Tours',
            'page_heading' => 'Gestión de Tours',

            'font_decrease_title' => 'Reducir tamaño de fuente',
            'font_increase_title' => 'Aumentar tamaño de fuente',

            'see_more' => 'Ver más',
            'see_less' => 'Ver menos',

            'none' => [
                'amenities'       => 'Sin amenidades',
                'exclusions'      => 'Sin exclusiones',
                'languages'       => 'Sin idiomas',
                'itinerary'       => 'Sin itinerario',
                'itinerary_items' => '(Sin ítems)',
                'schedules'       => 'Sin horarios',
            ],

            'toggle_on'         => 'Activar',
            'toggle_off'        => 'Desactivar',
            'toggle_on_title'   => '¿Deseas activar este tour?',
            'toggle_off_title'  => '¿Deseas desactivar este tour?',
            'toggle_on_button'  => 'Sí, activar',
            'toggle_off_button' => 'Sí, desactivar',

            'confirm_title'   => 'Confirmación',
            'confirm_text'    => '¿Confirmar acción?',
            'yes_confirm'     => 'Sí, confirmar',
            'cancel'          => 'Cancelar',

            'load_more'       => 'Cargar más',
            'loading'         => 'Cargando...',
            'load_more_error' => 'No se pudo cargar más',
        ],
        'success' => [
            'created'     => 'Tour creado correctamente.',
            'updated'     => 'Tour actualizado correctamente.',
            'activated'   => 'Tour activado correctamente.',
            'deactivated' => 'Tour desactivado correctamente.',
        ],
        'error' => [
            'create' => 'Hubo un problema al crear el tour.',
            'update' => 'Hubo un problema al actualizar el tour.',
            'toggle' => 'Hubo un problema al cambiar el estado del tour.',
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
    'saved'               => 'Guardado',
    'caption_updated'     => 'Leyenda actualizada correctamente.',
    'deleted'             => 'Eliminado',
    'image_removed'       => 'Imagen eliminada correctamente.',
    'invalid_order'       => 'Carga de orden inválida.',
    'nothing_to_reorder'  => 'Nada que reordenar.',
    'order_saved'         => 'Orden guardado.',
    'cover_updated_title' => 'Portada actualizada',
    'cover_updated_text'  => 'Esta imagen ahora es la portada.',

    'ui' => [
        'page_title_pick'   => 'Imágenes de Tours — Elegir tour',
        'page_heading'      => 'Imágenes de Tours',
        'choose_tour'       => 'Elegir tour',
        'search_placeholder'=> 'Buscar por ID o nombre…',
        'search_button'     => 'Buscar',
        'no_results'        => 'No se encontraron tours.',
        'manage_images'     => 'Administrar imágenes',
        'cover_alt'         => 'Portada',
    ],
],

];
