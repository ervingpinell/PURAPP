<?php

return [

    'hotels' => [

        // Título / encabezados
        'title'             => 'Lista de Hoteles',
        'header'            => 'Hoteles Registrados',
        'sort_alpha'        => 'Ordenar alfabéticamente',

        // Campos / columnas / acciones
        'name'              => 'Nombre',
        'status'            => 'Estado',
        'actions'           => 'Acciones',
        'active'            => 'Activo',
        'inactive'          => 'Inactivo',
        'add'               => 'Agregar',
        'edit'              => 'Editar',
        'delete'            => 'Eliminar',
        'activate'          => 'Activar',
        'deactivate'        => 'Desactivar',
        'save_changes'      => 'Guardar cambios',
        'cancel'            => 'Cancelar',
        'close'             => 'Cerrar',
        'no_records'        => 'No hay hoteles registrados.',
        'name_placeholder'  => 'Ej.: Hotel Arenal Springs',

        // Confirmaciones
        'confirm_activate_title'    => '¿Activar hotel?',
        'confirm_activate_text'     => '¿Seguro que deseas activar ":name"?',
        'confirm_deactivate_title'  => '¿Desactivar hotel?',
        'confirm_deactivate_text'   => '¿Seguro que deseas desactivar ":name"?',
        'confirm_delete_title'      => '¿Eliminar definitivamente?',
        'confirm_delete_text'       => 'Se eliminará ":name". Esta acción no se puede deshacer.',

        // Mensajes (flash)
        'created_success'    => 'Hotel creado correctamente.',
        'updated_success'    => 'Hotel actualizado correctamente.',
        'deleted_success'    => 'Hotel eliminado correctamente.',
        'activated_success'  => 'Hotel activado correctamente.',
        'deactivated_success' => 'Hotel desactivado correctamente.',
        'sorted_success'     => 'Hoteles ordenados alfabéticamente.',
        'unexpected_error'   => 'Ocurrió un error inesperado. Inténtalo de nuevo.',

        // Validación / genéricos
        'validation' => [
            'name_required' => 'El nombre es obligatorio.',
            'name_unique'   => 'Ese hotel ya existe en la lista.',
            'name_max'      => 'El nombre no puede exceder 255 caracteres.',
        ],
        'error_title' => 'Error',

        // Modales
        'edit_title' => 'Editar hotel',
    ],

    'meeting_point' => [

        // UI
        'ui' => [
            'page_title'   => 'Puntos de Encuentro',
            'page_heading' => 'Puntos de Encuentro',
        ],

        // Badges
        'badges' => [
            'count_badge' => ':count registros',
            'active'      => 'Activo',
            'inactive'    => 'Inactivo',
        ],

        // Crear
        'create' => [
            'title' => 'Añadir punto',
        ],

        // Listado
        'list' => [
            'title' => 'Listado',
            'empty' => 'No hay registros. Crea el primero arriba.',
        ],

        // Labels compactos en tarjetas
        'labels' => [
            'time'       => 'Hora',
            'sort_order' => 'Orden',
        ],

        // Fields
        'fields' => [
            'name'                    => 'Nombre',
            'pickup_time'             => 'Hora de recogida',
            'sort_order'              => 'Orden',
            'description'             => 'Descripción',
            'map_url'                 => 'URL de mapa',
            'active'                  => 'Activo',
            'time_short'              => 'Hora',
            'map'                     => 'Mapa',
            'status'                  => 'Estado',
            'actions'                 => 'Acciones',

            // Edición / traducciones
            'name_base'               => 'Nombre (base)',
            'description_base'        => 'Descripción (base)',
            'locale'                  => 'Locale',
            'name_translation'        => 'Nombre (traducción)',
            'description_translation' => 'Descripción (traducción)',
        ],

        // Placeholders
        'placeholders' => [
            'name'        => 'Parque Central de La Fortuna',
            'pickup_time' => '7:10 AM',
            'description' => 'Centro de La Fortuna',
            'map_url'     => 'https://maps.google.com/...',
            'search'      => 'Buscar…',
            'optional'    => 'Opcional',
        ],

        // Hints
        'hints' => [
            'name_example'   => 'Ej: "Parque Central de La Fortuna".',
            'name_base_sync' => 'Si no lo cambias, se mantiene. El nombre por idioma se edita abajo.',
            'fallback_sync'  => 'Si eliges el locale <strong>:fallback</strong>, también se sincroniza con los campos base.',
        ],

        // Buttons
        'buttons' => [
            'reload'       => 'Recargar',
            'save'         => 'Guardar',
            'clear'        => 'Limpiar',
            'create'       => 'Crear',
            'cancel'       => 'Cancelar',
            'save_changes' => 'Guardar cambios',
            'close'        => 'Cerrar',
            'ok'           => 'Entendido',
            'confirm'      => 'Sí, continuar',
            'delete'       => 'Eliminar',
            'activate'     => 'Activar',
            'deactivate'   => 'Desactivar',
        ],

        // Actions (titles / tooltips)
        'actions' => [
            'view_map'    => 'Ver mapa',
            'view_on_map' => 'Ver en mapa',
            'edit'        => 'Editar',
            'delete'      => 'Eliminar',
            'activate'    => 'Activar',
            'deactivate'  => 'Desactivar',
        ],

        // Confirm
        'confirm' => [
            'create_title'             => '¿Crear nuevo meeting point?',
            'create_text_with_name'    => 'Se creará ":name".',
            'create_text'              => 'Se creará un nuevo punto de encuentro.',

            'save_title'               => '¿Guardar cambios?',
            'save_text'                => 'Se actualizará el meeting point y la traducción seleccionada.',

            'deactivate_title'         => '¿Desactivar meeting point?',
            'deactivate_title_short'   => '¿Desactivar?',
            'deactivate_text'          => '":name" quedará inactivo.',

            'activate_title'           => '¿Activar meeting point?',
            'activate_title_short'     => '¿Activar?',
            'activate_text'            => '":name" quedará activo.',

            'delete_title'             => '¿Eliminar meeting point?',
            'delete_title_short'       => '¿Eliminar?',
            'delete_text'              => '":name" será eliminado permanentemente. Esta acción no se puede deshacer.',
        ],

        // Validation / Toastr / SweetAlert
        'validation' => [
            'title'                         => 'Errores de validación',
            'missing_translated_name_title' => 'Falta el nombre (traducción)',
            'missing_translated_name_text'  => 'Completa el campo de nombre traducido.',
        ],

        'toasts' => [
            'success_title' => 'Éxito',
            'error_title'   => 'Error',
            'no_create_permission' => 'No tienes permiso para crear.',
        ],
    ],

];
