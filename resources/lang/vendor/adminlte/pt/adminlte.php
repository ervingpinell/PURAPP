<?php

/**
 * Índice
 *
 * 1. AUTENTICAÇÃO E REGISTRO .................. Linha 37
 * 2. HOTÉIS ................................... Linha 57
 * 3. NAVEGAÇÃO GERAL .......................... Linha 67
 * 4. CONTEÚDO E PÁGINAS ....................... Linha 82
 * 5. PASSEIOS E AVALIAÇÕES .................... Linha 97
 * 6. HORÁRIOS ..................................Linha 131
 * 7. ITINERÁRIOS .............................. Linha 144
 * 8. HOTÉIS (DETALHE) ......................... Linha 156
 * 9. CARRINHO E RESERVAS ...................... Linha 180
 * 10. VALIDAÇÃO ............................... Linha 219
 * 11. BOTÕES E CRUD ........................... Linha 225
 * 12. RODAPÉ .................................. Linha 243
 * 13. WHATSAPP ................................ Linha 247
 * 14. AVALIAÇÕES .............................. Linha 257
 * 15. VIAJANTES ............................... Linha 273
 * 16. CONTATO ................................. Linha 286
 * 17. ERROS ................................... Linha 295
 * 18. MODAL DE LOGIN DO CARRINHO .............. Linha 298
 * 19. SWEETALERTS (AÇÕES) ..................... Linha 322
 * 20. SUCESSOS (EM CONTROLLERS) ............... Linha 328
 * 21. E-MAIL .................................. Linha 381
 * 22. PAINEL .................................. Linha 386
 * 23. ENTIDADES ............................... Linha 394
 * 24. SEÇÕES .................................. Linha 408
 * 25. ESTADOS VAZIOS .......................... Linha 414
 * 26. BOTÕES (GENÉRICOS) ...................... Linha 421
 * 27. RÓTULOS ................................. Linha 426
 */

