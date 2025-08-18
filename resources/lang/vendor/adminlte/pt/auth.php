<?php

return [

    'hello' => 'Olá',

    'login_message' => 'Faça login para iniciar sua sessão',
    'register_message' => 'Registrar uma nova conta',
    'password_reset_message' => 'Redefinir senha',
    'reset_password' => 'Redefinir senha',
    'send_password_reset_link' => 'Enviar link de redefinição',
    'i_forgot_my_password' => 'Esqueci minha senha',
    'back_to_login' => 'Voltar ao login',
    'verify_message' => 'Sua conta precisa de verificação de e-mail',
    'verify_email_sent' => 'Um novo link de verificação foi enviado para o seu e-mail.',
    'verify_check_email' => 'Antes de continuar, verifique seu e-mail para o link de verificação.',
    'verify_if_not_received' => 'Se você não recebeu o e-mail',
    'verify_request_another' => 'clique aqui para solicitar outro',

    'passwords' => [
        'reset'     => 'Sua senha foi redefinida.',
        'sent'      => 'Enviamos por e-mail o link de redefinição de senha.',
        'throttled' => 'Por favor, aguarde antes de tentar novamente.',
        'token'     => 'Este token de redefinição de senha é inválido.',
        'user'      => "Não conseguimos encontrar um usuário com esse e-mail.",
        'match'     => 'As senhas coincidem.',
        'link_sent' => 'Um link de redefinição foi enviado para o seu e-mail.',
    ],

    'password_requirements_title' => 'Sua senha deve conter:',
    'password_requirements' => [
        'length'  => '- Pelo menos 8 caracteres',
        'special' => '- 1 caractere especial ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 número',
    ],

    'sign_in' => 'Entrar',
    'sign_out' => 'Sair',
    'register' => 'Registrar',
    'send_link' => 'Enviar link',
    'confirm_password' => 'Confirmar senha',

    'account' => [
        'locked_title'     => 'Sua conta foi bloqueada',
        'locked_message'   => 'Você excedeu o número de tentativas permitidas. Por segurança, sua conta foi temporariamente bloqueada.',
        'unlock_hint'      => 'Digite seu e-mail e enviaremos um link de desbloqueio.',
        'send_unlock'      => 'Enviar link de desbloqueio',
        'unlock_link_sent' => 'Se a conta existir e estiver bloqueada, enviamos um link de desbloqueio.',
        'unlock_mail_subject' => 'Desbloqueio de conta',
        'unlock_mail_intro'   => 'Recebemos uma solicitação para desbloquear sua conta.',
        'unlock_mail_action'  => 'Desbloquear minha conta',
        'unlock_mail_outro'   => 'Se não foi você, ignore este e-mail.',
        'unlocked'            => 'Sua conta foi desbloqueada. Você já pode entrar.',
        'locked'              => 'Sua conta está bloqueada.',
    ],

    'verify' => [
        'title'     => 'Verifique seu e-mail',
        'message'   => 'Antes de continuar, verifique seu e-mail. Enviamos um link de verificação.',
        'resend'    => 'Reenviar e-mail de verificação',
        'link_sent' => 'Enviamos um novo link de verificação.',
        'subject'   => 'Verifique seu e-mail',
        'intro'     => 'Clique no botão para verificar seu e-mail.',
        'action'    => 'Verificar e-mail',
        'outro'     => 'Se você não criou esta conta, ignore esta mensagem.',
        'browser_hint' => 'Se tiver problemas ao clicar no botão ":action", copie e cole esta URL no navegador: :url',
        'verified_success' => 'Seu e-mail foi verificado com sucesso.',
        'verify_email_title'        => 'Verifique seu e-mail',
        'verify_email_header'       => 'Verifique sua caixa de entrada',
        'verify_email_sent'         => 'Enviamos um link de verificação para seu e-mail.',
        'verify_email_sent_to'      => 'Acabamos de enviar o link de verificação para:',
        'verify_email_generic'      => 'Acabamos de enviar um link de verificação para o seu e-mail.',
        'verify_email_instructions' => 'Abra o e-mail e clique no link para ativar sua conta. Se não encontrar, verifique a pasta de spam.',
        'back_to_login'             => 'Voltar ao login',
        'back_to_home'              => 'Voltar ao início',
    ],

];
