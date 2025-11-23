<?php

return [
    'title' => 'Gestão de Impostos',
    'create' => 'Criar Imposto',
    'edit' => 'Editar Imposto',
    'fields' => [
        'name' => 'Nome',
        'code' => 'Código',
        'rate' => 'Taxa/Valor',
        'type' => 'Tipo',
        'apply_to' => 'Aplicar a',
        'is_inclusive' => 'Inclusivo',
        'is_active' => 'Ativo',
        'sort_order' => 'Ordem',
    ],
    'types' => [
        'percentage' => 'Percentagem (%)',
        'fixed' => 'Valor Fixo ($)',
    ],
    'apply_to_options' => [
        'subtotal' => 'Subtotal',
        'total' => 'Total (Cascata)',
        'per_person' => 'Por Pessoa',
    ],
    'messages' => [
        'created' => 'Imposto criado com sucesso.',
        'updated' => 'Imposto atualizado com sucesso.',
        'deleted' => 'Imposto excluído com sucesso.',
        'toggled' => 'Status do imposto atualizado.',
        'select_taxes' => 'Selecione os impostos que se aplicam a este passeio.',
    ],
];
