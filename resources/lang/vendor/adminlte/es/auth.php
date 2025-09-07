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

    // 1. Greetings
    'hello' => 'Hola',

    // 2. Authentication Messages
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

    // 3. Password Validations
    'passwords' => [
        'reset'     => 'Tu contraseña ha sido restablecida.',
        'sent'      => 'Hemos enviado por correo electrónico su enlace de restablecimiento de contraseña.',
        'throttled' => 'Por favor espera antes de volver a intentarlo.',
        'token'     => 'Este token de restablecimiento de contraseña no es válido.',
        'user'      => "No podemos encontrar un usuario con esa dirección de correo electrónico.",
        'match'     => 'Las contraseñas coinciden.',
        'link_sent' => 'Se ha enviado un enlace de restablecimiento de contraseña a su dirección de correo electrónico.',
    ],

    // 4. Password Requirements
    'password_requirements_title' => 'Tu contraseña debe contener:',
    'password_requirements' => [
        'length'  => '- Al menos 8 caracteres',
        'special' => '- 1 carácter especial ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 número',
    ],

    // 5. Buttons and Actions
    'sign_in' => 'Iniciar sesión',
    'sign_out' => 'Cerrar sesión',
    'register' => 'Registrarse',
    'send_link' => 'Enviar enlace',
    'confirm_password' => 'Confirmar contraseña',

    // 6. Account Lock/Unlock
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

    // 7. Email Verification
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

    // 8. Unauthorized Admin
    'unauthorized_admin' => 'Acceso denegado. No tienes permisos para acceder al panel administrativo.',

    // 9. Account Locked Message
    'locked' => 'Tu cuenta está bloqueada. Revisa tu correo para desbloquearla o contacta soporte.',

    // 10. Two Factor Authentication
    'two_factor' => [
        'title'                => 'Verificación en dos pasos',
        'message'              => 'Introduce tu código de autenticación o un código de recuperación.',
        'code_placeholder'     => 'Código 123456',
        'recovery_placeholder' => 'Código de recuperación',
        'remember_device'      => 'Recordar este dispositivo',
        'verify_button'        => 'Verificar',
    ],

    // 11. Two Factor Notices
    'notices' => [
        'two_factor_confirmed'             => 'Autenticación en dos pasos activada.',
        'two_factor_recovery_regenerated'  => 'Se han regenerado tus códigos de recuperación.',
        'two_factor_disabled'              => 'La autenticación en dos pasos ha sido desactivada.',
        'two_factor_invalid_code'          => 'El código no es válido. Intenta de nuevo.',
        'two_factor_invalid_recovery_code' => 'El código de recuperación no es válido o ya fue usado.',
    ],

];
