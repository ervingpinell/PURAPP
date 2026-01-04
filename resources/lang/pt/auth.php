<?php

return [
    // Mensagens padrão do Laravel
    'failed'   => 'O e-mail ou a senha estão incorretos.',
    'password' => 'A senha fornecida está incorreta.',
    'throttle' => 'Muitas tentativas de login. Tente novamente em :seconds segundos.',

    // Mensagens específicas do negócio
    'inactive'   => 'Sua conta está inativa. Entre em contato com o suporte para reativá-la.',
    'locked'     => 'Sua conta está bloqueada. Entre em contato com o suporte para desbloqueá-la.',
    'unverified' => 'Você deve verificar seu e-mail antes de fazer login. Verifique sua caixa de entrada.',

    // Fortify 2FA (status e textos da UI)
    'two_factor' => [
        'title'                   => 'Autenticação em duas etapas',
        'header'                  => 'Autenticação em duas etapas',
        'enabled'                 => 'Autenticação em duas etapas ativada.',
        'confirmed'               => 'Autenticação em duas etapas confirmada.',
        'disabled'                => 'Autenticação em duas etapas desativada.',
        'recovery_codes_generated' => 'Novos códigos de recuperação foram gerados.',
        'remember_device'         => 'Lembrar deste dispositivo por 30 dias',
        'enter_code'              => 'Digite o código de 6 dígitos',
        'use_recovery'            => 'Usar um código de recuperação',
        'use_authenticator'       => 'Usar aplicativo autenticador',
        'code'                    => 'Código de autenticação',
        'recovery_code'           => 'Código de recuperação',
        'confirm'                 => 'Confirmar',
    ],

    // Tela de throttle
    'too_many_attempts' => [
        'title'        => 'Muitas tentativas',
        'intro'        => 'Você realizou muitas tentativas de login.',
        'blocked_for'  => 'Bloqueado por ~:minutes min',
        'retry_in'     => 'Você poderá tentar novamente em',
        'seconds_hint' => 'O tempo será atualizado automaticamente.',
        'generic_wait' => 'Aguarde um momento antes de tentar novamente.',
        'back'         => 'Voltar',
        'go_login'     => 'Ir para login',
    ],

    'throttle_page' => [
        'title'         => 'Muitas tentativas',
        'message'       => 'Você realizou muitas tentativas de login.',
        'retry_in'      => 'Você poderá tentar novamente em',
        'minutes_abbr'  => 'min',
        'seconds_abbr'  => 's',
        'total_seconds' => 'segundos totais',
        'redirecting'   => 'Redirecionando…',
    ],

    'remember_me'      => 'Lembrar de mim',
    'forgot_password'  => 'Esqueceu sua senha?',
    'send_link'        => 'Enviar link',
    'confirm_password' => 'Confirmar senha',

    'login' => [
        'remaining_attempts' => '{0} Credenciais inválidas.|{1} Credenciais inválidas. Você ainda tem 1 tentativa antes do bloqueio.|[2,*] Credenciais inválidas. Você ainda tem :count tentativas antes do bloqueio.',
    ],

    'account' => [
        'locked'   => 'Sua conta está bloqueada. Verifique seu e-mail para desbloqueá-la.',
        'unlocked' => 'Sua conta foi desbloqueada. Agora você pode fazer login.',
    ],

    'verify.verified' => 'Seu e-mail foi verificado. Agora você já pode fazer login.',

    'verify' => [
        'already'  => 'Você já verificou seu e-mail,',
        'verified' => 'Seu e-mail foi verificado.',
    ],
    'email_change_subject'        => 'Confirme a alteração do seu endereço de e-mail',
    'email_change_title'          => 'Confirme seu novo endereço de e-mail',
    'email_change_hello'          => 'Olá :name,',
    'email_change_intro'          => 'Você solicitou alterar o endereço de e-mail associado à sua conta. Para concluir a alteração, clique no botão abaixo:',
    'email_change_button'         => 'Confirmar novo e-mail',
    'email_change_footer'         => 'Se você não solicitou esta alteração, basta ignorar esta mensagem e seu e-mail atual permanecerá o mesmo.',
    'email_change_link_expired'   => 'O link para alterar seu e-mail expirou. Por favor, solicite a alteração novamente no seu perfil.',
    'email_change_confirmed'      => 'Seu endereço de e-mail foi atualizado e verificado com sucesso.',
    'create_account' => 'Criar Conta',
];
