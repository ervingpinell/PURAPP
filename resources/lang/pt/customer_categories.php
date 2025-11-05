<?php

return [
    'ui' => [
        'page_title_index'  => 'Categorias de Clientes',
        'page_title_create' => 'Nova Categoria de Cliente',
        'page_title_edit'   => 'Editar Categoria',
        'header_index'      => 'Categorias de Clientes',
        'header_create'     => 'Nova Categoria de Cliente',
        'header_edit'       => 'Editar Categoria: :name',
        'info_card_title'   => 'Informações',
        'list_title'        => 'Lista de Categorias',
        'empty_list'        => 'Nenhuma categoria registrada.',
    ],

    'buttons' => [
        'new_category' => 'Nova Categoria',
        'save'         => 'Salvar',
        'update'       => 'Atualizar',
        'cancel'       => 'Cancelar',
        'back'         => 'Voltar',
        'delete'       => 'Excluir',
        'edit'         => 'Editar',
    ],

    'table' => [
        'name'     => 'Nome',
        'age_from' => 'Idade a partir de',
        'age_to'   => 'Idade até',
        'range'    => 'Faixa',
        'active'   => 'Ativo',
        'actions'  => 'Ações',
        'order'    => 'Ordem',
        'slug'     => 'Slug',
    ],

    'form' => [
        'name' => [
            'label'       => 'Nome',
            'placeholder' => 'Ex: Adulto, Criança, Bebê',
            'required'    => 'O nome é obrigatório',
        ],
        'slug' => [
            'label'       => 'Slug (identificador único)',
            'placeholder' => 'Ex: adult, child, infant',
            'title'       => 'Somente letras minúsculas, números, hífens e underlines',
            'helper'      => 'Apenas letras minúsculas, números, hífens (-) e underlines (_)',
        ],
        'age_from' => [
            'label'       => 'Idade a partir de',
            'placeholder' => 'Ex: 0, 3, 13, 65',
        ],
        'age_to' => [
            'label'         => 'Idade até',
            'placeholder'   => 'Ex: 2, 12, 64 (deixe vazio para “sem limite”)',
            'hint_no_limit' => 'deixe vazio para “sem limite”',
        ],
        'order' => [
            'label'  => 'Ordem de Exibição',
            'helper' => 'Define a ordem em que as categorias aparecem (menor = primeiro)',
        ],
        'active' => [
            'label'  => 'Categoria Ativa',
            'helper' => 'Apenas categorias ativas aparecem nos formulários de reserva',
        ],
        'min_per_booking' => [
            'label'       => 'Mínimo por Reserva',
            'placeholder' => 'Ex: 0, 1',
        ],
        'max_per_booking' => [
            'label'       => 'Máximo por Reserva',
            'placeholder' => 'Ex: 10 (deixe vazio para “sem limite”)',
        ],
    ],

    'states' => [
        'active'   => 'Ativo',
        'inactive' => 'Inativo',
    ],

    'alerts' => [
        'success_created' => 'Categoria criada com sucesso.',
        'success_updated' => 'Categoria atualizada com sucesso.',
        'success_deleted' => 'Categoria excluída com sucesso.',
        'warning_title'  => 'Atenção',
        'warning_text'   => 'Excluir uma categoria usada em passeios ou reservas pode causar problemas. Recomenda-se desativá-la em vez de excluí-la.',
    ],

    'dialogs' => [
        'delete' => [
            'title'   => 'Confirmar Exclusão',
            'text'    => 'Tem certeza de que deseja excluir a categoria :name?',
            'caution' => 'Esta ação não pode ser desfeita.',
        ],
    ],

    'rules' => [
        'title'                 => 'Regras Importantes',
        'no_overlap'            => 'As faixas etárias não podem se sobrepor entre categorias ativas.',
        'no_upper_limit_hint'   => 'Deixe “Idade até” vazio para indicar “sem limite superior”.',
        'slug_unique'           => 'O slug deve ser único.',
        'order_affects_display' => 'A ordem define como elas são exibidas no sistema.',
    ],

    'help' => [
        'title'           => 'Ajuda',
        'examples_title'  => 'Exemplos de Categorias',
        'infant'          => 'Bebê',
        'child'           => 'Criança',
        'adult'           => 'Adulto',
        'senior'          => 'Idoso',
        'age_from_tip'    => 'Idade a partir de:',
        'age_to_tip'      => 'Idade até:',
        'range_tip'       => 'Faixa:',
        'notes_title'     => 'Notas',
        'notes' => [
            'use_null_age_to' => 'Use age_to = NULL para indicar "sem limite superior" (ex: 18+ anos).',
            'inactive_hidden' => 'Categorias inativas não aparecem nos formulários de reserva.',
        ],
    ],

    'info' => [
        'id'        => 'ID:',
        'created'   => 'Criado:',
        'updated'   => 'Atualizado:',
        'date_fmt'  => 'd/m/Y H:i',
    ],

    'validation' => [
        'age_to_gte_age_from' => '“Idade até” deve ser maior ou igual a “Idade a partir de”.',
    ],
];
