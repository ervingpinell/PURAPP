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
        'success_title'        => 'Sucesso',
        'error_title'          => 'Erro',
        'people'               => 'pessoas',
        'hours'                => 'horas',
        'success'              => 'Sucesso',
        'error'                => 'Erro',
        'cancel'               => 'Cancelar',
        'confirm_delete'       => 'Sim, excluir',
        'unspecified'          => 'Sem especificar',
        'no_description'       => 'Sem descrição',
        'required_fields_title'=> 'Campos obrigatórios',
        'required_fields_text' => 'Por favor, preencha os campos obrigatórios: Nome e Capacidade Máxima',
        'active'               => 'Ativo',
        'inactive'             => 'Inativo',
        'notice'               => 'Aviso',
        'na'                   => 'Não configurado',
        'create'               => 'Criar',
        'previous'             => 'Voltar',
        'info'                 => 'Informação',
        'close'                => 'Fechar',
        'save'                 => 'Salvar',
        'required'             => 'Este campo é obrigatório.',
        'add'                  => 'Adicionar',
        'translating'          => 'Traduzindo...',
        'error_translating'    => 'Não foi possível traduzir o texto.',
        'confirm'              => 'confirmar',
        'yes'                  => 'Sim',
        'form_errors_title'    => 'Por favor, corrija os seguintes erros:',
        'delete'               => 'Excluir',
        'delete_all'           => 'Excluir tudo',
        'actions'              => 'Ações',
        'updated_at'           => 'Última atualização',
        'not_set'              => 'Não especificado',
        'error_deleting'       => 'Ocorreu um erro ao excluir. Por favor, tente novamente.',
        'error_saving'         => 'Ocorreu um erro ao salvar. Por favor, tente novamente.',
        'crud_go_to_index'     => 'Gerenciar :element',
        'validation_title'     => 'Há erros de validação',
        'ok'                   => 'Aceitar',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'singular' => 'amenidade',
        'plural'   => 'amenidades',

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

            'add'            => 'Adicionar amenidade',
            'create_title'   => 'Registrar amenidade',
            'edit_title'     => 'Editar amenidade',
            'save'           => 'Salvar',
            'update'         => 'Atualizar',
            'cancel'         => 'Cancelar',
            'close'          => 'Fechar',
            'state'          => 'Estado',
            'actions'        => 'Ações',
            'delete_forever' => 'Excluir definitivamente',

            'processing' => 'Processando...',
            'applying'   => 'Aplicando...',
            'deleting'   => 'Excluindo...',

            'toggle_on'  => 'Ativar amenidade',
            'toggle_off' => 'Desativar amenidade',

            'toggle_confirm_on_title'  => 'Ativar amenidade?',
            'toggle_confirm_off_title' => 'Desativar amenidade?',
            'toggle_confirm_on_html'   => 'A amenidade <b>:label</b> ficará ativa.',
            'toggle_confirm_off_html'  => 'A amenidade <b>:label</b> ficará inativa.',

            'delete_confirm_title' => 'Excluir definitivamente?',
            'delete_confirm_html'  => 'A amenidade <b>:label</b> será excluída e não poderá ser desfeita.',

            'yes_continue' => 'Sim, continuar',
            'yes_delete'   => 'Sim, excluir',

            'item_this' => 'esta amenidade',
        ],

        'success' => [
            'created'     => 'Amenidade criada corretamente.',
            'updated'     => 'Amenidade atualizada corretamente.',
            'activated'   => 'Amenidade ativada corretamente.',
            'deactivated' => 'Amenidade desativada corretamente.',
            'deleted'     => 'Amenidade excluída definitivamente.',
        ],

        'error' => [
            'create' => 'Não foi possível criar a amenidade.',
            'update' => 'Não foi possível atualizar a amenidade.',
            'toggle' => 'Não foi possível alterar o estado da amenidade.',
            'delete' => 'Não foi possível excluir a amenidade.',
        ],

        'validation' => [
            'included_required' => 'Você deve selecionar ao menos uma amenidade incluída.',
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
            'button'           => 'Nova amenidade',
            'title'            => 'Criar amenidade rápida',
            'name_label'       => 'Nome da amenidade',
            'icon_label'       => 'Ícone (opcional)',
            'icon_placeholder' => 'Ex.: fas fa-utensils',
            'icon_help'        => 'Use uma classe de ícone do Font Awesome ou deixe em branco.',
            'save'             => 'Salvar amenidade',
            'cancel'           => 'Cancelar',
            'saving'           => 'Salvando...',
            'error_generic'    => 'Não foi possível criar a amenidade. Tente novamente.',
            'go_to_index'         => 'Ver todas',
            'go_to_index_title'   => 'Ir para a lista completa de amenidades',
            'success_title'       => 'Amenidade criada',
            'success_text'        => 'A amenidade foi adicionada à lista do tour.',
            'error_title'         => 'Erro ao criar a amenidade',
            'error_duplicate'     => 'Já existe uma amenidade com esse nome.',
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
            'no_tour_schedules' => 'Este tour ainda não possui horários.',
            'no_label'          => 'Sem rótulo',
            'assigned_count'    => 'horário(s) atribuídos',

            'toggle_global_title'     => 'Ativar/Desativar (global)',
            'toggle_global_on_title'  => 'Ativar horário (global)?',
            'toggle_global_off_title' => 'Desativar horário (global)?',
            'toggle_global_on_html'   => 'O horário <b>:label</b> será ativado para todos os tours.',
            'toggle_global_off_html'  => 'O horário <b>:label</b> será desativado para todos os tours.',

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
            'delete_confirm_title' => 'Excluir definitivamente?',
            'delete_confirm_html'  => 'O horário <b>:label</b> será excluído (globalmente) e não poderá ser desfeito.',

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

            'missing_fields_title' => 'Faltam dados',
            'missing_fields_text'  => 'Verifique os campos obrigatórios (início, fim e capacidade).',
            'could_not_save'       => 'Não foi possível salvar',
            'base_capacity_tour'             => 'Capacidade base do tour:',
            'capacity_not_defined'           => 'Não definida',
            'capacity_optional'              => 'Capacidade (opcional)',
            'capacity_placeholder_with_value' => 'Ex.: :capacity',
            'capacity_placeholder_generic'   => 'Usar capacidade do tour',
            'capacity_hint_with_value'       => 'Deixe em branco → :capacity',
            'capacity_hint_generic'          => 'Deixe em branco → capacidade do tour',
            'tip_label'                      => 'Dica:',
            'capacity_tip'                   => 'Você pode deixar o campo de capacidade vazio para que o sistema use a capacidade geral do tour (:capacity).',
            'new_schedule_for_tour'            => 'Novo horário',
            'modal_new_for_tour_title'         => 'Criar horário para :tour',
            'modal_save'                       => 'Salvar horário',
            'modal_cancel'                     => 'Cancelar',
            'capacity_modal_info_with_value'   => 'A capacidade base do tour é :capacity. Se você deixar o campo de capacidade vazio, será usado esse valor.',
            'capacity_modal_info_generic'      => 'Se você deixar o campo de capacidade vazio, será usada a capacidade geral do tour quando estiver definida.',
        ],

        'success' => [
            'created'                => 'Horário criado corretamente.',
            'updated'                => 'Horário atualizado corretamente.',
            'activated_global'       => 'Horário ativado corretamente (global).',
            'deactivated_global'     => 'Horário desativado corretamente (global).',
            'attached'               => 'Horário atribuído ao tour.',
            'detached'               => 'Horário removido do tour corretamente.',
            'assignment_activated'   => 'Atribuição ativada para este tour.',
            'assignment_deactivated' => 'Atribuição desativada para este tour.',
            'deleted'                => 'Horário excluído corretamente.',
        ],

        'error' => [
            'create'               => 'Ocorreu um problema ao criar o horário.',
            'update'               => 'Ocorreu um problema ao atualizar o horário.',
            'toggle'               => 'Não foi possível alterar o estado global do horário.',
            'attach'               => 'Não foi possível atribuir o horário ao tour.',
            'detach'               => 'Não foi possível desatribuir o horário do tour.',
            'assignment_toggle'    => 'Não foi possível alterar o estado da atribuição.',
            'not_assigned_to_tour' => 'O horário não está atribuído a este tour.',
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
            'list_title'          => 'Itens de Itinerário',
            'add_item'            => 'Adicionar item',
            'register_item'       => 'Registrar item',
            'edit_item'           => 'Editar item',
            'save'                => 'Salvar',
            'update'              => 'Atualizar',
            'cancel'              => 'Cancelar',
            'state'               => 'Estado',
            'actions'             => 'Ações',
            'see_more'            => 'Ver mais',
            'see_less'            => 'Ver menos',
            'assigned_items'      => 'Itens atribuídos ao itinerário',
            'drag_to_order'       => 'Arraste os itens para definir a ordem.',
            'pool_hint'           => 'Marque os itens disponíveis que você quer incluir neste itinerário.',
            'register_item_hint'  => 'Registre novos itens se precisar de etapas adicionais que ainda não existem.',

            'toggle_on'           => 'Ativar item',
            'toggle_off'          => 'Desativar item',

            'delete_forever'       => 'Excluir definitivamente',
            'delete_confirm_title' => 'Excluir definitivamente?',
            'delete_confirm_html'  => 'O item <b>:label</b> será excluído e não poderá ser desfeito.',
            'yes_delete'           => 'Sim, excluir',
            'item_this'            => 'este item',

            'processing'           => 'Processando...',
            'applying'             => 'Aplicando...',
            'deleting'             => 'Excluindo...',
        ],

        'success' => [
            'created'     => 'Item de itinerário criado corretamente.',
            'updated'     => 'Item atualizado corretamente.',
            'activated'   => 'Item ativado corretamente.',
            'deactivated' => 'Item desativado corretamente.',
            'deleted'     => 'Item excluído definitivamente.',
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
            'new_itinerary' => 'Novo itinerário',
            'select_or_create_hint' => 'Selecione um itinerário existente ou crie um novo para este tour.',
            'save_changes'          => 'Salve o itinerário para aplicar as alterações ao tour.',
            'select_existing'       => 'Selecionar itinerário existente',
            'create_new'            => 'Criar novo itinerário',
            'add_item'              => 'Adicionar item',
            'min_one_item'          => 'Deve haver pelo menos um item no itinerário',

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
            'yes_continue'             => 'Sim, continuar',

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
            'go_to_crud'              => 'Ir para o módulo',
        ],

        'modal' => [
            'create_itinerary' => 'Criar itinerário',
        ],

        'success' => [
            'created'        => 'Itinerário criado corretamente.',
            'updated'        => 'Itinerário atualizado corretamente.',
            'activated'      => 'Itinerário ativado corretamente.',
            'deactivated'    => 'Itinerário desativado corretamente.',
            'deleted'        => 'Itinerário excluído definitivamente.',
            'items_assigned' => 'Itens atribuídos corretamente.',
        ],

        'error' => [
            'create'  => 'Não foi possível criar o itinerário.',
            'update'  => 'Não foi possível atualizar o itinerário.',
            'toggle'  => 'Não foi possível alterar o estado do itinerário.',
            'delete'  => 'Não foi possível excluir o itinerário.',
            'assign'  => 'Não foi possível atribuir os itens.',
        ],

        'validation' => [
            'name_required' => 'Você deve indicar um nome para o itinerário.',
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
                'item'         => 'Item',
                'required'     => 'Você deve selecionar pelo menos um item.',
                'array'        => 'O formato dos itens não é válido.',
                'min'          => 'Você deve selecionar pelo menos um item.',
                'order_integer'=> 'A ordem deve ser um número inteiro.',
                'order_min'    => 'A ordem não pode ser negativa.',
                'order_max'    => 'A ordem não pode exceder 9999.',
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
                'state'   => 'Estado',
                'actions' => 'Ações',
            ],

            'add'            => 'Adicionar idioma',
            'create_title'   => 'Registrar idioma',
            'edit_title'     => 'Editar idioma',
            'save'           => 'Salvar',
            'update'         => 'Atualizar',
            'cancel'         => 'Cancelar',
            'close'          => 'Fechar',
            'actions'        => 'Ações',
            'delete_forever' => 'Excluir definitivamente',

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
                'activated_title'   => 'Idioma ativado',
                'deactivated_title' => 'Idioma desativado',
                'updated_title'     => 'Idioma atualizado',
                'created_title'     => 'Idioma registrado',
                'deleted_title'     => 'Idioma excluído',
            ],
        ],

        'success' => [
            'created'     => 'Idioma criado com sucesso.',
            'updated'     => 'Idioma atualizado corretamente.',
            'activated'   => 'Idioma ativado corretamente.',
            'deactivated' => 'Idioma desativado corretamente.',
            'deleted'     => 'Idioma excluído corretamente.',
        ],

        'error' => [
            'create' => 'Não foi possível criar o idioma.',
            'update' => 'Não foi possível atualizar o idioma.',
            'toggle' => 'Não foi possível alterar o estado do idioma.',
            'delete' => 'Não foi possível excluir o idioma.',
            'save'   => 'Não foi possível salvar.',
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
            // Mensagens gerais
            'required' => 'Este campo é obrigatório.',
            'min'      => 'Este campo deve ter pelo menos :min caracteres.',
            'max'      => 'Este campo não pode exceder :max caracteres.',
            'number'   => 'Este campo deve ser um número válido.',
            'slug'     => 'O slug só pode conter letras minúsculas, números e hífens.',
            'color'    => 'Por favor, selecione uma cor válida.',
            'select'   => 'Por favor, selecione uma opção.',

            // Mensagens específicas de campos
            'length_in_hours'   => 'Duração em horas (ex.: 2, 2.5, 4)',
            'max_capacity_help' => 'Número máximo de pessoas por tour',

            // Formulários
            'form_error_title'   => 'Atenção!',
            'form_error_message' => 'Por favor, corrija os erros no formulário antes de continuar.',
            'saving'             => 'Salvando...',

            // Sucesso
            'success'            => 'Sucesso!',
            'tour_type_created'  => 'Tipo de tour criado com sucesso.',
            'language_created'   => 'Idioma criado com sucesso.',

            // Erros
            'tour_type_error'  => 'Erro ao criar o tipo de tour.',
            'language_error'   => 'Erro ao criar o idioma.',
        ],

        'wizard' => [
            // Títulos gerais
            'create_new_tour' => 'Criar novo tour',
            'edit_tour'       => 'Editar tour',
            'step_number'     => 'Passo :number',
            'edit_step'       => 'Editar',
            'leave_warning'   => 'Você tem alterações não salvas no tour. Se sair agora, o rascunho permanecerá no banco de dados. Tem certeza de que deseja sair?',
            'cancel_title'    => 'Cancelar configuração do tour?',
            'cancel_text'     => 'Se você sair deste assistente, pode perder alterações não salvas nesta etapa.',
            'cancel_confirm'  => 'Sim, descartar alterações',
            'cancel_cancel'   => 'Não, continuar editando',
            'details_validation_text' => 'Revise os campos obrigatórios do formulário de detalhes antes de continuar.',
            'most_recent'     => 'Mais recente',
            'last_modified'   => 'Última modificação',
            'start_fresh'     => 'Começar novamente',
            'draft_details'   => 'Detalhes do rascunho',
            'drafts_found'    => 'Foi encontrado um rascunho',
            'basic_info'      => 'Detalhes',

            // Passos do wizard
            'steps' => [
                'details'   => 'Detalhes básicos',
                'itinerary' => 'Itinerário',
                'schedules' => 'Horários',
                'amenities' => 'Amenidades',
                'prices'    => 'Preços',
                'summary'   => 'Resumo',
            ],

            // Ações
            'save_and_continue' => 'Salvar e continuar',
            'publish_tour'      => 'Publicar tour',
            'delete_draft'      => 'Excluir rascunho',
            'ready_to_publish'  => 'Pronto para publicar?',

            // Mensagens
            'details_saved'    => 'Detalhes salvos corretamente.',
            'itinerary_saved'  => 'Itinerário salvo corretamente.',
            'schedules_saved'  => 'Horários salvos corretamente.',
            'amenities_saved'  => 'Amenidades salvas corretamente.',
            'prices_saved'     => 'Preços salvos corretamente.',
            'published_successfully' => 'Tour publicado com sucesso!',
            'draft_cancelled'  => 'Rascunho excluído.',

            // Estados
            'draft_mode'          => 'Modo rascunho',
            'draft_explanation'   => 'Este tour será salvo como rascunho até que você conclua todos os passos e o publique.',
            'already_published'   => 'Este tour já foi publicado. Use o editor normal para modificá-lo.',
            'cannot_cancel_published' => 'Você não pode cancelar um tour já publicado.',

            // Confirmações
            'confirm_cancel' => 'Tem certeza de que deseja cancelar e excluir este rascunho?',

            // Resumo
            'publish_explanation' => 'Revise todas as informações antes de publicar. Depois de publicado, o tour ficará disponível para reservas.',
            'can_edit_later'      => 'Você poderá editar o tour depois de publicá-lo, a partir do painel de administração.',
            'incomplete_warning'  => 'Alguns passos estão incompletos. Você pode publicar mesmo assim, mas é recomendável completar todas as informações.',

            // Checklist
            'checklist'                => 'Lista de verificação',
            'checklist_details'        => 'Detalhes básicos concluídos',
            'checklist_itinerary'      => 'Itinerário configurado',
            'checklist_schedules'      => 'Horários adicionados',
            'checklist_amenities'      => 'Amenidades configuradas',
            'checklist_prices'         => 'Preços definidos',

            // Dicas
            'hints' => [
                'status' => 'O estado pode ser alterado depois de publicar.',
            ],

            // Modal de rascunhos existentes
            'existing_drafts_title'   => 'Você tem tours em rascunho pendentes!',
            'existing_drafts_message' => 'Encontramos :count tour em rascunho que você ainda não concluiu.',
            'current_step'            => 'Passo atual',
            'step'                    => 'Passo',

            // Ações do modal
            'continue_draft'      => 'Continuar com este rascunho',
            'delete_all_drafts'   => 'Excluir todos os rascunhos',
            'create_new_anyway'   => 'Criar novo tour mesmo assim',

            // Informações adicionais
            'drafts_info' => 'Você pode continuar editando um rascunho existente, excluí-lo individualmente, excluir todos os rascunhos ou criar um novo tour ignorando os atuais.',

            // Confirmações de exclusão
            'confirm_delete_title'        => 'Excluir este rascunho?',
            'confirm_delete_message'      => 'Esta ação não pode ser desfeita. O rascunho será excluído permanentemente:',
            'confirm_delete_all_title'    => 'Excluir todos os rascunhos?',
            'confirm_delete_all_message'  => 'Serão excluídos permanentemente :count rascunho(s). Esta ação não pode ser desfeita.',

            // Mensagens de sucesso
            'draft_deleted'       => 'Rascunho excluído com sucesso.',
            'all_drafts_deleted'  => ':count rascunho(s) excluído(s) com sucesso.',
            'continuing_draft'    => 'Continuando com seu rascunho...',

            // Mensagens de erro
            'not_a_draft' => 'Este tour já não é um rascunho e não pode ser editado pelo assistente.',
        ],

        'title' => 'Tours',

        'fields' => [
            'id'           => 'ID',
            'name'         => 'Nome',
            'details'      => 'Detalhes',
            'price'        => 'Preços',
            'overview'     => 'Resumo',
            'amenities'    => 'Amenidades',
            'exclusions'   => 'Exclusões',
            'itinerary'    => 'Itinerário',
            'languages'    => 'Idiomas',
            'schedules'    => 'Horários',
            'adult_price'  => 'Preço adulto',
            'kid_price'    => 'Preço criança',
            'length_hours' => 'Duração (horas)',
            'max_capacity' => 'Capacidade máxima',
            'type'         => 'Tipo de tour',
            'viator_code'  => 'Código Viator',
            'status'       => 'Estado',
            'actions'      => 'Ações',
            'group_size'   => 'Tamanho do grupo',
        ],

        'pricing' => [
            'configured_categories'      => 'Categorias configuradas',
            'create_category'            => 'Criar categoria',
            'note_title'                 => 'Nota:',
            'note_text'                  => 'Defina aqui os preços base para cada categoria de cliente.',
            'manage_detailed_hint'       => ' Para gestão detalhada, use o botão "Gerenciar preços detalhados" acima.',
            'price_usd'                  => 'Preço (USD)',
            'min_quantity'               => 'Quantidade mínima',
            'max_quantity'               => 'Quantidade máxima',
            'status'                     => 'Estado',
            'active'                     => 'Ativo',
            'no_categories'              => 'Não há categorias de clientes configuradas.',
            'create_categories_first'    => 'Crie categorias primeiro',
            'page_title'                 => 'Preços - :name',
            'header_title'               => 'Preços: :name',
            'back_to_tours'              => 'Voltar aos tours',

            'configured_title'           => 'Categorias e preços configurados',
            'empty_title'                => 'Não há categorias configuradas para este tour.',
            'empty_hint'                 => 'Use o formulário à direita para adicionar categorias.',

            'save_changes'               => 'Salvar alterações',
            'auto_disable_note'          => 'Preços em $0 são desativados automaticamente',

            'add_category'               => 'Adicionar categoria',

            'all_assigned_title'         => 'Todas as categorias estão atribuídas',
            'all_assigned_text'          => 'Não há mais categorias disponíveis para este tour.',

            'info_title'                 => 'Informação',
            'tour_label'                 => 'Tour',
            'configured_count'           => 'Categorias configuradas',
            'active_count'               => 'Categorias ativas',

            'fields_title'               => 'Campos',
            'rules_title'                => 'Regras',

            'field_price'                => 'Preço',
            'field_min'                  => 'Mínimo',
            'field_max'                  => 'Máximo',
            'field_status'               => 'Estado',

            'rule_min_le_max'            => 'O mínimo deve ser menor ou igual ao máximo',
            'rule_zero_disable'          => 'Preços em $0 são desativados automaticamente',
            'rule_only_active'           => 'Somente as categorias ativas aparecem no site público',

            'status_active'              => 'Ativo',
            'add_existing_category'      => 'Adicionar categoria existente',
            'choose_category_placeholder'=> 'Selecione uma categoria…',
            'add_button'                 => 'Adicionar',
            'add_existing_hint'          => 'Adicione apenas as categorias de cliente necessárias para este tour.',
            'remove_category'            => 'Remover categoria',
            'category_already_added'     => 'Esta categoria já foi adicionada ao tour.',
            'no_prices_preview'          => 'Ainda não há preços configurados.',
            'already_added'              => 'Esta categoria já foi adicionada ao tour.',
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
                'age_to_empty_means_plus' => 'Se você deixar a idade máxima em branco, será interpretado como "+" (por exemplo 12+).',
                'min_le_max'              => 'O mínimo deve ser menor ou igual ao máximo.',
            ],

            'errors' => [
                'min_le_max' => 'O mínimo deve ser menor ou igual ao máximo.',
            ],
        ],

        'schedules_form' => [
            'available_title'       => 'Horários disponíveis',
            'select_hint'           => 'Selecione os horários para este tour',
            'no_schedules'          => 'Não há horários disponíveis.',
            'create_schedules_link' => 'Criar horários',

            'create_new_title'      => 'Criar novo horário',
            'label_placeholder'     => 'Ex.: Manhã, Tarde',
            'create_and_assign'     => 'Criar este horário e atribuí-lo ao tour',

            'info_title'            => 'Informação',
            'schedules_title'       => 'Horários',
            'schedules_text'        => 'Selecione um ou mais horários em que este tour estará disponível.',
            'create_block_title'    => 'Criar novo',
            'create_block_text'     => 'Se você precisar de um horário que não exista, pode criá-lo aqui marcando a opção "Criar este horário e atribuí-lo ao tour".',

            'current_title'         => 'Horários atuais',
            'none_assigned'         => 'Sem horários atribuídos',
        ],

        'summary' => [
            'preview_title'       => 'Pré-visualização do tour',
            'preview_text_create' => 'Revise todas as informações antes de criar o tour.',
            'preview_text_update' => 'Revise todas as informações antes de atualizar o tour.',

            'basic_details_title' => 'Detalhes básicos',
            'description_title'   => 'Descrição',
            'prices_title'        => 'Preços por categoria',
            'schedules_title'     => 'Horários',
            'languages_title'     => 'Idiomas',
            'itinerary_title'     => 'Itinerário',

            'table' => [
                'category' => 'Categoria',
                'price'    => 'Preço',
                'min_max'  => 'Mín-Máx',
                'status'   => 'Estado',
            ],

            'not_specified'      => 'Sem especificar',
            'slug_autogenerated' => 'Será gerado automaticamente',
            'no_description'     => 'Sem descrição',
            'no_active_prices'   => 'Sem preços ativos configurados',
            'no_languages'       => 'Sem idiomas atribuídos',
            'none_included'      => 'Nada incluído especificado',
            'none_excluded'      => 'Nada excluído especificado',

            'units' => [
                'hours'  => 'horas',
                'people' => 'pessoas',
            ],

            'create_note' => 'Horários, preços, idiomas e amenidades serão exibidos aqui depois de salvar o tour.',
        ],

        'alerts' => [
            'delete_title'             => 'Excluir tour?',
            'delete_text'              => 'O tour será movido para Excluídos. Você poderá restaurá-lo depois.',
            'purge_title'              => 'Excluir definitivamente?',
            'purge_text'               => 'Esta ação é irreversível.',
            'purge_text_with_bookings' => 'Este tour possui :count reserva(s). Elas não serão excluídas; apenas ficarão sem tour associado.',
            'toggle_question_active'   => 'Desativar tour?',
            'toggle_question_inactive' => 'Ativar tour?',
        ],

        'flash' => [
            'created'      => 'Tour criado com sucesso.',
            'updated'      => 'Tour atualizado com sucesso.',
            'deleted_soft' => 'Tour movido para Excluídos.',
            'restored'     => 'Tour restaurado com sucesso.',
            'purged'       => 'Tour excluído definitivamente.',
            'toggled_on'   => 'Tour ativado.',
            'toggled_off'  => 'Tour desativado.',
        ],

        'table' => [
            'id'           => 'ID',
            'name'         => 'Nome',
            'overview'     => 'Resumo',
            'amenities'    => 'Amenidades',
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
            'slug'         => 'URL',
            'prices'       => 'Preços',
            'capacity'     => 'Capacidade',
            'group_size'   => 'Máx. grupo',
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
            'group_size' => 'Tamanho do grupo por guia ou geral para este tour. (Este dado é exibido nas informações do produto)',
        ],

        'success' => [
            'created'     => 'O tour foi criado corretamente.',
            'updated'     => 'O tour foi atualizado corretamente.',
            'deleted'     => 'O tour foi excluído.',
            'toggled'     => 'O estado do tour foi atualizado.',
            'activated'   => 'Tour ativado corretamente.',
            'deactivated' => 'Tour desativado corretamente.',
            // novos
            'archived'    => 'Tour arquivado corretamente.',
            'restored'    => 'Tour restaurado corretamente.',
            'purged'      => 'Tour excluído permanentemente.',
        ],

        'error' => [
            'create'    => 'Não foi possível criar o tour.',
            'update'    => 'Não foi possível atualizar o tour.',
            'delete'    => 'Não foi possível excluir o tour.',
            'toggle'    => 'Não foi possível alterar o estado do tour.',
            'not_found' => 'O tour não existe.',
            // novos
            'restore'            => 'Não foi possível restaurar o tour.',
            'purge'              => 'Não foi possível excluir permanentemente o tour.',
            'purge_has_bookings' => 'Não é possível excluir permanentemente: o tour possui reservas.',
        ],

        'ui' => [
            'add_tour_type'       => 'Adicionar tipo de tour',
            'back'                => 'Voltar',
            'page_title'          => 'Gestão de tours',
            'page_heading'        => 'Gestão de tours',
            'create_title'        => 'Registrar tour',
            'edit_title'          => 'Editar tour',
            'delete_title'        => 'Excluir tour',
            'cancel'              => 'Cancelar',
            'save'                => 'Salvar',
            'save_changes'        => 'Salvar alterações',
            'update'              => 'Atualizar',
            'delete_confirm'      => 'Excluir este tour?',
            'toggle_on'           => 'Ativar',
            'toggle_off'          => 'Desativar',
            'toggle_on_title'     => 'Ativar tour?',
            'toggle_off_title'    => 'Desativar tour?',
            'toggle_on_button'    => 'Sim, ativar',
            'toggle_off_button'   => 'Sim, desativar',
            'see_more'            => 'Ver mais',
            'see_less'            => 'Ocultar',
            'load_more'           => 'Carregar mais',
            'loading'             => 'Carregando...',
            'load_more_error'     => 'Não foi possível carregar mais tours.',
            'confirm_title'       => 'Confirmação',
            'confirm_text'        => 'Você deseja confirmar esta ação?',
            'yes_confirm'         => 'Sim, confirmar',
            'no_confirm'          => 'Não, cancelar',
            'add_tour'            => 'Adicionar tour',
            'edit_tour'           => 'Editar tour',
            'delete_tour'         => 'Excluir tour',
            'toggle_tour'         => 'Ativar/Desativar tour',
            'view_cart'           => 'Ver carrinho',
            'add_to_cart'         => 'Adicionar ao carrinho',
            'slug_help'           => 'Identificador do tour na URL (sem espaços nem acentos)',
            'generate_auto'       => 'Gerar automaticamente',
            'slug_preview_label'  => 'Pré-visualização',
            'saved'               => 'Salvo',
            // chaves extras de UI
            'available_languages'    => 'Idiomas disponíveis',
            'default_capacity'       => 'Capacidade padrão',
            'create_new_schedules'   => 'Criar novos horários',
            'multiple_hint_ctrl_cmd' => 'Mantenha CTRL/CMD pressionado para selecionar vários',
            'use_existing_schedules' => 'Usar horários existentes',
            'add_schedule'           => 'Adicionar horário',
            'schedules_title'        => 'Horários do tour',
            'amenities_included'     => 'Amenidades incluídas',
            'amenities_excluded'     => 'Amenidades não incluídas',
            'color'                  => 'Cor do tour',
            'remove'                 => 'Remover',
            'choose_itinerary'       => 'Escolher itinerário',
            'select_type'            => 'Selecionar tipo',
            'empty_means_default'    => 'Padrão',
            'actives'                => 'Ativos',
            'inactives'              => 'Inativos',
            'archived'               => 'Arquivados',
            'all'                    => 'Todos',
            'help_title'             => 'Ajuda',
            'amenities_included_hint'=> 'Selecione o que está incluído no tour.',
            'amenities_excluded_hint'=> 'Selecione o que NÃO está incluído no tour.',
            'help_included_title'    => 'Incluído',
            'help_included_text'     => 'Marque tudo o que está incluído no preço do tour (transporte, refeições, entradas, equipamento, guia etc.).',
            'help_excluded_title'    => 'Não incluído',
            'help_excluded_text'     => 'Marque o que o cliente deve pagar à parte ou trazer (gorjetas, bebidas alcoólicas, souvenirs etc.).',
            'select_or_create_title' => 'Selecionar ou criar itinerário',
            'select_existing_items'  => 'Selecionar itens existentes',
            'name_hint'              => 'Nome identificador para este itinerário',
            'click_add_item_hint'    => 'Clique em "Adicionar item" para criar novos itens',
            'scroll_hint'            => 'Deslize horizontalmente para ver mais colunas',
            'no_schedules'           => 'Sem horários',
            'no_prices'              => 'Sem preços configurados',
            'edit'                   => 'Editar',
            'slug_auto'              => 'Será gerado automaticamente',
            'added_to_cart'          => 'Adicionado ao carrinho',
            'add_language'           => 'Adicionar idioma',
            'added_to_cart_text'     => 'O tour foi adicionado ao carrinho corretamente.',
            'amenities_excluded_auto_hint' => 'Por padrão, marcamos como “não incluídas” todas as amenidades que você não marcou como incluídas. Você pode desmarcar as que não se aplicam ao tour.',
            'quick_create_language_hint'    => 'Adicione um novo idioma rapidamente se ele não aparecer na lista.',
            'quick_create_type_hint'        => 'Adicione um novo tipo de tour rapidamente se ele não aparecer na lista.',

            'none' => [
                'amenities'       => 'Sem amenidades',
                'exclusions'      => 'Sem exclusões',
                'itinerary'       => 'Sem itinerário',
                'itinerary_items' => 'Sem itens',
                'languages'       => 'Sem idiomas',
                'schedules'       => 'Sem horários',
            ],

            // ações de arquivamento/restauração/limpeza
            'archive' => 'Arquivar',
            'restore' => 'Restaurar',
            'purge'   => 'Excluir definitivamente',

            'confirm_archive_title' => 'Arquivar tour?',
            'confirm_archive_text'  => 'O tour ficará indisponível para novas reservas, mas as reservas existentes serão mantidas.',
            'confirm_purge_title'   => 'Excluir definitivamente',
            'confirm_purge_text'    => 'Esta ação é irreversível e só é permitida se o tour nunca tiver tido reservas.',

            // Filtros de estado
            'filters' => [
                'active'   => 'Ativos',
                'inactive' => 'Inativos',
                'archived' => 'Arquivados',
                'all'      => 'Todos',
            ],

            // Toolbar de fonte
            'font_decrease_title' => 'Diminuir tamanho da fonte',
            'font_increase_title' => 'Aumentar tamanho da fonte',
        ],

    ],

    // =========================================================
    // [08] IMAGES
    // =========================================================
    'image' => [

        'limit_reached_title' => 'Limite alcançado',
        'limit_reached_text'  => 'Foi alcançado o limite de imagens para este tour.',
        'upload_success'      => 'Imagens enviadas corretamente.',
        'upload_none'         => 'Nenhuma imagem foi enviada.',
        'upload_truncated'    => 'Alguns arquivos foram ignorados devido ao limite por tour.',
        'done'                => 'Concluído',
        'notice'              => 'Aviso',
        'saved'               => 'Salvar',
        'caption_updated'     => 'Legenda atualizada corretamente.',
        'deleted'             => 'Excluído',
        'image_removed'       => 'Imagem excluída corretamente.',
        'invalid_order'       => 'Ordem inválida.',
        'nothing_to_reorder'  => 'Nada para reordenar.',
        'order_saved'         => 'Ordem salva.',
        'cover_updated_title' => 'Atualizar capa',
        'cover_updated_text'  => 'Esta imagem agora é a capa.',
        'deleting'            => 'Excluindo...',

        'ui' => [
            // Página de seleção de tour
            'page_title_pick'     => 'Imagens de tours',
            'page_heading'        => 'Imagens de tours',
            'choose_tour'         => 'Escolher tour',
            'search_placeholder'  => 'Buscar por ID ou nome…',
            'search_button'       => 'Buscar',
            'no_results'          => 'Nenhum tour encontrado.',
            'manage_images'       => 'Gerenciar imagens',
            'cover_alt'           => 'Capa',
            'images_label'        => 'imagens',

            // Botões genéricos
            'upload_btn'          => 'Enviar',
            'delete_btn'          => 'Excluir',
            'show_btn'            => 'Mostrar',
            'close_btn'           => 'Fechar',
            'preview_title'       => 'Pré-visualização da imagem',

            // Textos gerais de estado
            'error_title'         => 'Erro',
            'warning_title'       => 'Atenção',
            'success_title'       => 'Sucesso',
            'cancel_btn'          => 'Cancelar',

            // Confirmações básicas
            'confirm_delete_title' => 'Excluir esta imagem?',
            'confirm_delete_text'  => 'Esta ação não pode ser desfeita.',

            // Gestão de capa por formulário clássico
            'cover_current_title'    => 'Capa atual',
            'upload_new_cover_title' => 'Enviar nova capa',
            'cover_file_label'       => 'Arquivo de capa',
            'file_help_cover'        => 'JPEG/PNG/WebP, máx. 30 MB.',
            'id_label'               => 'ID',

            // Navegação / cabeçalho na vista de um tour
            'back_btn'              => 'Voltar à lista',

            // Stats (barra superior)
            'stats_images'          => 'Imagens enviadas',
            'stats_cover'           => 'Capas definidas',
            'stats_selected'        => 'Selecionadas',

            // Zona de envio
            'drag_or_click'         => 'Arraste e solte suas imagens ou clique para selecionar.',
            'upload_help'           => 'Formatos permitidos: JPG, PNG, WebP. Tamanho máximo total 100 MB.',
            'select_btn'            => 'Escolher arquivos',
            'limit_badge'           => 'Limite de :max imagens alcançado',
            'files_word'            => 'arquivos',

            // Toolbar de seleção múltipla
            'select_all'            => 'Selecionar todas',
            'delete_selected'       => 'Excluir selecionadas',
            'delete_all'            => 'Excluir todas',

            // Seletor por imagem (chip)
            'select_image_title'    => 'Selecionar esta imagem',
            'select_image_aria'     => 'Selecionar imagem :id',

            // Capa (chip / botão por cartão)
            'cover_label'           => 'Capa',
            'cover_btn'             => 'Definir como capa',

            // Estados de salvamento / helpers JS
            'caption_placeholder'   => 'Legenda (opcional)',
            'saving_label'          => 'Salvando…',
            'saving_fallback'       => 'Salvando…',
            'none_label'            => 'Sem legenda',
            'limit_word'            => 'Limite',

            // Confirmações avançadas (JS)
            'confirm_set_cover_title' => 'Definir como capa?',
            'confirm_set_cover_text'  => 'Esta imagem será a capa principal do tour.',
            'confirm_btn'             => 'Sim, continuar',

            'confirm_bulk_delete_title' => 'Excluir imagens selecionadas?',
            'confirm_bulk_delete_text'  => 'As imagens selecionadas serão excluídas definitivamente.',

            'confirm_delete_all_title'  => 'Excluir todas as imagens?',
            'confirm_delete_all_text'   => 'Todas as imagens deste tour serão excluídas.',

            // Vista sem imagens
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
            'too_large'      => 'O arquivo excede o tamanho máximo permitido. Tente com uma imagem menor.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Preços - :name',
            'header_title'       => 'Preços: :name',
            'back_to_tours'      => 'Voltar aos tours',

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
            'field_status'       => 'Estado',

            'rule_min_le_max'    => 'O mínimo deve ser menor ou igual ao máximo',
            'rule_zero_disable'  => 'Preços em $0 são desativados automaticamente',
            'rule_only_active'   => 'Somente as categorias ativas aparecem no site público',
        ],

        'table' => [
            'category'   => 'Categoria',
            'age_range'  => 'Faixa etária',
            'price_usd'  => 'Preço (USD)',
            'min'        => 'Mín',
            'max'        => 'Máx',
            'status'     => 'Estado',
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
            'create_disabled_hint' => 'Se o preço for $0, a categoria será criada desativada',
            'add'                  => 'Adicionar',
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
            'success_text'          => 'A categoria foi criada corretamente e adicionada ao tour.',
            'error_title'           => 'Erro',
            'error_generic'         => 'Ocorreu um problema ao criar a categoria.',
            'created_ok'            => 'Categoria criada corretamente.',
        ],
    ],

    'ajax' => [
        'category_created'  => 'Categoria criada com sucesso',
        'category_error'    => 'Erro ao criar a categoria',
        'language_created'  => 'Idioma criado com sucesso',
        'language_error'    => 'Erro ao criar o idioma',
        'amenity_created'   => 'Amenidade criada com sucesso',
        'amenity_error'     => 'Erro ao criar a amenidade',
        'schedule_created'  => 'Horário criado com sucesso',
        'schedule_error'    => 'Erro ao criar o horário',
        'itinerary_created' => 'Itinerário criado com sucesso',
        'itinerary_error'   => 'Erro ao criar o itinerário',
        'translation_error' => 'Erro ao traduzir',
    ],

    'modal' => [
        'create_category'  => 'Criar nova categoria',
        'create_language'  => 'Criar novo idioma',
        'create_amenity'   => 'Criar nova amenidade',
        'create_schedule'  => 'Criar novo horário',
        'create_itinerary' => 'Criar novo itinerário',
    ],

    'validation' => [
        'slug_taken'     => 'Este slug já está em uso',
        'slug_available' => 'Slug disponível',
    ],

    'tour_type' => [
        'fields' => [
            'name'        => 'Nome',
            'description' => 'Descrição',
            'status'      => 'Estado',
        ],
    ],
];
