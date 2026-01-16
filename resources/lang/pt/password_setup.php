<?php

return [
    // Email
    'email_subject' => 'Complete a configuração da sua conta',

    // Setup page
    'title' => 'Complete a configuração da sua conta',
    'welcome' => 'Bem-vindo(a), :name!',
    'booking_confirmed' => 'Sua reserva ref :reference foi confirmada.',
    'payment_success_message' => 'Pagamento Bem-Sucedido & Reserva Confirmada!',
    'create_password' => 'Crie uma senha para:',
    'benefits' => [
        'view_bookings' => 'Ver todas as suas reservas',
        'manage_profile' => 'Gerenciar seu perfil',
        'exclusive_offers' => 'Receber ofertas exclusivas',
    ],
    'password_label' => 'Senha',
    'confirm_password_label' => 'Confirmar Senha',
    'submit_button' => 'Criar Minha Conta',
    'maybe_later' => 'Talvez mais tarde',

    // Validation
    'password_requirements' => 'A senha deve conter pelo menos 1 número e 1 caractere especial (.¡!@#$%^&*()_+-)',
    'password_min_length' => 'A senha deve ter pelo menos 8 caracteres',
    'requirements' => [
        'one_number' => 'Pelo menos 1 número',
        'one_special_char' => 'Pelo menos 1 caractere especial',
    ],

    // Email Welcome
    'email_welcome_subject' => 'Bem-vindo ao ' . config('app.name') . '!',
    'email_welcome_title' => 'Bem-vindo, :name!',
    'email_welcome_text' => 'Sua conta foi criada com sucesso. Agora você pode acessar suas reservas e gerenciar seu perfil.',
    'email_action_button' => 'Ir para o Meu Painel',

    // JS / Strength
    'strength' => [
        'weak' => 'Fraca',
        'medium' => 'Média',
        'strong' => 'Forte',
    ],
    'passwords_do_not_match' => 'As senhas não coincidem',
    'creating_account' => 'Criando conta...',

    // Messages
    'token_expired' => 'Este link expirou. Por favor, solicite um novo.',
    'token_invalid' => 'Link de configuração inválido.',
    'expires_in' => 'Este link expira em :days dias',
    'fallback_link' => 'Se o botão não funcionar, copie e cole este link no seu navegador:',
    'success' => 'Conta criada com sucesso! Você pode entrar agora.',
    'user_not_found' => 'Usuário não encontrado.',
    'already_has_password' => 'Este usuário já possui uma senha definida.',
    'too_many_requests' => 'Muitas solicitações. Tente novamente mais tarde.',
    'send_failed' => 'Falha ao enviar e-mail. Tente novamente mais tarde.',
];
