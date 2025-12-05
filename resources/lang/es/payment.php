<?php

return [
    // Payment Page
    'payment' => 'Pago',
    'stripe_description' => 'Pago con Tarjeta de Crédito/Débito',
    'paypal_description' => 'Pago con PayPal',
    'tilopay_description' => 'Pago con Tarjeta de Crédito/Débito (Tilopay)',
    'banco_nacional_description' => 'Transferencia Banco Nacional',
    'bac_description' => 'Transferencia BAC Credomatic',
    'bcr_description' => 'Transferencia Banco de Costa Rica',
    'payment_information' => 'Información de Pago',
    'select_payment_method' => 'Selecciona tu método de pago',
    'secure_payment' => 'Pago Seguro',
    'payment_secure_encrypted' => 'Tu pago es seguro y está encriptado',
    'powered_by_stripe' => 'Powered by Stripe. La información de tu tarjeta nunca se almacena en nuestros servidores.',
    'pay' => 'Pagar',
    'back' => 'Volver',
    'processing' => 'Procesando...',
    'terms_agreement' => 'Al completar este pago, aceptas nuestros términos y condiciones.',
    'terms_agreement_checkbox' => 'He leído y acepto los <a href=":url" target="_blank">Términos y Condiciones</a>.',
    'terms_required' => 'Debes aceptar los términos y condiciones para continuar.',

    // Order Summary
    'order_summary' => 'Resumen del Pedido',
    'subtotal' => 'Subtotal',
    'total' => 'Total',
    'participants' => 'participantes',
    'free_cancellation' => 'Cancelación gratuita disponible',

    // Confirmation Page
    'payment_successful' => '¡Pago Exitoso!',
    'booking_confirmed' => 'Tu reserva ha sido confirmada',
    'booking_reference' => 'Referencia de Reserva',
    'what_happens_next' => '¿Qué sigue?',
    'view_my_bookings' => 'Ver Mis Reservas',
    'back_to_home' => 'Volver al Inicio',

    // Next Steps
    'next_step_email' => 'Recibirás un correo de confirmación con todos los detalles de tu reserva',
    'next_step_confirmed' => 'Tu tour está confirmado para la fecha y hora seleccionadas',
    'next_step_manage' => 'Puedes ver y gestionar tu reserva en "Mis Reservas"',
    'next_step_support' => 'Si tienes alguna pregunta, por favor contacta a nuestro equipo de soporte',

    // Countdown Timer
    'time_remaining' => 'Tiempo Restante',
    'complete_payment_in' => 'Completa tu pago en',
    'payment_expires_in' => 'El pago expira en',
    'session_expired' => 'Tu sesión de pago ha expirado',
    'session_expired_message' => 'Por favor regresa a tu carrito e intenta nuevamente.',

    // Errors
    'payment_failed' => 'Pago Fallido',
    'payment_error' => 'Ocurrió un error al procesar tu pago',
    'payment_declined' => 'Tu pago fue rechazado',
    'try_again' => 'Por favor intenta nuevamente',
    'no_pending_bookings' => 'No se encontraron reservas pendientes',
    'bookings_not_found' => 'Reservas no encontradas',
    'payment_not_successful' => 'El pago no fue exitoso. Por favor intenta nuevamente.',
    'payment_confirmation_error' => 'Ocurrió un error al confirmar tu pago.',

    // Progress Steps
    'checkout' => 'Checkout',
    'confirmation' => 'Confirmación',

    // Messages
    'complete_payment_message' => 'Por favor completa el pago para confirmar tu reserva',
    'payment_cancelled' => 'El pago fue cancelado. Puedes intentar nuevamente cuando estés listo.',
    'redirect_paypal' => 'Haz clic en Pagar para ser redirigido a PayPal y completar tu pago de forma segura.',
    'no_cart_data' => 'No se encontraron datos del carrito',
    'gateway_error' => 'Error de conexión con la pasarela de pago. Por favor verifica tu conexión a internet e intenta nuevamente.',

    // Admin / Management (merged from m_payments)
    'ui' => [
        'page_title' => 'Pagos',
        'page_heading' => 'Gestión de Pagos',
        'payment_details' => 'Detalles del Pago',
        'payments_list' => 'Lista de Pagos',
        'filters' => 'Filtros',
        'actions' => 'Acciones',
        'quick_actions' => 'Acciones Rápidas',
    ],

    'statistics' => [
        'total_revenue' => 'Ingresos Totales',
        'completed_payments' => 'Pagos Completados',
        'pending_payments' => 'Pagos Pendientes',
        'failed_payments' => 'Pagos Fallidos',
    ],

    'fields' => [
        'payment_id' => 'ID de Pago',
        'booking_ref' => 'Ref. Reserva',
        'customer' => 'Cliente',
        'tour' => 'Tour',
        'amount' => 'Monto',
        'gateway' => 'Pasarela',
        'status' => 'Estado',
        'date' => 'Fecha',
        'payment_method' => 'Método de Pago',
        'tour_date' => 'Fecha del Tour',
        'booking_status' => 'Estado de Reserva',
    ],

    'filters' => [
        'search' => 'Buscar',
        'search_placeholder' => 'Ref. reserva, email, nombre...',
        'status' => 'Estado',
        'gateway' => 'Pasarela',
        'date_from' => 'Fecha Desde',
        'date_to' => 'Fecha Hasta',
        'all' => 'Todos',
    ],

    'statuses' => [
        'pending' => 'Pendiente',
        'processing' => 'Procesando',
        'completed' => 'Completado',
        'failed' => 'Fallido',
        'refunded' => 'Reembolsado',
    ],

    'buttons' => [
        'export_csv' => 'Exportar CSV',
        'view_details' => 'Ver Detalles',
        'view_booking' => 'Ver Reserva',
        'process_refund' => 'Procesar Reembolso',
        'back_to_list' => 'Volver a la Lista',
    ],

    'messages' => [
        'no_payments_found' => 'No se encontraron pagos',
        'booking_deleted' => 'La reserva fue eliminada permanentemente',
        'booking_deleted_on' => 'La reserva fue eliminada permanentemente el',
    ],

    'info' => [
        'payment_information' => 'Información del Pago',
        'booking_information' => 'Información de la Reserva',
        'gateway_response' => 'Respuesta de la Pasarela',
        'payment_timeline' => 'Línea de Tiempo del Pago',
        'payment_created' => 'Pago Creado',
        'payment_completed' => 'Pago Completado',
    ],

    'pagination' => [
        'showing' => 'Mostrando',
        'to' => 'a',
        'of' => 'de',
        'results' => 'resultados',
    ],
];
