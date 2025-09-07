<?php

return [
    // Mensagens padrão do Laravel
    'failed'   => 'O endereço de e-mail ou a senha estão incorretos.',
    'password' => 'A senha fornecida está incorreta.',
    'throttle' => 'Muitas tentativas de login. Por favor, tente novamente em :seconds segundos.',

    // Mensagens específicas do negócio
    'inactive'   => 'Sua conta está inativa. Entre em contato com o suporte para reativá-la.',
    'locked'     => 'Sua conta está bloqueada. Entre em contato com o suporte para desbloqueá-la.',
    'unverified' => 'Você precisa verificar seu endereço de e-mail antes de fazer login. Verifique sua caixa de entrada.',

    // Fortify 2FA (status e textos da interface)
    'two_factor' => [
        'title'              => 'Autenticação em duas etapas',
        'header'                 => 'Autenticação em duas etapas',
        'enabled'                  => 'Autenticação em duas etapas ativada.',
        'confirmed'                => 'Autenticação em duas etapas confirmada.',
        'disabled'                 => 'Autenticação em duas etapas desativada.',
        'recovery_codes_generated' => 'Novos códigos de recuperação gerados.',
        'remember_device'   => 'Lembrar deste dispositivo por 30 dias',
        'enter_code'        => 'Digite o código de 6 dígitos',
        'use_recovery'      => 'Usar um código de recuperação',
        'use_authenticator' => 'Usar o aplicativo autenticador',
        'code'              => 'Código de autenticação',
        'recovery_code'     => 'Código de recuperação',
        'confirm'           => 'Confirmar',
    ],

    // Tela de bloqueio por tentativas
    'too_many_attempts' => [
        'title'        => 'Muitas tentativas',
        'intro'        => 'Você fez muitas tentativas de login.',
        'blocked_for'  => 'Bloqueado por ~:minutes min',
        'retry_in'     => 'Você poderá tentar novamente em',
        'seconds_hint' => 'O tempo será atualizado automaticamente.',
        'generic_wait' => 'Por favor, aguarde antes de tentar novamente.',
        'back'         => 'Voltar',
        'go_login'     => 'Ir para o login',
    ],

    'throttle_page' => [
        'title'         => 'Muitas tentativas',
        'message'       => 'Você fez muitas tentativas de login.',
        'retry_in'      => 'Você poderá tentar novamente em',
        'minutes_abbr'  => 'min',
        'seconds_abbr'  => 's',
        'total_seconds' => 'segundos totais',
        'redirecting'   => 'Redirecionando…',
    ],
    'remember_me' => 'Lembrar-me',
    'forgot_password' => 'Esqueceu a senha?',
    'send_link' => 'Enviar link',
    'confirm_password' => 'Confirmar senha',

    'login' => [
        'remaining_attempts' => '{0} Credenciais inválidas.|{1} Credenciais inválidas. Você tem mais 1 tentativa antes do bloqueio.|[2,*] Credenciais inválidas. Você tem mais :count tentativas antes do bloqueio.',
    ],

    'account' => [
        'locked'   => 'Sua conta está bloqueada. Verifique seu e-mail para desbloqueá-la.',
        'unlocked' => 'Sua conta foi desbloqueada. Agora você pode fazer login.',
    ],
];
