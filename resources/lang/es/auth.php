<?php

use Illuminate\Auth\Events\Verified;

use function Livewire\Volt\title;

return [
    // Mensajes estándar de Laravel
    'failed'   => 'El correo o la contraseña no son correctos.',
    'password' => 'La contraseña proporcionada es incorrecta.',
    'throttle' => 'Demasiados intentos de inicio de sesión. Inténtalo de nuevo en :seconds segundos.',
    'captcha_failed' => 'La verificación CAPTCHA falló. Por favor, inténtalo de nuevo.',

    // Mensajes específicos de tu negocio
    'inactive'   => 'Tu cuenta está inactiva. Contacta al soporte para reactivarla.',
    'locked'     => 'Tu cuenta está bloqueada. Contacta al soporte para desbloquearla.',
    'unverified' => 'Debes verificar tu correo electrónico antes de iniciar sesión. Revisa tu bandeja de entrada.',

    // Fortify 2FA (status y textos de UI)
    'two_factor' => [
        'title'              => 'Autenticación en dos pasos',
        'header'                 => 'Autenticación en dos pasos',
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
    'verify.verified' => 'Tu correo ha sido verificado. Ya puedes iniciar sesión.',

    'verify' => [
        'already'     => 'Ya verificaste tu correo,',
        'verified'        => 'Tu correo ha sido verificado.',
    ],

    'email_change_subject'        => 'Confirma el cambio de correo electrónico',
    'email_change_title'          => 'Confirma tu nuevo correo electrónico',
    'email_change_hello'          => 'Hola :name,',
    'email_change_intro'          => 'Has solicitado cambiar el correo electrónico asociado a tu cuenta. Para completar el cambio, haz clic en el siguiente botón:',
    'email_change_button'         => 'Confirmar nuevo correo',
    'email_change_footer'         => 'Si tú no solicitaste este cambio, puedes ignorar este mensaje y tu correo actual seguirá siendo el mismo.',
    'email_change_link_expired'   => 'El enlace para cambiar tu correo ha expirado. Por favor, vuelve a solicitar el cambio desde tu perfil.',
    'email_change_confirmed'      => 'Tu correo electrónico ha sido actualizado y verificado correctamente.',

    'reset_password' => [
        'subject'    => 'Notificación de restablecimiento de contraseña',
        'greeting'   => '¡Hola!',
        'line1'      => 'Estás recibiendo este correo porque recibimos una solicitud de restablecimiento de contraseña para tu cuenta.',
        'action'     => 'Restablecer contraseña',
        'line2'      => 'Este enlace de restablecimiento de contraseña expirará en :count minutos.',
        'line3'      => 'Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna otra acción.',
        'salutation' => 'Saludos,',
    ],

    'email_updated_notification' => [
        'subject'         => 'Tu correo electrónico ha sido actualizado',
        'greeting'        => '¡Hola!',
        'message'         => 'La dirección de correo electrónico de tu cuenta ha sido actualizada correctamente a: :email',
        'contact_support' => 'Si no fuiste tú, contacta a soporte.',
        'salutation'      => 'Saludos,',
    ],

    'password_updated_notification' => [
        'subject'         => 'Tu contraseña ha sido actualizada',
        'greeting'        => '¡Hola!',
        'line1'           => 'Te informamos que la contraseña de tu cuenta ha sido cambiada exitosamente.',
        'line2'           => 'Si no realizaste este cambio, por favor contacta a soporte inmediatamente.',
        'action'          => 'Iniciar Sesión',
        'salutation'      => 'Saludos,',
    ],

    // Password setup (guest to registered)
    'no_password_set' => 'Aún no has creado tu contraseña.',
    'send_setup_link' => 'Enviar enlace de configuración',
    'setup_link_sent' => 'Enlace de configuración enviado a tu correo.',
    'create_account' => 'Crear Cuenta',
    'verify_email' => [
        'subject' => 'Verificar correo electrónico',
        'title' => 'Verifique su dirección de correo',
        'line_1' => 'Haga clic en el botón de abajo para verificar su dirección de correo electrónico.',
        'action' => 'Verificar Correo',
        'line_2' => 'Si no creó una cuenta, no se requiere ninguna otra acción.',
        'button_trouble' => 'Si tiene problemas para hacer clic en el botón ":actionText", copie y pegue la URL a continuación en su navegador web:',
    ],
    'account_created_verify_email' => 'Cuenta creada con éxito. Por favor verifique su correo electrónico para activar su cuenta.',
];
