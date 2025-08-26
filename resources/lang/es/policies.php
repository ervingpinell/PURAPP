<?php

return [
    // Títulos / encabezados
    'categories_title'        => 'Categorías de Políticas',
    'sections_title'          => 'Secciones — :policy',

    // Columnas / campos comunes
    'id'                      => 'ID',
    'internal_name'           => 'Nombre interno',
    'title_current_locale'    => 'Título (idioma actual)',
    'validity_range'          => 'Rango de vigencia',
    'valid_from'              => 'Vigente desde',
    'valid_to'                => 'Vigente hasta',
    'status'                  => 'Estado',
    'sections'                => 'Secciones',
    'actions'                 => 'Acciones',
    'active'                  => 'Activa',
    'inactive'                => 'Inactiva',

    // Lista de categorías: acciones
    'new_category'            => 'Nueva categoría',
    'view_sections'           => 'Ver secciones',
    'edit'                    => 'Editar',
    'activate_category'       => 'Activar categoría',
    'deactivate_category'     => 'Desactivar categoría',
    'delete'                  => 'Eliminar',
    'delete_category_confirm' => '¿Eliminar la categoría y TODAS sus secciones?',
    'no_categories'           => 'Sin categorías registradas.',
    'edit_category'           => 'Editar categoría',

    // Formularios (categoría)
    'title_label'             => 'Título',
    'description_label'       => 'Descripción',
    'register'                => 'Registrar',
    'save_changes'            => 'Guardar cambios',
    'close'                   => 'Cerrar',

    // Secciones
    'back_to_categories'      => 'Volver a categorías',
    'new_section'             => 'Nueva sección',
    'key'                     => 'Clave',
    'order'                   => 'Orden',
    'activate_section'        => 'Activar sección',
    'deactivate_section'      => 'Desactivar sección',
    'delete_section_confirm'  => '¿Eliminar esta sección?',
    'no_sections'             => 'Sin secciones registradas.',
    'edit_section'            => 'Editar sección',
    'internal_key_optional'   => 'Clave interna (opcional)',
    'content_label'           => 'Contenido',

    // Público
    'page_title'              => 'Políticas',
    'no_policies'             => 'No hay políticas disponibles por el momento.',
    'section'                 => 'Sección',
    'cancellation_policy'     => 'Política de Cancelación',
    'refund_policy'           => 'Política de Reembolsos',
    'no_cancellation_policy'  => 'No hay una política de cancelación configurada.',
    'no_refund_policy'        => 'No hay una política de reembolsos configurada.',

    // Mensajes (categorías)
    'category_created'        => 'Categoría creada con éxito.',
    'category_updated'        => 'Categoría actualizada con éxito.',
    'category_activated'      => 'Categoría activada con éxito.',
    'category_deactivated'    => 'Categoría desactivada con éxito.',
    'category_deleted'        => 'Categoría eliminada con éxito.',

    // --- NUEVAS CLAVES (refactor / utilidades de módulo) ---
    'untitled'                => 'Sin título',
    'no_content'              => 'Sin contenido disponible.',
    'display_name'            => 'Nombre visible',
    'name'                    => 'Nombre',
    'name_base'               => 'Nombre base',
    'name_base_help'          => 'Identificador corto/slug de la sección (solo interno).',
    'translation_content'     => 'Contenido traducido',
    'locale'                  => 'Idioma',
    'save'                    => 'Guardar',
    'name_base_label'         => 'Nombre base',
    'translation_name'        => 'Nombre traducido',
    'lang_autodetect_hint'    => 'Puedes escribir en cualquier idioma; se detecta automáticamente.',
    'bulk_edit_sections'      => 'Edición rápida de secciones',
    'bulk_edit_hint'          => 'Las modificaciones en todas las secciones se guardarán junto con la traducción de la categoría cuando hagas clic en “Guardar”.',
    'no_changes_made'         => 'No se han realizado cambios.',
    'no_sections_found'       => 'No se han encontrado secciones.',

    // Mensajes (secciones)
    'section_created'         => 'Sección creada con éxito.',
    'section_updated'         => 'Sección actualizada con éxito.',
    'section_activated'       => 'Sección activada con éxito.',
    'section_deactivated'     => 'Sección desactivada con éxito.',
    'section_deleted'         => 'Sección eliminada con éxito.',

    // Mensajes genéricos del módulo (para controladores/alerts)
    'created_success'         => 'Creado correctamente.',
    'updated_success'         => 'Actualizado correctamente.',
    'deleted_success'         => 'Eliminado correctamente.',
    'activated_success'       => 'Activado correctamente.',
    'deactivated_success'     => 'Desactivado correctamente.',
    'unexpected_error'        => 'Ha ocurrido un error inesperado.',

    // Botones / textos comunes para SweetAlert (módulo)
    'create'                  => 'Crear',
    'activate'                => 'Activar',
    'deactivate'              => 'Desactivar',
    'cancel'                  => 'Cancelar',
    'ok'                      => 'Aceptar',
    'validation_errors'       => 'Hay errores de validación',
    'error_title'             => 'Error',

    // Confirmaciones específicas de secciones (SweetAlert)
    'confirm_create_section'      => '¿Crear esta sección?',
    'confirm_edit_section'        => '¿Guardar cambios de la sección?',
    'confirm_delete_section'      => '¿Seguro que deseas eliminar esta sección?',
    'confirm_deactivate_section'  => '¿Seguro que deseas desactivar esta sección?',
    'confirm_activate_section'    => '¿Seguro que deseas activar esta sección?',
];
