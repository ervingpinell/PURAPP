<?php

return [
    'title' => 'Gestión de Usuarios',
    'add_user' => 'Añadir Usuario',
    'no_role' => 'Sin rol',
    'user_marked_verified' => 'Usuario marcado como verificado exitosamente.',

    'filters' => [
        'role' => 'Filtrar por rol:',
        'state' => 'Filtrar por estado:',
        'email' => 'Filtrar por correo:',
        'email_placeholder' => 'ejemplo@correo.com',
        'all' => '-- Todos --',
        'search' => 'Buscar',
        'clear' => 'Limpiar',
    ],

    'table' => [
        'id' => 'ID',
        'name' => 'Nombre',
        'email' => 'Correo',
        'role' => 'Rol',
        'phone' => 'Teléfono',
        'status' => 'Estado',
        'verified' => 'Verificado',
        'locked' => 'Bloqueado',
        'actions' => 'Acciones',
    ],

    'status' => [
        'active' => 'Activo',
        'inactive' => 'Inactivo',
    ],

    'verified' => [
        'yes' => 'Sí',
        'no'  => 'No',
    ],

    'locked' => [
        'yes' => 'Sí',
        'no'  => 'No',
    ],

    'actions' => [
        'edit' => 'Editar',
        'deactivate' => 'Desactivar',
        'reactivate' => 'Reactivar',
        'lock' => 'Bloquear',
        'unlock' => 'Desbloquear',
        'mark_verified' => 'Marcar verificado',
    ],

    'dialog' => [
        'title' => 'Confirmación',
        'cancel' => 'Cancelar',
        'confirm_lock' => '¿Bloquear a este usuario?',
        'confirm_unlock' => '¿Desbloquear a este usuario?',
        'confirm_deactivate' => '¿Deseas desactivar este usuario?',
        'confirm_reactivate' => '¿Deseas reactivar este usuario?',
        'confirm_mark_verified' => '¿Marcar como verificado?',
        'action_lock' => 'Sí, bloquear',
        'action_unlock' => 'Sí, desbloquear',
        'action_deactivate' => 'Sí, desactivar',
        'action_reactivate' => 'Sí, reactivar',
        'action_mark_verified' => 'Sí, marcar',
    ],

    'modals' => [
        'register_user' => 'Registrar Usuario',
        'edit_user' => 'Editar Usuario',
        'save' => 'Guardar',
        'update' => 'Actualizar',
        'cancel' => 'Cancelar',
        'close' => 'Cerrar',
    ],

    'form' => [
        'full_name' => 'Nombre',
        'email' => 'Correo',
        'role' => 'Rol',
        'country_code' => 'Código de país',
        'phone_number' => 'Número de Teléfono',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmar contraseña',
        'toggle_password' => 'Mostrar/Ocultar contraseña',
    ],

    'password_reqs' => [
        'length'  => 'Al menos 8 caracteres',
        'special' => '1 carácter especial ( .,!@#$%^&*()_+- )',
        'number'  => '1 número',
        'match'   => 'Las contraseñas coinciden',
    ],

    'alert' => [
        'success' => 'Éxito',
        'error'   => 'Error',
    ],
];
