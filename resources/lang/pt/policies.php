<?php

return [

    'page_title' => 'Políticas',
    'no_policies' => 'Nenhuma política disponível no momento.',
    'no_sections' => 'Nenhuma seção disponível no momento.',

    // =========================================================
    // [01] CAMPOS
    // =========================================================
    'fields' => [
        'title'       => 'Título',
        'description' => 'Descrição',
        'type'        => 'Tipo',
        'is_active'   => 'Ativo',
    ],

    // =========================================================
    // [02] TIPOS
    // =========================================================
    'types' => [
        'cancellation' => 'Política de Cancelamento',
        'refund'       => 'Política de Reembolso',
        'terms'        => 'Termos e Condições',
    ],

    // =========================================================
    // [03] MENSAGENS
    // =========================================================
    'success' => [
        'created'   => 'Política criada com sucesso.',
        'updated'   => 'Política atualizada com sucesso.',
        'deleted'   => 'Política excluída com sucesso.',
    ],

    'error' => [
        'create' => 'Não foi possível criar a política.',
        'update' => 'Não foi possível atualizar a política.',
        'delete' => 'Não foi possível excluir a política.',
    ],
];
