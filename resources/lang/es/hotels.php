<?php

return [
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
    'created_success'   => 'Hotel creado correctamente.',
    'updated_success'   => 'Hotel actualizado correctamente.',
    'deleted_success'   => 'Hotel eliminado correctamente.',
    'activated_success' => 'Hotel activado correctamente.',
    'deactivated_success'=> 'Hotel desactivado correctamente.',
    'sorted_success'    => 'Hoteles ordenados alfabéticamente.',
    'unexpected_error'  => 'Ocurrió un error inesperado. Inténtalo de nuevo.',

    // Validación / genéricos
'validation' => [
    'name_required' => 'El nombre es obligatorio.',
    'name_unique'   => 'Ese hotel ya existe en la lista.',
    'name_max'      => 'El nombre no puede exceder 255 caracteres.',
],
    'error_title'       => 'Error',

    // Modales
    'edit_title'        => 'Editar hotel',
];
