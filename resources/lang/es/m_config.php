<?php

/*************************************************************
 *  MÓDULO DE CONFIGURACIÓN – TRADUCCIONES (ES)
 *  Archivo: resources/lang/es/m_config.php
 *
 *  Índice (anclas buscables)
 *  [01] POLICIES LÍNEA 20
 *  [02] TOURTYPES LÍNEA 139
 *  [03] FAQ LÍNEA 198
 *  [04] TRANSLATIONS LÍNEA 249
 *  [05] PROMOCODE LÍNEA 359
 *  [06] CUT-OFF LÍNEA 436
 *************************************************************/

return [


    // =========================================================
    // ==== GLOBAL BUTTONS =====================================
    // =========================================================
    'buttons' => [
        'back' => 'Volver',
        'save' => 'Guardar',
        'cancel' => 'Cancelar',
        'close' => 'Cerrar',
    ],

    // =========================================================
    // ==== POLICIES ===========================================
    // =========================================================
    'policies' => [
        // Títulos / encabezados
        'categories_title'        => 'Políticas',
        'sections_title'          => 'Secciones',

        // Columnas / campos comunes
        'id'                      => 'ID',
        'internal_name'           => 'Nombre interno',
        'title_current_locale'    => 'Título',
        'validity_range'          => 'Rango de vigencia',
        'valid_from'              => 'Válida desde',
        'valid_to'                => 'Válida hasta',
        'status'                  => 'Estado',
        'sections'                => 'Secciones',
        'actions'                 => 'Acciones',
        'active'                  => 'Activo',
        'inactive'                => 'Inactivo',
        'slug'                    => 'URL',
        'slug_hint'               => 'opcional',
        'slug_auto_hint'          => 'Se generará automáticamente del nombre si se deja vacío',
        'slug_edit_hint'          => 'Cambia la URL de la política. Usa solo letras minúsculas, números y guiones.',
        'updated'                  => 'Política actualizada con éxito.',
        'propagate_to_all_langs' => 'Propagar este cambio a todos los idiomas (EN, FR, DE, PT)',
        'propagate_hint'         => 'Se traducirá automáticamente desde el texto actual y se sobrescribirán las traducciones existentes en esos idiomas.',
        'update_base_es'         => 'Actualizar también la base (ES)',
        'update_base_hint'       => 'Sobrescribe el nombre y el contenido de la política en la tabla base (español). Úsalo solo si quieres que el texto original también cambie.',
        'filter_active'    => 'Activos',
        'filter_inactive'  => 'Inactivos',
        'filter_archived'  => 'Eliminados',
        'filter_all'       => 'Todos',
        'trash_tab'        => 'Papelera',
        'trash_title'      => 'Papelera de Políticas',
        'trash_empty'      => 'No hay políticas eliminadas',

        'slug_hint'      => 'minúsculas, sin espacios, separada-por-guiones',
        'slug_auto_hint' => 'Si se deja vacío, se generará automáticamente a partir del título.',
        'slug_edit_hint' => 'Cambiar esta URL puede afectar enlaces públicos existentes.',

        // Type field
        'type'                    => 'Tipo',
        'type_optional'           => 'Opcional',
        'type_none'               => '-- Ninguno (Política Genérica) --',
        'type_description'        => 'Selecciona un tipo solo para políticas especiales que aparecen en el footer (Términos, Privacidad, etc.)',

        'valid_from' => 'Válida desde',
        'valid_to'   => 'Válida hasta',

        'move_to_trash'  => 'Mover a papelera',
        'in_trash'       => 'En la papelera',
        'moved_to_trash' => 'La categoría se movió a la papelera.',

        'restore_category'         => 'Restaurar',
        'restore_category_confirm' => '¿Restaurar esta categoría y todas sus secciones?',
        'restored_ok'              => 'La categoría se restauró correctamente.',

        'delete_permanently'         => 'Eliminar permanentemente',
        'delete_permanently_confirm' => '¿Eliminar permanentemente esta categoría y todas sus secciones? Esta acción no se puede deshacer.',
        'deleted_permanently'        => 'La categoría y sus secciones se eliminaron permanentemente.',
        'restore' => 'Restaurar',
        'force_delete_confirm' => '¿Eliminar permanentemente esta categoría y todas sus secciones? Esta acción no se puede deshacer.',
        'created' => 'Categoría de política creada correctamente.',

        // Lista de categorías: acciones
        'new_category'            => 'Nueva categoría',
        'view_sections'           => 'Ver secciones',
        'edit'                    => 'Editar',
        'activate_category'       => 'Activar categoría',
        'deactivate_category'     => 'Desactivar categoría',
        'delete'                  => 'Eliminar',
        'delete_category_confirm' => '¿Eliminar esta categoría y TODAS sus secciones?<br>Esta acción no se puede deshacer.',
        'no_categories'           => 'No se encontraron categorías.',
        'edit_category'           => 'Editar categoría',
        'deleted_by'              => 'Eliminado por',
        'deleted_at'              => 'Fecha de eliminación',

        // Formularios (categoría)
        'title_label'             => 'Título',
        'description_label'       => 'Descripción',
        'register'                => 'Crear',
        'save_changes'            => 'Guardar cambios',
        'close'                   => 'Cerrar',

        // Secciones
        'back_to_categories'      => 'Volver a categorías',
        'new_section'             => 'Nueva sección',
        'key'                     => 'Clave',
        'order'                   => 'Orden',
        'activate_section'        => 'Activar sección',
        'deactivate_section'      => 'Desactivar sección',
        'delete_section_confirm'  => '¿Seguro que deseas eliminar esta sección?<br>Esta acción no se puede deshacer.',
        'no_sections'             => 'No se encontraron secciones.',
        'edit_section'            => 'Editar sección',
        'internal_key_optional'   => 'Clave interna (opcional)',
        'content_label'           => 'Contenido',
        'section_content'         => 'Contenido',
        'base_content_hint'       => 'Este es el texto principal de la política. Se traducirá automáticamente a otros idiomas al crearla, pero puedes editar cada traducción después.',


        // Público
        'page_title'              => 'Políticas',
        'no_policies'             => 'No hay políticas disponibles por el momento.',
        'section'                 => 'Sección',
        'cancellation_policy'     => 'Política de cancelación',
        'refund_policy'           => 'Política de reembolso',
        'no_cancellation_policy'  => 'No hay política de cancelación configurada.',
        'no_refund_policy'        => 'No hay política de reembolso configurada.',

        // Mensajes (categorías)
        'category_created'        => 'Categoría creada correctamente.',
        'category_updated'        => 'Categoría actualizada correctamente.',
        'category_activated'      => 'Categoría activada correctamente.',
        'category_deactivated'    => 'Categoría desactivada correctamente.',
        'category_deleted'        => 'Categoría eliminada correctamente.',

        // --- NUEVAS CLAVES (refactor / utilidades) ---
        'untitled'                => 'Sin título',
        'no_content'              => 'Sin contenido disponible.',
        'display_name'            => 'Nombre a mostrar',
        'name'                    => 'Nombre',
        'name_base'               => 'Nombre base',
        'name_base_help'          => 'Identificador corto/slug de la sección (solo interno).',
        'translation_content'     => 'Contenido',
        'locale'                  => 'Idioma',
        'save'                    => 'Guardar',
        'name_base_label'         => 'Nombre base',
        'translation_name'        => 'Nombre traducido',
        'lang_autodetect_hint'    => 'Puedes escribir en cualquier idioma; se detecta automáticamente.',
        'bulk_edit_sections'      => 'Edición rápida de secciones',
        'bulk_edit_hint'          => 'Los cambios a todas las secciones se guardarán junto con la traducción de la categoría cuando hagas clic en "Guardar".',
        'no_changes_made'         => 'No se realizaron cambios.',
        'no_sections_found'       => 'No se encontraron secciones.',
        'editing_locale'          => 'Editando',

        // Mensajes (secciones)
        'section_created'         => 'Sección creada correctamente.',
        'section_updated'         => 'Sección actualizada correctamente.',
        'section_activated'       => 'Sección activada correctamente.',
        'section_deactivated'     => 'Sección desactivada correctamente.',
        'section_deleted'         => 'Sección eliminada correctamente.',

        // Mensajes genéricos del módulo
        'created_success'         => 'Creado correctamente.',
        'updated_success'         => 'Actualizado correctamente.',
        'deleted_success'         => 'Eliminado correctamente.',
        'activated_success'       => 'Activado correctamente.',
        'deactivated_success'     => 'Desactivado correctamente.',
        'unexpected_error'        => 'Ocurrió un error inesperado.',

        // Botones / textos comunes (SweetAlert)
        'create'                  => 'Crear',
        'activate'                => 'Activar',
        'deactivate'              => 'Desactivar',
        'cancel'                  => 'Cancelar',
        'ok'                      => 'OK',
        'validation_errors'       => 'Hay errores de validación',
        'error_title'             => 'Error',

        // Confirmaciones específicas de sección
        'confirm_create_section'      => '¿Crear esta sección?',
        'confirm_edit_section'        => '¿Guardar los cambios de esta sección?',
        'confirm_deactivate_section'  => '¿Seguro que deseas desactivar esta sección?',
        'confirm_activate_section'    => '¿Seguro que deseas activar esta sección?',
        'confirm_delete_section'      => '¿Seguro que deseas eliminar esta sección?<br>Esta acción no se puede deshacer.',
        'section_restored'            => 'Sección restaurada correctamente.',
        'section_force_deleted'       => 'Sección eliminada permanentemente.',
        'confirm_restore_section'     => '¿Restaurar esta sección?',
        'confirm_force_delete_section' => '¿Eliminar permanentemente esta sección? Esta acción no se puede deshacer.',
    ],

    // =========================================================
    // ==== TOURTYPES ==========================================
    // =========================================================
    'tourtypes' => [
        // Títulos / encabezados
        'title'                   => 'Tipos de Productos',
        'new'                     => 'Agregar Tipo de Producto',

        'active_tab'              => 'Activos',
        'trash_tab'               => 'Papelera',
        'trash_title'             => 'Papelera de Tipos de Producto',
        'trash_header'            => 'Papelera de Tipos de Producto',
        'trash_empty'             => 'No hay tipos de producto eliminados',

        // Columnas / campos
        'id'                      => 'ID',
        'name'                    => 'Nombre',
        'description'             => 'Descripción',
        'duration'                => 'Duración',
        'status'                  => 'Estado',
        'actions'                 => 'Acciones',
        'active'                  => 'Activo',
        'inactive'                => 'Inactivo',

        // Botones / acciones
        'register'                => 'Guardar',
        'update'                  => 'Actualizar',
        'save'                    => 'Guardar',
        'close'                   => 'Cerrar',
        'cancel'                  => 'Cancelar',
        'edit'                    => 'Editar',
        'delete'                  => 'Eliminar',
        'activate'                => 'Activar',
        'deactivate'              => 'Desactivar',

        // Títulos de modal
        'edit_title'              => 'Editar Tipo de Producto',
        'create_title'            => 'Crear Tipo de Producto',

        // Placeholders / ayudas
        'examples_placeholder'    => 'Ej.: Aventura, Naturaleza, Relax',
        'duration_placeholder'    => 'Ej.: 4 horas, 8 horas',
        'suggested_duration_hint' => 'Formato sugerido: "4 horas", "8 horas".',
        'keep_default_hint'       => 'Deja "4 horas" si aplica; puedes cambiarlo.',
        'optional'                => 'opcional',

        // Confirmaciones
        'confirm_delete'          => '¿Seguro que deseas eliminar ":name"? Esta acción no se puede deshacer.',
        'confirm_activate'        => '¿Seguro que deseas activar ":name"?',
        'confirm_deactivate'      => '¿Seguro que deseas desactivar ":name"?',

        // Mensajes (flash)
        'created_success'         => 'Tipo de producto creado correctamente.',
        'updated_success'         => 'Tipo de producto actualizado correctamente.',
        'deleted_success'         => 'Tipo de producto eliminado correctamente.',
        'activated_success'       => 'Tipo de producto activado correctamente.',
        'deactivated_success'     => 'Tipo de producto desactivado correctamente.',
        'in_use_error'            => 'No se pudo eliminar: este tipo de producto está en uso.',
        'unexpected_error'        => 'Ocurrió un error inesperado. Intenta de nuevo.',

        // Validación / genéricos
        'validation_errors'       => 'Revisa los campos resaltados.',
        'error_title'             => 'Error',
    ],

    // =========================================================
    // ==== AMENITIES ==========================================
    // =========================================================
    'amenities' => [
        'title'           => 'Amenidades',
        'active_tab'      => 'Activos',
        'trash_tab'       => 'Papelera',
        'trash_title'     => 'Papelera de Amenidades',
        'trash_header'    => 'Papelera de Amenidades',
        'trash_empty'     => 'No hay amenidades eliminadas',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Título / cabecera
        'title'            => 'FAQ',

        // Tabs
        'active_tab'       => 'Activos',
        'trash_tab'        => 'Papelera',
        'trash_title'      => 'Papelera de FAQs',
        'trash_empty'      => 'No hay preguntas frecuentes eliminadas',

        // Campos / columnas
        'question'         => 'Pregunta',
        'answer'           => 'Respuesta',
        'status'           => 'Estado',
        'actions'          => 'Acciones',
        'active'           => 'Activo',
        'inactive'         => 'Inactivo',
        'deleted_by'       => 'Eliminado por',
        'deleted_at'       => 'Eliminado el',

        // Botones / acciones
        'new'              => 'Nueva pregunta',
        'create'           => 'Crear',
        'save'             => 'Guardar',
        'edit'             => 'Editar',
        'delete'           => 'Eliminar',
        'activate'         => 'Activar',
        'deactivate'       => 'Desactivar',
        'cancel'           => 'Cancelar',
        'close'            => 'Cerrar',
        'ok'               => 'OK',
        'restore'          => 'Restaurar',
        'force_delete'     => 'Eliminar permanentemente',
        'retranslate'      => 'Retraducir',

        // UI
        'read_more'        => 'Leer más',
        'read_less'        => 'Leer menos',

        // Confirmaciones
        'confirm_create'   => '¿Crear esta pregunta frecuente?',
        'confirm_edit'     => '¿Guardar los cambios de esta pregunta frecuente?',
        'confirm_delete'   => '¿Seguro que deseas eliminar esta pregunta frecuente?<br>Se moverá a la papelera.',
        'confirm_activate' => '¿Seguro que deseas activar esta pregunta frecuente?',
        'confirm_deactivate' => '¿Seguro que deseas desactivar esta pregunta frecuente?',
        'confirm_restore'  => '¿Restaurar esta pregunta frecuente?',
        'confirm_force_delete' => '¿Eliminar permanentemente esta pregunta frecuente?<br>Esta acción no se puede deshacer.',
        'confirm_retranslate' => '¿Regenerar todas las traducciones?<br>Esto sobrescribirá las traducciones existentes.',

        // Validación / errores
        'validation_errors' => 'Hay errores de validación',
        'error_title'      => 'Error',

        // Mensajes (flash)
        'created_success'      => 'Pregunta frecuente creada correctamente.',
        'updated_success'      => 'Pregunta frecuente actualizada correctamente.',
        'deleted_success'      => 'Pregunta frecuente movida a la papelera.',
        'activated_success'    => 'Pregunta frecuente activada correctamente.',
        'deactivated_success'  => 'Pregunta frecuente desactivada correctamente.',
        'restored_success'     => 'Pregunta frecuente restaurada correctamente.',
        'force_deleted_success' => 'Pregunta frecuente eliminada permanentemente.',
        'retranslated_success' => 'Traducciones regeneradas correctamente.',
        'unexpected_error'     => 'Ocurrió un error inesperado.',
    ],

    // =========================================================
    // ==== TRANSLATIONS =======================================
    // =========================================================
    'translations' => [
        // Títulos / textos generales
        'title'                 => 'Gestión de Traducciones',
        'index_title'           => 'Gestión de traducciones',
        'select_entity_title'   => 'Selecciona :entity para traducir',
        'edit_title'            => 'Editar traducción',
        'main_information'      => 'Información principal',
        'ok'                    => 'OK',
        'save'                  => 'Guardar',
        'validation_errors'     => 'Hay errores de validación',
        'updated_success'       => 'Traducción actualizada correctamente.',
        'unexpected_error'      => 'No se pudo actualizar la traducción.',

        'editing'            => 'Editando',
        'policy_name'        => 'Nombre de la política',
        'policy_content'     => 'Contenido',
        'policy_sections'    => 'Secciones de la política',
        'section'            => 'Sección',
        'section_name'       => 'Nombre de la sección',
        'section_content'    => 'Contenido de la sección',

        // Selector de idioma (pantalla y helpers)
        'choose_locale_title'   => 'Seleccionar idioma',
        'choose_locale_hint'    => 'Selecciona el idioma al que deseas traducir este elemento.',
        'select_language_title' => 'Seleccionar idioma',
        'select_language_intro' => 'Selecciona el idioma al que deseas traducir este elemento.',
        'languages' => [
            'es' => 'Español',
            'en' => 'Inglés',
            'fr' => 'Francés',
            'pt' => 'Portugués',
            'de' => 'Alemán',
        ],

        // Listados / botones
        'select'                => 'Seleccionar',
        'id_unavailable'        => 'ID no disponible',
        'no_items'              => 'No hay :entity disponibles para traducir.',

        // Campos comunes de formularios de traducción
        'name'                  => 'Nombre',
        'description'           => 'Descripción',
        'content'               => 'Contenido',
        'overview'              => 'Resumen',
        'itinerary'             => 'Itinerario',
        'itinerary_name'        => 'Nombre del Itinerario',
        'itinerary_description' => 'Descripción del Itinerario',
        'itinerary_items'       => 'Ítems del Itinerario',
        'item'                  => 'Ítem',
        'item_title'            => 'Título del Ítem',
        'item_description'      => 'Descripción del Ítem',
        'sections'              => 'Secciones',
        'edit'                  => 'Editar',
        'close'                 => 'Cerrar',
        'actions'               => 'Acciones',

        // === Etiquetas MODULARES por campo ====================
        // Úsalas como: __('m_config.translations.fields.<campo>')
        'fields' => [
            // Genéricos
            'name'                  => 'Nombre',
            'title'                 => 'Título',
            'overview'              => 'Resumen',
            'description'           => 'Descripción',
            'content'               => 'Contenido',
            'duration'              => 'Duración',
            'question'              => 'Pregunta',
            'answer'                => 'Respuesta',

            // Itinerario / ítems (parcial de tours)
            'itinerary'             => 'Itinerario',
            'itinerary_name'        => 'Nombre del itinerario',
            'itinerary_description' => 'Descripción del itinerario',
            'item'                  => 'Ítem',
            'item_title'            => 'Título del ítem',
            'item_description'      => 'Descripción del ítem',
        ],

        // === Overrides por ENTIDAD y CAMPO (opcional) =========
        // En el blade: primero intenta entity_fields.<type>.<field>,
        // si no existe usa fields.<field>.
        'entity_fields' => [
            'product_types' => [
                'duration' => 'Duración sugerida',
                'name'     => 'Nombre del tipo de producto',
            ],
            'faqs' => [
                'question' => 'Pregunta (visible al cliente)',
                'answer'   => 'Respuesta (visible al cliente)',
            ],
        ],

        // Nombres de entidades (plural)
        'entities' => [
            'tours'            => 'Tours',
            'itineraries'      => 'Itinerarios',
            'itinerary_items'  => 'Ítems del itinerario',
            'amenities'        => 'Amenidades',
            'faqs'             => 'Preguntas frecuentes',
            'policies'         => 'Políticas',
            'product_types'    => 'Tipos de producto',
        ],

        // Nombres de entidades (singular)
        'entities_singular' => [
            'tours'            => 'tour',
            'itineraries'      => 'itinerario',
            'itinerary_items'  => 'ítem del itinerario',
            'amenities'        => 'amenidad',
            'faqs'             => 'pregunta frecuente',
            'policies'         => 'política',
            'product_types'    => 'tipo de producto',
        ],
    ],

    // =========================================================
    // ==== PROMOCODE ==========================================
    // =========================================================
    'promocode' => [
        'title'        => 'Códigos Promocionales',
        'create_title' => 'Generar nuevo código promocional',
        'list_title'   => 'Códigos promocionales existentes',

        'success_title' => 'Éxito',
        'error_title'   => 'Error',

        'fields' => [
            'code'        => 'Código',
            'discount'    => 'Monto',

            'type'        => 'Tipo',
            'operation'   => 'Operación',
            'valid_from'  => 'Válido desde',
            'valid_until' => 'Válido hasta',
            'usage_limit' => 'Límite de usos',
            'promocode_hint'        => 'Tras aplicar, el cupón se guardará al enviar el formulario y se actualizarán los snapshots del historial.',
        ],

        'types' => [
            'percent' => '%',
            'amount'  => '$',
        ],

        'symbols' => [
            'percent'  => '%',
            'currency' => '$',
        ],

        'table' => [
            'code'         => 'Código',
            'discount'     => 'Monto',
            'operation'    => 'Operación',
            'validity'     => 'Vigencia',
            'date_status'  => 'Estado (fecha)',
            'usage'        => 'Usos',
            'usage_status' => 'Estado (uso)',
            'actions'      => 'Acciones',
        ],

        'status' => [
            'used'      => 'Usado',
            'available' => 'Disponible',
        ],

        'date_status' => [
            'scheduled' => 'Programado',
            'active'    => 'Vigente',
            'expired'   => 'Expirado',
        ],

        'actions' => [
            'generate' => 'Generar',
            'delete'   => 'Eliminar',
            'toggle_operation' => 'Cambiar entre Sumar/Restar',
        ],

        'labels' => [
            'unlimited_placeholder' => 'Vacío = ilimitado',
            'unlimited_hint'        => 'Déjalo vacío para usos ilimitados. Pon 1 para un solo uso.',
            'no_limit'              => '(sin límite)',
            'remaining'             => 'restantes',
        ],

        'confirm_delete' => '¿Estás seguro de que deseas eliminar este código?',
        'empty'          => 'No hay códigos promocionales disponibles.',

        'messages' => [
            'created_success'         => 'Código promocional creado correctamente.',
            'deleted_success'         => 'Código promocional eliminado correctamente.',
            'percent_over_100'        => 'El porcentaje no puede ser mayor a 100.',
            'code_exists_normalized'  => 'Este código (ignorando espacios y mayúsculas) ya existe.',
            'invalid_or_used'         => 'Código inválido o ya usado.',
            'valid'                   => 'Código válido.',
            'server_error'            => 'Error del servidor, inténtalo de nuevo.',
            'operation_updated'       => 'Operación actualizada correctamente.',
        ],

        'operations' => [
            'add'            => 'Sumar',
            'subtract'       => 'Restar',
            'make_add'       => 'Cambiar a “Sumar”',
            'make_subtract'  => 'Cambiar a “Restar”',
            'surcharge'       => 'Recargo',
            'discount'        => 'Descuento',
        ],
    ],

    // =========================================================
    // ==== CUTOFF =============================================
    // =========================================================
    'cut-off' => [
        // Títulos / encabezados
        'title'       => 'Cut-off',
        'header'      => 'Configuración de Cut-Off',
        'server_time' => 'Hora del servidor (:tz)',

        // Tabs
        'tabs' => [
            'global'   => 'Global (predeterminado)',
            'tour'     => 'Bloqueo por Tour',
            'schedule' => 'Bloqueo por Horario',
            'summary'  => 'Resumen',
            'help'     => 'Ayuda',
        ],

        // Campos
        'fields' => [
            'cutoff_hour'       => 'Hora de corte (24h)',
            'cutoff_hour_short' => 'Cutoff (24h)',
            'lead_days'         => 'Días de antelación',
            'timezone'          => 'Zona horaria',
            'tour'              => 'Tour',
            'schedule'          => 'Horario',
            'actions'           => 'Acciones'
        ],

        // Selects / placeholders
        'selects' => [
            'tour' => '— Selecciona un tour —',
            'time' => '— Selecciona un horario —',
        ],

        // Etiquetas
        'labels' => [
            'status' => 'Estado',
        ],

        // Badges / chips
        'badges' => [
            'inherits'            => 'Hereda Global',
            'override'            => 'Bloqueo',
            'inherit_tour_global' => 'Hereda del Tour/Global',
            'schedule'            => 'Horario',
            'tour'                => 'Tour',
            'global'              => 'Global',
        ],

        // Acciones
        'actions' => [
            'save_global'   => 'Guardar global',
            'save_tour'     => 'Guardar bloqueo de tour',
            'save_schedule' => 'Guardar bloqueo de horario',
            'clear'         => 'Limpiar bloqueo',
            'confirm'       => 'Confirmar',
            'cancel'        => 'Cancelar',
        ],

        // Confirmaciones (modales)
        'confirm' => [
            'tour' => [
                'title' => '¿Guardar bloqueo del tour?',
                'text'  => 'Se aplicará un bloqueo específico para este tour. Déjalo vacío para heredar.',
            ],
            'schedule' => [
                'title' => '¿Guardar bloqueo del horario?',
                'text'  => 'Se aplicará un bloqueo específico para este horario. Déjalo vacío para heredar.',
            ],
        ],

        // Resumen
        'summary' => [
            'tour_title'            => 'Bloqueos por Tour',
            'no_tour_overrides'     => 'No hay bloqueos a nivel tour.',
            'schedule_title'        => 'Bloqueos por Horario',
            'no_schedule_overrides' => 'No hay bloqueos a nivel horario.',
            'search_placeholder'    => 'Buscar tour u horario…',
        ],

        // Flash / toasts
        'flash' => [
            'success_title' => 'Éxito',
            'error_title'   => 'Error',
        ],

        // Ayuda
        'help' => [
            'title'      => '¿Cómo funciona?',
            'global'     => 'Valor por defecto para toda la web.',
            'tour'       => 'Si un tour tiene cutoff/días configurados, tiene prioridad sobre el Global.',
            'schedule'   => 'Si un horario del tour tiene bloqueo, tiene prioridad sobre el Tour.',
            'precedence' => 'Precedencia',
        ],

        // Pistas / ayudas (hints)
        'hints' => [
            // usados en Global
            'cutoff_example'    => 'Ej.: :ex. Después de esta hora, “hoy” deja de estar disponible.',
            'pattern_24h'       => 'Formato 24h HH:MM (ej. 09:30, 18:00).',
            'cutoff_behavior'   => 'Si ya pasó la hora de corte, la fecha más próxima disponible se mueve al día siguiente.',
            'lead_days'         => 'Días mínimos de antelación (0 permite reservar hoy si no se ha pasado el cutoff).',
            'lead_days_detail'  => 'Rango permitido: 0–30. 0 permite reservar el mismo día si no se ha pasado la hora de corte.',
            'timezone_source'   => 'Se toma de config(\'app.timezone\').',

            // usados en Tour
            'pick_tour'             => 'Selecciona primero un tour; luego define su bloqueo (opcional).',
            'tour_override_explain' => 'Si defines solo uno (cutoff o días), el otro hereda del Global.',
            'clear_button_hint'     => 'Usa “Limpiar bloqueo” para volver a heredar.',
            'leave_empty_inherit'   => 'Déjalo vacío para heredar.',

            // usados en Schedule
            'pick_schedule'             => 'Después selecciona el horario del tour.',
            'schedule_override_explain' => 'Los valores aquí tienen prioridad sobre los del Tour. Déjalo vacío para heredar.',
            'schedule_precedence_hint'  => 'Precedencia: Horario → Tour → Global.',

            // usados en Resumen
            'dash_means_inherit' => 'El símbolo “—” indica que el valor se hereda.',
        ],
    ],

];
