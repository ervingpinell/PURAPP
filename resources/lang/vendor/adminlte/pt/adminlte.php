<?php

/**
 * Table of Contents
 *
 * 1. AUTHENTICATION AND REGISTRATION ........... Line 37
 * 2. HOTELS ................................... Line 57
 * 3. GENERAL NAVIGATION ....................... Line 67
 * 4. CONTENT AND PAGES ........................ Line 82
 * 5. TOURS AND REVIEWS ........................ Line 97
 * 6. SCHEDULES ................................ Line 131
 * 7. ITINERARIES .............................. Line 144
 * 8. HOTELS (DETAIL) .......................... Line 156
 * 9. CART AND BOOKINGS ........................ Line 180
 * 10. VALIDATION .............................. Line 219
 * 11. BUTTONS AND CRUD ........................ Line 225
 * 12. FOOTER .................................. Line 243
 * 13. WHATSAPP ................................ Line 247
 * 14. REVIEWS ................................. Line 257
 * 15. TRAVELERS ............................... Line 273
 * 16. CONTACT ................................. Line 286
 * 17. ERRORS .................................. Line 295
 * 18. CART LOGIN MODAL ........................ Line 298
 * 19. SWEETALERTS (ACTIONS) ................... Line 322
 * 20. SUCCESSES (USED IN CONTROLLERS) ......... Line 328
 * 21. MAIL .................................... Line 381
 * 22. DASHBOARD ............................... Line 386
 * 23. ENTITIES ................................ Line 394
 * 24. SECTIONS ................................ Line 408
 * 25. EMPTY STATES ............................ Line 414
 * 26. BUTTONS (GENERIC) ....................... Line 421
 * 27. LABELS .................................. Line 426
 */

