<?php
/*************************************************************
 *  MÓDULO DE CONFIGURACIÓN – TRADUCCIONES (ES)
 *  Archivo: resources/lang/es/m_config.php
 *
 *  Índice (anclas buscables)
 *  [01] POLICIES LÍNEA 16
 *  [02] TOURTYPES LÍNEA 134
 *  [03] FAQ LÍNEA 193
 *  [04] TRANSLATIONS LÍNEA 244
 *  [05] PROMOCODE LÍNEA 354
 *************************************************************/

return [

    // =========================================================
    // ==== POLICIES ===========================================
    // =========================================================
    'policies' => [
        // Títulos / encabezados
        'categories_title'        => 'Categorías de Políticas',
        'sections_title'          => 'Secciones — :policy',

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
    ],

    // =========================================================
    // ==== TOURTYPES ==========================================
    // =========================================================
    'tourtypes' => [
        // Títulos / encabezados
        'title'                   => 'Tipos de Tours',
        'new'                     => 'Agregar Tipo de Tour',

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
        'edit_title'              => 'Editar Tipo de Tour',
        'create_title'            => 'Crear Tipo de Tour',

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
        'created_success'         => 'Tipo de tour creado correctamente.',
        'updated_success'         => 'Tipo de tour actualizado correctamente.',
        'deleted_success'         => 'Tipo de tour eliminado correctamente.',
        'activated_success'       => 'Tipo de tour activado correctamente.',
        'deactivated_success'     => 'Tipo de tour desactivado correctamente.',
        'in_use_error'            => 'No se pudo eliminar: este tipo de tour está en uso.',
        'unexpected_error'        => 'Ocurrió un error inesperado. Intenta de nuevo.',

        // Validación / genéricos
        'validation_errors'       => 'Revisa los campos resaltados.',
        'error_title'             => 'Error',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Título / cabecera
        'title'            => 'Preguntas Frecuentes',

        // Campos / columnas
        'question'         => 'Pregunta',
        'answer'           => 'Respuesta',
        'status'           => 'Estado',
        'actions'          => 'Acciones',
        'active'           => 'Activo',
        'inactive'         => 'Inactivo',

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

        // UI
        'read_more'        => 'Leer más',
        'read_less'        => 'Leer menos',

        // Confirmaciones
        'confirm_create'   => '¿Crear esta pregunta frecuente?',
        'confirm_edit'     => '¿Guardar los cambios de esta pregunta frecuente?',
        'confirm_delete'   => '¿Seguro que deseas eliminar esta pregunta frecuente?<br>Esta acción no se puede deshacer.',
        'confirm_activate' => '¿Seguro que deseas activar esta pregunta frecuente?',
        'confirm_deactivate'=> '¿Seguro que deseas desactivar esta pregunta frecuente?',

        // Validación / errores
        'validation_errors'=> 'Hay errores de validación',
        'error_title'      => 'Error',

        // Mensajes (flash)
        'created_success'      => 'Pregunta frecuente creada correctamente.',
        'updated_success'      => 'Pregunta frecuente actualizada correctamente.',
        'deleted_success'      => 'Pregunta frecuente eliminada correctamente.',
        'activated_success'    => 'Pregunta frecuente activada correctamente.',
        'deactivated_success'  => 'Pregunta frecuente desactivada correctamente.',
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
            'tour_types' => [
                'duration' => 'Duración sugerida',
                'name'     => 'Nombre del tipo de tour',
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
            'tour_types'       => 'Tipos de tour',
        ],

        // Nombres de entidades (singular)
        'entities_singular' => [
            'tours'            => 'tour',
            'itineraries'      => 'itinerario',
            'itinerary_items'  => 'ítem del itinerario',
            'amenities'        => 'amenidad',
            'faqs'             => 'pregunta frecuente',
            'policies'         => 'política',
            'tour_types'       => 'tipo de tour',
        ],
    ],

    // =========================================================
    // ==== PROMOCODE ==========================================
    // =========================================================
    'promocode' => [
        'title'         => 'Códigos Promocionales',
        'create_title'  => 'Generar nuevo código promocional',
        'list_title'    => 'Códigos promocionales existentes',

        'success_title' => 'Éxito',
        'error_title'   => 'Error',

        'fields' => [
            'code'     => 'Código',
            'discount' => 'Descuento',
            'type'     => 'Tipo',
        ],

        'types' => [
            'percent'  => '%',
            'amount'   => '$',
        ],

        'symbols' => [
            'percent'  => '%',
            'currency' => '$',
        ],

        'table' => [
            'code'     => 'Código',
            'discount' => 'Descuento',
            'status'   => 'Estado',
            'actions'  => 'Acciones',
        ],

        'status' => [
            'used'       => 'Usado',
            'available'  => 'Disponible',
        ],

        'actions' => [
            'generate' => 'Generar',
            'delete'   => 'Eliminar',
        ],

        'confirm_delete' => '¿Estás seguro de que deseas eliminar este código?',
        'empty'          => 'No hay códigos promocionales disponibles.',

        'messages' => [
    'created_success'       => 'Código promocional creado correctamente.',
    'deleted_success'       => 'Código promocional eliminado correctamente.',
    'percent_over_100'      => 'El porcentaje no puede ser mayor a 100.',
    'code_exists_normalized'=> 'Este código (ignorando espacios y mayúsculas) ya existe.',
    'invalid_or_used'       => 'Código inválido o ya usado.',
    'valid'                 => 'Código válido.',
    'server_error'          => 'Error del servidor, inténtalo de nuevo.',
],
    ],
];
