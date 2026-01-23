<?php

return [

    'messages' => [
        'date_no_longer_available'   => 'A data :date não está mais disponível para reserva (mínimo: :min).',
        'limited_seats_available'    => 'Restam apenas :available vagas para ":tour" em :date.',
        'bookings_created_from_cart' => 'Suas reservas foram criadas com sucesso a partir do carrinho.',
        'capacity_exceeded'          => 'Capacidade excedida',
        'meeting_point_hint'         => 'Na lista é exibido apenas o nome do ponto.',
    ],

    'validation' => [
        'max_persons_exceeded'    => 'Máximo de :max pessoas por reserva no total.',
        'min_adults_required'     => 'São necessários pelo menos :min adultos por reserva.',
        'max_kids_exceeded'       => 'Máximo de :max crianças por reserva.',
        'no_active_categories'    => 'Este tour não possui categorias de clientes ativas.',
        'min_category_not_met'    => 'São necessárias pelo menos :min pessoas na categoria ":category".',
        'max_category_exceeded'   => 'Máximo de :max pessoas permitido na categoria ":category".',
        'min_one_person_required' => 'Deve haver pelo menos uma pessoa na reserva.',
        'category_not_available'  => 'A categoria com ID :category_id não está disponível para este tour.',
        'max_persons_label'       => 'Máximo de pessoas permitidas por reserva',
        'date_range_hint'         => 'Selecione uma data entre :from — :to',
    ],

    // =========================================================
    // [01] DISPONIBILIDADE
    // =========================================================
    'availability' => [
        'fields' => [
            'tour'       => 'Tour',
            'date'       => 'Data',
            'start_time' => 'Hora de início',
            'end_time'   => 'Hora de término',
            'available'  => 'Disponível',
            'is_active'  => 'Ativo',
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
                'required' => 'O campo :attribute é obrigatório.',
                'integer'  => 'O campo :attribute deve ser um número inteiro.',
                'exists'   => 'O :attribute selecionado não existe.',
            ],
            'date' => [
                'required'    => 'O campo :attribute é obrigatório.',
                'date_format' => 'O campo :attribute deve estar no formato AAAA-MM-DD.',
            ],
            'start_time' => [
                'date_format'   => 'O campo :attribute deve estar no formato HH:MM (24h).',
                'required_with' => 'O campo :attribute é obrigatório quando a hora de término é informada.',
            ],
            'end_time' => [
                'date_format'    => 'O campo :attribute deve estar no formato HH:MM (24h).',
                'after_or_equal' => 'O campo :attribute deve ser maior ou igual à hora de início.',
            ],
            'available' => [
                'boolean' => 'O campo :attribute é inválido.',
            ],
            'is_active' => [
                'boolean' => 'O campo :attribute é inválido.',
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
            'search_placeholder' => 'Buscar tour...',
            'update_state'       => 'Atualizar status',
            'view_blocked'       => 'Ver bloqueados',
            'tip'                => 'Dica: marque as linhas e use uma ação do menu.',
        ],

        'blocks' => [
            'am_tours'    => 'Tours AM (todos os tours que iniciam antes das 12h00)',
            'pm_tours'    => 'Tours PM (todos os tours que iniciam depois das 12h00)',
            'am_blocked'  => 'AM bloqueados',
            'pm_blocked'  => 'PM bloqueados',
            'empty_block' => 'Não há tours neste bloco.',
            'empty_am'    => 'Não há tours bloqueados pela manhã.',
            'empty_pm'    => 'Não há tours bloqueados à tarde.',
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
            'view_blocked_text'     => 'A tela de tours bloqueados será aberta para que você possa desbloqueá-los.',
            'block_title'           => 'Bloquear tour?',
            'block_html'            => '<b>:label</b> será bloqueado na data <b>:day</b>.',
            'block_btn'             => 'Sim, bloquear',
            'unblock_title'         => 'Desbloquear tour?',
            'unblock_html'          => '<b>:label</b> será desbloqueado na data <b>:day</b>.',
            'unblock_btn'           => 'Sim, desbloquear',
            'bulk_title'            => 'Confirmar ação',
            'bulk_items_html'       => 'Itens a serem afetados: <b>:count</b>.',
            'bulk_block_day_html'   => 'Bloquear todos os disponíveis no dia <b>:day</b>',
            'bulk_block_block_html' => 'Bloquear todos os disponíveis no bloco <b>:block</b> em <b>:day</b>',
        ],

        'toasts' => [
            'applying_filters'   => 'Aplicando filtros...',
            'searching'          => 'Buscando...',
            'updating_range'     => 'Atualizando intervalo...',
            'invalid_date_title' => 'Data inválida',
            'invalid_date_text'  => 'Datas passadas não são permitidas.',
            'marked_n'           => ':n marcado(s)',
            'unmarked_n'         => ':n desmarcado(s)',
            'updated'            => 'Alteração aplicada',
            'updated_count'      => 'Atualizados: :count',
            'unblocked_count'    => 'Desbloqueados: :count',
            'no_selection_title' => 'Sem seleção',
            'no_selection_text'  => 'Marque pelo menos um tour.',
            'no_changes_title'   => 'Sem alterações',
            'no_changes_text'    => 'Não há itens aplicáveis.',
            'error_generic'      => 'Não foi possível concluir a atualização.',
            'error_update'       => 'Não foi possível atualizar.',
        ],
    ],

    // =========================================================
    // [02] RESERVAS
    // =========================================================
    'tabs' => [
        'general' => 'Geral',
        'active' => 'Ativas',
        'cancelled' => 'Canceladas',
        'pending' => 'Pendentes',
        'trash' => 'Lixeira',
    ],

    'bookings' => [
        'singular' => 'Reserva',
        'plural' => 'Reservas',
        'customer' => 'Cliente',
        'payment_link_info' => 'Link de pagamento para o cliente',
        'regenerate_warning' => 'Aviso: Ao regenerar o link, o anterior deixará de funcionar.',
        'steps' => [
            'customer' => 'Cliente',
            'select_tour_date' => 'Selecionar Tour e Data',
            'select_schedule_language' => 'Selecionar Horário e Idioma',
            'select_participants' => 'Selecionar Participantes',
            'customer_details' => 'Cliente e Detalhes',
        ],
        'ui' => [
            'page_title'        => 'Reservas',
            'page_heading'      => 'Gestão de Reservas',
            'register_booking'  => 'Registrar reserva',
            'add_booking'       => 'Adicionar reserva',
            'edit_booking'      => 'Editar reserva',
            'booking_details'   => 'Detalhes da reserva',
            'download_receipt'  => 'Baixar recibo',
            'actions'           => 'Ações',
            'view_details'      => 'Ver detalhes',
            'click_to_view'     => 'Clique para ver detalhes',
            'zoom_in'           => 'Ampliar',
            'zoom_out'          => 'Reduzir',
            'zoom_reset'        => 'Redefinir zoom',
            'no_promo'          => 'Nenhum código promocional aplicado',
            'create_booking'    => 'Criar reserva',
            'create_title'      => 'Criar Nova Reserva',
            'booking_info'      => 'Informações da reserva',
            'select_customer'   => 'Selecionar cliente',
            'select_tour'       => 'Selecionar tour',
            'select_tour_first' => 'Selecione um tour primeiro',
            'select_option'     => 'Selecionar',
            'select_tour_to_see_categories' => 'Selecione um tour para ver as categorias',
            'loading'           => 'Carregando...',
            'no_results'        => 'Nenhum resultado',
            'error_loading'     => 'Erro ao carregar os dados',
            'tour_without_categories' => 'Este tour não possui categorias configuradas',
            'verifying'         => 'Verificando...',
            'min'               => 'Mínimo',
            'max'               => 'Máximo',
            'confirm_booking' => 'Confirmar reserva',
            'subtotal' => 'Subtotal',
            'total' => 'Total',
            'select_meeting_point' => 'Selecionar ponto de encontro',
            'no_pickup' => 'Sem coleta',
            'hotel' => 'Hotel',
            'meeting_point' => 'Ponto de encontro',
            'surcharge' => 'Acréscimo',
            'discount' => 'Desconto',
            'participants' => 'Participantes',
            'price_breakdown' => 'Detalhamento de preços',
            'enter_promo' => 'Digite o código promocional',
            'select_hotel' => 'Selecionar hotel',
            'payment_link' => 'Link de pagamento',
            'view_payment' => 'Ver Pagamento',
            'hotel_pickup' => 'Recolha no Hotel',
            'meeting_point_pickup' => 'Ponto de Encontro',
            'no_pickup' => 'Sem recolha',
            'optional' => '(Opcional)',
            'pickup_info' => 'Defina a hora de recolha para esta reserva.',
            'confirm_booking_alert' => 'A confirmação desta reserva enviará um e-mail de confirmação para o cliente.',
            'regenerating' => 'A regenerar...',
            'copied' => 'Copiado!',
            'copy_failed' => 'Falha ao copiar',
            'pickup_warning' => 'Aviso: A hora de recolha é :pickup mas o tour começa às :tour. Por favor verifique.',
        ],

        'fields' => [
            'booking_id'        => 'ID da reserva',
            'status'            => 'Status',
            'booking_date'      => 'Data da reserva',
            'booking_origin'    => 'Data da reserva (origem)',
            'reference'         => 'Referência',
            'booking_reference' => 'Referência da Reserva',
            'customer'          => 'Cliente',
            'email'             => 'E-mail',
            'phone'             => 'Telefone',
            'tour'              => 'Tour',
            'language'          => 'Idioma',
            'tour_date'         => 'Data do tour',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Nome de outro hotel',
            'meeting_point'     => 'Ponto de encontro',
            'pickup_location'   => 'Local de pickup',
            'schedule'          => 'Horário',
            'type'              => 'Tipo',
            'adults'            => 'Adultos',
            'adults_quantity'   => 'Quantidade de adultos',
            'children'          => 'Crianças',
            'children_quantity' => 'Quantidade de crianças',
            'promo_code'        => 'Código promocional',
            'total'             => 'Total',
            'total_to_pay'      => 'Total a pagar',
            'adult_price'       => 'Preço adulto',
            'child_price'       => 'Preço criança',
            'notes'             => 'Observações',
            'hotel_name'        => 'Nome do hotel',
            'travelers'         => 'Viajantes',
            'subtotal'          => 'Subtotal',
            'discount'          => 'Desconto',
            'total_persons'     => 'Pessoas',
            'pickup_place'      => 'Local de coleta',
            'date'              => 'Data',
            'category'          => 'Categoria',
            'quantity'          => 'Quantidade',
            'price'             => 'Preço',
            'pickup'            => 'Coleta',
        ],

        'placeholders' => [
            'select_customer'  => 'Selecionar cliente',
            'select_tour'      => 'Selecionar um tour',
            'select_schedule'  => 'Selecionar um horário',
            'select_language'  => 'Selecionar idioma',
            'select_hotel'     => 'Selecionar hotel',
            'select_point'     => 'Selecionar ponto de encontro',
            'select_status'    => 'Selecionar status',
            'enter_hotel_name' => 'Digite o nome do hotel',
            'enter_promo_code' => 'Digite o código promocional',
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
            'time'     => 'Horário:',
            'view_map' => 'Ver mapa',
        ],

        'pricing' => [
            'title' => 'Resumo de preços',
        ],

        'optional' => 'opcional',

        'messages' => [
            'past_booking_warning'  => 'Esta reserva é de uma data passada e não pode ser editada.',
            'tour_archived_warning' => 'O tour desta reserva foi removido/arquivado e não pôde ser carregado. Selecione um tour para ver os horários.',
            'no_schedules'          => 'Não há horários disponíveis',
            'deleted_tour'          => 'Tour removido',
            'deleted_tour_snapshot' => 'Tour removido (:name)',
            'tour_archived'         => '(arquivado)',
            'meeting_point_hint'    => 'Na lista é exibido apenas o nome do ponto.',
            'customer_locked'       => 'O cliente está bloqueado e não pode ser editado.',
            'promo_applied_subtract' => 'Desconto aplicado:',
            'promo_applied_add'     => 'Acréscimo aplicado:',
            'hotel_locked_by_meeting_point'   => 'Foi selecionado um ponto de encontro; não é possível selecionar um hotel.',
            'meeting_point_locked_by_hotel'   => 'Foi selecionado um hotel; não é possível selecionar um ponto de encontro.',
            'promo_removed'         => 'Código promocional removido',
        ],

        'alerts' => [
            'error_summary' => 'Por favor, corrija os seguintes erros:',
        ],

        'validation' => [
            'past_date'          => 'Você não pode reservar para datas anteriores a hoje.',
            'promo_required'     => 'Digite primeiro um código promocional.',
            'promo_checking'     => 'Verificando código…',
            'promo_invalid'      => 'Código promocional inválido.',
            'promo_error'        => 'Não foi possível validar o código.',
            'promo_apply_required' => 'Por favor, clique em Aplicar para validar seu código promocional primeiro.',
            'promo_empty'        => 'Digite um código primeiro.',
            'promo_needs_subtotal' => 'Adicione pelo menos 1 passageiro para calcular o desconto.',
        ],

        'promo' => [
            'applied'         => 'Código aplicado',
            'applied_percent' => 'Código aplicado: -:percent%',
            'applied_amount'  => 'Código aplicado: -$:amount',
        ],

        'loading' => [
            'saving'     => 'Salvando...',
            'validating' => 'Validando…',
            'updating'   => 'Atualizando...',
        ],

        'success' => [
            'created'          => 'Reserva criada com sucesso.',
            'updated'          => 'Reserva atualizada com sucesso.',
            'deleted'          => 'Reserva excluída com sucesso.',
            'status_updated'   => 'Status da reserva atualizado com sucesso.',
            'status_confirmed' => 'Reserva confirmada com sucesso.',
            'status_cancelled' => 'Reserva cancelada com sucesso.',
            'status_pending'   => 'Reserva marcada como pendente com sucesso.',
        ],

        'errors' => [
            'create'               => 'Não foi possível criar a reserva.',
            'update'               => 'Não foi possível atualizar a reserva.',
            'delete'               => 'Não foi possível excluir a reserva.',
            'status_update_failed' => 'Não foi possível atualizar o status da reserva.',
            'detail_not_found'     => 'Detalhes da reserva não encontrados.',
            'schedule_not_found'   => 'Horário não encontrado.',
            'insufficient_capacity' => 'Capacidade insuficiente para ":tour" em :date às :time. Solicitado: :requested, disponível: :available (máx: :max).',
        ],

        'confirm' => [
            'delete' => 'Você tem certeza de que deseja excluir esta reserva?',
        ],

        // Checkout Links (for admin-created bookings)
        'checkout_link_label' => 'Link de pagamento do cliente',
        'checkout_link_description' => 'Envie este link ao cliente para que ele possa concluir o pagamento de sua reserva.',
        'checkout_link_copy' => 'Copiar link',
        'checkout_link_copied' => 'Link copiado!',
        'checkout_link_copy_failed' => 'Não foi possível copiar o link. Por favor, copie manualmente.',
        'checkout_link_valid_until' => 'Válido até',
        'checkout_link_expired' => 'Este link de pagamento expirou ou não é mais válido.',
        'booking_already_paid' => 'Esta reserva já foi paga',
        'payment_link_regenerated' => 'Link de pagamento regenerado com sucesso',
        'regenerate_payment_link' => 'Regenerar link de pagamento',
        'confirm_regenerate_payment_link' => 'Tem certeza de que deseja regenerar o link de pagamento? O link antigo não funcionará mais.',
        'payment_link_expired_label' => 'Link expirado',
        'checkout_link_accessed' => 'Cliente acessou o checkout',

        // Payment Status
        'payment_status' => [
            'label' => 'Estado de Pagamento',
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'failed' => 'Falhado',
            'refunded' => 'Reembolsado',
        ],
        // SoftDelete & Trash
        'trash' => [
            'active_bookings' => 'Reservas Ativas',
            'trash' => 'Lixeira',
            'back_to_bookings' => 'Voltar para Reservas',
            'restore_booking' => 'Restaurar reserva',
            'permanently_delete' => 'Excluir permanentemente',
            'force_delete_title' => 'EXCLUSÃO PERMANENTE',
            'force_delete_warning' => 'Esta ação NÃO PODE ser desfeita!',
            'force_delete_message' => 'será excluído permanentemente.',
            'force_delete_data_loss' => 'Todos os dados relacionados serão perdidos para sempre.',
            'force_delete_confirm' => 'Sim, EXCLUIR PARA SEMPRE',
            'booking_deleted' => 'Reserva excluída.',
            'booking_restored' => 'Reserva restaurada com sucesso.',
            'booking_force_deleted' => 'Reserva excluída permanentemente. Registros de pagamento preservados para auditoria.',
            'force_delete_failed' => 'Falha ao excluir permanentemente a reserva.',
            'deleted_booking_indicator' => '(EXCLUÍDA)',
        ],
    ],

    // =========================================================
    // [03] AÇÕES
    // =========================================================
    'actions' => [
        'confirm'        => 'Confirmar',
        'cancel'         => 'Cancelar reserva',
        'confirm_cancel' => 'Você tem certeza de que deseja cancelar esta reserva?',
        'remove' => 'Remover',
        'confirm_create' => 'Confirmar e criar',
        'review_booking' => 'Revisar reserva',
        'apply'          => 'Aplicar',
    ],

    // =========================================================
    // [04] FILTROS
    // =========================================================
    'filters' => [
        'advanced_filters' => 'Filtros Avançados',
        'booking_dates' => 'Datas de Reserva',
        'tour_dates' => 'Datas do Tour',
        'tour_schedule' => 'Tour e Horário',
        'status_reference' => 'Status e Referência',
        'dates'            => 'Datas',
        'booked_from'      => 'Reservado a partir de',
        'booked_until'     => 'Reservado até',
        'tour_from'        => 'Tour a partir de',
        'tour_until'       => 'Tour até',
        'all'              => 'Todos',
        'apply'            => 'Aplicar',
        'clear'            => 'Limpar',
        'close_filters'    => 'Fechar filtros',
        'search_reference' => 'Buscar referência...',
        'enter_reference'  => 'Digite a referência da reserva',
    ],

    // =========================================================
    // [05] RELATÓRIOS
    // =========================================================
    'reports' => [
        'excel_title'          => 'Exportação de Reservas',
        'pdf_title'            => 'Relatório de Reservas - ' . config('company.short_name') . '',
        'general_report_title' => 'Relatório Geral de Reservas - ' . config('company.name') . '',
        'download_pdf'         => 'Baixar PDF',
        'export_excel'         => 'Exportar para Excel',
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
        'total_adults'         => 'Total de adultos',
        'total_kids'           => 'Total de crianças',
        'total_people'         => 'Total de pessoas',
    ],

    // =========================================================
    // [06] RECIBO
    // =========================================================
    'receipt' => [
        'title'         => 'Recibo de Reserva',
        'company'       => '' . config('company.short_name') . '',
        'code'          => 'Código',
        'client'        => 'Cliente',
        'tour'          => 'Tour',
        'booking_date'  => 'Data da reserva',
        'tour_date'     => 'Data do tour',
        'schedule'      => 'Horário',
        'hotel'         => 'Hotel',
        'meeting_point' => 'Ponto de encontro',
        'status'        => 'Status',
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
        'payment_status' => 'Estado de pagamento:',
        'persons'       => '{1} pessoa|[2,*] pessoas',
    ],

    // =========================================================
    // [07] MODAL DE DETALHES
    // =========================================================
    'details' => [
        'booking_info'  => 'Informações da reserva',
        'customer_info' => 'Informações do cliente',
        'tour_info'     => 'Informações do tour',
        'pricing_info'  => 'Informações de preços',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Desconto',
        'total_persons' => 'Total de pessoas',
    ],

    // =========================================================
    // [08] VIAJANTES (MODAL)
    // =========================================================
    'travelers' => [
        'title_warning'        => 'Atenção',
        'title_info'           => 'Informação',
        'title_error'          => 'Erro',
        'max_persons_reached'  => 'Máximo de :max pessoas por reserva.',
        'max_category_reached' => 'O máximo para esta categoria é :max.',
        'invalid_quantity'     => 'Quantidade inválida. Digite um número válido.',
        'age_between'          => 'Idade :min-:max',
        'age_from'             => 'Idade a partir de :min',
        'age_to'               => 'Até :max anos',
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
            'title'                 => 'Legenda de capacidades',
            'base_tour'             => 'Tour base',
            'override_schedule'     => 'Override de horário',
            'override_day'          => 'Override de dia',
            'override_day_schedule' => 'Override de dia+horário',
            'blocked'               => 'Bloqueado',
        ],

        'filters' => [
            'date'               => 'Data',
            'days'               => 'Dias',
            'product'            => 'Buscar tour',
            'search_placeholder' => 'Nome do tour…',
            'bulk_actions'       => 'Ações em massa',
            'update_state'       => 'Atualizar status',
        ],

        'blocks' => [
            'am'          => 'TOURS AM',
            'pm'          => 'TOURS PM',
            'am_blocked'  => 'TOURS AM (bloqueados)',
            'pm_blocked'  => 'TOURS PM (bloqueados)',
            'empty_am'    => 'Não há tours neste bloco',
            'empty_pm'    => 'Não há tours neste bloco',
            'no_data'     => 'Não há dados para exibir',
            'no_blocked'  => 'Não há tours bloqueados para o intervalo selecionado',
        ],

        'buttons' => [
            'mark_all'          => 'Marcar todos',
            'unmark_all'        => 'Desmarcar todos',
            'block_all'         => 'Bloquear todos',
            'unblock_all'       => 'Desbloquear todos',
            'block_selected'    => 'Bloquear selecionados',
            'unblock_selected'  => 'Desbloquear selecionados',
            'set_capacity'      => 'Ajustar capacidade',
            'capacity'          => 'Capacidade',
            'view_blocked'      => 'Ver bloqueados',
            'capacity_settings' => 'Configurações de capacidade',
            'block'             => 'Bloquear',
            'unblock'           => 'Desbloquear',
            'apply'             => 'Aplicar',
            'save'              => 'Salvar',
            'cancel'            => 'Cancelar',
            'back'              => 'Voltar',
        ],

        'states' => [
            'available' => 'Disponível',
            'blocked'   => 'Bloqueado',
        ],

        'badges' => [
            'tooltip_prefix' => 'Ocupados/Capacidade -',
        ],

        'modals' => [
            'capacity_title'          => 'Ajustar capacidade',
            'selected_capacity_title' => 'Ajustar capacidade dos selecionados',
            'date'                    => 'Data:',
            'hierarchy_title'         => 'Hierarquia de capacidades:',
            'new_capacity'            => 'Nova capacidade',
            'hint_zero_blocks'        => 'Deixar em 0 para bloquear completamente',
            'selected_count'          => 'A capacidade de :count item(ns) selecionado(s) será atualizada.',
            'capacity_day_title'      => 'Ajustar capacidade para o dia',
            'capacity_day_subtitle'   => 'Todos os horários do dia',
        ],

        'confirm' => [
            'block_title'       => 'Bloquear?',
            'unblock_title'     => 'Desbloquear?',
            'block_html'        => '<strong>:label</strong><br>Data: :day',
            'unblock_html'      => '<strong>:label</strong><br>Data: :day',
            'block_btn'         => 'Bloquear',
            'unblock_btn'       => 'Desbloquear',
            'bulk_title'        => 'Confirmar operação em massa',
            'bulk_items_html'   => ':count item(ns) serão afetados',
            'block_day_title'   => 'Bloquear o dia inteiro',
            'block_block_title' => 'Bloquear o bloco :block em :day',
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
            'marked_n'           => ':n item(ns) marcado(s)',
            'unmarked_n'         => ':n item(ns) desmarcado(s)',
            'error_generic'      => 'Não foi possível concluir a operação',
            'updated'            => 'Atualizado',
            'updated_count'      => ':count item(ns) atualizado(s)',
            'unblocked_count'    => ':count item(ns) desbloqueado(s)',
            'blocked'            => 'Bloqueado',
            'unblocked'          => 'Desbloqueado',
            'capacity_updated'   => 'Capacidade atualizada',
        ],

    ],

    'capacity' => [

        // =========================================================
        // [01] TÍTULOS E CABEÇALHOS DE UI
        // =========================================================
        'ui' => [
            'page_title'   => 'Gestão de Capacidades',
            'page_heading' => 'Gestão de Capacidades',
        ],

        // =========================================================
        // [02] ABAS
        // =========================================================
        'tabs' => [
            'global'        => 'Globais',
            'by_tour'       => 'Por Tour + Horário',
            'day_schedules' => 'Overrides Dia + Horário',
        ],

        // =========================================================
        // [03] ALERTAS
        // =========================================================
        'alerts' => [
            'global_info'        => '<strong>Capacidades globais:</strong> definem o limite base para cada tour (todos os dias e horários).',
            'by_tour_info'       => '<strong>Por Tour + Horário:</strong> override de capacidade específico para cada horário de cada tour. Estes overrides têm prioridade sobre a capacidade global do tour.',
            'day_schedules_info' => '<strong>Dia + Horário:</strong> override de maior prioridade para um dia e horário específicos. São gerenciados na tela de "Disponibilidade e Capacidade".',
        ],

        // =========================================================
        // [04] CABEÇALHOS DE TABELA
        // =========================================================
        'tables' => [
            'global' => [
                'tour'     => 'Tour',
                'type'     => 'Tipo',
                'capacity' => 'Capacidade global',
                'level'    => 'Nível',
            ],
            'by_tour' => [
                'schedule'    => 'Horário',
                'capacity'    => 'Capacidade override',
                'level'       => 'Nível',
                'no_schedules' => 'Este tour não possui horários atribuídos',
            ],
            'day_schedules' => [
                'date'        => 'Data',
                'tour'        => 'Tour',
                'schedule'    => 'Horário',
                'capacity'    => 'Capacidade',
                'actions'     => 'Ações',
                'no_overrides' => 'Não há overrides de dia + horário',
            ],
        ],

        // =========================================================
        // [05] BADGES / RÓTULOS
        // =========================================================
        'badges' => [
            'base'      => 'Base',
            'override'  => 'Override',
            'global'    => 'Global',
            'blocked'   => 'BLOQUEADO',
            'unlimited' => '∞',
        ],

        // =========================================================
        // [06] BOTÕES
        // =========================================================
        'buttons' => [
            'save'   => 'Salvar',
            'delete' => 'Excluir',
            'back'   => 'Voltar',
            'apply'  => 'Aplicar',
            'cancel' => 'Cancelar',
        ],

        // =========================================================
        // [07] MENSAGENS
        // =========================================================
        'messages' => [
            'empty_placeholder' => 'Vazio = usar capacidade global (:capacity)',
            'deleted_confirm'   => 'Excluir este override?',
            'no_day_overrides'  => 'Não há overrides de dia + horário.',
        ],

        // =========================================================
        // [08] TOASTS (SweetAlert2)
        // =========================================================
        'toasts' => [
            'success_title' => 'Sucesso',
            'deleted_booking_indicator' => '(EXCLUÍDO)',
        ],

        // Checkout Links (for admin-created bookings)
        'checkout_link_label' => 'Link de pagamento do cliente',
        'checkout_link_description' => 'Envie este link ao cliente para que ele possa concluir o pagamento de sua reserva.',
        'checkout_link_copy' => 'Copiar link',
        'checkout_link_copied' => 'Link copiado!',
        'checkout_link_copy_failed' => 'Não foi possível copiar o link. Por favor, copie manualmente.',
        'checkout_link_valid_until' => 'Válido até',
        'checkout_link_expired' => 'Este link de pagamento expirou ou não é mais válido.',
        'checkout_link_accessed' => 'Cliente acessou o checkout',
    ],
    // Large group message
    'large_group_message' => 'If your group is larger than :max people, please',
    'contact_us_link' => 'contact us for a better quote',

];