return [

    // 1. AUTHENTICATION AND REGISTRATION
    'hello' => 'Olá',
    'full_name' => 'Nome completo',
    'email' => 'E-mail',
    'password' => 'Senha',
    'phone' => 'Telefone',
    'retype_password' => 'Repetir senha',
    'remember_me' => 'Lembrar-me',
    'remember_me_hint' => 'Mantenha a sessão aberta indefinidamente ou até ser fechada manualmente',
    'register' => 'Registrar',
    'promo_invalid' => 'Código promocional inválido.',
    'promo_already_used' => 'Esse código promocional já foi usado em outra reserva.',
    'no_past_dates' => 'Você não pode reservar datas anteriores a hoje.',
    'dupe_submit_cart' => 'Uma reserva semelhante já está sendo processada. Por favor, tente novamente em alguns segundos.',
    'schedule_not_available' => 'O horário não está disponível para este passeio (inativo ou não atribuído).',
    'date_blocked' => 'A data selecionada está bloqueada para este passeio.',
    'capacity_left' => 'Apenas :available vagas restantes para este horário.',
    'booking_created_success' => 'Reserva criada com sucesso.',
    'booking_updated_success' => 'Reserva atualizada com sucesso.',

    // 2. HOTELS
    'hotel_name_required' => 'O nome do hotel é obrigatório.',
    'hotel_name_unique'   => 'Já existe um hotel com esse nome.',
    'hotel_name_max'      => 'O nome do hotel não pode exceder :max caracteres.',
    'hotel_created_success' => 'Hotel criado com sucesso.',
    'hotel_updated_success' => 'Hotel atualizado com sucesso.',
    'is_active_required'  => 'O status é obrigatório.',
    'is_active_boolean'   => 'O status deve ser verdadeiro ou falso.',
    'outside_list' => 'Este hotel está fora da nossa lista. Por favor, entre em contato para verificar se podemos oferecer transporte.',

    // 3. GENERAL NAVIGATION
    'back' => 'Voltar',
    'home' => 'Início',
    'dashboard' => 'Painel',
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

    // 4. CONTENT AND PAGES
    'faq' => 'Perguntas Frequentes',
    'faqpage' => 'Perguntas Frequentes',
    'no_faqs_available' => 'Nenhuma FAQ disponível.',
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

    // 5. TOURS AND REVIEWS
    'tours' => 'Passeios',
    'tour' => 'Passeio',
    'tour_name' => 'Nome do passeio',
    'overview' => 'Visão geral',
    'duration' => 'Duração',
    'price' => 'Preço',
    'type' => 'Tipo de passeio',
    'languages_available' => 'Idiomas disponíveis',
    'amenities_included' => 'Comodidades incluídas',
    'excluded_amenities' => 'Comodidades excluídas',
    'tour_details' => 'Detalhes do passeio',
    'select_tour' => 'Selecione um passeio',
    'reviews' => 'Avaliações',
    'hero_title' => 'Descubra a magia da Costa Rica',
    'hero_subtext' => 'Explore nossos passeios únicos e viva a aventura.',
    'book_now' => 'Reservar agora',
    'our_tours' => 'Nossos passeios',
    'half_day' => 'Meio dia',
    'full_day' => 'Dia inteiro',
    'full_day_description' => 'Perfeito para quem busca uma experiência completa em um dia',
    'half_day_description' => 'Passeios ideais para uma aventura rápida para quem tem pouco tempo.',
    'full_day_tours' => 'Passeios de dia inteiro',
    'half_day_tours' => 'Passeios de meio dia',
    'see_tour' => 'Ver passeio',
    'see_tours' => 'Ver passeios',
    'what_visitors_say' => 'O que nossos visitantes dizem',
    'quote_1' => 'Uma experiência inesquecível!',
    'guest_1' => 'Carlos M.',
    'quote_2' => 'Com certeza voltarei.',
    'guest_2' => 'Ana G.',
    'tour_information'=> 'Informações do passeio',
    'group_size'=> 'Tamanho do grupo',

    // 6. SCHEDULES
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

    // 7. ITINERARIES
    'itinerary' => 'Roteiro',
    'itineraries' => 'Roteiros',
    'new_itinerary' => 'Novo roteiro',
    'itinerary_items' => 'Itens do roteiro',
    'item_title' => 'Título do item',
    'item_description' => 'Descrição do item',
    'add_item' => 'Adicionar item',
    'edit_itinerary' => 'Editar roteiro',
    'no_itinerary_info' => 'Nenhuma informação de roteiro.',
    'whats_included' => 'O que está incluído',

    // 8. HOTELS (DETAIL)
    'hotels' => 'Hotéis',
    'hotel' => 'Hotel',
    'select_hotel' => 'Hotel ou ponto de embarque',
    'hotel_other' => 'Outro (especificar manualmente)',
    'hotel_name' => 'Nome do hotel',
    'other_hotel' => 'Outro hotel (especificar)',
    'hotel_pickup' => 'Embarque no hotel',
    'outside_area' => 'Este hotel está fora da área de cobertura. Entre em contato para revisar suas opções.',
    'pickup_valid' => 'O hotel selecionado é válido! Assim que confirmar a reserva, entraremos em contato para coordenar o horário de embarque.',
    'pickup_details' => 'Detalhes do embarque',
    'pickup_note' => 'Embarques gratuitos aplicam-se apenas para hotéis na região de La Fortuna...',
    'pickup_points' => 'Pontos de embarque',
    'select_pickup' => 'Selecione um ponto de embarque',
    'type_to_search' => 'Digite para pesquisar...',
    'no_pickup_available' => 'Nenhum ponto de embarque disponível.',
    'pickup_not_found' => 'Hotel não encontrado.',
    'meeting_points' => 'Pontos de encontro',
    'select_meeting' => 'Selecione um ponto de encontro',
    'meeting_not_found' => 'Ponto de encontro não encontrado.',
    'main_street_entrance' => 'Entrada da rua principal',
    'example_address' => 'Endereço exemplo 123',
    'hotels_meeting_points' => 'Hotéis e pontos de encontro',

    // 9. CART AND BOOKINGS
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
    'reservation_failed' => 'Ocorreu um erro ao fazer a reserva.',
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
    'deleteItemTitle' => 'Excluir item',
    'deleteItemText' => 'Tem certeza de que deseja excluir este item? Esta ação não pode ser desfeita.',
    'deleteItemConfirm' => 'Excluir',
    'deleteItemCancel' => 'Cancelar',
    'selectOption' => 'Selecione uma opção',

    // 10. VALIDATION
    'required_field' => 'Este campo é obrigatório.',
    'invalid_email' => 'E-mail inválido.',
    'invalid_date' => 'Data inválida.',
    'select_option' => 'Selecione uma opção',

    // 11. BUTTONS AND CRUD
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
    'read_more' => 'Leia mais',
    'read_less' => 'Leia menos',
    'switch_view' => 'Alternar visualização',
    'close' => 'Fechar',

    // 12. FOOTER
    'contact_us' => 'Fale conosco',
    'location' => 'San José, Costa Rica',

    // 13. WHATSAPP
    'whatsapp_title' => 'Green Vacations CR',
    'whatsapp_subtitle' => 'Normalmente responde instantaneamente',
    'whatsapp_attention_schedule' => 'Segunda a domingo, das 7h30 às 19h30 (GMT-6)',
    'whatsapp_attention_language' => 'Atendimento apenas em espanhol e inglês',
    'whatsapp_greeting' => '👋 Olá! Como podemos ajudar a planejar sua aventura na Costa Rica?',
    'whatsapp_placeholder' => 'Olá, tenho interesse em um dos seus passeios. Pode me dar mais informações?',
    'whatsapp_button' => 'Enviar mensagem',
    'whatsapp_footer' => 'Conectado pelo WhatsApp Business',

    // 14. REVIEWS
    'what_customers_thinks_about' => 'O que nossos clientes acham sobre',
    'loading_reviews' => 'Carregando avaliações',
    'redirect_to_tour' => 'Redirecionar para o passeio',
    'would_you_like_to_visit' => 'Você gostaria de visitar ',
    'this_tour' => 'este passeio',
    'no_reviews_found' => 'Nenhuma avaliação encontrada para este passeio.',
    'no_reviews_available' => 'Nenhuma avaliação disponível.',
    'error_loading_reviews' => 'Erro ao carregar avaliações.',
    'anonymous_user' => 'Anônimo',
    'see_more' => 'Ver mais',
    'see_less' => 'Ver menos',
    'powered_by_viator' => 'Fornecido por Viator',
    'go_to_tour' => 'Você quer ir para o passeio ":name"?',
    'view_in_viator' => 'Ver :name no Viator',

    // 15. TRAVELERS
    'select_travelers' => 'Selecione os viajantes',
    'max_travelers_info' => 'Você pode selecionar até 12 pessoas no total.',
    'adult' => 'Adulto',
    'adults' => 'Adultos',
    'adults_quantity' => 'Quantidade de adultos',
    'kid' => 'Criança',
    'kids' => 'Crianças',
    'kids_quantity' => 'Quantidade de crianças',
    'age_10_plus' => 'Idade 10+',
    'age_4_to_9' => 'Idade 0-9',
    'max_limits_info' => 'Máx. 12 viajantes, máx. 2 crianças.',

    // 16. CONTACT
    'name' => 'Nome',
    'subject' => 'Assunto',
    'message' => 'Mensagem',
    'send_message' => 'Enviar mensagem',
    'message_sent' => 'Mensagem enviada',
    'business_hours' => 'Horário de atendimento',
    'business_schedule' => 'Segunda a domingo, das 7h30 às 19h30.',

    // 17. ERRORS
    'access_denied' => 'Acesso negado',

    // 18. CART LOGIN MODAL
    'login' => 'Entrar',
    'view_cart' => 'Ver carrinho',
    'login_required_title' => 'Você precisa fazer login',
    'login_required_text' => 'Para adicionar ao carrinho, você deve fazer login.',
    'login_required_text_confirm' => 'Para adicionar ao carrinho, você deve fazer login. Ir para login?',
    'pax' => 'pax',
    'remove_item_title' => 'Remover do carrinho',
    'remove_item_text' => 'Deseja remover este passeio do carrinho?',
    'success' => 'Sucesso',
    'error' => 'Erro',
    'validation_error' => 'Dados incompletos',
    'editItem'          => 'Editar item',
    // Removed duplicate keys: date, schedule, language, adults, kids, hotel, status, active, cancel, update
    'scheduleHelp'      => 'Se o passeio não exigir horário, deixe em branco.',
    'customHotel'       => 'Hotel personalizado…',
    'otherHotel'        => 'Usar hotel personalizado',
    'customHotelName'   => 'Nome do hotel personalizado',
    'customHotelHelp'   => 'Se você inserir um hotel personalizado, a seleção da lista será ignorada.',
    'inactive'          => 'Inativo',
    'close'             => 'Fechar',
    'notSpecified'     => 'Não especificado',
    'saving' => 'Salvando…',

    // 19. SWEETALERTS (ACTIONS)
    'confirmReservationTitle' => 'Tem certeza?',
    'confirmReservationText' => 'Sua reserva será confirmada',
    'confirmReservationConfirm' => 'Sim, confirmar',
    'confirmReservationCancel' => 'Cancelar',

    // 20. SUCCESSES (USED IN CONTROLLERS)
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
    'status_confirmed' => 'Confirmado',
    'status_cancelled' => 'Cancelado',
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

    // Successes (used in controllers)
    'show_password' => 'Mostrar senha',
    'user_registered_successfully'   => 'Usuário registrado com sucesso.',
    'user_updated_successfully'      => 'Usuário atualizado com sucesso.',
    'user_reactivated_successfully'  => 'Usuário reativado com sucesso.',
    'user_deactivated_successfully'  => 'Usuário desativado com sucesso.',
    'profile_updated_successfully'   => 'Perfil atualizado com sucesso.',
    'user_unlocked_successfully' => 'Sua conta foi desbloqueada. Agora você pode fazer login.',
    'user_locked_successfully' => 'Usuário bloqueado com sucesso.',
    'auth_required_title' => 'Você deve fazer login para reservar',
    'auth_required_body'  => 'Faça login ou registre-se para iniciar sua compra. Os campos estão bloqueados até o login.',
    'login_now'           => 'Entrar',
    'back_to_login'      => 'Voltar para login',

    // 21. MAIL
    'mail' => [
        'trouble_clicking' => 'Se você estiver com problemas para clicar no botão ":actionText", copie e cole a URL abaixo no seu navegador',
    ],

    // 22. DASHBOARD
    'dashboard' => [
        'title'      => 'Painel',
        'greeting'   => 'Olá :name! 👋',
        'welcome_to' => 'Bem-vindo ao painel administrativo do :app.',
        'hint'       => 'Use o menu lateral para começar a gerenciar o conteúdo.',
    ],

    // 23. ENTITIES
    'entities' => [
        'users'        => 'Usuários',
        'tours'        => 'Passeios',
        'tour_types'   => 'Tipos de passeio',
        'languages'    => 'Idiomas',
        'schedules'    => 'Horários',
        'amenities'    => 'Comodidades',
        'bookings'     => 'Reservas',
        'total_bookings'=> 'Total de reservas',
        'itineraries'  => 'Roteiros',
        'items'        => 'Itens',
    ],

    // 24. SECTIONS
    'sections' => [
        'available_itineraries' => 'Roteiros disponíveis',
        'upcoming_bookings'     => 'Próximas reservas',
    ],

    // 25. EMPTY STATES
    'empty' => [
        'itinerary_items'   => 'Este roteiro ainda não possui itens.',
        'itineraries'       => 'Nenhum roteiro encontrado.',
        'upcoming_bookings' => 'Nenhuma reserva futura.',
    ],

    // 26. BUTTONS (GENERIC)
    'buttons' => [
        'view' => 'Visualizar',
    ],

    // 27. LABELS
    'labels' => [
        'reference' => 'Referência',
        'date'      => 'Data',
    ],

];
