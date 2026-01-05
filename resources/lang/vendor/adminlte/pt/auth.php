<?php

/*
|--------------------------------------------------------------------------
|  1. Greetings ............................................. Line 10
|  2. Authentication Messages ............................... Line 13
|  3. Password Validations .................................. Line 27
|  4. Password Requirements ................................. Line 38
|  5. Buttons and Actions ................................... Line 46
|  6. Account Lock/Unlock ................................... Line 53
|  7. Email Verification .................................... Line 67
|  8. Unauthorized Admin .................................... Line 87
|  9. Account Locked Message ................................ Line 90
| 10. Two Factor Authentication ............................. Line 93
| 11. Two Factor Notices .................................... Line 102
|--------------------------------------------------------------------------
*/

return [
'captcha_failed' => 'A verificação do captcha falhou.',

    // 1. Greetings
    'hello' => 'Olá',
    'captcha_failed' => 'Falló el captcha',
    // 2. Authentication Messages
    'login_message' => 'Faça login para iniciar sua sessão',
    'register_message' => 'Registrar uma nova conta',
    'password_reset_message' => 'Redefinir senha',
    'reset_password' => 'Redefinir senha',
    'send_password_reset_link' => 'Enviar link de redefinição',
    'i_forgot_my_password' => 'Esqueci minha senha',
    'back_to_login' => 'Voltar para o login',
    'verify_message' => 'Sua conta precisa de confirmação de e-mail',
    'verify_email_sent' => 'Um novo link de confirmação foi enviado para seu e-mail.',
    'verify_check_email' => 'Por favor, verifique seu e-mail para o link de confirmação antes de continuar.',
    'verify_if_not_received' => 'Se você não recebeu o e-mail',
    'verify_request_another' => 'clique aqui para solicitar outro',

    // 3. Password Validations
    'passwords' => [
        'reset'     => 'Sua senha foi redefinida.',
        'sent'      => 'Enviamos o link de redefinição de senha para seu e-mail.',
        'throttled' => 'Por favor, aguarde antes de tentar novamente.',
        'token'     => 'Este token de redefinição de senha é inválido.',
        'user'      => "Não conseguimos encontrar um usuário com esse endereço de e-mail.",
        'match'     => 'As senhas coincidem.',
        'link_sent' => 'Um link de redefinição de senha foi enviado para seu e-mail.',
        'resent' => 'Um novo link de verificação foi enviado para o seu endereço de e-mail.',
    ],

    // 4. Password Requirements
    'password_requirements_title' => 'Sua senha deve conter:',
    'password_requirements' => [
        'length'  => '- Pelo menos 8 caracteres',
        'special' => '- 1 caractere especial ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 número',
    ],

    // 5. Buttons and Actions
    'sign_in' => 'Entrar',
    'sign_out' => 'Sair',
    'register' => 'Registrar',
    'send_link' => 'Enviar link',
    'confirm_password' => 'Confirmar senha',

    // 6. Account Lock/Unlock
    'account' => [
        'locked_title'     => 'Sua conta foi bloqueada',
        'locked_message'   => 'Você excedeu o número permitido de tentativas. Por motivos de segurança, sua conta foi temporariamente bloqueada.',
        'unlock_hint'      => 'Digite seu e-mail e enviaremos um link para desbloquear sua conta.',
        'send_unlock'      => 'Enviar link de desbloqueio',
        'unlock_link_sent' => 'Se a conta existir e estiver bloqueada, enviamos um link de desbloqueio.',
        'unlock_mail_subject' => 'Desbloquear conta',
        'unlock_mail_intro'   => 'Recebemos uma solicitação para desbloquear sua conta.',
        'unlock_mail_action'  => 'Desbloquear minha conta',
        'unlock_mail_outro'   => 'Se não foi você, por favor ignore este e-mail.',
        'unlocked'            => 'Sua conta foi desbloqueada. Agora você pode fazer login.',
        'locked' => 'Sua conta está bloqueada. Verifique seu e-mail.',
    ],

    // 7. Email Verification
    'verify' => [
        'title'     => 'Confirme seu endereço de e-mail',
        'message'   => 'Por favor, confirme seu e-mail antes de continuar. Enviamos um link de confirmação para você.',
        'resend'    => 'Reenviar e-mail de confirmação',
        'link_sent' => 'Enviamos um novo link de confirmação para você.',
        'resent' => 'Um novo link de verificação foi enviado para o seu endereço de e-mail.',
        'sent_to'      => 'Enviamos um link de verificação para :email.',
'email_label'  => 'E-mail',
        'subject'   => 'Confirme seu endereço de e-mail',
        'intro'     => 'Por favor, clique no botão para confirmar seu endereço de e-mail.',
        'action'    => 'Confirmar endereço de e-mail',
        'outro'     => 'Se você não criou esta conta, por favor ignore esta mensagem.',
        'browser_hint' => 'Se você tiver problemas ao clicar no botão ":action", copie e cole esta URL no seu navegador: :url',
        'verified_success' => 'Seu e-mail foi confirmado com sucesso.',
        'verify_email_title'        => 'Confirmar e-mail',
        'verify_email_header'       => 'Verifique sua caixa de entrada',
        'verify_email_sent'         => 'Enviamos um link de confirmação para seu e-mail.',
        'verify_email_sent_to'      => 'Acabamos de enviar o link de confirmação para:',
        'verify_email_generic'      => 'Acabamos de enviar um link de confirmação para seu e-mail.',
        'verify_email_instructions' => 'Abra o e-mail e clique no link para ativar sua conta. Se não encontrar o e-mail, verifique sua caixa de spam.',
        'back_to_login'             => 'Voltar para o login',
        'back_to_home'              => 'Voltar para a página inicial',
    ],

    // 8. Unauthorized Admin
    'unauthorized_admin' => 'Acesso negado. Você não tem permissão para acessar o painel de administração.',

    // 9. Account Locked Message
    'locked' => 'Sua conta está bloqueada. Verifique seu e-mail para desbloqueá-la ou entre em contato com o suporte.',

    // 10. Two Factor Authentication
    'two_factor' => [
        'title'                => 'Autenticação em dois fatores',
        'message'              => 'Digite seu código de autenticação ou um código de recuperação.',
        'code_placeholder'     => 'Código 123456',
        'recovery_placeholder' => 'Código de recuperação',
        'remember_device'      => 'Lembrar deste dispositivo',
        'verify_button'        => 'Verificar',
    ],

    // 11. Two Factor Notices
    'notices' => [
        'two_factor_confirmed'             => 'Autenticação em dois fatores ativada.',
        'two_factor_recovery_regenerated'  => 'Seus códigos de recuperação foram regenerados.',
        'two_factor_disabled'              => 'Autenticação em dois fatores desativada.',
        'two_factor_invalid_code'          => 'O código é inválido. Por favor, tente novamente.',
        'two_factor_invalid_recovery_code' => 'O código de recuperação é inválido ou já foi utilizado.',
    ],

];
