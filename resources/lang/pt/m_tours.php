<?php

/*************************************************************
 *  MÓDULO DE TRADUÇÕES: TOURS
 *  Arquivo: resources/lang/pt_BR/m_tours.php
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title'        => 'Sucesso',
        'error_title'          => 'Erro',
        'people'               => 'pessoas',
        'hours'                => 'horas',
        'success'              => 'Sucesso',
        'error'                => 'Erro',
        'cancel'               => 'Cancelar',
        'confirm_delete'       => 'Sim, excluir',
        'unspecified'          => 'Não especificado',
        'no_description'       => 'Sem descrição',
        'required_fields_title'=> 'Campos obrigatórios',
        'required_fields_text' => 'Por favor, preencha os campos obrigatórios: Nome e Capacidade Máxima',
        'active'               => 'Ativo',
        'inactive'             => 'Inativo',
        'notice'               => 'Aviso',
        'na'                   => 'Não configurado',
        'create'               => 'Criar',
        'info'                 => 'Informações',
        'close'                => 'Fechar',
        'required'             => 'Este campo é obrigatório.',
        'add'                  => 'Adicionar',
        'translating'          => 'Traduzindo...',
        'error_translating'    => 'Não foi possível traduzir o texto.',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'fields' => [
            'name' => 'Nome',
            'icon' => 'Ícone (FontAwesome)',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
        ],

        'ui' => [
            'page_title'    => 'Amenidades',
            'page_heading'  => 'Gestão de Amenidades',
            'list_title'    => 'Lista de Amenidades',

            'add'            => 'Adicionar Amenidade',
            'create_title'   => 'Cadastrar Amenidade',
            'edit_title'     => 'Editar Amenidade',
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

            'toggle_on'  => 'Ativar amenidade',
            'toggle_off' => 'Desativar amenidade',

            'toggle_confirm_on_title'  => 'Ativar amenidade?',
            'toggle_confirm_off_title' => 'Desativar amenidade?',
            'toggle_confirm_on_html'   => 'A amenidade <b>:label</b> será ativada.',
            'toggle_confirm_off_html'  => 'A amenidade <b>:label</b> será desativada.',

            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => '<b>:label</b> será excluída e você não poderá desfazer esta ação.',

            'yes_continue' => 'Sim, continuar',
            'yes_delete'   => 'Sim, excluir',

            'item_this' => 'esta amenidade',
        ],

        'success' => [
            'created'     => 'Amenidade criada com sucesso.',
            'updated'     => 'Amenidade atualizada com sucesso.',
            'activated'   => 'Amenidade ativada com sucesso.',
            'deactivated' => 'Amenidade desativada com sucesso.',
            'deleted'     => 'Amenidade excluída permanentemente.',
        ],

        'error' => [
            'create' => 'Não foi possível criar a amenidade.',
            'update' => 'Não foi possível atualizar a amenidade.',
            'toggle' => 'Não foi possível alterar o status da amenidade.',
            'delete' => 'Não foi possível excluir a amenidade.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nome inválido',
                'required' => 'O campo :attribute é obrigatório.',
                'string'   => 'O campo :attribute deve ser um texto.',
                'max'      => 'O campo :attribute não pode ter mais de :max caracteres.',
            ],
        ],

        'hints' => [
            'fontawesome' => 'Use classes do FontAwesome, por exemplo: "fas fa-check".',
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
            'page_title'        => 'Horários de Passeios',
            'page_heading'      => 'Gestão de Horários',

            'general_title'     => 'Horários gerais',
            'new_schedule'      => 'Novo horário',
            'new_general_title' => 'Novo horário geral',
            'new'               => 'Novo',
            'edit_schedule'     => 'Editar horário',
            'edit_global'       => 'Editar (global)',

            'assign_existing'    => 'Vincular existente',
            'assign_to_tour'     => 'Vincular horário ao passeio ":tour"',
            'select_schedule'    => 'Selecione um horário',
            'choose'             => 'Escolher',
            'assign'             => 'Vincular',
            'new_for_tour_title' => 'Novo horário para ":tour"',

            'time_range'        => 'Horário',
            'state'             => 'Status',
            'actions'           => 'Ações',
            'schedule_state'    => 'Horário',
            'assignment_state'  => 'Vinculação',
            'no_general'        => 'Não há horários gerais cadastrados.',
            'no_tour_schedules' => 'Este passeio ainda não possui horários.',
            'no_label'          => 'Sem rótulo',
            'assigned_count'    => 'horário(s) vinculado(s)',

            'toggle_global_title'     => 'Ativar/Desativar (global)',
            'toggle_global_on_title'  => 'Ativar horário (global)?',
            'toggle_global_off_title' => 'Desativar horário (global)?',
            'toggle_global_on_html'   => '<b>:label</b> será ativado para todos os passeios.',
            'toggle_global_off_html'  => '<b>:label</b> será desativado para todos os passeios.',

            'toggle_on_tour'          => 'Ativar neste passeio',
            'toggle_off_tour'         => 'Desativar neste passeio',
            'toggle_assign_on_title'  => 'Ativar neste passeio?',
            'toggle_assign_off_title' => 'Desativar neste passeio?',
            'toggle_assign_on_html'   => 'A vinculação ficará <b>ativa</b> para <b>:tour</b>.',
            'toggle_assign_off_html'  => 'A vinculação ficará <b>inativa</b> para <b>:tour</b>.',

            'detach_from_tour'     => 'Remover do passeio',
            'detach_confirm_title' => 'Remover do passeio?',
            'detach_confirm_html'  => 'O horário será <b>removido</b> de <b>:tour</b>.',

            'delete_forever'       => 'Excluir (global)',
            'delete_confirm_title' => 'Excluir permanentemente?',
            'delete_confirm_html'  => '<b>:label</b> (global) será excluído e você não poderá desfazer esta ação.',

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

            'missing_fields_title' => 'Dados faltando',
            'missing_fields_text'  => 'Verifique os campos obrigatórios (início, fim e capacidade).',
            'could_not_save'       => 'Não foi possível salvar',
        ],

        'success' => [
            'created'                => 'Horário criado com sucesso.',
            'updated'                => 'Horário atualizado com sucesso.',
            'activated_global'       => 'Horário ativado com sucesso (global).',
            'deactivated_global'     => 'Horário desativado com sucesso (global).',
            'attached'               => 'Horário vinculado ao passeio.',
            'detached'               => 'Horário removido do passeio com sucesso.',
            'assignment_activated'   => 'Vinculação ativada para este passeio.',
            'assignment_deactivated' => 'Vinculação desativada para este passeio.',
            'deleted'                => 'Horário excluído com sucesso.',
        ],

        'error' => [
            'create'               => 'Ocorreu um problema ao criar o horário.',
            'update'               => 'Ocorreu um problema ao atualizar o horário.',
            'toggle'               => 'Não foi possível alterar o status global do horário.',
            'attach'               => 'Não foi possível vincular o horário ao passeio.',
            'detach'               => 'Não foi possível remover o horário do passeio.',
            'assignment_toggle'    => 'Não foi possível alterar o status da vinculação.',
            'not_assigned_to_tour' => 'O horário não está vinculado a este passeio.',
            'delete'               => 'Ocorreu um problema ao excluir o horário.',
        ],

        'placeholders' => [
            'morning' => 'Ex.: Manhã',
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
            'register_item' => 'Cadastrar Item',
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
            'delete_confirm_html'  => '<b>:label</b> será excluído e você não poderá desfazer esta ação.',
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
                'max'      => 'O campo :attribute não pode ter mais de :max caracteres.',
            ],
            'description' => [
                'required' => 'O campo :attribute é obrigatório.',
                'string'   => 'O campo :attribute deve ser um texto.',
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

            'assign'        => 'Vincular',
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

            'assign_title'          => 'Vincular itens a :name',
            'drag_hint'             => 'Arraste e solte os itens para definir a ordem.',
            'drag_handle'           => 'Arrastar para reordenar',
            'select_one_title'      => 'Selecione pelo menos um item',
            'select_one_text'       => 'Por favor, selecione pelo menos um item para continuar.',
            'assign_confirm_title'  => 'Vincular itens selecionados?',
            'assign_confirm_button' => 'Sim, vincular',
            'assigning'             => 'Vinculando...',

            'no_items_assigned'       => 'Não há itens vinculados a este itinerário.',
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
        'modal' => [
            'create_itinerary' => 'Criar itinerário',
        ],

        'success' => [
            'created'        => 'Itinerário criado com sucesso.',
            'updated'        => 'Itinerário atualizado com sucesso.',
            'activated'      => 'Itinerário ativado com sucesso.',
            'deactivated'    => 'Itinerário desativado com sucesso.',
            'deleted'        => 'Itinerário excluído permanentemente.',
            'items_assigned' => 'Itens vinculados com sucesso.',
        ],

        'error' => [
            'create'  => 'Não foi possível criar o itinerário.',
            'update'  => 'Não foi possível atualizar o itinerário.',
            'toggle'  => 'Não foi possível alterar o status do itinerário.',
            'delete'  => 'Não foi possível excluir o itinerário.',
            'assign'  => 'Não foi possível vincular os itens.',
        ],

        'validation' => [
            'name_required' => 'Você deve informar um nome para o itinerário.',
            'name' => [
                'required' => 'O nome do itinerário é obrigatório.',
                'string'   => 'O nome deve ser um texto.',
                'max'      => 'O nome não pode ter mais de 255 caracteres.',
                'unique'   => 'Já existe um itinerário com esse nome.',
            ],
            'description' => [
                'string' => 'A descrição deve ser um texto.',
                'max'    => 'A descrição não pode ter mais de 1000 caracteres.',
            ],
            'items' => [
                'required'      => 'Você deve selecionar pelo menos um item.',
                'array'         => 'O formato dos itens não é válido.',
                'min'           => 'Você deve selecionar pelo menos um item.',
                'order_integer' => 'A ordem deve ser um número inteiro.',
                'order_min'     => 'A ordem não pode ser negativa.',
                'order_max'     => 'A ordem não pode ser maior que 9999.',
            ],
        ],

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
            'page_title'   => 'Idiomas de Passeios',
            'page_heading' => 'Gestão de Idiomas',
            'list_title'   => 'Lista de Idiomas',

            'table' => [
                'id'      => 'ID',
                'name'    => 'Idioma',
                'state'   => 'Status',
                'actions' => 'Ações',
            ],

            'add'            => 'Adicionar Idioma',
            'create_title'   => 'Cadastrar Idioma',
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
                'created_title'     => 'Idioma Cadastrado',
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
                'string'   => 'O campo :attribute deve ser um texto.',
                'max'      => 'O campo :attribute não pode ter mais de :max caracteres.',
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
        'title' => 'Passeios',

        'fields' => [
            'id'            => 'ID',
            'name'          => 'Nome',
            'details'       => 'Detalhes',
            'price'         => 'Preços',
            'overview'      => 'Resumo',
            'amenities'     => 'Amenidades',
            'exclusions'    => 'Exclusões',
            'itinerary'     => 'Itinerário',
            'languages'     => 'Idiomas',
            'schedules'     => 'Horários',
            'adult_price'   => 'Preço Adulto',
            'kid_price'     => 'Preço Criança',
            'length_hours'  => 'Duração (horas)',
            'max_capacity'  => 'Capacidade máx.',
            'type'          => 'Tipo de Passeio',
            'viator_code'   => 'Código Viator',
            'status'        => 'Status',
            'actions'       => 'Ações',
            'group_size'    => 'Tamanho do grupo',
        ],

        'pricing' => [
            'configured_categories' => 'Categorias configuradas',
            'create_category'       => 'Criar categoria',
            'note_title'            => 'Nota:',
            'note_text'             => 'Defina aqui os preços base para cada categoria de cliente.',
            'manage_detailed_hint'  => 'Para uma gestão detalhada, use o botão "Gerenciar Preços Detalhados" acima.',
            'price_usd'             => 'Preço (USD)',
            'min_quantity'          => 'Quantidade mínima',
            'max_quantity'          => 'Quantidade máxima',
            'status'                => 'Status',
            'active'                => 'Ativo',
            'no_categories'         => 'Não há categorias de clientes configuradas.',
            'create_categories_first' => 'Crie as categorias primeiro',
            'page_title'            => 'Preços - :name',
            'header_title'          => 'Preços: :name',
            'back_to_tours'         => 'Voltar para os passeios',

            'configured_title'      => 'Categorias e preços configurados',
            'empty_title'           => 'Não há categorias configuradas para este passeio.',
            'empty_hint'            => 'Use o formulário à direita para adicionar categorias.',

            'save_changes'          => 'Salvar alterações',
            'auto_disable_note'     => 'Preços em US$ 0 são desativados automaticamente',

            'add_category'          => 'Adicionar categoria',

            'all_assigned_title'    => 'Todas as categorias estão vinculadas',
            'all_assigned_text'     => 'Não há mais categorias disponíveis para este passeio.',

            'info_title'            => 'Informações',
            'tour_label'            => 'Passeio',
            'configured_count'      => 'Categorias configuradas',
            'active_count'          => 'Categorias ativas',

            'fields_title'          => 'Campos',
            'rules_title'           => 'Regras',

            'field_price'           => 'Preço',
            'field_min'             => 'Mínimo',
            'field_max'             => 'Máximo',
            'field_status'          => 'Status',

            'rule_min_le_max'       => 'O mínimo deve ser menor ou igual ao máximo',
            'rule_zero_disable'     => 'Preços em US$ 0 são desativados automaticamente',
            'rule_only_active'      => 'Somente categorias ativas aparecem no site público',

            'status_active'         => 'Ativo',
            'add_existing_category'       => 'Adicionar categoria existente',
            'choose_category_placeholder' => 'Selecione uma categoria…',
            'add_button'                  => 'Adicionar',
            'add_existing_hint'           => 'Adicione apenas as categorias de cliente necessárias para este passeio.',
            'remove_category'             => 'Remover categoria',
            'category_already_added'      => 'Esta categoria já foi adicionada ao passeio.',
            'no_prices_preview'           => 'Ainda não há preços configurados.',
        ],

        'modal' => [
            'create_category' => 'Criar categoria',

            'fields' => [
                'name'           => 'Nome',
                'age_from'       => 'Idade a partir de',
                'age_to'         => 'Idade até',
                'age_range'      => 'Faixa etária',
                'min'            => 'Mínimo',
                'max'            => 'Máximo',
                'order'          => 'Ordem',
                'is_active'      => 'Ativo',
                'auto_translate' => 'Traduzir automaticamente',
            ],

            'placeholders' => [
                'name'            => 'Ex.: Adulto, Criança, Bebê',
                'age_to_optional' => 'Deixe em branco para "+"',
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
            'select_hint'            => 'Selecione os horários para este passeio',
            'no_schedules'           => 'Não há horários disponíveis.',
            'create_schedules_link'  => 'Criar horários',

            'create_new_title'       => 'Criar Novo Horário',
            'label_placeholder'      => 'Ex.: Manhã, Tarde',
            'create_and_assign'      => 'Criar este horário e vinculá-lo ao passeio',

            'info_title'             => 'Informações',
            'schedules_title'        => 'Horários',
            'schedules_text'         => 'Selecione um ou mais horários em que este passeio estará disponível.',
            'create_block_title'     => 'Criar Novo',
            'create_block_text'      => 'Se você precisar de um horário que ainda não existe, pode criá-lo aqui marcando a opção "Criar este horário e vinculá-lo ao passeio".',

            'current_title'          => 'Horários Atuais',
            'none_assigned'          => 'Sem horários vinculados',
        ],

        'summary' => [
            'preview_title'        => 'Prévia do Passeio',
            'preview_text_create'  => 'Revise todas as informações antes de criar o passeio.',
            'preview_text_update'  => 'Revise todas as informações antes de atualizar o passeio.',

            'basic_details_title'  => 'Detalhes Básicos',
            'description_title'    => 'Descrição',
            'prices_title'         => 'Preços por Categoria',
            'schedules_title'      => 'Horários',
            'languages_title'      => 'Idiomas',
            'itinerary_title'      => 'Itinerário',

            'table' => [
                'category' => 'Categoria',
                'price'    => 'Preço',
                'min_max'  => 'Mín–Máx',
            ],

            'not_specified'        => 'Não especificado',
            'slug_autogenerated'   => 'Será gerado automaticamente',
            'no_description'       => 'Sem descrição',
            'no_active_prices'     => 'Sem preços ativos configurados',
            'no_languages'         => 'Sem idiomas vinculados',
            'none_included'        => 'Nada marcado como incluído',
            'none_excluded'        => 'Nada marcado como excluído',

            'units' => [
                'hours'  => 'horas',
                'people' => 'pessoas',
            ],

            'create_note' => 'Os horários, preços, idiomas e amenidades serão exibidos aqui após salvar o passeio.',
        ],

        'alerts' => [
            'delete_title' => 'Excluir passeio?',
            'delete_text'  => 'O passeio será movido para Excluídos. Você poderá restaurá-lo depois.',
            'purge_title'  => 'Excluir permanentemente?',
            'purge_text'   => 'Esta ação é irreversível.',
            'purge_text_with_bookings' => 'Este passeio possui :count reserva(s). Elas não serão excluídas; ficarão sem passeio associado.',
            'toggle_question_active'   => 'Desativar passeio?',
            'toggle_question_inactive' => 'Ativar passeio?',
        ],

        'flash' => [
            'created'       => 'Passeio criado com sucesso.',
            'updated'       => 'Passeio atualizado com sucesso.',
            'deleted_soft'  => 'Passeio movido para Excluídos.',
            'restored'      => 'Passeio restaurado com sucesso.',
            'purged'        => 'Passeio excluído permanentemente.',
            'toggled_on'    => 'Passeio ativado.',
            'toggled_off'   => 'Passeio desativado.',
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
            'max_capacity'  => 'Capacidade máx.',
            'type'          => 'Tipo',
            'viator_code'   => 'Código Viator',
            'status'        => 'Status',
            'actions'       => 'Ações',
            'slug'          => 'URL',
            'prices'        => 'Preços',
            'capacity'      => 'Capacidade',
            'group_size'    => 'Máx. grupo',
        ],

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Inativo',
            'archived' => 'Arquivado',
        ],

        'placeholders' => [
            'group_size' => 'Ex.: 10',
        ],

        'hints' => [
            'group_size' => 'Capacidade/tamanho de grupo recomendado para este passeio.',
        ],

        'success' => [
            'created'     => 'O passeio foi criado com sucesso.',
            'updated'     => 'O passeio foi atualizado com sucesso.',
            'deleted'     => 'O passeio foi excluído.',
            'toggled'     => 'O status do passeio foi atualizado.',
            'activated'   => 'Passeio ativado com sucesso.',
            'deactivated' => 'Passeio desativado com sucesso.',
            'archived'    => 'Passeio arquivado com sucesso.',
            'restored'    => 'Passeio restaurado com sucesso.',
            'purged'      => 'Passeio excluído permanentemente.',
        ],

        'error' => [
            'create'    => 'Não foi possível criar o passeio.',
            'update'    => 'Não foi possível atualizar o passeio.',
            'delete'    => 'Não foi possível excluir o passeio.',
            'toggle'    => 'Não foi possível alterar o status do passeio.',
            'not_found' => 'O passeio não existe.',
            'restore'            => 'Não foi possível restaurar o passeio.',
            'purge'              => 'Não foi possível excluir o passeio permanentemente.',
            'purge_has_bookings' => 'Não é possível excluir permanentemente: o passeio possui reservas.',
        ],

        'ui' => [
            'page_title'       => 'Gestão de Passeios',
            'page_heading'     => 'Gestão de Passeios',
            'create_title'     => 'Cadastrar Passeio',
            'edit_title'       => 'Editar Passeio',
            'delete_title'     => 'Excluir Passeio',
            'cancel'           => 'Cancelar',
            'save'             => 'Salvar',
            'save_changes'     => 'Salvar alterações',
            'update'           => 'Atualizar',
            'delete_confirm'   => 'Excluir este passeio?',
            'toggle_on'        => 'Ativar',
            'toggle_off'       => 'Desativar',
            'toggle_on_title'  => 'Ativar passeio?',
            'toggle_off_title' => 'Desativar passeio?',
            'toggle_on_button'  => 'Sim, ativar',
            'toggle_off_button' => 'Sim, desativar',
            'see_more'         => 'Ver mais',
            'see_less'         => 'Ocultar',
            'load_more'        => 'Carregar mais',
            'loading'          => 'Carregando...',
            'load_more_error'  => 'Não foi possível carregar mais passeios.',
            'confirm_title'    => 'Confirmação',
            'confirm_text'     => 'Você deseja confirmar esta ação?',
            'yes_confirm'      => 'Sim, confirmar',
            'no_confirm'       => 'Não, cancelar',
            'add_tour'         => 'Adicionar Passeio',
            'edit_tour'        => 'Editar Passeio',
            'delete_tour'      => 'Excluir Passeio',
            'toggle_tour'      => 'Ativar/Desativar Passeio',
            'view_cart'        => 'Ver Carrinho',
            'add_to_cart'      => 'Adicionar ao Carrinho',
            'slug_help'        => 'Identificador do passeio na URL (sem espaços nem acentos)',
            'generate_auto'       => 'Gerar automaticamente',
            'slug_preview_label'  => 'Pré-visualização',
            'saved'               => 'Salvo',

            'available_languages'    => 'Idiomas disponíveis',
            'default_capacity'       => 'Capacidade padrão',
            'create_new_schedules'   => 'Criar novos horários',
            'multiple_hint_ctrl_cmd' => 'Segure CTRL/CMD para selecionar vários',
            'use_existing_schedules' => 'Usar horários existentes',
            'add_schedule'           => 'Adicionar horário',
            'schedules_title'        => 'Horários do Passeio',
            'amenities_included'     => 'Amenidades incluídas',
            'amenities_excluded'     => 'Amenidades não incluídas',
            'color'                  => 'Cor do Passeio',
            'remove'                 => 'Remover',
            'choose_itinerary'       => 'Escolher itinerário',
            'select_type'            => 'Selecionar tipo',
            'empty_means_default'    => 'Padrão',
            'actives'                => 'Ativos',
            'inactives'              => 'Inativos',
            'archived'               => 'Arquivados',
            'all'                    => 'Todos',
            'help_title'             => 'Ajuda',
            'amenities_included_hint' => 'Selecione o que está incluído no passeio.',
            'amenities_excluded_hint' => 'Selecione o que NÃO está incluído no passeio.',
            'help_included_title'     => 'Incluído',
            'help_included_text'      => 'Marque tudo o que está incluído no preço do passeio (transporte, refeições, ingressos, equipamento, guia etc.).',
            'help_excluded_title'     => 'Não incluído',
            'help_excluded_text'      => 'Marque o que o cliente deve pagar à parte ou levar (gorjetas, bebidas alcoólicas, souvenirs etc.).',
            'select_or_create_title' => 'Selecionar ou Criar Itinerário',
            'select_existing_items'  => 'Selecionar Itens Existentes',
            'name_hint'              => 'Nome identificador para este itinerário',
            'click_add_item_hint'    => 'Clique em "Adicionar Item" para criar novos itens',
            'scroll_hint'            => 'Role horizontalmente para ver mais colunas',
            'no_schedules'           => 'Sem horários',
            'no_prices'              => 'Sem preços configurados',
            'edit'                   => 'Editar',
            'slug_auto'              => 'Será gerado automaticamente',
            'added_to_cart'          => 'Adicionado ao carrinho',
            'added_to_cart_text'     => 'O passeio foi adicionado ao carrinho com sucesso.',

            'none' => [
                'amenities'       => 'Sem amenidades',
                'exclusions'      => 'Sem exclusões',
                'itinerary'       => 'Sem itinerário',
                'itinerary_items' => 'Sem itens',
                'languages'       => 'Sem idiomas',
                'schedules'       => 'Sem horários',
            ],

            'archive' => 'Arquivar',
            'restore' => 'Restaurar',
            'purge'   => 'Excluir permanentemente',

            'confirm_archive_title' => 'Arquivar passeio?',
            'confirm_archive_text'  => 'O passeio ficará indisponível para novas reservas, mas as reservas existentes serão mantidas.',
            'confirm_purge_title'   => 'Excluir permanentemente',
            'confirm_purge_text'    => 'Esta ação é irreversível e só é permitida se o passeio nunca teve reservas.',

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

        'limit_reached_title' => 'Limite alcançado',
        'limit_reached_text'  => 'Foi alcançado o limite de imagens para este passeio.',
        'upload_success'      => 'Imagens enviadas com sucesso.',
        'upload_none'         => 'Nenhuma imagem foi enviada.',
        'upload_truncated'    => 'Alguns arquivos foram ignorados por causa do limite por passeio.',
        'done'                => 'Concluído',
        'notice'              => 'Aviso',
        'saved'               => 'Salvar',
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
            'page_title_pick'     => 'Imagens de Passeios',
            'page_heading'        => 'Imagens de Passeios',
            'choose_tour'         => 'Escolher passeio',
            'search_placeholder'  => 'Buscar por ID ou nome…',
            'search_button'       => 'Buscar',
            'no_results'          => 'Nenhum passeio encontrado.',
            'manage_images'       => 'Gerenciar imagens',
            'cover_alt'           => 'Capa',
            'images_label'        => 'imagens',

            'upload_btn'          => 'Enviar',
            'delete_btn'          => 'Excluir',
            'show_btn'            => 'Exibir',
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
            'cover_file_label'       => 'Arquivo de capa',
            'file_help_cover'        => 'JPEG/PNG/WebP, 30 MB máx.',
            'id_label'               => 'ID',

            'back_btn'          => 'Voltar para a lista',

            'stats_images'      => 'Imagens enviadas',
            'stats_cover'       => 'Capas definidas',
            'stats_selected'    => 'Selecionadas',

            'drag_or_click'     => 'Arraste e solte suas imagens ou clique para selecionar.',
            'upload_help'       => 'Formatos permitidos: JPG, PNG, WebP. Tamanho total máximo de 100 MB.',
            'select_btn'        => 'Escolher arquivos',
            'limit_badge'       => 'Limite de :max imagens alcançado',
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
            'confirm_set_cover_text'  => 'Esta imagem será a capa principal do passeio.',
            'confirm_btn'             => 'Sim, continuar',

            'confirm_bulk_delete_title' => 'Excluir imagens selecionadas?',
            'confirm_bulk_delete_text'  => 'As imagens selecionadas serão excluídas permanentemente.',

            'confirm_delete_all_title'  => 'Excluir todas as imagens?',
            'confirm_delete_all_text'   => 'Todas as imagens deste passeio serão excluídas.',

            'no_images'           => 'Ainda não há imagens para este passeio.',
        ],

        'errors' => [
            'validation'     => 'Os dados enviados não são válidos.',
            'upload_generic' => 'Não foi possível enviar algumas imagens.',
            'update_caption' => 'Não foi possível atualizar a legenda.',
            'delete'         => 'Não foi possível excluir a imagem.',
            'reorder'        => 'Não foi possível salvar a nova ordem.',
            'set_cover'      => 'Não foi possível definir a capa.',
            'load_list'      => 'Não foi possível carregar a lista.',
            'too_large'      => 'O arquivo excede o tamanho máximo permitido. Tente uma imagem menor.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Preços - :name',
            'header_title'       => 'Preços: :name',
            'back_to_tours'      => 'Voltar para os passeios',

            'configured_title'   => 'Categorias e preços configurados',
            'empty_title'        => 'Não há categorias configuradas para este passeio.',
            'empty_hint'         => 'Use o formulário à direita para adicionar categorias.',

            'save_changes'       => 'Salvar alterações',
            'auto_disable_note'  => 'Preços em US$ 0 são desativados automaticamente',

            'add_category'       => 'Adicionar categoria',

            'all_assigned_title' => 'Todas as categorias estão vinculadas',
            'all_assigned_text'  => 'Não há mais categorias disponíveis para este passeio.',

            'info_title'         => 'Informações',
            'tour_label'         => 'Passeio',
            'configured_count'   => 'Categorias configuradas',
            'active_count'       => 'Categorias ativas',

            'fields_title'       => 'Campos',
            'rules_title'        => 'Regras',

            'field_price'        => 'Preço',
            'field_min'          => 'Mínimo',
            'field_max'          => 'Máximo',
            'field_status'       => 'Status',

            'rule_min_le_max'    => 'O mínimo deve ser menor ou igual ao máximo',
            'rule_zero_disable'  => 'Preços em US$ 0 são desativados automaticamente',
            'rule_only_active'   => 'Somente categorias ativas aparecem no site público',
        ],

        'table' => [
            'category'   => 'Categoria',
            'age_range'  => 'Faixa etária',
            'price_usd'  => 'Preço (USD)',
            'min'        => 'Mín',
            'max'        => 'Máx',
            'status'     => 'Status',
            'action'     => 'Ação',
            'active'     => 'Ativo',
            'inactive'   => 'Inativo',
        ],

        'forms' => [
            'select_placeholder'   => '-- Selecionar --',
            'category'             => 'Categoria',
            'price_usd'            => 'Preço (USD)',
            'min'                  => 'Mínimo',
            'max'                  => 'Máximo',
            'create_disabled_hint' => 'Se o preço for US$ 0, a categoria será criada como desativada',
            'add'                  => 'Adicionar',
        ],

        'modal' => [
            'delete_title'   => 'Excluir categoria',
            'delete_text'    => 'Excluir esta categoria deste passeio?',
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
            'auto_disabled_tooltip' => 'Preço em US$ 0 – desativado automaticamente',
            'fix_errors'            => 'Corrija as quantidades mínimas e máximas',
        ],
    ],

    'ajax' => [
        'category_created' => 'Categoria criada com sucesso',
        'category_error'   => 'Erro ao criar a categoria',
        'language_created' => 'Idioma criado com sucesso',
        'language_error'   => 'Erro ao criar o idioma',
        'amenity_created'  => 'Amenidade criada com sucesso',
        'amenity_error'    => 'Erro ao criar a amenidade',
        'schedule_created' => 'Horário criado com sucesso',
        'schedule_error'   => 'Erro ao criar o horário',
        'itinerary_created' => 'Itinerário criado com sucesso',
        'itinerary_error'   => 'Erro ao criar o itinerário',
        'translation_error' => 'Erro ao traduzir',
    ],

    'modal' => [
        'create_category'  => 'Criar Nova Categoria',
        'create_language'  => 'Criar Novo Idioma',
        'create_amenity'   => 'Criar Nova Amenidade',
        'create_schedule'  => 'Criar Novo Horário',
        'create_itinerary' => 'Criar Novo Itinerário',
    ],

    'validation' => [
        'slug_taken'     => 'Este slug já está em uso',
        'slug_available' => 'Slug disponível',
    ],

];
