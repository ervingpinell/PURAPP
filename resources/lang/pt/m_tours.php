<?php

/*************************************************************
 *  MÓDULO DE TRADUÇÕES: TOURS
 *  Arquivo: resources/lang/pt_BR/m_tours.php
 *
 *  Índice (seção e linha de início)
 *  [01] COMMON           -> linha 23
 *  [02] AMENITY          -> linha 31
 *  [03] SCHEDULE         -> linha 106
 *  [04] ITINERARY_ITEM   -> linha 218
 *  [05] ITINERARY        -> linha 288
 *  [06] LANGUAGE         -> linha 364
 *  [07] TOUR             -> linha 453
 *  [08] IMAGES           -> linha 579
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'optional' => 'Opcional',
        'success_title' => 'Sucesso',
        'error_title'   => 'Erro',
        'people' => 'pessoas',
        'hours' => 'horas',
        'success' => 'Sucesso',
        'error' => 'Erro',
        'cancel' => 'Cancelar',
        'confirm_delete' => 'Sim, excluir',
        'unspecified' => 'Não especificado',
        'no_description' => 'Sem descrição',
        'required_fields_title' => 'Campos obrigatórios',
        'required_fields_text' => 'Por favor, preencha os campos obrigatórios: Nome e Capacidade Máxima',
        'active' => 'Ativo',
        'inactive' => 'Inativo',
        'notice' => 'Aviso',
        'na'    => 'Não configurado',
        'create' => 'Criar',
        'previous' => 'Voltar',
        'info'               => 'Informação',
        'close'              => 'Fechar',
        'save'               => 'Salvar',
        'required'           => 'Este campo é obrigatório.',
        'add'                => 'Adicionar',
        'translating'        => 'Traduzindo...',
        'error_translating'  => 'Não foi possível traduzir o texto.',
        'confirm' => 'Confirmar',
        'yes' => 'Sim',
        'form_errors_title' => 'Por favor, corrija os seguintes erros:',
        'delete' => 'Excluir',
        'delete_all' => 'Excluir tudo',
        'actions' => 'Ações',
        'updated_at' => 'Última atualização',
        'not_set' => 'Não especificado',
        'error_deleting' => 'Ocorreu um erro ao excluir. Tente novamente.',
        'error_saving' => 'Ocorreu um erro ao salvar. Tente novamente.',
        'crud_go_to_index' => 'Gerenciar :element',
        'validation_title' => 'Há erros de validação',
        'ok'               => 'OK',
        'confirm_delete_title' => 'Excluir este item?',
        'confirm_delete_text' => 'Esta ação não pode ser desfeita',
        'saving' => 'Salvando...',
        'network_error' => 'Erro de rede',
        'custom' => 'Personalizado',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'singular' => 'comodidade',
        'plural'   => 'comodidades',

        'fields' => [
            'name' => 'Nome',
            'icon' => 'Ícone (FontAwesome)',
        ],

        'status' => [
            'active'   => 'Ativa',
            'inactive' => 'Inativa',
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
            'delete_confirm_html'  => 'A comodidade <b>:label</b> será excluída e não poderá ser desfeita.',

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
            'included_required' => 'Você deve selecionar ao menos uma comodidade incluída.',
            'name' => [
                'title'    => 'Nome inválido',
                'required' => 'O campo :attribute é obrigatório.',
                'string'   => 'O campo :attribute deve ser um texto.',
                'max'      => 'O campo :attribute não pode exceder :max caracteres.',
            ],
        ],

        'hints' => [
            'fontawesome' => 'Use classes do FontAwesome, por exemplo: "fas fa-check".',
        ],

        'quick_create' => [
            'button'           => 'Nova comodidade',
            'title'            => 'Criar comodidade rápida',
            'name_label'       => 'Nome da comodidade',
            'icon_label'       => 'Ícone (opcional)',
            'icon_placeholder' => 'Ex: fas fa-utensils',
            'icon_help'        => 'Use uma classe de ícone do Font Awesome ou deixe em branco.',
            'save'             => 'Salvar comodidade',
            'cancel'           => 'Cancelar',
            'saving'           => 'Salvando...',
            'error_generic'    => 'Não foi possível criar a comodidade. Tente novamente.',
            'go_to_index'         => 'Ver todas',
            'go_to_index_title'   => 'Ir para a lista completa de comodidades',
            'success_title'       => 'Comodidade criada',
            'success_text'        => 'A comodidade foi adicionada à lista do tour.',
            'error_title'         => 'Erro ao criar a comodidade',
            'error_duplicate'     => 'Já existe uma comodidade com esse nome.',
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'plural'   => 'Horários',
        'singular' => 'Horário',

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

            'assign_existing'    => 'Vincular existente',
            'assign_to_tour'     => 'Vincular horário a ":tour"',
            'select_schedule'    => 'Selecione um horário',
            'choose'             => 'Escolher',
            'assign'             => 'Vincular',
            'new_for_tour_title' => 'Novo horário para ":tour"',

            'time_range'        => 'Horário',
            'state'             => 'Status',
            'actions'           => 'Ações',
            'schedule_state'    => 'Horário',
            'assignment_state'  => 'Vinculação',
            'no_general'        => 'Não há horários gerais.',
            'no_tour_schedules' => 'Este tour ainda não possui horários.',
            'no_label'          => 'Sem rótulo',
            'assigned_count'    => 'horário(s) vinculado(s)',

            'toggle_global_title'     => 'Ativar/Desativar (global)',
            'toggle_global_on_title'  => 'Ativar horário (global)?',
            'toggle_global_off_title' => 'Desativar horário (global)?',
            'toggle_global_on_html'   => 'O horário <b>:label</b> será ativado para todos os tours.',
            'toggle_global_off_html'  => 'O horário <b>:label</b> será desativado para todos os tours.',

            'toggle_on_tour'          => 'Ativar neste tour',
            'toggle_off_tour'         => 'Desativar neste tour',
            'toggle_assign_on_title'  => 'Ativar neste tour?',
            'toggle_assign_off_title' => 'Desativar neste tour?',
            'toggle_assign_on_html'   => 'A vinculação ficará <b>ativa</b> para <b>:tour</b>.',
            'toggle_assign_off_html'  => 'A vinculação ficará <b>inativa</b> para <b>:tour</b>.',

            'detach_from_tour'     => 'Remover do tour',
            'detach_confirm_title' => 'Remover do tour?',
            'detach_confirm_html'  => 'O horário será <b>desvinculado</b> de <b>:tour</b>.',

            'delete_forever'       => 'Excluir (global)',
            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => 'O horário <b>:label</b> (global) será excluído e não poderá ser desfeito.',

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

            'missing_fields_title' => 'Dados faltando',
            'missing_fields_text'  => 'Verifique os campos obrigatórios (início, fim e capacidade).',
            'could_not_save'       => 'Não foi possível salvar',
            'base_capacity_tour'             => 'Capacidade base do tour:',
            'capacity_not_defined'           => 'Não definida',
            'capacity_optional'              => 'Capacidade (opcional)',
            'capacity_placeholder_with_value' => 'Ex: :capacity',
            'capacity_placeholder_generic'   => 'Usar capacidade do tour',
            'capacity_hint_with_value'       => 'Deixar em branco → :capacity',
            'capacity_hint_generic'          => 'Deixar em branco → capacidade do tour',
            'tip_label'                      => 'Dica:',
            'capacity_tip'                   => 'Você pode deixar a capacidade em branco para o sistema usar a capacidade geral do tour (:capacity).',

            'new_schedule_for_tour'            => 'Novo horário',
            'modal_new_for_tour_title'         => 'Criar horário para :tour',
            'modal_save'                       => 'Salvar horário',
            'modal_cancel'                     => 'Cancelar',
            'capacity_modal_info_with_value'   => 'A capacidade base do tour é :capacity. Se você deixar o campo de capacidade em branco, esse valor será utilizado.',
            'capacity_modal_info_generic'      => 'Se você deixar o campo de capacidade em branco, será usada a capacidade geral do tour quando estiver definida.',
            'trash_title'                      => 'Lixeira de Horários',
            'trash_list_title'                 => 'Horários Eliminados',
            'restore'                          => 'Restaurar',
            'empty_trash'                      => 'Lixeira vazia',
            'deleted_at'                       => 'Data de Eliminação',
            'deleted_by'                       => 'Eliminado Por',
            'original_start'                   => 'Início Original',
            'original_end'                     => 'Fim Original',
            'back_to_list'                     => 'Voltar para a lista',
            'success' => [
                'restored'      => 'Horário restaurado com sucesso.',
                'force_deleted' => 'Horário eliminado permanentemente.',
            ],
            'error' => [
                'restore'      => 'Não foi possível restaurar o horário.',
                'force_delete' => 'Não foi possível eliminar permanentemente o horário.',
            ],
        ],

        'success' => [
            'created'                => 'Horário criado com sucesso.',
            'updated'                => 'Horário atualizado com sucesso.',
            'activated_global'       => 'Horário ativado com sucesso (global).',
            'deactivated_global'     => 'Horário desativado com sucesso (global).',
            'attached'               => 'Horário vinculado ao tour.',
            'detached'               => 'Horário removido do tour com sucesso.',
            'assignment_activated'   => 'Vinculação ativada para este tour.',
            'assignment_deactivated' => 'Vinculação desativada para este tour.',
            'deleted'                => 'Horário excluído com sucesso.',
            'created_and_attached'   => 'O horário foi criado e vinculado ao tour com sucesso.',
        ],

        'error' => [
            'create'               => 'Houve um problema ao criar o horário.',
            'update'               => 'Houve um problema ao atualizar o horário.',
            'toggle'               => 'Não foi possível alterar o status global do horário.',
            'attach'               => 'Não foi possível vincular o horário ao tour.',
            'detach'               => 'Não foi possível desvincular o horário do tour.',
            'assignment_toggle'    => 'Não foi possível alterar o status da vinculação.',
            'not_assigned_to_tour' => 'O horário não está vinculado a este tour.',
            'delete'               => 'Houve um problema ao excluir o horário.',
        ],

        'placeholders' => [
            'morning' => 'Ex: Manhã',
        ],

        'validation' => [
            'no_schedule_selected' => 'Você deve selecionar pelo menos um horário.',
            'title' => 'Validação de Horários',
            'end_after_start' => 'O horário de término deve ser posterior ao horário de início.',
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
            'state'         => 'Status',
            'actions'       => 'Ações',
            'see_more'      => 'Ver mais',
            'see_less'      => 'Ver menos',
            'assigned_items'       => 'Itens atribuídos ao itinerário',
            'drag_to_order'        => 'Arraste os itens para definir a ordem.',
            'pool_hint'            => 'Marque os itens disponíveis que você deseja incluir neste itinerário.',
            'register_item_hint'   => 'Registre novos itens se precisar de etapas adicionais que ainda não existem.',
            'translations_updated' => 'Tradução atualizada',

            'toggle_on'  => 'Ativar item',
            'toggle_off' => 'Desativar item',

            'delete_forever'       => 'Excluir permanentemente',
            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => 'O item <b>:label</b> será excluído e não poderá ser desfeito.',
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
            'toggle' => 'Não foi possível alterar o status do item.',
            'delete' => 'Não foi possível excluir o item.',
        ],

        'validation' => [
            'title' => [
                'required' => 'O campo :attribute é obrigatório.',
                'string'   => 'O campo :attribute deve ser um texto.',
                'max'      => 'O campo :attribute não pode exceder :max caracteres.',
            ],
            'description' => [
                'required' => 'O campo :attribute é obrigatório.',
                'string'   => 'O campo :attribute deve ser um texto.',
                'max'      => 'O campo :attribute não pode exceder :max caracteres.',
            ],
        ],
    ],

    // =========================================================
    // [05] ITINERARY
    // =========================================================
    'itinerary' => [
        'plural'   => 'Itinerários',
        'singular' => 'Itinerário',

        'fields' => [
            'name'                 => 'Nome do itinerário',
            'description'          => 'Descrição',
            'description_optional' => 'Descrição (opcional)',
            'items'                => 'Itens',
            'item_title'           => 'Título do item',
            'item_description'     => 'Descrição do item',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],

        'ui' => [
            'page_title'    => 'Itinerários e Itens',
            'page_heading'  => 'Itinerários e Gestão de Itens',
            'new_itinerary' => 'Novo Itinerário',
            'select_or_create_hint' => 'Selecione um itinerário existente ou crie um novo para este tour.',
            'save_changes'          => 'Salve o itinerário para aplicar as alterações ao tour.',
            'select_existing'       => 'Selecionar itinerário existente',
            'create_new'            => 'Criar novo itinerário',
            'add_item'              => 'Adicionar item',
            'min_one_item'          => 'Deve haver pelo menos um item no itinerário.',
            'cannot_delete_item'    => 'Não é possível excluir',
            'item_added'            => 'Item adicionado',
            'item_added_success'    => 'O item foi adicionado ao itinerário com sucesso.',
            'error_creating_item'   => 'Erro de validação ao criar o item.',
            'translations_updated' => 'Tradução atualizada',

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
            'select_one_title'      => 'Você deve selecionar pelo menos um item',
            'select_one_text'       => 'Por favor, selecione pelo menos um item para continuar.',
            'assign_confirm_title'  => 'Atribuir itens selecionados?',
            'assign_confirm_button' => 'Sim, atribuir',
            'assigning'             => 'Atribuindo...',

            'no_items_assigned'       => 'Não há itens atribuídos a este itinerário.',
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
            'go_to_crud'              => 'Ir para o Módulo',
        ],

        'modal' => [
            'create_itinerary' => 'Criar itinerário',
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
            'toggle'  => 'Não foi possível alterar o status do itinerário.',
            'delete'  => 'Não foi possível excluir o itinerário.',
            'assign'  => 'Não foi possível atribuir os itens.',
        ],

        'validation' => [
            'name_required'  => 'Você deve informar um nome para o itinerário.',
            'must_add_items' => 'Você deve adicionar pelo menos um item ao novo itinerário.',
            'title' => 'Validação de Itinerário',
            'name' => [
                'required' => 'O nome do itinerário é obrigatório.',
                'string'   => 'O nome deve ser um texto.',
                'max'      => 'O nome não pode exceder 255 caracteres.',
                'unique'   => 'Já existe um itinerário com esse nome.',
            ],
            'description' => [
                'string' => 'A descrição deve ser um texto.',
                'max'    => 'A descrição não pode exceder 1000 caracteres.',
            ],
            'items' => [
                'item'          => 'Item',
                'required'      => 'Você deve selecionar pelo menos um item.',
                'array'         => 'O formato dos itens não é válido.',
                'min'           => 'Você deve selecionar pelo menos um item.',
                'order_integer' => 'A ordem deve ser um número inteiro.',
                'order_min'     => 'A ordem não pode ser negativa.',
                'order_max'     => 'A ordem não pode exceder 9999.',
            ],
        ],

        'item'  => 'Item',
        'items' => 'Itens',
    ],

    // =========================================================
    // [06] LANGUAGE
    // =========================================================
    'language' => [
        'fields' => [
            'name' => 'Idioma',
            'code' => 'Código',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],

        'ui' => [
            'page_title'   => 'Idiomas de Tours',
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
            'trash_title'      => 'Lixeira de Idiomas',
            'trash_list_title' => 'Idiomas Excluídos',
            'restore'          => 'Restaurar',
            'empty_trash'      => 'Lixeira vazia',
            'deleted_at'       => 'Data de exclusão',
            'deleted_by'       => 'Excluído por',
            'back_to_list'     => 'Voltar para a lista',
            'delete'           => 'Excluir',
            'alerts' => [
                'delete_title' => 'Excluir este idioma?',
                'delete_text'  => 'O idioma será movido para a lixeira.',
            ],
        ],

        'success' => [
            'created'     => 'Idioma criado com sucesso.',
            'updated'     => 'Idioma atualizado com sucesso.',
            'activated'   => 'Idioma ativado com sucesso.',
            'deactivated' => 'Idioma desativado com sucesso.',
            'deleted'     => 'Idioma excluído com sucesso.',
            'restored'      => 'Idioma restaurado com sucesso.',
            'force_deleted' => 'Idioma excluído permanentemente.',
        ],

        'error' => [
            'create' => 'Não foi possível criar o idioma.',
            'update' => 'Não foi possível atualizar o idioma.',
            'toggle' => 'Não foi possível alterar o status do idioma.',
            'delete' => 'Não foi possível excluir o idioma.',
            'save'   => 'Não foi possível salvar',
            'restore'      => 'Não foi possível restaurar o idioma.',
            'force_delete' => 'Não foi possível excluir permanentemente o idioma.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nome inválido',
                'required' => 'O nome do idioma é obrigatório.',
                'string'   => 'O campo :attribute deve ser um texto.',
                'max'      => 'O campo :attribute não pode exceder :max caracteres.',
                'unique'   => 'Já existe um idioma com esse nome.',
            ],
        ],

        'hints' => [
            'iso_639_1' => 'Código ISO 639-1, por exemplo: es, en, fr.',
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [

        'validation' => [
            'required' => 'Este campo é obrigatório.',
            'min'      => 'Este campo deve ter pelo menos :min caracteres.',
            'max'      => 'Este campo não pode exceder :max caracteres.',
            'number'   => 'Este campo deve ser um número válido.',
            'slug'     => 'O slug só pode conter letras minúsculas, números e hifens.',
            'color'    => 'Selecione uma cor válida.',
            'select'   => 'Selecione uma opção.',

            'length_in_hours'   => 'Duração em horas (ex: 2, 2.5, 4)',
            'max_capacity_help' => 'Número máximo de pessoas por tour',

            'form_error_title'   => 'Atenção!',
            'form_error_message' => 'Por favor, corrija os erros do formulário antes de continuar.',
            'saving'             => 'Salvando...',

            'success'           => 'Sucesso!',
            'tour_type_created' => 'Tipo de tour criado com sucesso.',
            'language_created'  => 'Idioma criado com sucesso.',

            'tour_type_error' => 'Erro ao criar o tipo de tour.',
            'language_error'  => 'Erro ao criar o idioma.',
            'languages_hint' => 'Selecione os idiomas disponíveis para este tour.',
        ],

        'wizard' => [
            'create_new_tour' => 'Criar Novo Tour',
            'edit_tour'       => 'Editar Tour',
            'step_number'     => 'Passo :number',
            'edit_step'       => 'Editar',
            'leave_warning'   => 'Você tem alterações não salvas neste tour. Se sair agora, o rascunho permanecerá no banco de dados. Tem certeza de que deseja sair?',
            'cancel_title'    => 'Cancelar a configuração do tour?',
            'cancel_text'     => 'Se você sair deste assistente, poderá perder alterações não salvas nesta etapa.',
            'cancel_confirm'  => 'Sim, descartar alterações',
            'cancel_cancel'   => 'Não, continuar editando',
            'details_validation_text' => 'Verifique os campos obrigatórios do formulário de detalhes antes de continuar.',
            'most_recent'     => 'Mais recente',
            'last_modified'   => 'Última modificação',
            'start_fresh'     => 'Começar novamente',
            'draft_details'   => 'Detalhes do rascunho',
            'drafts_found'    => 'Foi encontrado um rascunho',
            'basic_info'      => 'Detalhes',

            'steps' => [
                'details'   => 'Detalhes Básicos',
                'itinerary' => 'Itinerário',
                'schedules' => 'Horários',
                'amenities' => 'Comodidades',
                'prices'    => 'Preços',
                'summary'   => 'Resumo',
            ],

            'save_and_continue' => 'Salvar e Continuar',
            'publish_tour'      => 'Publicar Tour',
            'delete_draft'      => 'Excluir Rascunho',
            'ready_to_publish'  => 'Pronto para Publicar?',

            'details_saved'          => 'Detalhes salvos com sucesso.',
            'itinerary_saved'        => 'Itinerário salvo com sucesso.',
            'schedules_saved'        => 'Horários salvos com sucesso.',
            'amenities_saved'        => 'Comodidades salvas com sucesso.',
            'prices_saved'           => 'Preços salvos com sucesso.',
            'published_successfully' => 'Tour publicado com sucesso!',
            'draft_cancelled'        => 'Rascunho excluído.',

            'draft_mode'        => 'Modo Rascunho',
            'draft_explanation' => 'Este tour será salvo como rascunho até que você conclua todos os passos e o publique.',
            'already_published' => 'Este tour já foi publicado. Use o editor normal para modificá-lo.',
            'cannot_cancel_published' => 'Você não pode cancelar um tour já publicado.',

            'confirm_cancel' => 'Tem certeza de que deseja cancelar e excluir este rascunho?',

            'publish_explanation' => 'Revise todas as informações antes de publicar. Uma vez publicado, o tour ficará disponível para reservas.',
            'can_edit_later'      => 'Você poderá editar o tour após a publicação no painel administrativo.',
            'incomplete_warning'  => 'Alguns passos estão incompletos. Você pode publicar mesmo assim, mas é recomendável completar todas as informações.',

            'checklist'           => 'Lista de Verificação',
            'checklist_details'   => 'Detalhes básicos completos',
            'checklist_itinerary' => 'Itinerário configurado',
            'checklist_schedules' => 'Horários adicionados',
            'checklist_amenities' => 'Comodidades configuradas',
            'checklist_prices'    => 'Preços definidos',

            'hints' => [
                'status' => 'O status pode ser alterado após a publicação',
            ],

            'existing_drafts_title'   => 'Você tem tours em rascunho não finalizados!',
            'existing_drafts_message' => 'Encontramos :count tour(es) em rascunho que você ainda não concluiu.',
            'current_step'            => 'Passo atual',
            'step'                    => 'Passo',

            'continue_draft'      => 'Continuar com este rascunho',
            'delete_all_drafts'   => 'Excluir Todos os Rascunhos',
            'create_new_anyway'   => 'Criar Novo Tour Mesmo Assim',

            'drafts_info' => 'Você pode continuar editando um rascunho existente, excluí-lo individualmente, excluir todos os rascunhos ou criar um novo tour ignorando os rascunhos atuais.',

            'confirm_delete_title'        => 'Excluir este rascunho?',
            'confirm_delete_message'      => 'Esta ação não pode ser desfeita. O rascunho será excluído permanentemente:',
            'confirm_delete_all_title'    => 'Excluir todos os rascunhos?',
            'confirm_delete_all_message'  => 'Serão excluídos permanentemente :count rascunho(s). Esta ação não pode ser desfeita.',

            'draft_deleted'       => 'Rascunho excluído com sucesso.',
            'all_drafts_deleted'  => ':count rascunho(s) excluído(s) com sucesso.',
            'continuing_draft'    => 'Continuando com o seu rascunho...',

            'not_a_draft' => 'Este tour já não é um rascunho e não pode ser editado através do assistente.',
        ],

        'title' => 'Tours',

        'fields' => [
            'id'            => 'ID',
            'name'          => 'Nome',
            'details'       => 'Detalhes',
            'price'         => 'Preços',
            'overview'      => 'Resumo',
            'recommendations' => 'Recomendações',
            'amenities'     => 'Comodidades',
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
            'group_size'    => 'Tamanho do grupo',
        ],

        'pricing' => [
            'already_added' => 'Esta categoria já foi adicionada.',
            'configured_categories' => 'Categorias configuradas',
            'create_category' => 'Criar categoria',
            'note_title'              => 'Nota:',
            'note_text'               => 'Defina aqui os preços base para cada categoria de cliente.',
            'manage_detailed_hint'    => ' Para uma gestão detalhada, use o botão "Gerenciar Preços Detalhados" acima.',
            'price_usd'               => 'Preço (USD)',
            'min_quantity'            => 'Quantidade mínima',
            'max_quantity'            => 'Quantidade máxima',
            'status'                  => 'Status',
            'active'                  => 'Ativa',
            'no_categories'           => 'Não há categorias de clientes configuradas.',
            'create_categories_first' => 'Crie categorias primeiro',
            'page_title'         => 'Preços - :name',
            'header_title'       => 'Preços: :name',
            'back_to_tours'      => 'Voltar para tours',

            'configured_title'   => 'Categorias e preços configurados',
            'empty_title'        => 'Não há categorias configuradas para este tour.',
            'empty_hint'         => 'Use o formulário à direita para adicionar categorias.',

            'save_changes'       => 'Salvar alterações',
            'auto_disable_note'  => 'Preços em $0 são desativados automaticamente',
            'not_available_for_date' => 'Não disponível para esta data',

            // Calendar price indicators
            'price_lower' => 'Preço mais baixo',
            'price_higher' => 'Preço mais alto',
            'price_normal' => 'Preço normal',
            'price_legend' => 'Legenda de preços',

            'add_category'       => 'Adicionar categoria',
            'period_name'        => 'Nome do Período',
            'period_name_placeholder' => 'Ex. Alta Temporada',

            'all_assigned_title' => 'Todas as categorias estão atribuídas',
            'all_assigned_text'  => 'Não há mais categorias disponíveis para este tour.',

            'info_title'         => 'Informação',
            'tour_label'         => 'Tour',
            'configured_count'   => 'Categorias configuradas',
            'active_count'       => 'Categorias ativas',

            'fields_title'       => 'Campos',
            'rules_title'        => 'Regras',

            'field_price'        => 'Preço',
            'field_min'          => 'Mínimo',
            'field_max'          => 'Máximo',
            'field_status'       => 'Status',

            'rule_min_le_max'    => 'O mínimo deve ser menor ou igual ao máximo',
            'rule_zero_disable'  => 'Preços em $0 são desativados automaticamente',
            'rule_only_active'   => 'Apenas as categorias ativas aparecem no site público',

            'status_active'      => 'Ativa',
            'add_existing_category'      => 'Adicionar categoria existente',
            'choose_category_placeholder' => 'Selecione uma categoria…',
            'add_button'                 => 'Adicionar',
            'add_existing_hint'          => 'Adicione apenas as categorias de clientes necessárias para este tour.',
            'remove_category'            => 'Remover categoria',
            'category_already_added'     => 'Esta categoria já está adicionada ao tour.',
            'no_prices_preview'          => 'Ainda não há preços configurados.',
            'already_added'              => 'Esta categoria já está adicionada ao tour.',

            // Seasonal pricing
            'valid_from'                  => 'Válido a partir de',
            'valid_until'                 => 'Válido até',
            'default_price'               => 'Preço padrão',
            'seasonal_price'              => 'Preço sazonal',
            'season_label'                => 'Temporada',
            'all_year'                    => 'Todo o ano',
            'date_overlap_warning'        => 'As datas se sobrepõem a outro preço desta categoria',
            'invalid_date_range'          => 'A data de início deve ser anterior à data de término',
            'wizard_description'          => 'Definir preços por temporada e categoria de cliente',
            'add_period'                  => 'Adicionar Período de Preço',
            'confirm_remove_period'       => 'Remover este período de preço?',
            'category_already_in_period'  => 'Esta categoria já está adicionada a este período',
            'category'                    => 'Categoria',
            'age_range'                   => 'Idade',
            'taxes'                       => 'Impostos',
            'category_removed_success'    => 'Categoria removida com sucesso',
            'leave_empty_no_limit'        => 'Deixar vazio para sem limite',
            'category_added_success'      => 'Categoria adicionada com sucesso',
            'period_removed_success'      => 'Período removido com sucesso',
            'period_added_success'        => 'Período adicionado com sucesso',
            'overlap_not_allowed_title'   => 'Intervalo de datas não permitido',
            'overlap_not_allowed_text'    => 'As datas selecionadas coincidem com outro período de preços. Ajuste o intervalo para evitar conflitos.',
            'overlap_conflict_with'       => 'Conflito com os seguintes períodos:',
            'duplicate_category_title'    => 'Categoria duplicada',
            'invalid_date_range_title'    => 'Intervalo de datas inválido',
            'remove_category_confirm_text' => 'Esta categoria será removida do período',
            'validation_failed'           => 'Falha na validação',
            'are_you_sure'                => 'Tem certeza?',
            'yes_delete'                  => 'Sim, excluir',
            'cancel'                      => 'Cancelar',
            'attention'                   => 'Atenção',
        ],

        'modal' => [
            'create_category' => 'Criar categoria',

            'fields' => [
                'name'          => 'Nome',
                'age_from'      => 'Idade a partir de',
                'age_to'        => 'Idade até',
                'age_range'     => 'Faixa etária',
                'min'           => 'Mínimo',
                'max'           => 'Máximo',
                'order'         => 'Ordem',
                'is_active'     => 'Ativo',
                'auto_translate' => 'Traduzir automaticamente',
            ],

            'placeholders' => [
                'name'              => 'Ex: Adulto, Criança, Bebê',
                'age_to_optional'   => 'Deixe em branco para "+"',
            ],

            'hints' => [
                'age_to_empty_means_plus' => 'Se você deixar a idade máxima em branco, será interpretado como "+" (por exemplo, 12+).',
                'min_le_max'              => 'O mínimo deve ser menor ou igual ao máximo.',
            ],

            'errors' => [
                'min_le_max' => 'O mínimo deve ser menor ou igual ao máximo.',
            ],
        ],

        'schedules_form' => [
            'available_title'        => 'Horários Disponíveis',
            'select_hint'            => 'Selecione os horários para este tour',
            'no_schedules'           => 'Não há horários disponíveis.',
            'create_schedules_link'  => 'Criar horários',

            'create_new_title'       => 'Criar Novo Horário',
            'label_placeholder'      => 'Ex: Manhã, Tarde',
            'create_and_assign'      => 'Criar este horário e vinculá-lo ao tour',

            'info_title'             => 'Informação',
            'schedules_title'        => 'Horários',
            'schedules_text'         => 'Selecione um ou mais horários em que este tour estará disponível.',
            'create_block_title'     => 'Criar Novo',
            'create_block_text'      => 'Se você precisar de um horário que não existe, pode criá-lo aqui marcando "Criar este horário e vinculá-lo ao tour".',

            'current_title'          => 'Horários Atuais',
            'none_assigned'          => 'Sem horários atribuídos',
        ],

        'summary' => [
            'preview_title'        => 'Pré-visualização do Tour',
            'preview_text_create'  => 'Revise todas as informações antes de criar o tour.',
            'preview_text_update'  => 'Revise todas as informações antes de atualizar o tour.',

            'basic_details_title'  => 'Detalhes Básicos',
            'description_title'    => 'Descrição',
            'prices_title'         => 'Preços por Categoria',
            'schedules_title'      => 'Horários',
            'languages_title'      => 'Idiomas',
            'itinerary_title'      => 'Itinerário',
            'amenities_title' => 'Comodidades',

            'table' => [
                'category' => 'Categoria',
                'price'    => 'Preço',
                'min_max'  => 'Mín-Máx',
                'status'   => 'Status',
            ],

            'not_specified'        => 'Não especificado',
            'slug_autogenerated'   => 'Será gerado automaticamente',
            'deactivate' => 'Desativar',
            'manage_prices' => 'Gerenciar preços',
            'manage_images' => 'Gerenciar imagens',
            'manage_delete' => 'Excluir',
            'no_description'       => 'Sem descrição',
            'no_active_prices'     => 'Nenhum preço ativo configurado',
            'no_languages'         => 'Nenhum idioma atribuído',
            'none_included'        => 'Nada incluído especificado',
            'none_excluded'        => 'Nada excluído especificado',
            'date_range'           => 'Intervalo de Datas',

            'units' => [
                'hours'  => 'horas',
                'people' => 'pessoas',
            ],

            'create_note' => 'Os horários, preços, idiomas e comodidades serão exibidos aqui após salvar o tour.',
        ],

        'alerts' => [
            'delete_title' => 'Excluir tour?',
            'delete_text'  => 'O tour será movido para Excluídos. Você poderá restaurá-lo depois.',
            'purge_title'  => 'Excluir permanentemente?',
            'purge_text'   => 'Esta ação é irreversível.',
            'purge_text_with_bookings' => 'Este tour possui :count reserva(s). Elas não serão excluídas; permanecerão sem tour associado.',
            'toggle_question_active'   => 'Desativar tour?',
            'toggle_question_inactive' => 'Ativar tour?',
        ],

        'flash' => [
            'created'       => 'Tour criado com sucesso.',
            'updated'       => 'Tour atualizado com sucesso.',
            'deleted_soft'  => 'Tour movido para Excluídos.',
            'restored'      => 'Tour restaurado com sucesso.',
            'purged'        => 'Tour excluído permanentemente.',
            'toggled_on'    => 'Tour ativado.',
            'toggled_off'   => 'Tour desativado.',
        ],

        'table' => [
            'id'            => 'ID',
            'name'          => 'Nome',
            'overview'      => 'Resumo',
            'amenities'     => 'Comodidades',
            'exclusions'    => 'Exclusões',
            'itinerary'     => 'Itinerário',
            'languages'     => 'Idiomas',
            'schedules'     => 'Horários',
            'adult_price'   => 'Preço Adulto',
            'kid_price'     => 'Preço Criança',
            'length_hours'  => 'Duração (h)',
            'max_capacity'  => 'Capacidade Máx.',
            'type'          => 'Tipo',
            'viator_code'   => 'Código Viator',
            'status'        => 'Status',
            'actions'       => 'Ações',
            'slug'          => 'URL',
            'prices'        => 'Preços',
            'capacity'      => 'Capacidade',
            'group_size'        => 'Máx. Grupo',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
            'archived' => 'Arquivado',
        ],

        'placeholders' => [
            'group_size' => 'Ex: 10',
        ],

        'hints' => [
            'group_size' => 'Tamanho do grupo por guia ou geral para este tour. (Este dado aparece nas informações do produto.)',
        ],

        'success' => [
            'created'     => 'O tour foi criado com sucesso.',
            'updated'     => 'O tour foi atualizado com sucesso.',
            'deleted'     => 'O tour foi excluído.',
            'toggled'     => 'O status do tour foi atualizado.',
            'activated'   => 'Tour ativado com sucesso.',
            'deactivated' => 'Tour desativado com sucesso.',
            'archived'    => 'Tour arquivado com sucesso.',
            'restored'    => 'Tour restaurado com sucesso.',
            'purged'      => 'Tour excluído permanentemente.',
        ],

        'error' => [
            'create'    => 'Não foi possível criar o tour.',
            'update'    => 'Não foi possível atualizar o tour.',
            'delete'    => 'Não foi possível excluir o tour.',
            'toggle'    => 'Não foi possível alterar o status do tour.',
            'not_found' => 'O tour não existe.',
            'restore'            => 'Não foi possível restaurar o tour.',
            'purge'              => 'Não foi possível excluir permanentemente o tour.',
            'purge_has_bookings' => 'Não é possível excluir permanentemente: o tour possui reservas.',
        ],

        'ui' => [
            'add_tour_type' => 'Adicionar tipo de tour',
            'back' => 'Voltar',
            'page_title'       => 'Gestão de Tours',
            'page_heading'     => 'Gestão de Tours',
            'create_title'     => 'Registrar Tour',
            'edit_title'       => 'Editar Tour',
            'delete_title'     => 'Excluir Tour',
            'cancel'           => 'Cancelar',
            'save'             => 'Salvar',
            'save_changes'     => 'Salvar alterações',
            'update'           => 'Atualizar',
            'delete_confirm'   => 'Excluir este tour?',
            'toggle_on'        => 'Ativar',
            'toggle_off'       => 'Desativar',
            'toggle_on_title'  => 'Ativar tour?',
            'toggle_off_title' => 'Desativar tour?',
            'toggle_on_button' => 'Sim, ativar',
            'toggle_off_button' => 'Sim, desativar',
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
            'slug_help'        => 'Identificador do tour na URL (sem espaços nem acentos)',
            'generate_auto'       => 'Gerar automaticamente',
            'slug_preview_label'  => 'Pré-visualização',
            'saved'               => 'Salvo',
            'available_languages'    => 'Idiomas disponíveis',
            'default_capacity'       => 'Capacidade padrão',
            'create_new_schedules'   => 'Criar novos horários',
            'multiple_hint_ctrl_cmd' => 'Mantenha CTRL/CMD para selecionar vários',
            'use_existing_schedules' => 'Usar horários existentes',
            'add_schedule'           => 'Adicionar horário',
            'schedules_title'        => 'Horários do Tour',
            'amenities_included'     => 'Comodidades incluídas',
            'amenities_excluded'     => 'Comodidades não incluídas',
            'color'                  => 'Cor do Tour',
            'remove'                 => 'Remover',
            'delete'                 => 'Excluir',
            'choose_itinerary'       => 'Escolher itinerário',
            'select_type'            => 'Selecionar tipo',
            'empty_means_default'    => 'Padrão',
            'actives'                 => 'Ativos',
            'inactives'               => 'Inativos',
            'archived'                => 'Arquivados',
            'all'                     => 'Todos',
            'help_title'              => 'Ajuda',
            'amenities_included_hint' => 'Selecione o que está incluído no tour.',
            'amenities_excluded_hint' => 'Selecione o que NÃO está incluído no tour.',
            'help_included_title'     => 'Incluído',
            'help_included_text'      => 'Marque tudo o que está incluído no preço do tour (transporte, refeições, ingressos, equipamento, guia, etc.).',
            'help_excluded_title'     => 'Não Incluído',
            'help_excluded_text'      => 'Marque o que o cliente deve pagar à parte ou trazer (gorjetas, bebidas alcoólicas, souvenirs, etc.).',
            'select_or_create_title' => 'Selecionar ou Criar Itinerário',
            'select_existing_items'  => 'Selecionar Itens Existentes',
            'name_hint'              => 'Nome identificador para este itinerário',
            'click_add_item_hint'    => 'Clique em "Adicionar Item" para criar novos itens',
            'scroll_hint' => 'Deslize horizontalmente para ver mais colunas',
            'no_schedules' => 'Sem horários',
            'no_prices' => 'Sem preços configurados',

            // Badges de preços
            'prices_by_period' => 'Preços por Período',
            'period' => 'período',
            'periods' => 'períodos',
            'all_year' => 'Todo o ano',
            'from' => 'Desde',
            'until' => 'Até',
            'no_prices' => 'Sem preços',

            'edit' => 'Editar',
            'slug_auto' => 'Será gerado automaticamente',
            'deactivate' => 'Desativar',
            'manage_prices' => 'Gerenciar preços',
            'manage_images' => 'Gerenciar imagens',
            'manage_delete' => 'Excluir',
            'added_to_cart' => 'Adicionado ao carrinho',
            'add_language' => 'Adicionar idioma',
            'added_to_cart_text' => 'O tour foi adicionado ao carrinho com sucesso.',
            'amenities_excluded_auto_hint'    => 'Por padrão, marcamos como “não incluídas” todas as comodidades que você não selecionou como incluídas. Você pode desmarcar as que não se aplicam ao tour.',
            'quick_create_language_hint' => 'Adicione um novo idioma rapidamente se ele não aparecer na lista.',
            'quick_create_type_hint' => 'Adicione um novo tipo de tour rapidamente se ele não aparecer na lista.',

            'none' => [
                'amenities'       => 'Sem comodidades',
                'exclusions'      => 'Sem exclusões',
                'itinerary'       => 'Sem itinerário',
                'itinerary_items' => 'Sem itens',
                'languages'       => 'Sem idiomas',
                'schedules'       => 'Sem horários',
            ],

            'archive' => 'Arquivar',
            'restore' => 'Restaurar',
            'purge'   => 'Excluir permanentemente',

            'confirm_archive_title' => 'Arquivar tour?',
            'confirm_archive_text'  => 'O tour ficará indisponível para novas reservas, mas as reservas existentes serão mantidas.',
            'confirm_purge_title'   => 'Excluir permanentemente',
            'confirm_purge_text'    => 'Esta ação é irreversível e só é permitida se o tour nunca teve reservas.',

            'filters' => [
                'active'   => 'Ativos',
                'inactive' => 'Inativos',
                'archived' => 'Arquivados',
                'all'      => 'Todos',
            ],

            'font_decrease_title' => 'Diminuir tamanho da fonte',
            'font_increase_title' => 'Aumentar tamanho da fonte',
        ],

    ],

    // =========================================================
    // [08] IMAGES
    // =========================================================
    'image' => [

        'limit_reached_title' => 'Limite atingido',
        'limit_reached_text'  => 'Foi atingido o limite de imagens para este tour.',
        'upload_success'      => 'Imagens enviadas com sucesso.',
        'upload_none'         => 'Nenhuma imagem foi enviada.',
        'upload_truncated'    => 'Alguns arquivos foram ignorados devido ao limite por tour.',
        'done'                => 'Concluído',
        'notice'              => 'Aviso',
        'saved'               => 'Salvo',
        'caption_updated'     => 'Legenda atualizada com sucesso.',
        'deleted'             => 'Excluída',
        'image_removed'       => 'Imagem excluída com sucesso.',
        'invalid_order'       => 'Ordem inválida.',
        'nothing_to_reorder'  => 'Nada para reordenar.',
        'order_saved'         => 'Ordem salva.',
        'cover_updated_title' => 'Atualizar capa',
        'cover_updated_text'  => 'Esta imagem agora é a capa.',
        'deleting'            => 'Excluindo...',

        'ui' => [
            'page_title_pick'     => 'Imagens de Tours',
            'page_heading'        => 'Imagens de Tours',
            'choose_tour'         => 'Escolher tour',
            'search_placeholder'  => 'Buscar por ID ou nome…',
            'search_button'       => 'Buscar',
            'no_results'          => 'Nenhum tour encontrado.',
            'manage_images'       => 'Gerenciar imagens',
            'cover_alt'           => 'Capa',
            'images_label'        => 'imagens',

            'upload_btn'          => 'Enviar',
            'delete_btn'          => 'Excluir',
            'show_btn'            => 'Mostrar',
            'close_btn'           => 'Fechar',
            'preview_title'       => 'Pré-visualização da imagem',

            'error_title'         => 'Erro',
            'warning_title'       => 'Atenção',
            'success_title'       => 'Sucesso',
            'cancel_btn'          => 'Cancelar',

            'confirm_delete_title' => 'Excluir esta imagem?',
            'confirm_delete_text'  => 'Esta ação não pode ser desfeita.',

            'cover_current_title'    => 'Capa atual',
            'upload_new_cover_title' => 'Enviar nova capa',
            'cover_file_label'       => 'Arquivo da capa',
            'file_help_cover'        => 'JPEG/PNG/WebP, 30 MB máx.',
            'id_label'               => 'ID',

            'back_btn'          => 'Voltar à lista',

            'stats_images'      => 'Imagens enviadas',
            'stats_cover'       => 'Capas definidas',
            'stats_selected'    => 'Selecionadas',

            'drag_or_click'     => 'Arraste e solte suas imagens ou clique para selecionar.',
            'upload_help'       => 'Formatos permitidos: JPG, PNG, WebP. Tamanho máximo total 100 MB.',
            'select_btn'        => 'Selecionar arquivos',
            'limit_badge'       => 'Limite de :max imagens atingido',
            'files_word'        => 'arquivos',

            'select_all'        => 'Selecionar todas',
            'delete_selected'   => 'Excluir selecionadas',
            'delete_all'        => 'Excluir todas',

            'select_image_title' => 'Selecionar esta imagem',
            'select_image_aria'  => 'Selecionar imagem :id',

            'cover_label'       => 'Capa',
            'cover_btn'         => 'Definir como capa',

            'caption_placeholder' => 'Legenda (opcional)',
            'saving_label'        => 'Salvando…',
            'saving_fallback'     => 'Salvando…',
            'none_label'          => 'Sem legenda',
            'limit_word'          => 'Limite',

            'confirm_set_cover_title' => 'Definir como capa?',
            'confirm_set_cover_text'  => 'Esta imagem será a capa principal do tour.',
            'confirm_btn'             => 'Sim, continuar',

            'confirm_bulk_delete_title' => 'Excluir as imagens selecionadas?',
            'confirm_bulk_delete_text'  => 'As imagens selecionadas serão excluídas permanentemente.',

            'confirm_delete_all_title'  => 'Excluir todas as imagens?',
            'confirm_delete_all_text'   => 'Todas as imagens deste tour serão excluídas.',

            'no_images'           => 'Ainda não há imagens para este tour.',
        ],

        'errors' => [
            'validation'     => 'Os dados enviados não são válidos.',
            'upload_generic' => 'Não foi possível enviar algumas imagens.',
            'update_caption' => 'Não foi possível atualizar a legenda.',
            'delete'         => 'Não foi possível excluir a imagem.',
            'reorder'        => 'Não foi possível salvar a ordem.',
            'set_cover'      => 'Não foi possível definir a capa.',
            'load_list'      => 'Não foi possível carregar a lista.',
            'too_large'      => 'O arquivo excede o tamanho máximo permitido. Tente uma imagem menor.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Preços - :name',
            'header_title'       => 'Preços: :name',
            'back_to_tours'      => 'Voltar para tours',

            'configured_title'   => 'Categorias e preços configurados',
            'empty_title'        => 'Não há categorias configuradas para este tour.',
            'empty_hint'         => 'Use o formulário à direita para adicionar categorias.',

            'save_changes'       => 'Salvar alterações',
            'auto_disable_note'  => 'Preços em $0 são desativados automaticamente',

            'add_category'       => 'Adicionar categoria',

            'all_assigned_title' => 'Todas as categorias estão atribuídas',
            'all_assigned_text'  => 'Não há mais categorias disponíveis para este tour.',

            'info_title'         => 'Informação',
            'tour_label'         => 'Tour',
            'configured_count'   => 'Categorias configuradas',
            'active_count'       => 'Categorias ativas',

            'fields_title'       => 'Campos',
            'rules_title'        => 'Regras',

            'field_price'        => 'Preço',
            'field_min'          => 'Mínimo',
            'field_max'          => 'Máximo',
            'field_status'       => 'Status',

            'rule_min_le_max'    => 'O mínimo deve ser menor ou igual ao máximo',
            'rule_zero_disable'  => 'Preços em $0 são desativados automaticamente',
            'rule_only_active'   => 'Apenas as categorias ativas aparecem no site público',
        ],

        'table' => [
            'category'   => 'Categoria',
            'age_range'  => 'Faixa etária',
            'price_usd'  => 'Preço (USD)',
            'min'        => 'Mín',
            'max'        => 'Máx',
            'status'     => 'Status',
            'action'     => 'Ação',
            'active'     => 'Ativa',
            'inactive'   => 'Inativa',
        ],

        'forms' => [
            'select_placeholder'  => '-- Selecionar --',
            'category'            => 'Categoria',
            'price_usd'           => 'Preço (USD)',
            'min'                 => 'Mínimo',
            'max'                 => 'Máximo',
            'create_disabled_hint' => 'Se o preço for $0, a categoria será criada desativada',
            'add'                 => 'Adicionar',
        ],

        'modal' => [
            'delete_title'   => 'Excluir categoria',
            'delete_text'    => 'Excluir esta categoria deste tour?',
            'cancel'         => 'Cancelar',
            'delete'         => 'Excluir',
            'delete_tooltip' => 'Excluir categoria',
        ],

        'flash' => [
            'success' => 'Operação realizada com sucesso.',
            'error'   => 'Ocorreu um erro.',
        ],

        'js' => [
            'max_ge_min'            => 'O máximo deve ser maior ou igual ao mínimo',
            'auto_disabled_tooltip' => 'Preço em $0 – desativado automaticamente',
            'fix_errors'            => 'Corrija as quantidades mínimas e máximas',
        ],

        'quick_category' => [
            'title'                 => 'Criar categoria rápida',
            'button'                => 'Nova categoria',
            'go_to_index'           => 'Ver todas as categorias',
            'go_to_index_title'     => 'Abrir a lista completa de categorias',
            'name_label'            => 'Nome da categoria',
            'age_from'              => 'Idade a partir de',
            'age_to'                => 'Idade até',
            'save'                  => 'Salvar categoria',
            'cancel'                => 'Cancelar',
            'saving'                => 'Salvando...',
            'success_title'         => 'Categoria criada',
            'success_text'          => 'A categoria foi criada e adicionada ao tour com sucesso.',
            'error_title'           => 'Erro',
            'error_generic'         => 'Ocorreu um problema ao criar a categoria.',
            'created_ok'            => 'Categoria criada com sucesso.',
            'no_limit'              => 'Vazio para sem limite',
        ],

        'validation' => [
            'title' => 'Validação de Preços',
            'no_categories' => 'Você deve adicionar pelo menos uma categoria de preço',
            'no_price_greater_zero' => 'Deve haver pelo menos uma categoria com preço maior que $0,00',
            'price_required' => 'O preço é obrigatório',
            'price_min' => 'O preço deve ser maior ou igual a 0',
            'age_to_greater_equal' => 'A idade até deve ser maior ou igual à idade a partir de',
        ],

        'alerts' => [
            'price_updated' => 'Preço atualizado com sucesso',
            'price_created' => 'Categoria adicionada ao período com sucesso',
            'price_deleted' => 'Preço excluído com sucesso',
            'status_updated' => 'Status atualizado',
            'period_updated' => 'Datas do período atualizadas',
            'period_deleted' => 'Período excluído com sucesso',

            'error_title' => 'Erro',
            'error_unexpected' => 'Ocorreu um erro inesperado',
            'error_delete_price' => 'Não foi possível excluir o preço',
            'error_add_category' => 'Não foi possível adicionar a categoria',
            'error_update_period' => 'Não foi possível atualizar as datas do período',

            'attention' => 'Atenção',
            'select_category_first' => 'Selecione uma categoria primeiro',
            'duplicate_category_title' => 'Categoria duplicada',
            'duplicate_category_text' => 'Esta categoria já está adicionada neste período',

            'confirm_delete_price_title' => 'Excluir preço?',
            'confirm_delete_price_text' => 'Esta ação não pode ser desfeita.',
            'confirm_delete_period_title' => 'Excluir este período?',
            'confirm_delete_period_text' => 'Todos os preços associados a este período serão excluídos.',
            'confirm_yes_delete' => 'Sim, excluir',
            'confirm_cancel' => 'Cancelar',

            'no_categories' => 'Este período não tem categorias',
        ],
    ],

    'ajax' => [
        'category_created' => 'Categoria criada com sucesso',
        'category_error' => 'Erro ao criar a categoria',
        'language_created' => 'Idioma criado com sucesso',
        'language_error' => 'Erro ao criar o idioma',
        'amenity_created' => 'Comodidade criada com sucesso',
        'amenity_error' => 'Erro ao criar a comodidade',
        'schedule_created' => 'Horário criado com sucesso',
        'schedule_error' => 'Erro ao criar o horário',
        'itinerary_created' => 'Itinerário criado com sucesso',
        'itinerary_error' => 'Erro ao criar o itinerário',
        'translation_error' => 'Erro ao traduzir',
    ],

    'modal' => [
        'create_category' => 'Criar Nova Categoria',
        'create_language' => 'Criar Novo Idioma',
        'create_amenity' => 'Criar Nova Comodidade',
        'create_schedule' => 'Criar Novo Horário',
        'create_itinerary' => 'Criar Novo Itinerário',
    ],

    'validation' => [
        'slug_taken' => 'Este slug já está em uso',
        'slug_available' => 'Slug disponível',
    ],

    'tour_type' => [
        'fields' => [
            'name' => 'Nome',
            'description' => 'Descrição',
            'status' => 'Status',
            'duration' => 'Duração',
            'duration_hint' => 'Duração sugerida do passeio (opcional)',
            'duration_placeholder' => 'Exemplo: 4 horas, 6 horas, etc.',
        ],
    ],
];
