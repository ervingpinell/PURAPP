<?php

return [

    'hotels' => [

        // Título / cabeçalhos
        'title'             => 'Lista de hotéis',
        'header'            => 'Hotéis registrados',
        'sort_alpha'        => 'Ordenar alfabeticamente',

        // Campos / colunas / ações
        'name'              => 'Nome',
        'status'            => 'Status',
        'actions'           => 'Ações',
        'active'            => 'Ativo',
        'inactive'          => 'Inativo',
        'add'               => 'Adicionar',
        'edit'              => 'Editar',
        'delete'            => 'Excluir',
        'activate'          => 'Ativar',
        'deactivate'        => 'Desativar',
        'save_changes'      => 'Salvar alterações',
        'cancel'            => 'Cancelar',
        'close'             => 'Fechar',
        'no_records'        => 'Não há hotéis registrados.',
        'name_placeholder'  => 'Ex.: Hotel Arenal Springs',

        // Confirmações
        'confirm_activate_title'    => 'Ativar hotel?',
        'confirm_activate_text'     => 'Tem certeza de que deseja ativar ":name"?',
        'confirm_deactivate_title'  => 'Desativar hotel?',
        'confirm_deactivate_text'   => 'Tem certeza de que deseja desativar ":name"?',
        'confirm_delete_title'      => 'Excluir definitivamente?',
        'confirm_delete_text'       => '":name" será excluído. Esta ação não pode ser desfeita.',

        // Mensagens (flash)
        'created_success'    => 'Hotel criado com sucesso.',
        'updated_success'    => 'Hotel atualizado com sucesso.',
        'deleted_success'    => 'Hotel excluído com sucesso.',
        'activated_success'  => 'Hotel ativado com sucesso.',
        'deactivated_success'=> 'Hotel desativado com sucesso.',
        'sorted_success'     => 'Hotéis ordenados alfabeticamente.',
        'unexpected_error'   => 'Ocorreu um erro inesperado. Tente novamente.',

        // Validação / genéricos
        'validation' => [
            'name_required' => 'O nome é obrigatório.',
            'name_unique'   => 'Esse hotel já existe na lista.',
            'name_max'      => 'O nome não pode exceder 255 caracteres.',
        ],
        'error_title' => 'Erro',

        // Modais
        'edit_title' => 'Editar hotel',
    ],

    'meeting_point' => [

        // UI
        'ui' => [
            'page_title'   => 'Pontos de encontro',
            'page_heading' => 'Pontos de encontro',
        ],

        // Badges
        'badges' => [
            'count_badge' => ':count registros',
            'active'      => 'Ativo',
            'inactive'    => 'Inativo',
        ],

        // Criar
        'create' => [
            'title' => 'Adicionar ponto',
        ],

        // Listagem
        'list' => [
            'title' => 'Listagem',
            'empty' => 'Não há registros. Crie o primeiro acima.',
        ],

        // Rótulos compactos nos cards
        'labels' => [
            'time'       => 'Hora',
            'sort_order' => 'Ordem',
        ],

        // Campos
        'fields' => [
            'name'                    => 'Nome',
            'pickup_time'             => 'Horário de busca',
            'sort_order'              => 'Ordem',
            'description'             => 'Descrição',
            'map_url'                 => 'URL do mapa',
            'active'                  => 'Ativo',
            'time_short'              => 'Hora',
            'map'                     => 'Mapa',
            'status'                  => 'Status',
            'actions'                 => 'Ações',

            // Edição / traduções
            'name_base'               => 'Nome (base)',
            'description_base'        => 'Descrição (base)',
            'locale'                  => 'Idioma',
            'name_translation'        => 'Nome (tradução)',
            'description_translation' => 'Descrição (tradução)',
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

        // Dicas
        'hints' => [
            'name_example'   => 'Ex.: "Parque Central de La Fortuna".',
            'name_base_sync' => 'Se você não alterar, permanece igual. O nome por idioma é editado abaixo.',
            'fallback_sync'  => 'Se você escolher o locale <strong>:fallback</strong>, ele também será sincronizado com os campos base.',
        ],

        // Botões
        'buttons' => [
            'reload'       => 'Recarregar',
            'save'         => 'Salvar',
            'clear'        => 'Limpar',
            'create'       => 'Criar',
            'cancel'       => 'Cancelar',
            'save_changes' => 'Salvar alterações',
            'close'        => 'Fechar',
            'ok'           => 'Entendi',
            'confirm'      => 'Sim, continuar',
            'delete'       => 'Excluir',
            'activate'     => 'Ativar',
            'deactivate'   => 'Desativar',
        ],

        // Ações (títulos / tooltips)
        'actions' => [
            'view_map'    => 'Ver mapa',
            'view_on_map' => 'Ver no mapa',
            'edit'        => 'Editar',
            'delete'      => 'Excluir',
            'activate'    => 'Ativar',
            'deactivate'  => 'Desativar',
        ],

        // Confirmações
        'confirm' => [
            'create_title'             => 'Criar novo ponto de encontro?',
            'create_text_with_name'    => '":name" será criado.',
            'create_text'              => 'Um novo ponto de encontro será criado.',

            'save_title'               => 'Salvar alterações?',
            'save_text'                => 'O ponto de encontro e a tradução selecionada serão atualizados.',

            'deactivate_title'         => 'Desativar ponto de encontro?',
            'deactivate_title_short'   => 'Desativar?',
            'deactivate_text'          => '":name" ficará inativo.',

            'activate_title'           => 'Ativar ponto de encontro?',
            'activate_title_short'     => 'Ativar?',
            'activate_text'            => '":name" ficará ativo.',

            'delete_title'             => 'Excluir ponto de encontro?',
            'delete_title_short'       => 'Excluir?',
            'delete_text'              => '":name" será excluído permanentemente. Esta ação não pode ser desfeita.',
        ],

        // Validação / Toastr / SweetAlert
        'validation' => [
            'title'                         => 'Erros de validação',
            'missing_translated_name_title' => 'Falta o nome traduzido',
            'missing_translated_name_text'  => 'Preencha o campo de nome traduzido.',
        ],

        'toasts' => [
            'success_title' => 'Sucesso',
            'error_title'   => 'Erro',
        ],
    ],

];
