<?php

return [

    'messages' => [
        'date_no_longer_available' => 'A data :date não está mais disponível para reserva (mínimo: :min).',
        'limited_seats_available' => 'Apenas :available vagas restantes para ":tour" em :date.',
        'bookings_created_from_cart' => 'Suas reservas foram criadas com sucesso a partir do carrinho.',
        'capacity_exceeded' => 'Capacidade Excedida',
        'meeting_point_hint' => 'Apenas o nome do ponto é exibido na lista.',
    ],

    'availability' => [
        'fields' => [
            'tour'        => 'Tour',
            'date'        => 'Data',
            'start_time'  => 'Horário de início',
            'end_time'    => 'Horário de término',
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

        'ui' => [
            'page_title'           => 'Disponibilidade',
            'page_heading'         => 'Disponibilidade',
            'blocked_page_title'   => 'Tours bloqueados',
            'blocked_page_heading' => 'Tours bloqueados',
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
    ],

    'bookings' => [
        'ui' => [
            'page_title'         => 'Reservas',
            'page_heading'       => 'Gerenciamento de Reservas',
            'register_booking'   => 'Registrar Reserva',
            'add_booking'        => 'Adicionar Reserva',
            'edit_booking'       => 'Editar Reserva',
            'booking_details'    => 'Detalhes da Reserva',
            'download_receipt'   => 'Baixar recibo',
            'actions'            => 'Ações',
            'view_details'       => 'Ver Detalhes',
            'click_to_view'      => 'Clique para ver detalhes',
            'zoom_in'            => 'Ampliar',
            'zoom_out'           => 'Reduzir',
            'zoom_reset'         => 'Redefinir Zoom',
            'no_promo'        => 'Nenhum código promocional aplicado',

        ],

        'fields' => [
            'booking_id'        => 'ID da Reserva',
            'status'            => 'Status',
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
            'pickup_location'   => 'Local de Busca',
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
            'notes'             => 'Observações',
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
            'confirm_changes' => 'Confirmar Alterações',
            'apply'           => 'Aplicar',
            'update'          => 'Atualizar',
            'close'           => 'Fechar',
        ],

        'meeting_point' => [
            'time'     => 'Horário:',
            'view_map' => 'Ver mapa',
        ],

        'pricing' => [
            'title' => 'Resumo de Preços',
        ],

        'optional' => 'opcional',

        'messages' => [
            'past_booking_warning'  => 'Esta reserva corresponde a uma data passada e não pode ser editada.',
            'tour_archived_warning' => 'O tour desta reserva foi excluído/arquivado e não pôde ser carregado. Selecione um tour para ver seus horários.',
            'no_schedules'          => 'Nenhum horário disponível',
            'deleted_tour'          => 'Tour excluído',
            'deleted_tour_snapshot' => 'Tour Excluído (:name)',
            'tour_archived'         => '(arquivado)',
            'meeting_point_hint'    => 'Apenas o nome do ponto é exibido na lista.',
            'customer_locked'       => 'O cliente está bloqueado e não pode ser editado.',

        ],

        'alerts' => [
            'error_summary' => 'Por favor, corrija os seguintes erros:',
        ],

        'validation' => [
            'past_date'      => 'Você não pode reservar para datas anteriores a hoje.',
            'promo_required' => 'Digite um código promocional primeiro.',
            'promo_checking' => 'Verificando código…',
            'promo_invalid'  => 'Código promocional inválido.',
            'promo_error'    => 'Não foi possível validar o código.',
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
            'status_pending'   => 'Reserva definida como pendente com sucesso.',
        ],

        'errors' => [
            'create'                => 'Não foi possível criar a reserva.',
            'update'                => 'Não foi possível atualizar a reserva.',
            'delete'                => 'Não foi possível excluir a reserva.',
            'status_update_failed'  => 'Não foi possível atualizar o status da reserva.',
            'detail_not_found'      => 'Detalhes da reserva não encontrados.',
            'schedule_not_found'    => 'Horário não encontrado.',
            'insufficient_capacity' => 'Não é possível confirmar a reserva. Capacidade insuficiente para :tour em :date às :time. Solicitado: :requested pessoas, Disponível: :available/:max.',
        ],

        'confirm' => [
            'delete' => 'Tem certeza de que deseja excluir esta reserva?',
        ],
    ],

    'actions' => [
        'confirm'        => 'Confirmar',
        'cancel'         => 'Cancelar Reserva',
        'confirm_cancel' => 'Tem certeza de que deseja cancelar esta reserva?',
    ],

    'filters' => [
        'advanced_filters' => 'Filtros Avançados',
        'dates'            => 'Datas',
        'booked_from'      => 'Reservado desde',
        'booked_until'     => 'Reservado até',
        'tour_from'        => 'Tour desde',
        'tour_until'       => 'Tour até',
        'all'              => 'Todos',
        'apply'            => 'Aplicar',
        'clear'            => 'Limpar',
        'close_filters'    => 'Fechar filtros',
        'search_reference' => 'Buscar referência...',
        'enter_reference'  => 'Digite a referência da reserva',
    ],

    'reports' => [
        'excel_title'          => 'Exportação de Reservas',
        'pdf_title'            => 'Relatório de Reservas - Green Vacations CR',
        'general_report_title' => 'Relatório Geral de Reservas - Green Vacations Costa Rica',
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
        'total_adults'         => 'Total de Adultos',
        'total_kids'           => 'Total de Crianças',
        'total_people'         => 'Total de Pessoas',
    ],

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
    ],

    'details' => [
        'booking_info'  => 'Informações da Reserva',
        'customer_info' => 'Informações do Cliente',
        'tour_info'     => 'Informações do Tour',
        'pricing_info'  => 'Informações de Preços',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Desconto',
    ],


    // =========================================================
    // [08] VIAJEROS (MODAL)
    // =========================================================
    'travelers' => [
        'title_warning'        => 'Atenção',
        'title_info'           => 'Informação',
        'title_error'          => 'Erro',
        'max_persons_reached'  => 'Máximo de :max pessoas por reserva.',
        'max_category_reached' => 'O máximo para esta categoria é :max.',
        'invalid_quantity'     => 'Quantidade inválida. Informe um número válido.',
        'age_between'          => 'Idade :min-:max',
        'age_from'             => 'Idade :min+',
        'age_to'               => 'Até :max anos',
    ],

];
