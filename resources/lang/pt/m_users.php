<?php

return [
    'title' => 'Gerenciamento de Usuários',
    'add_user' => 'Adicionar Usuário',
    'no_role' => 'Sem função',
    'user_marked_verified' => 'Usuário marcado como verificado com sucesso.',

    'filters' => [
        'role' => 'Filtrar por função:',
        'state' => 'Filtrar por status:',
        'email' => 'Filtrar por e-mail:',
        'email_placeholder' => 'exemplo@dominio.com',
        'all' => '-- Todos --',
        'search' => 'Buscar',
        'clear' => 'Limpar',
    ],

    'table' => [
        'id' => 'ID',
        'name' => 'Nome',
        'email' => 'E-mail',
        'role' => 'Função',
        'phone' => 'Telefone',
        'status' => 'Status',
        'verified' => 'Verificado',
        'locked' => 'Bloqueado',
        'actions' => 'Ações',
    ],

    'status' => [
        'active' => 'Ativo',
        'inactive' => 'Inativo',
    ],

    'verified' => [
        'yes' => 'Sim',
        'no'  => 'Não',
    ],

    'locked' => [
        'yes' => 'Sim',
        'no'  => 'Não',
    ],

    'actions' => [
        'edit' => 'Editar',
        'deactivate' => 'Desativar',
        'reactivate' => 'Reativar',
        'lock' => 'Bloquear',
        'unlock' => 'Desbloquear',
        'mark_verified' => 'Marcar como verificado',
    ],

    'dialog' => [
        'title' => 'Confirmação',
        'cancel' => 'Cancelar',
        'confirm_lock' => 'Bloquear este usuário?',
        'confirm_unlock' => 'Desbloquear este usuário?',
        'confirm_deactivate' => 'Desativar este usuário?',
        'confirm_reactivate' => 'Reativar este usuário?',
        'confirm_mark_verified' => 'Marcar como verificado?',
        'action_lock' => 'Sim, bloquear',
        'action_unlock' => 'Sim, desbloquear',
        'action_deactivate' => 'Sim, desativar',
        'action_reactivate' => 'Sim, reativar',
        'action_mark_verified' => 'Sim, marcar',
    ],

    'modals' => [
        'register_user' => 'Registrar Usuário',
        'edit_user' => 'Editar Usuário',
        'save' => 'Salvar',
        'update' => 'Atualizar',
        'cancel' => 'Cancelar',
        'close' => 'Fechar',
    ],

    'form' => [
        'full_name' => 'Nome',
        'email' => 'E-mail',
        'role' => 'Função',
        'country_code' => 'Código do país',
        'phone_number' => 'Número de telefone',
        'password' => 'Senha',
        'password_confirmation' => 'Confirmar senha',
        'toggle_password' => 'Mostrar/Ocultar senha',
    ],

    'password_reqs' => [
        'length'  => 'Pelo menos 8 caracteres',
        'special' => '1 caractere especial (.,!@#$%^&*()_+-)',
        'number'  => '1 número',
        'match'   => 'As senhas coincidem',
    ],

    'alert' => [
        'success' => 'Sucesso',
        'error'   => 'Erro',
    ],
];