return [
    'no_slots_for_date' => 'Não há espaços disponíveis para esta data',
    // 1. AUTENTICAÇÃO E REGISTRO
    'hello' => 'Olá',
    'full_name' => 'Nome completo',
    'email' => 'E-mail',
    'password' => 'Senha',
    'phone' => 'Telefone',
    'retype_password' => 'Repetir senha',
    'remember_me' => 'Lembrar de mim',
    'remember_me_hint' => 'Manter a sessão aberta indefinidamente ou até sair manualmente',
    'register' => 'Cadastrar',
    'promo_invalid' => 'Código promocional inválido.',
    'promo_already_used' => 'Esse código promocional já foi utilizado em outra reserva.',
    'no_past_dates' => 'Você não pode reservar para datas anteriores a hoje.',
    'dupe_submit_cart' => 'Uma reserva semelhante já está sendo processada. Tente novamente em alguns segundos.',
    'schedule_not_available' => 'O horário não está disponível para este passeio (inativo ou não atribuído).',
    'date_blocked' => 'A data selecionada está bloqueada para este passeio.',
    'capacity_left' => 'Restam apenas :available vagas para este horário.',
    'booking_created_success' => 'Reserva criada com sucesso.',
    'booking_updated_success' => 'Reserva atualizada com sucesso.',
    'two_factor_authentication' => 'Autenticação em dois fatores (2FA)',

    // 2. HOTÉIS
    'hotel_name_required' => 'O nome do hotel é obrigatório.',
    'hotel_name_unique'   => 'Já existe um hotel com esse nome.',
    'hotel_name_max'      => 'O nome do hotel não pode exceder :max caracteres.',
    'hotel_created_success' => 'Hotel criado com sucesso.',
    'hotel_updated_success' => 'Hotel atualizado com sucesso.',
    'is_active_required'  => 'O status é obrigatório.',
    'is_active_boolean'   => 'O status deve ser verdadeiro ou falso.',
    'outside_list' => 'Este hotel está fora da nossa lista. Entre em contato para verificar se podemos oferecer transporte.',

    // 3. NAVEGAÇÃO GERAL
    'back' => 'Voltar',
    'home' => 'Início',
    'dashboard_menu' => 'Painel',
    'profile' => 'Perfil',
    'settings' => 'Configurações',
    'users' => 'Usuários',
    'roles' => 'Funções',
    'notifications' => 'Notificações',
    'messages' => 'Mensagens',
    'help' => 'Ajuda',
    'language' => 'Idioma',
    'support' => 'Suporte',
    'admin_panel' => 'Painel administrativo',

    // 4. CONTEÚDO E PÁGINAS
    'faq' => 'Perguntas frequentes',
    'faqpage' => 'Perguntas frequentes',
    'no_faqs_available' => 'Não há perguntas frequentes disponíveis.',
    'contact' => 'Contato',
    'about' => 'Sobre nós',
    'privacy_policy' => 'Política de privacidade',
    'terms_and_conditions' => 'Termos e condições',
    'all_policies' => 'Todas as nossas políticas',
    'cancellation_and_refunds_policies' => 'Políticas de cancelamento e reembolso',
    'reports' => 'Relatórios',
    'footer_text'=> 'Green Vacations CR',
    'quick_links'=> 'Links rápidos',
    'rights_reserved' => 'Todos os direitos reservados',

    // 5. PASSEIOS E AVALIAÇÕES
    'tours' => 'Passeios',
    'tour' => 'Passeio',
    'tour_name' => 'Nome do passeio',
    'overview' => 'Visão geral',
    'duration' => 'Duração',
    'price' => 'Preço',
    'type' => 'Tipo de passeio',
    'languages_available' => 'Idiomas disponíveis',
    'amenities_included' => 'Serviços incluídos',
    'excluded_amenities' => 'Serviços não incluídos',
    'tour_details' => 'Detalhes do passeio',
    'select_tour' => 'Selecione um passeio',
    'reviews' => 'Avaliações',
    'hero_title' => 'Descubra a magia da Costa Rica',
    'hero_subtext' => 'Explore nossos passeios exclusivos e viva a aventura.',
    'book_now' => 'Reservar agora',
    'our_tours' => 'Nossos passeios',
    'half_day' => 'Meio dia',
    'full_day' => 'Dia inteiro',
    'full_day_description' => 'Perfeito para quem busca uma experiência completa em um dia',
    'half_day_description' => 'Passeios ideais para uma aventura rápida quando há pouco tempo.',
    'full_day_tours' => 'Passeios de dia inteiro',
    'half_day_tours' => 'Passeios de meio dia',
    'see_tour' => 'Ver passeio',
    'see_tours' => 'Ver passeios',
    'what_visitors_say' => 'O que dizem nossos visitantes',
    'quote_1' => 'Uma experiência inesquecível!',
    'guest_1' => 'Carlos M.',
    'quote_2' => 'Com certeza voltarei.',
    'guest_2' => 'Ana G.',
    'tour_information'=> 'Informações do passeio',
    'group_size'=> 'Tamanho do grupo',
    'no_prices_available' => 'Não há preços disponíveis',
    'no_prices_configured' => 'Este passeio não tem preços configurados',
    'total_persons' => 'Total de pessoas',
    'quantity' => 'Quantidade',
    'decrease' => 'Diminuir',
    'increase' => 'Aumentar',
    'max_persons_reached' => 'Máximo de :max pessoas por reserva',
    'min_category_required' => 'Mínimo de :min em :category',
    'max_category_exceeded' => 'Máximo de :max permitido em :category',
    'max_persons_exceeded' => 'Máximo de :max pessoas no total',
    'min_one_person' => 'É necessário pelo menos uma pessoa',
    'persons_max' => 'pessoas máx.',
    'or' => 'Ou',
    'open_map' => 'Ver localização',

    // 6. HORÁRIOS
    'schedule' => 'Horário',
    'schedule_am' => 'Horário da manhã',
    'schedule_pm' => 'Horário da tarde',
    'start_time' => 'Hora de início',
    'end_time' => 'Hora de término',
    'select_date' => 'Selecione uma data',
    'select_time' => 'Selecione um horário',
    'select_language' => 'Selecione um idioma',
    'schedules' => 'Horários',
    'horas' => 'horas',
    'hours' => 'horas',

    // 7. ITINERÁRIOS
    'itinerary' => 'Roteiro',
    'itineraries' => 'Roteiros',
    'new_itinerary' => 'Novo roteiro',
    'itinerary_items' => 'Itens do roteiro',
    'item_title' => 'Título do item',
    'item_description' => 'Descrição do item',
    'add_item' => 'Adicionar item',
    'edit_itinerary' => 'Editar roteiro',
    'no_itinerary_info' => 'Sem informações do roteiro.',
    'whats_included' => 'O que está incluído',

    // 8. HOTÉIS (DETALHE)
    'hotels' => 'Hotéis',
    'hotel' => 'Hotel',
    'select_hotel' => 'Hotel',
    'hotel_other' => 'Outro (informar manualmente)',
    'hotel_name' => 'Nome do hotel',
    'other_hotel' => 'Outro hotel (informar)',
    'hotel_pickup' => 'Traslado no hotel',
    'outside_area' => 'Este hotel está fora da área de cobertura. Entre em contato para verificar as opções.',
    'pickup_valid' => 'O hotel selecionado é válido! Após confirmar a reserva, entraremos em contato para combinar o horário do traslado.',
    'pickup_details' => 'Detalhes do traslado',
    'pickup_note' => 'Traslados gratuitos se aplicam apenas para hotéis na área de La Fortuna...',
    'pickup_points' => 'Pontos de traslado',
    'select_pickup' => 'Selecione um ponto de traslado',
    'type_to_search' => 'Digite para buscar...',
    'no_pickup_available' => 'Não há pontos de traslado disponíveis.',
    'pickup_not_found' => 'Hotel não encontrado.',
    'meeting_points' => 'Pontos de encontro',
    'select_meeting' => 'Selecione um ponto de encontro',
    'meeting_point_details' => 'Detalhes do ponto de encontro',
    'meeting_not_found' => 'Ponto de encontro não encontrado.',
    'main_street_entrance' => 'Entrada da rua principal',
    'example_address' => 'Endereço de exemplo 123',
    'hotels_meeting_points' => 'Hotéis e pontos de encontro',
    'meeting_valid' => 'O ponto de encontro selecionado é válido! Após confirmar sua reserva, enviaremos instruções e o horário exato.',
    'meeting_point' => 'Ponto de encontro',
    'meetingPoint'  => 'Ponto de encontro',
    'selectHotelHelp' => 'Selecione seu hotel na lista.',
    'selectFromList'      => 'Selecione um item da lista',
    'fillThisField'       => 'Preencha este campo',
    'pickupRequiredTitle' => 'Traslado obrigatório',
    'pickupRequiredBody'  => 'Você deve selecionar um hotel ou um ponto de encontro para continuar.',
    'ok'                  => 'OK',
    'pickup_time' => 'Horário do traslado',
    'pickupTime'  => 'Horário do traslado',
    'meeting_time' => 'Horário do encontro',
    'open_map' => 'Abrir mapa',
    'openMap'  => 'Abrir mapa',

    // 9. CARRINHO E RESERVAS
    'cart' => 'Carrinho',
    'myCart' => 'Meu carrinho',
    'my_reservations' => 'Minhas reservas',
    'your_cart' => 'Seu carrinho',
    'add_to_cart' => 'Adicionar ao carrinho',
    'remove_from_cart' => 'Remover do carrinho',
    'confirm_reservation' => 'Confirmar reserva',
    'confirmBooking' => 'Confirmar reserva',
    'cart_updated' => 'Carrinho atualizado com sucesso.',
    'itemUpdated' => 'Item do carrinho atualizado com sucesso.',
    'cartItemAdded' => 'Passeio adicionado ao carrinho com sucesso.',
    'cartItemDeleted' => 'Passeio removido do carrinho com sucesso.',
    'emptyCart' => 'Seu carrinho está vazio.',
    'no_items_in_cart' => 'Seu carrinho está vazio.',
    'reservation_success' => 'Reserva concluída com sucesso!',
    'reservation_failed' => 'Ocorreu um erro ao processar a reserva.',
    'booking_reference' => 'Referência da reserva',
    'booking_date' => 'Data da reserva',
    'reservation_status' => 'Status da reserva',
    'blocked_date_for_tour' => 'A data :date está bloqueada para ":tour".',
    'tourCapacityFull' => 'A capacidade máxima para este passeio já foi atingida.',
    'totalEstimated' => 'Total estimado',
    'total_price' => 'Preço total',
    'total' => 'Total',
    'date'=> 'Data',
    'status' => 'Status',
    'actions' => 'Ações',
    'active'=> 'Ativo',
    'delete'=> 'Excluir',
    'promoCode' => 'Você tem um código promocional?',
    'promoCodePlaceholder' => 'Digite seu código promocional',
    'apply' => 'Aplicar',
    'remove' => 'Remover',
    'deleteItemTitle' => 'Excluir item',
    'deleteItemText' => 'Tem certeza de que deseja excluir este item? Esta ação não pode ser desfeita.',
    'deleteItemConfirm' => 'Excluir',
    'deleteItemCancel' => 'Cancelar',
    'selectOption' => 'Selecione uma opção',
    'breakdown' => 'Detalhamento',
    'subtotal'  => 'Subtotal',
    'senior'    => 'Idoso',
    'student'   => 'Estudante',
    'custom' => 'Personalizado',

    // 10. VALIDAÇÃO
    'required_field' => 'Este campo é obrigatório.',
    'invalid_email' => 'E-mail inválido.',
    'invalid_date' => 'Data inválida.',
    'select_option' => 'Selecione uma opção',

    // 11. BOTÕES E CRUD
    'create' => 'Criar',
    'edit' => 'Editar',
    'update' => 'Atualizar',
    'activate' => 'Ativar',
    'deactivate' => 'Desativar',
    'confirm' => 'Confirmar',
    'cancel' => 'Cancelar',
    'save' => 'Salvar',
    'save_changes' => 'Salvar alterações',
    'are_you_sure' => 'Tem certeza?',
    'optional' => 'Opcional',
    'edit_profile' => 'Editar perfil',
    'read_more' => 'Ler mais',
    'read_less' => 'Ler menos',
    'switch_view' => 'Alterar visualização',
    'close' => 'Fechar',

    // 12. RODAPÉ
    'contact_us' => 'Fale conosco',
    'location' => 'San José, Costa Rica',

    // 13. WHATSAPP
    'whatsapp_title' => 'Green Vacations CR',
    'whatsapp_subtitle' => 'Normalmente responde na hora',
    'whatsapp_attention_schedule' => 'Segunda a domingo, das 7h30 às 19h30 (GMT-6)',
    'whatsapp_attention_language' => 'Atendimento apenas em espanhol e inglês',
    'whatsapp_greeting' => '👋 Olá! Como podemos ajudar a planejar sua aventura na Costa Rica?',
    'whatsapp_placeholder' => 'Olá, tenho interesse em um dos passeios. Poderiam me enviar mais informações?',
    'whatsapp_button' => 'Enviar mensagem',
    'whatsapp_footer' => 'Conectado pelo WhatsApp Business',

    // 14. AVALIAÇÕES
    'what_customers_thinks_about' => 'O que os nossos clientes acham de',
    'loading_reviews' => 'Carregando avaliações',
    'redirect_to_tour' => 'Redirecionar para o passeio',
    'would_you_like_to_visit' => 'Você gostaria de visitar ',
    'this_tour' => 'este passeio',
    'no_reviews_found' => 'Nenhuma avaliação encontrada para este passeio.',
    'no_reviews_available' => 'Não há avaliações disponíveis.',
    'error_loading_reviews' => 'Erro ao carregar as avaliações.',
    'anonymous_user' => 'Anônimo',
    'see_more' => 'Ver mais',
    'see_less' => 'Ver menos',
    'powered_by_viator' => 'Fornecido por Viator',
    'go_to_tour' => 'Deseja ir ao passeio ":name"?',
    'view_in_viator' => 'Ver :name no Viator',

    // 15. VIAJANTES
    'select_travelers' => 'Selecionar viajantes',
    'max_travelers_info' => 'Você pode selecionar até 12 pessoas no total.',
    'adult' => 'Adulto',
    'adults' => 'Adultos',
    'adults_quantity' => 'Quantidade de adultos',
    'kid' => 'Criança',
    'kids' => 'Crianças',
    'kids_quantity' => 'Quantidade de crianças',
    'age_10_plus' => 'Idade 10+',
    'age_4_to_9' => 'Idade 4–9',
    'max_limits_info' => 'Máx. 12 viajantes, máx. 2 crianças.',
    'total_persons' => 'Total de pessoas',
    'or' => 'ou',
    'min' => 'Mín',

    // 16. CONTATO
    'name' => 'Nome',
    'subject' => 'Assunto',
    'message' => 'Mensagem',
    'send_message' => 'Enviar mensagem',
    'message_sent' => 'Mensagem enviada',
    'business_hours' => 'Horário de atendimento',
    'business_schedule' => 'Segunda a domingo, das 7h30 às 19h30',

    // 17. ERROS
    'access_denied' => 'Acesso negado',
    'need_language' => 'Selecione um idioma.',
    'need_pickup'   => 'Selecione um hotel ou um ponto de encontro.',
    'need_schedule_title' => 'Horário obrigatório',
    'need_schedule'       => 'Por favor, selecione um horário.',
    'need_language_title' => 'Idioma obrigatório',
    'need_pickup_title'   => 'Ponto de retirada obrigatório',
    'no_slots_title'      => 'Sem horários disponíveis',
    'no_slots'            => 'Não há horários disponíveis para a data selecionada. Por favor, escolha outra data.',

    // 18. MODAL DE LOGIN DO CARRINHO
    'login' => 'Entrar',
    'view_cart' => 'Ver carrinho',
    'login_required_title' => 'É necessário entrar',
    'login_required_text' => 'Para adicionar ao carrinho você precisa entrar.',
    'login_required_text_confirm' => 'Para adicionar ao carrinho você precisa entrar. Ir para a tela de login?',
    'pax' => 'pax',
    'remove_item_title' => 'Remover do carrinho',
    'remove_item_text' => 'Deseja remover este passeio do carrinho?',
    'success' => 'Sucesso',
    'error' => 'Erro',
    'validation_error' => 'Dados incompletos',
    'editItem' => 'Editar item',
    'scheduleHelp' => 'Se o passeio não exigir horário, deixe em branco.',
    'customHotel' => 'Hotel personalizado…',
    'otherHotel' => 'Usar hotel personalizado',
    'customHotelName' => 'Nome do hotel personalizado',
    'customHotelHelp' => 'Se você informar um hotel personalizado, a seleção da lista será ignorada.',
    'inactive' => 'Inativo',
    'notSpecified' => 'Não especificado',
    'saving' => 'Salvando…',

    // 19. SWEETALERTS (AÇÕES)
    'confirmReservationTitle' => 'Tem certeza?',
    'confirmReservationText' => 'Sua reserva será confirmada',
    'confirmReservationConfirm' => 'Sim, confirmar',
    'confirmReservationCancel' => 'Cancelar',

    // 20. SUCESSOS (EM CONTROLLERS)
    'edit_profile_of' => 'Editar perfil',
    'profile_information' => 'Informações do perfil',
    'new_password_optional' => 'Nova senha (opcional)',
    'leave_blank_if_no_change' => 'Deixe em branco se não quiser alterar',
    'confirm_new_password_placeholder' => 'Confirmar nova senha',

    'policies' => 'Políticas',
    'no_reservations_yet' => 'Você ainda não tem reservas!',
    'no_reservations_message' => 'Parece que você ainda não reservou nenhuma aventura conosco. Que tal explorar nossos passeios incríveis?',
    'view_available_tours' => 'Ver passeios disponíveis',
    'pending_reservations' => 'Reservas pendentes',
    'confirmed_reservations' => 'Reservas confirmadas',
    'cancelled_reservations' => 'Reservas canceladas',
    'reservations_generic' => 'Reservas',
    'generic_tour' => 'Passeio genérico',
    'unknown_tour' => 'Passeio desconhecido',
    'tour_date' => 'Data do passeio',
    'participants' => 'Participantes',
    'children' => 'Crianças',
    'not_specified' => 'Não especificado',
    'status_pending' => 'Pendente',
    'status_confirmed' => 'Confirmada',
    'status_cancelled' => 'Cancelada',
    'status_unknown' => 'Desconhecido',

    'view_receipt' => 'Ver recibo',

    'validation.unique' => 'Este e-mail já está em uso',

    'validation' => [
        'too_many_attempts' => 'Muitas tentativas falhas. Tente novamente em :seconds segundos.',
    ],

    'open_tour'          => 'Ir para o passeio?',
    'open_tour_text_pre' => 'Você está prestes a abrir a página do passeio',
    'open_tour_confirm'  => 'Ir agora',
    'open_tour_cancel'   => 'Cancelar',

    // Sucessos (em controllers)
    'show_password' => 'Mostrar senha',
    'user_registered_successfully'   => 'Usuário cadastrado com sucesso.',
    'user_updated_successfully'      => 'Usuário atualizado com sucesso.',
    'user_reactivated_successfully'  => 'Usuário reativado com sucesso.',
    'user_deactivated_successfully'  => 'Usuário desativado com sucesso.',
    'profile_updated_successfully'   => 'Perfil atualizado com sucesso.',
    'user_unlocked_successfully' => 'Sua conta foi desbloqueada. Você já pode entrar.',
    'user_locked_successfully' => 'Usuário bloqueado com sucesso.',
    'auth_required_title' => 'Você precisa entrar para reservar',
    'auth_required_body'  => 'Entre ou cadastre-se para iniciar sua compra. Os campos ficam bloqueados até você entrar.',
    'login_now'           => 'Entrar',
    'back_to_login'       => 'Voltar para entrar',

    // 21. E-MAIL
    'mail' => [
        'trouble_clicking' => 'Se você tiver problemas para clicar no botão ":actionText", copie e cole a URL abaixo no seu navegador',
    ],

    // 22. PAINEL
    'dashboard' => [
        'title'      => 'Painel',
        'greeting'   => 'Olá, :name! 👋',
        'welcome_to' => 'Bem-vindo ao painel administrativo do :app.',
        'hint'       => 'Use o menu lateral para começar a gerenciar o conteúdo.',
    ],

    // 23. ENTIDADES
    'entities' => [
        'users'        => 'Usuários',
        'tours'        => 'Passeios',
        'tour_types'   => 'Tipos de passeio',
        'languages'    => 'Idiomas',
        'schedules'    => 'Horários',
        'amenities'    => 'Serviços',
        'bookings'     => 'Reservas',
        'total_bookings'=> 'Reservas totais',
        'itineraries'  => 'Roteiros',
        'items'        => 'Itens',
    ],

    // 24. SEÇÕES
    'sections' => [
        'available_tours' => 'Passeios disponíveis',
        'upcoming_bookings'     => 'Próximas reservas',
    ],

    // 25. ESTADOS VAZIOS
    'empty' => [
        'itinerary_items'   => 'Este roteiro ainda não possui itens.',
        'itineraries'       => 'Nenhum roteiro encontrado.',
        'upcoming_bookings' => 'Não há próximas reservas.',
    ],

    // 26. BOTÕES (GENÉRICOS)
    'buttons' => [
        'view' => 'Ver',
    ],

    'persons' => [
        'count' => '{0} 0 pessoas|{1} 1 pessoa|[2,*] :count pessoas',
        'title'            => 'Pessoas',
        'pax'              => 'PAX',
        'adults'           => 'Adultos',
        'kids'             => 'Crianças',
        'seniors'          => 'Idosos',
        'infants'          => 'Bebês',
        'students'         => 'Estudantes',
        'guides'           => 'Guias',
        'drivers'          => 'Motoristas',
        'free'             => 'Grátis',
        'other'            => 'Outros',
        'category'         => 'Categoria',
        'categories'       => 'Categorias',
        'quantity'         => 'Quantidade',
        'min'              => 'Mín',
        'max'              => 'Máx',
        'per_person'       => 'por pessoa',
        'price'            => 'Preço',
        'subtotal'         => 'Subtotal',
        'total'            => 'Total',
        'add_category'     => 'Adicionar categoria',
        'remove_category'  => 'Remover',
        'select_category'  => 'Selecione uma categoria',
        'required'         => 'Obrigatório',
        'optional'         => 'Opcional',
        'min_required'     => 'Mínimo obrigatório: :min',
        'max_allowed'      => 'Máximo permitido: :max',
        'invalid_quantity' => 'Quantidade inválida para ":category".',
    ],

    // 27. RÓTULOS
    'labels' => [
        'reference' => 'Referência',
        'date'      => 'Data',
    ],
    'pickup'      => 'Local de traslado',

];
