<?php

return [
    // Mensajes estándar de Laravel
    'failed'   => 'El correo o la contraseña no son correctos.',
    'password' => 'La contraseña proporcionada es incorrecta.',
    'throttle' => 'Demasiados intentos de inicio de sesión. Inténtalo de nuevo en :seconds segundos.',

    // Mensajes específicos de tu negocio
    'inactive'   => 'Tu cuenta está inactiva. Contacta al soporte para reactivarla.',
    'locked'     => 'Tu cuenta está bloqueada. Contacta al soporte para desbloquearla.',
    'unverified' => 'Debes verificar tu correo electrónico antes de iniciar sesión. Revisa tu bandeja de entrada.',

    // Fortify 2FA (status y textos de UI)
    'two_factor' => [
        'enabled'                  => 'Autenticación en dos pasos activada.',
        'confirmed'                => 'Autenticación en dos pasos confirmada.',
        'disabled'                 => 'Autenticación en dos pasos desactivada.',
        'recovery_codes_generated' => 'Se generaron nuevos códigos de recuperación.',
        'remember_device'   => 'Recordar este dispositivo durante 30 días',
        'enter_code'        => 'Introduce el código de 6 dígitos',
        'use_recovery'      => 'Usar un código de recuperación',
        'use_authenticator' => 'Usar app autenticadora',
        'code'              => 'Código de autenticación',
        'recovery_code'     => 'Código de recuperación',
        'confirm'           => 'Confirmar',
    ],

    // Pantalla de throttle
    'too_many_attempts' => [
        'title'        => 'Demasiados intentos',
        'intro'        => 'Has realizado demasiados intentos de inicio de sesión.',
        'blocked_for'  => 'Bloqueado por ~:minutes min',
        'retry_in'     => 'Podrás volver a intentarlo en',
        'seconds_hint' => 'El tiempo se actualizará automáticamente.',
        'generic_wait' => 'Espera un momento antes de volver a intentarlo.',
        'back'         => 'Volver',
        'go_login'     => 'Ir al inicio de sesión',
    ],

    'throttle_page' => [
    'title'         => 'Demasiados intentos',
    'message'       => 'Has realizado demasiados intentos de inicio de sesión.',
    'retry_in'      => 'Podrás volver a intentarlo en',
    'minutes_abbr'  => 'min',
    'seconds_abbr'  => 's',
    'total_seconds'  => 'segundos totales',
    'redirecting'   => 'Redirigiendo…',
],
    'remember_me' => 'Recuérdame',
    'forgot_password' => '¿Olvidaste tu contraseña?',
    'send_link' => 'Enviar enlace',
    'confirm_password' => 'Confirmar contraseña',

    'login' => [
        'remaining_attempts' => '{0} Credenciales inválidas.|{1} Credenciales inválidas. Te queda 1 intento antes del bloqueo.|[2,*] Credenciales inválidas. Te quedan :count intentos antes del bloqueo.',
    ],

     'account' => [
        'locked'   => 'Tu cuenta está bloqueada. Revisa tu correo para desbloquearla.',
        'unlocked' => 'Tu cuenta ha sido desbloqueada. Ya puedes iniciar sesión.',
    ],
];
