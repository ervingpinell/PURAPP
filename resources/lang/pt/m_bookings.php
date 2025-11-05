<?php

return [

    'messages' => [
        'date_no_longer_available' => 'A data :date não está mais disponível para reserva (mínimo: :min).',
        'limited_seats_available' => 'Restam apenas :available vagas para “:tour” em :date.',
        'bookings_created_from_cart' => 'Suas reservas foram criadas com sucesso a partir do carrinho.',
        'capacity_exceeded' => 'Capacidade excedida',
        'meeting_point_hint' => 'Apenas o nome do ponto de encontro é exibido na lista.',
    ],

    'validation' => [
        'max_persons_exceeded' => 'Máximo de :max pessoas por reserva no total.',
        'min_adults_required' => 'São necessários no mínimo :min adultos por reserva.',
        'max_kids_exceeded' => 'Máximo de :max crianças por reserva.',
        'no_active_categories' => 'Este tour não possui categorias de clientes ativas.',
        'min_category_not_met' => 'São necessárias ao menos :min pessoas na categoria “:category”.',
        'max_category_exceeded' => 'Máximo de :max pessoas permitido na categoria “:category”.',
        'min_one_person_required' => 'Pelo menos uma pessoa é necessária na reserva.',
        'category_not_available' => 'A categoria com ID :category_id não está disponível para este tour.',
    ],

    // =========================================================
    // [01] DISPONIBILIDADE
    // =========================================================
    'availability' => [
        'fields' => [
            'tour'        => 'Tour',
            'date'        => 'Data',
            'start_time'  => 'Hora de início',
            'end_time'    => 'Hora de término',
            'available'   => 'Disponível',
            'is_active'   => 'Ativo',
        ],

        'success' => [
            'created'     => 'Disponibilidade criada com sucesso.',
            'updated'     => 'Disponibilidade atualizada com sucesso.',
            'deactivated' => 'Disponibilidade desativada com sucesso.',
        ],

        'error' => [
            'create'     => 'Não foi possível criar a disponibilidade.',
            'update'     => 'Não foi possível atualizar a disponibilidade.',
            'deactivate' => 'Não foi possível desativar a disponibilidade.',
        ],

        'validation' => [
            'tour_id' => [
                'required' => 'O :attribute é obrigatório.',
                'integer'  => 'O :attribute deve ser um número inteiro.',
                'exists'   => 'O :attribute selecionado não existe.',
            ],
            'date' => [
                'required'    => 'A :attribute é obrigatória.',
                'date_format' => 'A :attribute deve estar no formato YYYY-MM-DD.',
            ],
            'start_time' => [
                'date_format'   => 'A :attribute deve estar no formato HH:MM (24h).',
                'required_with' => 'A :attribute é obrigatória quando a hora de término é especificada.',
            ],
            'end_time' => [
                'date_format'    => 'A :attribute deve estar no formato HH:MM (24h).',
                'after_or_equal' => 'A :attribute deve ser maior ou igual à hora de início.',
            ],
            'available' => [
                'boolean' => 'O campo :attribute é inválido.',
            ],
            'is_active' => [
                'boolean' => 'O :attribute é inválido.',
            ],
        ],

        'ui' => [
            'page_title'           => 'Disponibilidade',
            'page_heading'         => 'Disponibilidade',
            'blocked_page_title'   => 'Tours bloqueados',
            'blocked_page_heading' => 'Tours bloqueados',
            'tours_count'          => '( :count tours )',
            'blocked_count'        => '( :count bloqueados )',
        ],

        'filters' => [
            'date'               => 'Data',
            'days'               => 'Dias',
            'product'            => 'Produto',
            'search_placeholder' => 'Buscar tour…',
            'update_state'       => 'Atualizar estado',
            'view_blocked'       => 'Ver bloqueados',
            'tip'                => 'Dica: marque as linhas e use uma ação no menu.',
        ],

        'blocks' => [
            'am_tours'    => 'Tours AM (todos os tours que iniciam antes das 12:00h)',
            'pm_tours'    => 'Tours PM (todos os tours que iniciam após as 12:00h)',
            'am_blocked'  => 'AM bloqueados',
            'pm_blocked'  => 'PM bloqueados',
            'empty_block' => 'Não há tours neste bloco.',
            'empty_am'    => 'Não há tours AM bloqueados.',
            'empty_pm'    => 'Não há tours PM bloqueados.',
            'no_data'     => 'Não há dados para os filtros selecionados.',
            'no_blocked'  => 'Não há tours bloqueados no intervalo selecionado.',
        ],

        'states' => [
            'available' => 'Disponível',
            'blocked'   => 'Bloqueado',
        ],

        'buttons' => [
            'mark_all'         => 'Marcar todos',
            'unmark_all'       => 'Desmarcar todos',
            'block_all'        => 'Bloquear todos',
            'unblock_all'      => 'Desbloquear todos',
            'block_selected'   => 'Bloquear selecionados',
            'unblock_selected' => 'Desbloquear selecionados',
            'back'             => 'Voltar',
            'open'             => 'Abrir',
            'cancel'           => 'Cancelar',
            'block'            => 'Bloquear',
            'unblock'          => 'Desbloquear',
        ],

        'confirm' => [
            'view_blocked_title'    => 'Ver tours bloqueados',
            'view_blocked_text'     => 'A vista com os tours bloqueados será aberta para liberá-los.',
            'block_title'           => 'Bloquear tour?',
            'block_html'            => '<b>:label</b> será bloqueado para a data <b>:day</b>.',
            'block_btn'             => 'Sim, bloquear',
            'unblock_title'         => 'Desbloquear tour?',
            'unblock_html'          => '<b>:label</b> será desbloqueado para a data <b>:day</b>.',
            'unblock_btn'           => 'Sim, desbloquear',
            'bulk_title'            => 'Confirmar ação',
            'bulk_items_html'       => 'Itens a serem afetados: <b>:count</b>.',
            'bulk_block_day_html'   => 'Bloquear todos os disponíveis no dia <b>:day</b>',
            'bulk_block_block_html' => 'Bloquear todos os disponíveis no bloco <b>:block</b> no dia <b>:day</b>',
        ],

        'toasts' => [
            'applying_filters'   => 'Aplicando filtros…',
            'searching'          => 'Buscando…',
            'updating_range'     => 'Atualizando intervalo…',
            'invalid_date_title' => 'Data inválida',
            'invalid_date_text'  => 'Datas passadas não são permitidas.',
            'marked_n'           => 'Marcados :n',
            'unmarked_n'         => 'Desmarcados :n',
            'updated'            => 'Alteração aplicada',
            'updated_count'      => 'Atualizados: :count',
            'unblocked_count'    => 'Desbloqueados: :count',
            'no_selection_title' => 'Sem seleção',
            'no_selection_text'  => 'Marque pelo menos um tour.',
            'no_changes_title'   => 'Sem alterações',
            'no_changes_text'    => 'Não há itens aplicáveis.',
            'error_generic'      => 'Não foi possível completar a atualização.',
            'error_update'       => 'Não foi possível atualizar.',
        ],
    ],

    // =========================================================
    // [02] RESERVAS
    // =========================================================
    'bookings' => [
        'ui' => [
            'page_title'         => 'Reservas',
            'page_heading'       => 'Gestão de Reservas',
            'register_booking'   => 'Registrar Reserva',
            'add_booking'        => 'Adicionar Reserva',
            'edit_booking'       => 'Editar Reserva',
            'booking_details'    => 'Detalhes da Reserva',
            'download_receipt'   => 'Baixar recibo',
            'actions'            => 'Ações',
            'view_details'       => 'Ver Detalhes',
            'click_to_view'      => 'Clique para ver detalhes',
            'zoom_in'            => 'Aproximar',
            'zoom_out'           => 'Afastar',
            'zoom_reset'         => 'Redefinir Zoom',
            'no_promo'           => 'Nenhum código promocional aplicado',
            'create_booking'     => 'Criar Reserva',
            'booking_info'       => 'Informação da Reserva',
            'select_customer'    => 'Selecionar cliente',
            'select_tour'        => 'Selecionar tour',
            'select_tour_first'  => 'Selecione um tour primeiro',
            'select_option'      => 'Selecionar',
            'select_tour_to_see_categories' => 'Selecione um tour para ver as categorias',
            'loading'            => 'Carregando…',
            'no_results'         => 'Sem resultados',
            'error_loading'      => 'Erro ao carregar dados',
            'tour_without_categories' => 'Este tour não possui categorias configuradas',
            'verifying'          => 'Verificando…',
        ],

        'fields' => [
            'booking_id'        => 'ID da Reserva',
            'status'            => 'Estado',
            'booking_date'      => 'Data da Reserva',
            'booking_origin'    => 'Data da Reserva (origem)',
            'reference'         => 'Referência',
            'customer'          => 'Cliente',
            'email'             => 'E-mail',
            'phone'             => 'Telefone',
            'tour'              => 'Tour',
            'language'          => 'Idioma',
            'tour_date'         => 'Data do Tour',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Nome de outro hotel',
            'meeting_point'     => 'Ponto de Encontro',
            'pickup_location'   => 'Local de Retirada',
            'schedule'          => 'Horário',
            'type'              => 'Tipo',
            'adults'            => 'Adultos',
            'adults_quantity'   => 'Quantidade de Adultos',
            'children'          => 'Crianças',
            'children_quantity' => 'Quantidade de Crianças',
            'promo_code'        => 'Código promocional',
            'total'             => 'Total',
            'total_to_pay'      => 'Total a Pagar',
            'adult_price'       => 'Preço Adulto',
            'child_price'       => 'Preço Criança',
            'notes'             => 'Notas',
            'hotel_name'        => 'Nome do Hotel',
            'travelers'         => 'Viajantes',
            'subtotal'          => 'Subtotal',
            'discount'          => 'Desconto',
            'total_persons'     => 'Pessoas',
        ],

        'placeholders' => [
            'select_customer'  => 'Selecionar cliente',
            'select_tour'      => 'Selecionar um tour',
            'select_schedule'  => 'Selecionar um horário',
            'select_language'  => 'Selecionar idioma',
            'select_hotel'     => 'Selecionar hotel',
            'select_point'     => 'Selecionar ponto de encontro',
            'select_status'    => 'Selecionar estado',
            'enter_hotel_name' => 'Insira o nome do hotel',
            'enter_promo_code' => 'Insira o código promocional',
            'other'            => 'Outro…',
        ],

        'statuses' => [
            'pending'   => 'Pendente',
            'confirmed' => 'Confirmada',
            'cancelled' => 'Cancelada',
        ],

        'buttons' => [
            'save'            => 'Salvar',
            'cancel'          => 'Cancelar',
            'edit'            => 'Editar',
            'delete'          => 'Excluir',
            'confirm_changes' => 'Confirmar alterações',
            'apply'           => 'Aplicar',
            'update'          => 'Atualizar',
            'close'           => 'Fechar',
            'back'            => 'Voltar',
        ],

        'meeting_point' => [
            'time'     => 'Hora:',
            'view_map' => 'Ver mapa',
        ],

        'pricing' => [
            'title' => 'Resumo de Preços',
        ],

        'optional' => 'opcional',

        'messages' => [
            'past_booking_warning'   => 'Esta reserva corresponde a uma data passada e não pode ser editada.',
            'tour_archived_warning'  => 'O tour desta reserva foi excluído/arquivado e não pode ser carregado. Selecione um tour para ver os seus horários.',
            'no_schedules'           => 'Não há horários disponíveis',
            'deleted_tour'           => 'Tour excluído',
            'deleted_tour_snapshot'  => 'Tour excluído (:name)',
            'tour_archived'          => '(arquivado)',
            'meeting_point_hint'     => 'Apenas o nome do ponto de encontro é exibido na lista.',
            'customer_locked'        => 'O cliente está bloqueado e não pode ser editado.',
            'promo_applied_subtract' => 'Desconto aplicado:',
            'promo_applied_add'      => 'Cobrança aplicada:',
            'hotel_locked_by_meeting_point' => 'Um ponto de encontro foi selecionado; não é possível selecionar hotel.',
            'meeting_point_locked_by_hotel' => 'Um hotel foi selecionado; não é possível selecionar ponto de encontro.',
            'promo_removed'          => 'Código promocional removido',
        ],

        'alerts' => [
            'error_summary' => 'Por favor, corrija os seguintes erros:',
        ],

        'validation' => [
            'past_date'       => 'Você não pode reservar para datas anteriores a hoje.',
            'promo_required'  => 'Insira um código promocional primeiro.',
            'promo_checking'  => 'Verificando código…',
            'promo_invalid'   => 'Código promocional inválido.',
            'promo_error'     => 'Não foi possível validar o código.',
            'promo_empty'     => 'Digite um código primeiro.',
            'promo_needs_subtotal' => 'Adicione pelo menos 1 passageiro para calcular o desconto.',
        ],

        'promo' => [
            'applied'         => 'Código aplicado',
            'applied_percent' => 'Código aplicado: -:percent%',
            'applied_amount'  => 'Código aplicado: -$:amount',
        ],

        'loading' => [
            'saving'     => 'Salvando…',
            'validating' => 'Validando…',
            'updating'   => 'Atualizando…',
        ],

        'success' => [
            'created'          => 'Reserva criada com sucesso.',
            'updated'          => 'Reserva atualizada com sucesso.',
            'deleted'          => 'Reserva excluída com sucesso.',
            'status_updated'   => 'Estado da reserva atualizado com sucesso.',
            'status_confirmed' => 'Reserva confirmada com sucesso.',
            'status_cancelled' => 'Reserva cancelada com sucesso.',
            'status_pending'   => 'Reserva definida como pendente com sucesso.',
        ],

        'errors' => [
            'create'               => 'Não foi possível criar a reserva.',
            'update'               => 'Não foi possível atualizar a reserva.',
            'delete'               => 'Não foi possível excluir a reserva.',
            'status_update_failed' => 'Não foi possível atualizar o estado da reserva.',
            'detail_not_found'     => 'Detalhes da reserva não encontrados.',
            'schedule_not_found'   => 'Horário não encontrado.',
            'insufficient_capacity'=> 'Não há capacidade suficiente para “:tour” em :date às :time. Solicitado: :requested, disponível: :available (máx: :max).',
        ],

        'confirm' => [
            'delete' => 'Tem certeza de que deseja excluir esta reserva?',
        ],
    ],

    // =========================================================
    // [03] AÇÕES
    // =========================================================
    'actions' => [
        'confirm'        => 'Confirmar',
        'cancel'         => 'Cancelar Reserva',
        'confirm_cancel' => 'Tem certeza de que deseja cancelar esta reserva?',
    ],

    // =========================================================
    // [04] FILTROS
    // =========================================================
    'filters' => [
        'advanced_filters' => 'Filtros Avançados',
        'dates'            => 'Datas',
        'booked_from'      => 'Reservado de',
        'booked_until'     => 'Reservado até',
        'tour_from'        => 'Tour de',
        'tour_until'       => 'Tour até',
        'all'              => 'Todos',
        'apply'            => 'Aplicar',
        'clear'            => 'Limpar',
        'close_filters'    => 'Fechar filtros',
        'search_reference' => 'Buscar referência…',
        'enter_reference'  => 'Digite referência da reserva',
    ],

    // =========================================================
    // [05] RELATÓRIOS
    // =========================================================
    'reports' => [
        'excel_title'          => 'Exportação de Reservas',
        'pdf_title'            => 'Relatório de Reservas – Green Vacations CR',
        'general_report_title' => 'Relatório Geral de Reservas – Green Vacations Costa Rica',
        'download_pdf'         => 'Baixar PDF',
        'export_excel'         => 'Exportar Excel',
        'coupon'               => 'Cupom',
        'adjustment'           => 'Ajuste',
        'totals'               => 'Totais',
        'adults_qty'           => 'Adultos (x:qty)',
        'kids_qty'             => 'Crianças (x:qty)',
        'people'               => 'Pessoas',
        'subtotal'             => 'Subtotal',
        'discount'             => 'Desconto',
        'surcharge'            => 'Acréscimo',
        'original_price'       => 'Preço original',
        'total_adults'         => 'Total Adultos',
        'total_kids'           => 'Total Crianças',
        'total_people'         => 'Total Pessoas',
    ],

    // =========================================================
    // [06] RECIBO
    // =========================================================
    'receipt' => [
        'title'         => 'Recibo de Reserva',
        'company'       => 'Green Vacations CR',
        'code'          => 'Código',
        'client'        => 'Cliente',
        'tour'          => 'Tour',
        'booking_date'  => 'Data da Reserva',
        'tour_date'     => 'Data do Tour',
        'schedule'      => 'Horário',
        'hotel'         => 'Hotel',
        'meeting_point' => 'Ponto de Encontro',
        'status'        => 'Estado',
        'adults_x'      => 'Adultos (x:count)',
        'kids_x'        => 'Crianças (x:count)',
        'people'        => 'Pessoas',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Desconto',
        'surcharge'     => 'Acréscimo',
        'total'         => 'TOTAL',
        'no_schedule'   => 'Sem horário',
        'qr_alt'        => 'Código QR',
        'qr_scan'       => 'Escaneie para ver a reserva',
        'thanks'        => 'Obrigado por escolher :company!',
    ],

    // =========================================================
    // [07] MODAL DE DETALHES
    // =========================================================
    'details' => [
        'booking_info'  => 'Informação da Reserva',
        'customer_info' => 'Informação do Cliente',
        'tour_info'     => 'Informação do Tour',
        'pricing_info'  => 'Informação de Preços',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Desconto',
    ],

    'excluded_dates' => [

        'ui' => [
            'page_title'           => 'Gestão de Disponibilidade e Capacidade',
            'page_heading'         => 'Gestão de Disponibilidade e Capacidade',
            'tours_count'          => 'tours',
            'blocked_page_title'   => 'Tours bloqueados',
            'blocked_page_heading' => 'Tours bloqueados',
            'blocked_count'        => ':count tours bloqueados',
        ],

        'legend' => [
            'title'                  => 'Legenda de Capacidades',
            'base_tour'              => 'Tour Base',
            'override_schedule'      => 'Substituir Horário',
            'override_day'           => 'Substituir Dia',
            'override_day_schedule'  => 'Substituir Dia+Horário',
            'blocked'                => 'Bloqueado',
        ],

        'filters' => [
            'date'               => 'Data',
            'days'               => 'Dias',
            'product'            => 'Buscar Tour',
            'search_placeholder' => 'Nome do tour…',
            'bulk_actions'       => 'Ações em Massa',
            'update_state'       => 'Atualizar estado',
        ],

        'blocks' => [
            'am'          => 'TOURS AM',
            'pm'          => 'TOURS PM',
            'am_blocked'  => 'TOURS AM (bloqueados)',
            'pm_blocked'  => 'TOURS PM (bloqueados)',
            'empty_am'    => 'Não há tours neste bloco',
            'empty_pm'    => 'Não há tours neste bloco',
            'no_data'     => 'Sem dados para exibir',
            'no_blocked'  => 'Nenhum tour bloqueado para o intervalo selecionado',
        ],

        'buttons' => [
            'mark_all'         => 'Marcar Todos',
            'unmark_all'       => 'Desmarcar Todos',
            'block_all'        => 'Bloquear Todos',
            'unblock_all'      => 'Desbloquear Todos',
            'block_selected'   => 'Bloquear Selecionados',
            'unblock_selected' => 'Desbloquear Selecionados',
            'set_capacity'     => 'Ajustar Capacidade',
            'capacity'         => 'Capacidade',
            'view_blocked'     => 'Ver Bloqueados',
            'capacity_settings'=> 'Configurações de Capacidade',
            'block'            => 'Bloquear',
            'unblock'          => 'Desbloquear',
            'apply'            => 'Aplicar',
            'save'             => 'Salvar',
            'cancel'           => 'Cancelar',
            'back'             => 'Voltar',
        ],

        'states' => [
            'available' => 'Disponível',
            'blocked'   => 'Bloqueado',
        ],

        'badges' => [
            'tooltip_prefix' => 'Ocupado/Capacidade -',
        ],

        'modals' => [
            'capacity_title'            => 'Ajustar Capacidade',
            'selected_capacity_title'   => 'Ajustar Capacidade dos Selecionados',
            'date'                      => 'Data:',
            'hierarchy_title'           => 'Hierarquia de capacidades:',
            'new_capacity'              => 'Nova Capacidade',
            'hint_zero_blocks'          => 'Deixe em 0 para bloquear completamente',
            'selected_count'            => 'A capacidade será atualizada para :count itens selecionados.',
            'capacity_day_title'        => 'Ajustar capacidade para o dia',
            'capacity_day_subtitle'     => 'Todos os horários do dia',
        ],

        'confirm' => [
            'block_title'        => 'Bloquear?',
            'unblock_title'      => 'Desbloquear?',
            'block_html'         => '<strong>:label</strong><br>Data: :day',
            'unblock_html'       => '<strong>:label</strong><br>Data: :day',
            'block_btn'          => 'Bloquear',
            'unblock_btn'        => 'Desbloquear',
            'bulk_title'         => 'Confirmar operação em massa',
            'bulk_items_html'    => ':count itens serão afetados',
            'block_day_title'    => 'Bloquear o dia inteiro',
            'block_block_title'  => 'Bloquear bloco :block em :day',
        ],

        'toasts' => [
            'invalid_date_title' => 'Data inválida',
            'invalid_date_text'  => 'Você não pode selecionar datas passadas',
            'searching'          => 'Buscando…',
            'applying_filters'   => 'Aplicando filtros…',
            'updating_range'     => 'Atualizando intervalo…',
            'no_selection_title' => 'Sem seleção',
            'no_selection_text'  => 'Você deve selecionar pelo menos um item',
            'no_changes_title'   => 'Sem alterações',
            'no_changes_text'    => 'Não há itens para atualizar',
            'marked_n'           => 'Marcados :n itens',
            'unmarked_n'         => 'Desmarcados :n itens',
            'error_generic'      => 'Não foi possível completar a operação',
            'updated'            => 'Atualizado',
            'updated_count'      => ':count itens atualizados',
            'unblocked_count'    => ':count itens desbloqueados',
            'blocked'            => 'Bloqueado',
            'unblocked'          => 'Desbloqueado',
            'capacity_updated'   => 'Capacidade atualizada',
        ],
    ],
'capacity' => [

    'ui' => [
        'page_title'   => 'Gestão de Capacidades',
        'page_heading' => 'Gestão de Capacidades',
    ],

    'tabs' => [
        'global'         => 'Globais',
        'by_tour'        => 'Por Tour + Horário',
        'day_schedules'  => 'Substituições Dia + Horário',
    ],

    'alerts' => [
        'global_info' => '<strong>Capacidades globais:</strong> Define o limite base para cada tour (todos os dias e horários).',
        'by_tour_info' => '<strong>Por Tour + Horário:</strong> Substituição específica de capacidade para cada horário de cada tour. Estas substituições têm prioridade sobre a capacidade global do tour.',
        'day_schedules_info' => '<strong>Dia + Horário:</strong> Substituição de maior prioridade para um dia e horário específicos. Elas são gerenciadas na tela de "Disponibilidade e Capacidade".',
    ],

    'tables' => [
        'global' => [
            'tour'       => 'Tour',
            'type'       => 'Tipo',
            'capacity'   => 'Capacidade Global',
            'level'      => 'Nível',
        ],
        'by_tour' => [
            'schedule'   => 'Horário',
            'capacity'   => 'Substituição de Capacidade',
            'level'      => 'Nível',
            'no_schedules' => 'Este tour não possui horários atribuídos',
        ],
        'day_schedules' => [
            'date'       => 'Data',
            'tour'       => 'Tour',
            'schedule'   => 'Horário',
            'capacity'   => 'Capacidade',
            'actions'    => 'Ações',
            'no_overrides' => 'Não há substituições Dia + Horário',
        ],
    ],

    'badges' => [
        'base'       => 'Base',
        'override'   => 'Substituição',
        'global'     => 'Global',
        'blocked'    => 'BLOQUEADO',
        'unlimited'  => '∞',
    ],

    'buttons' => [
        'save'      => 'Salvar',
        'delete'    => 'Excluir',
        'back'      => 'Voltar',
        'apply'     => 'Aplicar',
        'cancel'    => 'Cancelar',
    ],

    'messages' => [
        'empty_placeholder' => 'Vazio = usa capacidade global (:capacity)',
        'deleted_confirm'   => 'Excluir esta substituição?',
        'no_day_overrides'  => 'Não há substituições Dia + Horário disponíveis.',
    ],

    'toasts' => [
        'success_title' => 'Sucesso',
        'error_title'   => 'Erro',
    ],
],

];
