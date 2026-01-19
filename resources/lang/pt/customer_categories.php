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

        // Novas para index/listagem
        'list_title'        => 'Lista de Categorias',
        'empty_list'        => 'Não há categorias cadastradas.',
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
        'active'   => 'Ativa',
        'actions'  => 'Ações',

        // Novos usados no index
        'order'    => 'Ordem',
        'slug'     => 'Slug',
    ],

    'form' => [
        'translations' => [
            'title'          => 'Traduções do nome',
            'auto_translate' => 'Traduzir automaticamente os outros idiomas (DeepL)',
            'regen_missing'  => 'Preencher automaticamente os campos vazios (DeepL)',
            'at_least_first' => 'Você deve preencher pelo menos o primeiro idioma.',
            'locale_hint'    => 'Tradução para o locale :loc.',
            'auto_translate_hint' => 'Tradução automática por DeepL',
            'auto_translate_hint' => 'Tradução automática por DeepL',
        ],
        'name' => [
            'label'       => 'Nome',
            'placeholder' => 'Ex: Adulto, Criança, Bebê',
            'required'    => 'O nome é obrigatório.',
        ],
        'slug' => [
            'label'       => 'Slug (identificador único)',
            'placeholder' => 'Ex: adult, child, infant',
            'title'       => 'Apenas letras minúsculas, números, hifens e underlines',
            'helper'      => 'Somente letras minúsculas, números, hifens (-) e underlines (_)',
        ],
        'age_from' => [
            'label'       => 'Idade a partir de',
            'placeholder' => 'Ex: 0, 3, 13, 65',
        ],
        'age_to' => [
            'label'         => 'Idade até',
            'placeholder'   => 'Ex: 2, 12, 64 (deixar vazio para “sem limite”)',
            'hint_no_limit' => 'deixar vazio para “sem limite”',
        ],
        'order' => [
            'label'  => 'Ordem de exibição',
            'helper' => 'Define a ordem em que as categorias aparecem (menor = primeiro)',
        ],
        'active' => [
            'label'  => 'Categoria ativa',
            'helper' => 'Apenas categorias ativas são exibidas nos formulários de reserva',
        ],
        'min_per_booking' => [
            'label'       => 'Mínimo por reserva',
            'placeholder' => 'Ex: 0, 1',
        ],
        'max_per_booking' => [
            'label'       => 'Máximo por reserva',
            'placeholder' => 'Ex: 10 (deixar vazio para “sem limite”)',
        ],
    ],

    'states' => [
        'active'     => 'Ativa',
        'inactive'   => 'Inativa',
        'activate'   => 'Ativar',
        'deactivate' => 'Desativar',
    ],

    'alerts' => [
        'success_created' => 'Categoria criada com sucesso.',
        'success_updated' => 'Categoria atualizada com sucesso.',
        'success_deleted' => 'Categoria excluída com sucesso.',
        'warning_title'   => 'Aviso',
        'warning_text'    => 'Excluir uma categoria que está em uso em passeios ou reservas pode causar problemas. Recomenda-se desativá-la em vez de excluí-la.',
    ],

    'dialogs' => [
        'delete' => [
            'title'   => 'Confirmar exclusão',
            'text'    => 'Tem certeza de que deseja excluir a categoria :name?',
            'caution' => 'Essa ação não pode ser desfeita.',
        ],
    ],

    'rules' => [
        'title'                 => 'Regras importantes',
        'no_overlap'            => 'As faixas de idade não podem se sobrepor entre categorias ativas.',
        'no_upper_limit_hint'   => 'Deixe “Idade até” vazio para indicar “sem limite superior”.',
        'slug_unique'           => 'O slug deve ser único.',
        'order_affects_display' => 'A ordem define como elas são exibidas no sistema.',
    ],

    'help' => [
        'title'           => 'Ajuda',
        'examples_title'  => 'Exemplos de categorias',
        'infant'          => 'Bebê',
        'child'           => 'Criança',
        'adult'           => 'Adulto',
        'senior'          => 'Idoso',
        'age_from_tip'    => 'Idade a partir de:',
        'age_to_tip'      => 'Idade até:',
        'range_tip'       => 'Faixa:',
        'notes_title'     => 'Notas',
        'notes' => [
            'use_null_age_to' => 'Use age_to = NULL para indicar “sem limite superior” (ex: 18+ anos).',
            'inactive_hidden' => 'Categorias inativas não são exibidas nos formulários de reserva.',
        ],
    ],

    'info' => [
        'id'       => 'ID:',
        'created'  => 'Criado em:',
        'updated'  => 'Atualizado em:',
        'date_fmt' => 'd/m/Y H:i',
    ],

    'validation' => [
        'age_to_gte_age_from' => 'A idade máxima deve ser maior ou igual à idade mínima.',
    ],
];
