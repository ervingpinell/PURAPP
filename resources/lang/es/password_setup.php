<?php

return [
    // Email
    'email_subject' => 'Completa la configuración de tu cuenta',

    // Setup page
    'title' => 'Completa la configuración de tu cuenta',
    'welcome' => '¡Bienvenido, :name!',
    'booking_confirmed' => 'Tu reserva #:reference ha sido confirmada.',
    'create_password' => 'Crea una contraseña para:',
    'benefits' => [
        'view_bookings' => 'Ver todas tus reservas',
        'manage_profile' => 'Gestionar tu perfil',
        'exclusive_offers' => 'Recibir ofertas exclusivas',
    ],
    'password_label' => 'Contraseña',
    'confirm_password_label' => 'Confirmar contraseña',
    'submit_button' => 'Crear mi cuenta',
    'maybe_later' => 'Tal vez más tarde',

    // Validation
    'password_requirements' => 'La contraseña debe contener al menos 1 número y 1 carácter especial (.¡!@#$%^&*()_+-)',
    'password_min_length' => 'La contraseña debe tener al menos 8 caracteres',
    'requirements' => [
        'one_number' => 'Al menos 1 número',
        'one_special_char' => 'Al menos 1 carácter especial',
    ],

    // Email Welcome
    'email_welcome_subject' => '¡Bienvenido a ' . config('app.name') . '!',
    'email_welcome_title' => '¡Bienvenido, :name!',
    'email_welcome_text' => 'Tu cuenta ha sido creada exitosamente. Ahora puedes acceder a tus reservas y gestionar tu perfil.',
    'email_action_button' => 'Ir a Mi Panel',

    // JS / Strength
    'strength' => [
        'weak' => 'Débil',
        'medium' => 'Media',
        'strong' => 'Fuerte',
    ],
    'passwords_do_not_match' => 'Las contraseñas no coinciden',
    'creating_account' => 'Creando cuenta...',
    'payment_success_message' => '¡Pago Exitoso y Reserva Confirmada!',

    // Messages
    'token_expired' => 'Este enlace ha expirado. Por favor solicita uno nuevo.',
    'token_invalid' => 'Enlace de configuración inválido.',
    'expires_in' => 'Este enlace expira en :days días',
    'fallback_link' => 'Si el botón no funciona, copia y pega este enlace en tu navegador:',
    'success' => '¡Cuenta creada exitosamente! Ya puedes iniciar sesión.',
    'user_not_found' => 'Usuario no encontrado.',
    'already_has_password' => 'Este usuario ya tiene una contraseña configurada.',
    'too_many_requests' => 'Demasiadas solicitudes. Por favor intenta más tarde.',
    'send_failed' => 'Error al enviar el correo. Por favor intenta más tarde.',
];
