<?php

/*************************************************************
 *  MÓDULO DE TRADUÇÃO: TOURS
 *  Arquivo: resources/lang/pt/m_tours.php
 *
 *  Índice (seção e linha inicial)
 *  [01] COMMON           -> linha 23
 *  [02] AMENITY          -> linha 31
 *  [03] SCHEDULE         -> linha 106
 *  [04] ITINERARY_ITEM   -> linha 218
 *  [05] ITINERARY        -> linha 288
 *  [06] LANGUAGE         -> linha 364
 *  [07] TOUR             -> linha 454
 *  [08] IMAGES           -> linha 578
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
            'state'          => 'Status',
            'actions'        => 'Ações',
            'delete_forever' => 'Excluir permanentemente',

            'processing' => 'Processando...',
            'applying'   => 'Aplicando...',
            'deleting'   => 'Excluindo...',

            'toggle_on'  => 'Ativar comodidade',
            'toggle_off' => 'Desativar comodidade',

            'toggle_confirm_on_title'  => 'Ativar comodidade?',
            'toggle_confirm_off_title' => 'Desativar comodidade?',
            'toggle_confirm_on_html'   => 'A comodidade <b>:label</b> ficará ativa.',
            'toggle_confirm_off_html'  => 'A comodidade <b>:label</b> ficará inativa.',

            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => '<b>:label</b> será excluída e você não poderá desfazer.',

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
            'toggle' => 'Não foi possível alterar o status da comodidade.',
            'delete' => 'Não foi possível excluir a comodidade.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nome inválido',
                'required' => 'O :attribute é obrigatório.',
                'string'   => 'O :attribute deve ser uma string.',
                'max'      => 'O :attribute não pode exceder :max caracteres.',
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
            'page_title'        => 'Horários de Tours',
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
            'state'             => 'Status',
            'actions'           => 'Ações',
            'schedule_state'    => 'Horário',
            'assignment_state'  => 'Atribuição',
            'no_general'        => 'Não há horários gerais.',
            'no_tour_schedules' => 'Este tour ainda não possui horários.',
            'no_label'          => 'Sem rótulo',
            'assigned_count'    => 'horário(s) atribuído(s)',

            'toggle_global_title'     => 'Ativar/Desativar (global)',
            'toggle_global_on_title'  => 'Ativar horário (global)?',
            'toggle_global_off_title' => 'Desativar horário (global)?',
            'toggle_global_on_html'   => '<b>:label</b> será ativado para todos os tours.',
            'toggle_global_off_html'  => '<b>:label</b> será desativado para todos os tours.',

            'toggle_on_tour'          => 'Ativar neste tour',
            'toggle_off_tour'         => 'Desativar neste tour',
            'toggle_assign_on_title'  => 'Ativar neste tour?',
            'toggle_assign_off_title' => 'Desativar neste tour?',
            'toggle_assign_on_html'   => 'A atribuição ficará <b>ativa</b> para <b>:tour</b>.',
            'toggle_assign_off_html'  => 'A atribuição ficará <b>inativa</b> para <b>:tour</b>.',

            'detach_from_tour'     => 'Remover do tour',
            'detach_confirm_title' => 'Remover do tour?',
            'detach_confirm_html'  => 'O horário será <b>desatribuído</b> de <b>:tour</b>.',

            'delete_forever'       => 'Excluir (global)',
            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => '<b>:label</b> (global) será excluído e você não poderá desfazer.',

            'yes_continue' => 'Sim, continuar',
            'yes_delete'   => 'Sim, excluir',
            'yes_detach'   => 'Sim, remover',

            'this_schedule' => 'este horário',
            'this_tour'     => 'este tour',

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
            'attached'               => 'Horário atribuído ao tour.',
            'detached'               => 'Horário removido do tour.',
            'assignment_activated'   => 'Atribuição ativada para este tour.',
            'assignment_deactivated' => 'Atribuição desativada para este tour.',
            'deleted'                => 'Horário excluído com sucesso.',
        ],

        'error' => [
            'create'               => 'Houve um problema ao criar o horário.',
            'update'               => 'Houve um problema ao atualizar o horário.',
            'toggle'               => 'Não foi possível alterar o status global do horário.',
            'attach'               => 'Não foi possível atribuir o horário ao tour.',
            'detach'               => 'Não foi possível desatribuir o horário do tour.',
            'assignment_toggle'    => 'Não foi possível alterar o status da atribuição.',
            'not_assigned_to_tour' => 'O horário não está atribuído a este tour.',
            'delete'               => 'Houve um problema ao excluir o horário.',
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
            'list_title'    => 'Itens do Roteiro',
            'add_item'      => 'Adicionar Item',
            'register_item' => 'Registrar Item',
            'edit_item'     => 'Editar Item',
            'save'          => 'Salvar',
            'update'        => 'Atualizar',
            'cancel'        => 'Cancelar',
            'state'         => 'Status',
            'actions'       => 'Ações',
            'see_more'      => 'Ver mais',
            'see_less'      => 'Ver menos',

            'toggle_on'  => 'Ativar item',
            'toggle_off' => 'Desativar item',

            'delete_forever'       => 'Excluir permanentemente',
            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => '<b>:label</b> será excluído e você não poderá desfazer.',
            'yes_delete'           => 'Sim, excluir',
            'item_this'            => 'este item',

            'processing' => 'Processando...',
            'applying'   => 'Aplicando...',
            'deleting'   => 'Excluindo...',
        ],

        'success' => [
            'created'     => 'Item do roteiro criado com sucesso.',
            'updated'     => 'Item atualizado com sucesso.',
            'activated'   => 'Item ativado com sucesso.',
            'deactivated' => 'Item desativado com sucesso.',
            'deleted'     => 'Item excluído permanentemente.',
        ],

        'error' => [
            'create' => 'Não foi possível criar o item.',
            'update' => 'Não foi possível atualizar o item.',
            'toggle' => 'Não foi possível alterar o status do item.',
            'delete' => 'Não foi possível excluir o item.',
        ],

        'validation' => [
            'title' => [
                'required' => 'O :attribute é obrigatório.',
                'string'   => 'O :attribute deve ser uma string.',
                'max'      => 'O :attribute não pode exceder :max caracteres.',
            ],
            'description' => [
                'required' => 'A :attribute é obrigatória.',
                'string'   => 'A :attribute deve ser uma string.',
                'max'      => 'A :attribute não pode exceder :max caracteres.',
            ],
        ],
    ],

    // =========================================================
    // [05] ITINERARY
    // =========================================================
    'itinerary' => [
        'fields' => [
            'name'                 => 'Nome do roteiro',
            'description'          => 'Descrição',
            'description_optional' => 'Descrição (opcional)',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],

        'ui' => [
            'page_title'    => 'Roteiros e Itens',
            'page_heading'  => 'Gestão de Roteiros e Itens',
            'new_itinerary' => 'Novo Roteiro',

            'assign'        => 'Atribuir',
            'edit'          => 'Editar',
            'save'          => 'Salvar',
            'cancel'        => 'Cancelar',
            'close'         => 'Fechar',
            'create_title'  => 'Criar novo roteiro',
            'create_button' => 'Criar',

            'toggle_on'  => 'Ativar roteiro',
            'toggle_off' => 'Desativar roteiro',
            'toggle_confirm_on_title'  => 'Ativar roteiro?',
            'toggle_confirm_off_title' => 'Desativar roteiro?',
            'toggle_confirm_on_html'   => 'O roteiro <b>:label</b> ficará <b>ativo</b>.',
            'toggle_confirm_off_html'  => 'O roteiro <b>:label</b> ficará <b>inativo</b>.',
            'yes_continue' => 'Sim, continuar',

            'assign_title'          => 'Atribuir itens a :name',
            'drag_hint'             => 'Arraste e solte os itens para definir a ordem.',
            'drag_handle'           => 'Arrastar para reordenar',
            'select_one_title'      => 'Selecione ao menos um item',
            'select_one_text'       => 'Selecione ao menos um item para continuar.',
            'assign_confirm_title'  => 'Atribuir itens selecionados?',
            'assign_confirm_button' => 'Sim, atribuir',
            'assigning'             => 'Atribuindo...',

            'no_items_assigned'       => 'Não há itens atribuídos a este roteiro.',
            'itinerary_this'          => 'este roteiro',
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
            'created'        => 'Roteiro criado com sucesso.',
            'updated'        => 'Roteiro atualizado com sucesso.',
            'activated'      => 'Roteiro ativado com sucesso.',
            'deactivated'    => 'Roteiro desativado com sucesso.',
            'deleted'        => 'Roteiro excluído permanentemente.',
            'items_assigned' => 'Itens atribuídos com sucesso.',
        ],

        'error' => [
            'create'  => 'Não foi possível criar o roteiro.',
            'update'  => 'Não foi possível atualizar o roteiro.',
            'toggle'  => 'Não foi possível alterar o status do roteiro.',
            'delete'  => 'Não foi possível excluir o roteiro.',
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
            'page_title'   => 'Idiomas dos Tours',
            'page_heading' => 'Gestão de Idiomas',
            'list_title'   => 'Lista de Idiomas',

            'table' => [
                'id'      => 'ID',
                'name'    => 'Idioma',
                'state'   => 'Status',
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
            'toggle' => 'Não foi possível alterar o status do idioma.',
            'delete' => 'Não foi possível excluir o idioma.',
            'save'   => 'Não foi possível salvar',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nome inválido',
                'required' => 'O nome do idioma é obrigatório.',
                'string'   => 'O :attribute deve ser uma string.',
                'max'      => 'O :attribute não pode exceder :max caracteres.',
                'unique'   => 'Já existe um idioma com esse nome.',
            ],
        ],
    ],

  // =========================================================
// [07] TOUR
// =========================================================
'tour' => [
    'title' => 'Tours',

    'fields' => [
        'id'            => 'ID',
        'name'          => 'Nome',
        'overview'      => 'Resumo',
        'amenities'     => 'Amenidades',
        'exclusions'    => 'Exclusões',
        'itinerary'     => 'Itinerário',
        'languages'     => 'Idiomas',
        'schedules'     => 'Horários',
        'adult_price'   => 'Preço Adulto',
        'kid_price'     => 'Preço Criança',
        'length_hours'  => 'Duração (horas)',
        'max_capacity'  => 'Capacidade máxima',
        'type'          => 'Tipo de Tour',
        'viator_code'   => 'Código Viator',
        'status'        => 'Status',
        'actions'       => 'Ações',
        'slug'          => 'url',
    ],

    'table' => [
        'id'            => 'ID',
        'name'          => 'Nome',
        'overview'      => 'Resumo',
        'amenities'     => 'Amenidades',
        'exclusions'    => 'Exclusões',
        'itinerary'     => 'Itinerário',
        'languages'     => 'Idiomas',
        'schedules'     => 'Horários',
        'adult_price'   => 'Preço Adulto',
        'kid_price'     => 'Preço Criança',
        'length_hours'  => 'Duração (h)',
        'max_capacity'  => 'Cap. Máx.',
        'type'          => 'Tipo',
        'viator_code'   => 'Código Viator',
        'status'        => 'Status',
        'actions'       => 'Ações',
    ],

    'status' => [
        'active'   => 'Ativo',
        'inactive' => 'Inativo',
    ],

    'success' => [
        'created'     => 'Tour criado com sucesso.',
        'updated'     => 'Tour atualizado com sucesso.',
        'deleted'     => 'Tour excluído.',
        'toggled'     => 'Status do tour atualizado.',
        'activated'   => 'Tour ativado com sucesso.',
        'deactivated' => 'Tour desativado com sucesso.',
    ],

    'error' => [
        'create'    => 'Ocorreu um problema ao criar o tour.',
        'update'    => 'Ocorreu um problema ao atualizar o tour.',
        'delete'    => 'Ocorreu um problema ao excluir o tour.',
        'toggle'    => 'Ocorreu um problema ao alterar o status do tour.',
        'not_found' => 'O tour não existe.',
    ],

    'ui' => [
        'page_title'       => 'Gerenciar Tours',
        'page_heading'     => 'Gerenciar Tours',
        'create_title'     => 'Registrar Tour',
        'edit_title'       => 'Editar Tour',
        'delete_title'     => 'Excluir Tour',
        'cancel'           => 'Cancelar',
        'save'             => 'Salvar',
        'update'           => 'Atualizar',
        'delete_confirm'   => 'Excluir este tour?',
        'toggle_on'        => 'Ativar',
        'toggle_off'       => 'Desativar',
        'toggle_on_title'  => 'Ativar tour?',
        'toggle_off_title' => 'Desativar tour?',
        'toggle_on_button' => 'Sim, ativar',
        'toggle_off_button'=> 'Sim, desativar',
        'see_more'         => 'Ver mais',
        'see_less'         => 'Ocultar',
        'load_more'        => 'Carregar mais',
        'loading'          => 'Carregando...',
        'load_more_error'  => 'Não foi possível carregar mais tours.',
        'confirm_title'    => 'Confirmação',
        'confirm_text'     => 'Deseja confirmar esta ação?',
        'yes_confirm'      => 'Sim, confirmar',
        'no_confirm'       => 'Não, cancelar',
        'add_tour'         => 'Adicionar Tour',
        'edit_tour'        => 'Editar Tour',
        'delete_tour'      => 'Excluir Tour',
        'toggle_tour'      => 'Ativar/Desativar Tour',
        'view_cart'        => 'Ver Carrinho',
        'add_to_cart'      => 'Adicionar ao Carrinho',

        'available_languages'    => 'Idiomas disponíveis',
        'default_capacity'       => 'Capacidade padrão',
        'create_new_schedules'   => 'Criar novos horários',
        'multiple_hint_ctrl_cmd' => 'Mantenha CTRL/CMD para selecionar vários',
        'use_existing_schedules' => 'Usar horários existentes',
        'add_schedule'           => 'Adicionar horário',
        'schedules_title'        => 'Horários do Tour',
        'amenities_included'     => 'Amenidades incluídas',
        'amenities_excluded'     => 'Amenidades não incluídas',
        'color'                  => 'Cor do Tour',
        'remove'                 => 'Remover',
        'choose_itinerary'       => 'Escolher itinerário',
        'select_type'            => 'Selecionar tipo',
        'empty_means_default'    => 'Padrão',

        'none' => [
            'amenities'       => 'Sem amenidades',
            'exclusions'      => 'Sem exclusões',
            'itinerary'       => 'Sem itinerário',
            'itinerary_items' => 'Sem itens',
            'languages'       => 'Sem idiomas',
            'schedules'       => 'Sem horários',
        ],
    ],
],

// =========================================================
// [08] IMAGES
// =========================================================
'image' => [

    'limit_reached_title' => 'Limite atingido',
    'limit_reached_text'  => 'O limite de imagens para este tour foi atingido.',
    'upload_success'      => 'Imagens enviadas com sucesso.',
    'upload_none'         => 'Nenhuma imagem foi enviada.',
    'upload_truncated'    => 'Alguns arquivos foram ignorados devido ao limite por tour.',
    'done'                => 'Concluído',
    'notice'              => 'Aviso',
    'saved'               => 'Salvo',
    'caption_updated'     => 'Legenda atualizada com sucesso.',
    'deleted'             => 'Excluído',
    'image_removed'       => 'Imagem excluída com sucesso.',
    'invalid_order'       => 'Ordem inválida.',
    'nothing_to_reorder'  => 'Nada para reordenar.',
    'order_saved'         => 'Ordem salva.',
    'cover_updated_title' => 'Capa atualizada',
    'cover_updated_text'  => 'Esta imagem agora é a capa.',
    'deleting'            => 'Excluindo...',

    'ui' => [
        'page_title_pick'     => 'Imagens de Tours — Escolher tour',
        'page_heading'        => 'Imagens de Tours',
        'choose_tour'         => 'Escolher tour',
        'search_placeholder'  => 'Buscar por ID ou nome…',
        'search_button'       => 'Buscar',
        'no_results'          => 'Nenhum tour encontrado.',
        'manage_images'       => 'Gerenciar imagens',
        'cover_alt'           => 'Capa',
        'images_label'        => 'imagens',
        'upload_btn'          => 'Enviar',
        'caption_placeholder' => 'Legenda (opcional)',
        'set_cover_btn'       => 'Definir como capa',
        'no_images'           => 'Ainda não há imagens para este tour.',
        'delete_btn'          => 'Excluir',
        'show_btn'            => 'Exibir',
        'close_btn'           => 'Fechar',
        'preview_title'      => 'Pré-visualização da imagem',

        'error_title'         => 'Erro',
        'warning_title'       => 'Atenção',
        'success_title'       => 'Sucesso',
        'cancel_btn'          => 'Cancelar',
        'confirm_delete_title'=> 'Excluir esta imagem?',
        'confirm_delete_text' => 'Esta ação não pode ser desfeita.',
    ],

    'errors' => [
        'validation'     => 'Os dados enviados não são válidos.',
        'upload_generic' => 'Algumas imagens não puderam ser enviadas.',
        'update_caption' => 'A legenda não pôde ser atualizada.',
        'delete'         => 'A imagem não pôde ser excluída.',
        'reorder'        => 'A ordem não pôde ser salva.',
        'set_cover'      => 'A capa não pôde ser definida.',
        'load_list'      => 'A lista não pôde ser carregada.',
        'too_large'      => 'O arquivo excede o tamanho máximo permitido. Tente com uma imagem menor.',
    ],
],

];
