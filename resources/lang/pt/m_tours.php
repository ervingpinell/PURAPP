<?php

/*************************************************************
 *  MÓDULO DE TRADUÇÃO: TOURS
 *  Arquivo: resources/lang/pt/m_tours.php
 *
 *  Sumário (seção & linha inicial)
 *  [01] COMMON           -> linha 19
 *  [02] AMENITY          -> linha 27
 *  [03] SCHEDULE         -> linha 90
 *  [04] ITINERARY_ITEM   -> linha 176
 *  [05] ITINERARY        -> linha 239
 *  [06] LANGUAGE         -> linha 302
 *  [07] TOUR             -> linha 386
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title' => 'Sucesso',
        'error_title'   => 'Erro',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'fields' => [
            'name' => 'Nome',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],

        'ui' => [
            'page_title'    => 'Comodidades',
            'page_heading'  => 'Gestão de Comodidades',
            'list_title'    => 'Lista de Comodidades',

            'add'            => 'Adicionar Comodidade',
            'create_title'   => 'Registrar Comodidade',
            'edit_title'     => 'Editar Comodidade',
            'save'           => 'Salvar',
            'update'         => 'Atualizar',
            'cancel'         => 'Cancelar',
            'close'          => 'Fechar',
            'state'          => 'Estado',
            'actions'        => 'Ações',
            'delete_forever' => 'Excluir permanentemente',

            'processing' => 'Processando...',
            'applying'   => 'Aplicando...',
            'deleting'   => 'Excluindo...',

            'toggle_on'  => 'Ativar comodidade',
            'toggle_off' => 'Desativar comodidade',

            'toggle_confirm_on_title'  => 'Ativar comodidade?',
            'toggle_confirm_off_title' => 'Desativar comodidade?',
            'toggle_confirm_on_html'   => 'A comodidade <b>:label</b> será ativada.',
            'toggle_confirm_off_html'  => 'A comodidade <b>:label</b> será desativada.',

            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => '<b>:label</b> será excluída e não poderá ser desfeita.',

            'yes_continue' => 'Sim, continuar',
            'yes_delete'   => 'Sim, excluir',

            'item_this' => 'esta comodidade',
        ],

        'success' => [
            'created'     => 'Comodidade criada com sucesso.',
            'updated'     => 'Comodidade atualizada com sucesso.',
            'activated'   => 'Comodidade ativada com sucesso.',
            'deactivated' => 'Comodidade desativada com sucesso.',
            'deleted'     => 'Comodidade excluída permanentemente.',
        ],

        'error' => [
            'create' => 'Não foi possível criar a comodidade.',
            'update' => 'Não foi possível atualizar a comodidade.',
            'toggle' => 'Não foi possível alterar o estado da comodidade.',
            'delete' => 'Não foi possível excluir a comodidade.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nome inválido',
                'required' => 'O campo :attribute é obrigatório.',
                'string'   => 'O campo :attribute deve ser uma string.',
                'max'      => 'O campo :attribute não pode ter mais de :max caracteres.',
            ],
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'fields' => [
            'start_time'     => 'Início',
            'end_time'       => 'Fim',
            'label'          => 'Rótulo',
            'label_optional' => 'Rótulo (opcional)',
            'max_capacity'   => 'Capacidade máx.',
            'active'         => 'Ativo',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],

        'ui' => [
            'page_title'        => 'Horários dos Passeios',
            'page_heading'      => 'Gestão de Horários',

            'general_title'     => 'Horários gerais',
            'new_schedule'      => 'Novo horário',
            'new_general_title' => 'Novo horário geral',
            'new'               => 'Novo',
            'edit_schedule'     => 'Editar horário',
            'edit_global'       => 'Editar (global)',

            'assign_existing'    => 'Atribuir existente',
            'assign_to_tour'     => 'Atribuir horário a ":tour"',
            'select_schedule'    => 'Selecione um horário',
            'choose'             => 'Escolher',
            'assign'             => 'Atribuir',
            'new_for_tour_title' => 'Novo horário para ":tour"',

            'time_range'        => 'Horário',
            'state'             => 'Estado',
            'actions'           => 'Ações',
            'schedule_state'    => 'Horário',
            'assignment_state'  => 'Atribuição',
            'no_general'        => 'Não há horários gerais.',
            'no_tour_schedules' => 'Este passeio ainda não tem horários.',
            'no_label'          => 'Sem rótulo',
            'assigned_count'    => 'horário(s) atribuído(s)',

            'toggle_global_title'     => 'Ativar/Desativar (global)',
            'toggle_global_on_title'  => 'Ativar horário (global)?',
            'toggle_global_off_title' => 'Desativar horário (global)?',
            'toggle_global_on_html'   => '<b>:label</b> será ativado para todos os passeios.',
            'toggle_global_off_html'  => '<b>:label</b> será desativado para todos os passeios.',

            'toggle_on_tour'          => 'Ativar neste passeio',
            'toggle_off_tour'         => 'Desativar neste passeio',
            'toggle_assign_on_title'  => 'Ativar neste passeio?',
            'toggle_assign_off_title' => 'Desativar neste passeio?',
            'toggle_assign_on_html'   => 'A atribuição ficará <b>ativa</b> para <b>:tour</b>.',
            'toggle_assign_off_html'  => 'A atribuição ficará <b>inativa</b> para <b>:tour</b>.',

            'detach_from_tour'     => 'Remover do passeio',
            'detach_confirm_title' => 'Remover do passeio?',
            'detach_confirm_html'  => 'O horário será <b>removido</b> de <b>:tour</b>.',

            'delete_forever'       => 'Excluir (global)',
            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => '<b>:label</b> será excluído (global) e não poderá ser desfeito.',

            'yes_continue' => 'Sim, continuar',
            'yes_delete'   => 'Sim, excluir',
            'yes_detach'   => 'Sim, remover',

            'this_schedule' => 'este horário',
            'this_tour'     => 'este passeio',

            'processing'     => 'Processando...',
            'applying'       => 'Aplicando...',
            'deleting'       => 'Excluindo...',
            'removing'       => 'Removendo...',
            'saving_changes' => 'Salvando alterações...',
            'save'           => 'Salvar',
            'save_changes'   => 'Salvar alterações',
            'cancel'         => 'Cancelar',

            'missing_fields_title' => 'Dados ausentes',
            'missing_fields_text'  => 'Verifique os campos obrigatórios (início, fim e capacidade).',
            'could_not_save'       => 'Não foi possível salvar',
        ],

        'success' => [
            'created'                => 'Horário criado com sucesso.',
            'updated'                => 'Horário atualizado com sucesso.',
            'activated_global'       => 'Horário ativado com sucesso (global).',
            'deactivated_global'     => 'Horário desativado com sucesso (global).',
            'attached'               => 'Horário atribuído ao passeio.',
            'detached'               => 'Horário removido do passeio.',
            'assignment_activated'   => 'Atribuição ativada para este passeio.',
            'assignment_deactivated' => 'Atribuição desativada para este passeio.',
            'deleted'                => 'Horário excluído com sucesso.',
        ],

        'error' => [
            'create'               => 'Ocorreu um problema ao criar o horário.',
            'update'               => 'Ocorreu um problema ao atualizar o horário.',
            'toggle'               => 'Não foi possível alterar o estado global do horário.',
            'attach'               => 'Não foi possível atribuir o horário ao passeio.',
            'detach'               => 'Não foi possível remover o horário do passeio.',
            'assignment_toggle'    => 'Não foi possível alterar o estado da atribuição.',
            'not_assigned_to_tour' => 'O horário não está atribuído a este passeio.',
            'delete'               => 'Ocorreu um problema ao excluir o horário.',
        ],
    ],

    // =========================================================
    // [04] ITINERARY_ITEM
    // =========================================================
    'itinerary_item' => [
        'fields' => [
            'title'       => 'Título',
            'description' => 'Descrição',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],

        'ui' => [
            'list_title'    => 'Itens de Itinerário',
            'add_item'      => 'Adicionar Item',
            'register_item' => 'Registrar Item',
            'edit_item'     => 'Editar Item',
            'save'          => 'Salvar',
            'update'        => 'Atualizar',
            'cancel'        => 'Cancelar',
            'state'         => 'Estado',
            'actions'       => 'Ações',
            'see_more'      => 'Ver mais',
            'see_less'      => 'Ver menos',

            'toggle_on'  => 'Ativar item',
            'toggle_off' => 'Desativar item',

            'delete_forever'       => 'Excluir permanentemente',
            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => '<b>:label</b> será excluído e não poderá ser desfeito.',
            'yes_delete'           => 'Sim, excluir',
            'item_this'            => 'este item',

            'processing' => 'Processando...',
            'applying'   => 'Aplicando...',
            'deleting'   => 'Excluindo...',
        ],

        'success' => [
            'created'     => 'Item de itinerário criado com sucesso.',
            'updated'     => 'Item atualizado com sucesso.',
            'activated'   => 'Item ativado com sucesso.',
            'deactivated' => 'Item desativado com sucesso.',
            'deleted'     => 'Item excluído permanentemente.',
        ],

        'error' => [
            'create' => 'Não foi possível criar o item.',
            'update' => 'Não foi possível atualizar o item.',
            'toggle' => 'Não foi possível alterar o estado do item.',
            'delete' => 'Não foi possível excluir o item.',
        ],

        'validation' => [
            'title' => [
                'required' => 'O campo :attribute é obrigatório.',
                'string'   => 'O campo :attribute deve ser uma string.',
                'max'      => 'O campo :attribute não pode ter mais de :max caracteres.',
            ],
            'description' => [
                'required' => 'O campo :attribute é obrigatório.',
                'string'   => 'O campo :attribute deve ser uma string.',
                'max'      => 'O campo :attribute não pode ter mais de :max caracteres.',
            ],
        ],
    ],

    // =========================================================
    // [05] ITINERARY
    // =========================================================
    'itinerary' => [
        'fields' => [
            'name'                 => 'Nome do itinerário',
            'description'          => 'Descrição',
            'description_optional' => 'Descrição (opcional)',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],

        'ui' => [
            'page_title'    => 'Itinerários & Itens',
            'page_heading'  => 'Gestão de Itinerários e Itens',
            'new_itinerary' => 'Novo Itinerário',

            'assign'        => 'Atribuir',
            'edit'          => 'Editar',
            'save'          => 'Salvar',
            'cancel'        => 'Cancelar',
            'close'         => 'Fechar',
            'create_title'  => 'Criar novo itinerário',
            'create_button' => 'Criar',

            'toggle_on'  => 'Ativar itinerário',
            'toggle_off' => 'Desativar itinerário',
            'toggle_confirm_on_title'  => 'Ativar itinerário?',
            'toggle_confirm_off_title' => 'Desativar itinerário?',
            'toggle_confirm_on_html'   => 'O itinerário <b>:label</b> ficará <b>ativo</b>.',
            'toggle_confirm_off_html'  => 'O itinerário <b>:label</b> ficará <b>inativo</b>.',
            'yes_continue' => 'Sim, continuar',

            'assign_title'          => 'Atribuir itens a :name',
            'drag_hint'             => 'Arraste e solte os itens para definir a ordem.',
            'drag_handle'           => 'Arrastar para reordenar',
            'select_one_title'      => 'Selecione pelo menos um item',
            'select_one_text'       => 'Selecione pelo menos um item para continuar.',
            'assign_confirm_title'  => 'Atribuir itens selecionados?',
            'assign_confirm_button' => 'Sim, atribuir',
            'assigning'             => 'Atribuindo...',

            'no_items_assigned'       => 'Nenhum item atribuído a este itinerário.',
            'itinerary_this'          => 'este itinerário',
            'processing'              => 'Processando...',
            'saving'                  => 'Salvando...',
            'activating'              => 'Ativando...',
            'deactivating'            => 'Desativando...',
            'applying'                => 'Aplicando...',
            'deleting'                => 'Excluindo...',
            'flash_success_title'     => 'Sucesso',
            'flash_error_title'       => 'Erro',
            'validation_failed_title' => 'Não foi possível processar',
        ],

        'success' => [
            'created'        => 'Itinerário criado com sucesso.',
            'updated'        => 'Itinerário atualizado com sucesso.',
            'activated'      => 'Itinerário ativado com sucesso.',
            'deactivated'    => 'Itinerário desativado com sucesso.',
            'deleted'        => 'Itinerário excluído permanentemente.',
            'items_assigned' => 'Itens atribuídos com sucesso.',
        ],

        'error' => [
            'create'  => 'Não foi possível criar o itinerário.',
            'update'  => 'Não foi possível atualizar o itinerário.',
            'toggle'  => 'Não foi possível alterar o estado do itinerário.',
            'delete'  => 'Não foi possível excluir o itinerário.',
            'assign'  => 'Não foi possível atribuir os itens.',
        ],
    ],

    // =========================================================
    // [06] LANGUAGE
    // =========================================================
    'language' => [
        'fields' => [
            'name' => 'Idioma',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],

        'ui' => [
            'page_title'   => 'Idiomas dos Passeios',
            'page_heading' => 'Gestão de Idiomas',
            'list_title'   => 'Lista de Idiomas',

            'table' => [
                'id'      => 'ID',
                'name'    => 'Idioma',
                'state'   => 'Estado',
                'actions' => 'Ações',
            ],

            'add'            => 'Adicionar Idioma',
            'create_title'   => 'Registrar Idioma',
            'edit_title'     => 'Editar Idioma',
            'save'           => 'Salvar',
            'update'         => 'Atualizar',
            'cancel'         => 'Cancelar',
            'close'          => 'Fechar',
            'actions'        => 'Ações',
            'delete_forever' => 'Excluir permanentemente',

            'processing'   => 'Processando...',
            'saving'       => 'Salvando...',
            'activating'   => 'Ativando...',
            'deactivating' => 'Desativando...',
            'deleting'     => 'Excluindo...',

            'toggle_on'  => 'Ativar idioma',
            'toggle_off' => 'Desativar idioma',
            'toggle_confirm_on_title'  => 'Ativar idioma?',
            'toggle_confirm_off_title' => 'Desativar idioma?',
            'toggle_confirm_on_html'   => 'O idioma <b>:label</b> ficará <b>ativo</b>.',
            'toggle_confirm_off_html'  => 'O idioma <b>:label</b> ficará <b>inativo</b>.',
            'edit_confirm_title'       => 'Salvar alterações?',
            'edit_confirm_button'      => 'Sim, salvar',

            'yes_continue' => 'Sim, continuar',
            'yes_delete'   => 'Sim, excluir',
            'item_this'    => 'este idioma',

            'flash' => [
                'activated_title'   => 'Idioma Ativado',
                'deactivated_title' => 'Idioma Desativado',
                'updated_title'     => 'Idioma Atualizado',
                'created_title'     => 'Idioma Registrado',
                'deleted_title'     => 'Idioma Excluído',
            ],
        ],

        'success' => [
            'created'     => 'Idioma criado com sucesso.',
            'updated'     => 'Idioma atualizado com sucesso.',
            'activated'   => 'Idioma ativado com sucesso.',
            'deactivated' => 'Idioma desativado com sucesso.',
            'deleted'     => 'Idioma excluído com sucesso.',
        ],

        'error' => [
            'create' => 'Não foi possível criar o idioma.',
            'update' => 'Não foi possível atualizar o idioma.',
            'toggle' => 'Não foi possível alterar o estado do idioma.',
            'delete' => 'Não foi possível excluir o idioma.',
            'save'   => 'Não foi possível salvar',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nome inválido',
                'required' => 'O nome do idioma é obrigatório.',
                'string'   => 'O campo :attribute deve ser uma string.',
                'max'      => 'O campo :attribute não pode ter mais de :max caracteres.',
                'unique'   => 'Já existe um idioma com este nome.',
            ],
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'fields' => [
            'id'           => 'ID',
            'name'         => 'Nome',
            'overview'     => 'Resumo',
            'amenities'    => 'Comodidades',
            'exclusions'   => 'Exclusões',
            'itinerary'    => 'Itinerário',
            'languages'    => 'Idiomas',
            'schedules'    => 'Horários',
            'adult_price'  => 'Preço adulto',
            'kid_price'    => 'Preço criança',
            'length_hours' => 'Duração (h)',
            'max_capacity' => 'Capacidade máx.',
            'type'         => 'Tipo',
            'viator_code'  => 'Código Viator',
            'status'       => 'Estado',
            'actions'      => 'Ações',
        ],
        'table' => [
            'id'           => 'ID',
            'name'         => 'Nome',
            'overview'     => 'Resumo',
            'amenities'    => 'Comodidades',
            'exclusions'   => 'Exclusões',
            'itinerary'    => 'Itinerário',
            'languages'    => 'Idiomas',
            'schedules'    => 'Horários',
            'adult_price'  => 'Preço adulto',
            'kid_price'    => 'Preço criança',
            'length_hours' => 'Duração (h)',
            'max_capacity' => 'Capacidade máx.',
            'type'         => 'Tipo',
            'viator_code'  => 'Código Viator',
            'status'       => 'Estado',
            'actions'      => 'Ações',
        ],
        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],
        'ui' => [
            'page_title'   => 'Passeios',
            'page_heading' => 'Gestão de Passeios',

            'font_decrease_title' => 'Diminuir tamanho da fonte',
            'font_increase_title' => 'Aumentar tamanho da fonte',

            'see_more' => 'Ver mais',
            'see_less' => 'Ver menos',

            'none' => [
                'amenities'       => 'Sem comodidades',
                'exclusions'      => 'Sem exclusões',
                'languages'       => 'Sem idiomas',
                'itinerary'       => 'Sem itinerário',
                'itinerary_items' => '(Sem itens)',
                'schedules'       => 'Sem horários',
            ],

            'toggle_on'         => 'Ativar',
            'toggle_off'        => 'Desativar',
            'toggle_on_title'   => 'Deseja ativar este passeio?',
            'toggle_off_title'  => 'Deseja desativar este passeio?',
            'toggle_on_button'  => 'Sim, ativar',
            'toggle_off_button' => 'Sim, desativar',

            'confirm_title'   => 'Confirmação',
            'confirm_text'    => 'Confirmar ação?',
            'yes_confirm'     => 'Sim, confirmar',
            'cancel'          => 'Cancelar',

            'load_more'       => 'Carregar mais',
            'loading'         => 'Carregando...',
            'load_more_error' => 'Não foi possível carregar mais',
        ],
        'success' => [
            'created'     => 'Passeio criado com sucesso.',
            'updated'     => 'Passeio atualizado com sucesso.',
            'activated'   => 'Passeio ativado com sucesso.',
            'deactivated' => 'Passeio desativado com sucesso.',
        ],
        'error' => [
            'create' => 'Ocorreu um problema ao criar o passeio.',
            'update' => 'Ocorreu um problema ao atualizar o passeio.',
            'toggle' => 'Ocorreu um problema ao alterar o estado do passeio.',
        ],
    ],
    // =========================================================
    // [08] IMAGES
    // =========================================================
    'image' => [
    'limit_reached_title' => 'Limite atingido',
    'limit_reached_text'  => 'O limite de imagens para este passeio foi atingido.',
    'upload_success'      => 'Imagens enviadas com sucesso.',
    'upload_none'         => 'Nenhuma imagem foi enviada.',
    'upload_truncated'    => 'Alguns arquivos foram ignorados devido ao limite por passeio.',
    'done'                => 'Concluído',
    'notice'              => 'Aviso',
    'saved'               => 'Salvo',
    'caption_updated'     => 'Legenda atualizada com sucesso.',
    'deleted'             => 'Excluído',
    'image_removed'       => 'Imagem removida com sucesso.',
    'invalid_order'       => 'Payload de ordenação inválido.',
    'nothing_to_reorder'  => 'Nada para reordenar.',
    'order_saved'         => 'Ordem salva.',
    'cover_updated_title' => 'Capa atualizada',
    'cover_updated_text'  => 'Esta imagem agora é a capa.',

    'ui' => [
        'page_title_pick'   => 'Imagens dos Passeios — Escolher passeio',
        'page_heading'      => 'Imagens dos Passeios',
        'choose_tour'       => 'Escolher passeio',
        'search_placeholder'=> 'Buscar por ID ou nome…',
        'search_button'     => 'Buscar',
        'no_results'        => 'Nenhum passeio encontrado.',
        'manage_images'     => 'Gerenciar imagens',
        'cover_alt'         => 'Capa',
    ],
],

];
