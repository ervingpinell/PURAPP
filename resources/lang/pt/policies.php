<?php

return [

    // =========================================================
    // [00] GENÉRICO
    // =========================================================
    'page_title'  => 'Políticas',
    'no_policies' => 'Não há políticas disponíveis no momento.',
    'no_sections' => 'Não há seções disponíveis no momento.',
    'propagate_to_all_langs' => 'Propagar esta alteração para todos os idiomas (EN, FR, DE, PT)',
    'propagate_hint'         => 'Será traduzida automaticamente a partir do texto atual e as traduções existentes nesses idiomas serão sobrescritas.',
    'update_base_es'         => 'Atualizar também a base (ES)',
    'update_base_hint'       => 'Substitui o nome e o conteúdo da política na tabela base (espanhol). Use apenas se você também quiser alterar o texto original.',

    // =========================================================
    // [01] CHECKOUT
    // =========================================================
    'checkout' => [
        'card_title'  => 'Seu pedido',
        'details'     => 'Detalhes',
        'must_accept' => 'Você deve ler e aceitar todas as políticas para continuar com o pagamento.',
        'accept_label_html' =>
            'Li e aceito os <strong>Termos e Condições</strong>, a <strong>Política de Privacidade</strong> e todas as <strong>Políticas de Cancelamento, Reembolso e Garantia</strong>.',
        'back'       => 'Voltar',
        'pay'        => 'Processar pagamento',
        'order_full' => 'Detalhamento completo do pedido',

        'titles' => [
            'terms'        => 'Termos e Condições',
            'privacy'      => 'Política de Privacidade',
            'cancellation' => 'Política de Cancelamento',
            'refunds'      => 'Política de Reembolso',
            'warranty'     => 'Política de Garantia',
            'payments'     => 'Formas de Pagamento',
        ],
    ],

    // =========================================================
    // [02] CAMPOS
    // =========================================================
    'fields' => [
        'title'       => 'Título',
        'description' => 'Descrição',
        'type'        => 'Tipo',
        'is_active'   => 'Ativo',
    ],

    // =========================================================
    // [03] TIPOS
    // =========================================================
    'types' => [
        'cancellation' => 'Política de Cancelamento',
        'refund'       => 'Política de Reembolso',
        'terms'        => 'Termos e Condições',
    ],

    // =========================================================
    // [04] MENSAGENS
    // =========================================================
    'success' => [
        'created' => 'Política criada com sucesso.',
        'updated' => 'Política atualizada com sucesso.',
        'deleted' => 'Política excluída com sucesso.',
    ],

    'error' => [
        'create' => 'Não foi possível criar a política.',
        'update' => 'Não foi possível atualizar a política.',
        'delete' => 'Não foi possível excluir a política.',
    ],
];
