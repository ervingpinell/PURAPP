<?php

return [

    'hello' => 'Hola',
    /*
    |--------------------------------------------------------------------------
    | Autenticación (Login, Register, Forgot Password, Reset Password)
    |--------------------------------------------------------------------------
    */

    'login_message' => 'Inicia sesión para comenzar tu sesión',
    'register_message' => 'Registra una nueva cuenta',
    'password_reset_message' => 'Restablecer contraseña',
    'reset_password' => 'Restablecer contraseña',
    'send_password_reset_link' => 'Enviar enlace de restablecimiento',
    'i_forgot_my_password' => 'Olvidé mi contraseña',
    'back_to_login' => 'Volver al login',
    'verify_message' => 'Tu cuenta necesita una verificación de correo',
    'verify_email_sent' => 'Se ha enviado un nuevo enlace de verificación a tu correo electrónico.',
    'verify_check_email' => 'Antes de continuar, revisa tu correo electrónico para obtener el enlace de verificación.',
    'verify_if_not_received' => 'Si no recibiste el correo',
    'verify_request_another' => 'haz clic aquí para solicitar otro',

    /*
    |--------------------------------------------------------------------------
    | Validaciones específicas de Auth (usadas en AdminLTE::validation)
    |--------------------------------------------------------------------------
    */
    'passwords' => [
    'reset'     => 'Tu contraseña ha sido restablecida.',
    'sent'      => 'Hemos enviado por correo electrónico su enlace de restablecimiento de contraseña.',
    'throttled' => 'Por favor espera antes de volver a intentarlo.',
    'token'     => 'Este token de restablecimiento de contraseña no es válido.',
    'user'      => "No podemos encontrar un usuario con esa dirección de correo electrónico.",
    'match' => 'Las contraseñas coinciden.',
    'link_sent' => 'Se ha enviado un enlace de restablecimiento de contraseña a su dirección de correo electrónico.',
],


    'password_requirements_title' => 'Tu contraseña debe contener:',
    'password_requirements' => [
        'length'  => '- Al menos 8 caracteres',
        'special' => '- 1 carácter especial ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 número',
    ],

    /*
    |--------------------------------------------------------------------------
    | Botones y Acciones
    |--------------------------------------------------------------------------
    */

    'sign_in' => 'Iniciar sesión',
    'sign_out' => 'Cerrar sesión',
    'register' => 'Registrarse',
    'send_link' => 'Enviar enlace',
    'confirm_password' => 'Confirmar contraseña',



'account' => [
    'locked_title'     => 'Tu cuenta ha sido bloqueada',
    'locked_message'   => 'Has superado el número de intentos permitidos. Por seguridad, tu cuenta fue bloqueada temporalmente.',
    'unlock_hint'      => 'Ingresa tu correo y te enviaremos un enlace para desbloquear tu cuenta.',
    'send_unlock'      => 'Enviar enlace de desbloqueo',
    'unlock_link_sent' => 'Si la cuenta existe y está bloqueada, enviamos un enlace de desbloqueo.',
    'unlock_mail_subject' => 'Desbloqueo de cuenta',
    'unlock_mail_intro'   => 'Recibimos una solicitud para desbloquear tu cuenta.',
    'unlock_mail_action'  => 'Desbloquear mi cuenta',
    'unlock_mail_outro'   => 'Si no fuiste tú, ignora este correo.',
    'unlocked'            => 'Tu cuenta ha sido desbloqueada. Ya puedes iniciar sesión.',
    'locked'              => 'Tu cuenta está bloqueada.',

],
    'verify' => [
        'title'     => 'Verifica tu correo electrónico',
        'message'   => 'Antes de continuar, por favor verifica tu correo. Te hemos enviado un enlace de verificación.',
        'resend'    => 'Reenviar correo de verificación',
        'link_sent' => 'Te hemos enviado un nuevo enlace de verificación.',
        'subject'   => 'Verifica tu correo electrónico',
        'intro'     => 'Por favor haz clic en el botón para verificar tu dirección de correo electrónico.',
        'action'    => 'Verificar correo electrónico',
        'outro'     => 'Si no creaste esta cuenta, por favor ignora este mensaje.',
        'browser_hint' => 'Si tienes problemas para hacer clic en el botón ":action", copia y pega esta URL en tu navegador: :url',
        'verified_success' => 'Tu correo ha sido verificado con éxito.',
            'verify_email_title'        => 'Verifica tu correo',
    'verify_email_header'       => 'Revisa tu bandeja de entrada',
    'verify_email_sent'         => 'Te enviamos un enlace de verificación a tu correo.',
    'verify_email_sent_to'      => 'Acabamos de enviar el enlace de verificación a:',
    'verify_email_generic'      => 'Acabamos de enviarte un enlace de verificación a tu correo.',
    'verify_email_instructions' => 'Abre el correo y haz clic en el enlace para activar tu cuenta. Si no lo ves, revisa la carpeta de spam.',
    'back_to_login'             => 'Volver a iniciar sesión',
    'back_to_home'              => 'Ir al inicio',
        ],

];
