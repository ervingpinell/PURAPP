<?php

return [
    'title'                  => 'Pago',
    'panels' => [
        'terms_title'        => 'Políticas y Términos',
        'secure_subtitle'    => 'El proceso de pago es rápido y seguro',
        'required_title'     => 'Campos requeridos',
        'required_read_accept' => 'Debes leer y aceptar todas las políticas para continuar con el pago',
        'terms_block_title'  => 'Términos, Condiciones y Políticas',
        'version'            => 'v',
        'no_policies_configured' => 'No hay políticas configuradas. Contacta al administrador.',
    ],

    'customer_info' => [
        'title'              => 'Información del Cliente',
        'subtitle'           => 'Por favor proporciona tu información de contacto para continuar',
        'full_name'          => 'Nombre Completo',
        'first_name'         => 'Nombre',
        'last_name'          => 'Apellido',
        'email'              => 'Correo Electrónico',
        'phone'              => 'Teléfono',
        'optional'           => 'opcional',
        'placeholder_name'   => 'Juan Pérez',
        'placeholder_email'  => 'correo@ejemplo.com',
        'why_need_title'     => '¿Por qué necesitamos esto?',
        'why_need_text'      => 'Tu correo electrónico será usado para enviar confirmaciones de reserva, actualizaciones y enlaces de pago. Opcionalmente puedes crear una cuenta después de reservar para gestionar tus reservas.',
        'logged_in_as'       => 'Sesión iniciada como',
        'address'            => 'Dirección',
        'city'               => 'Ciudad',
        'state'              => 'Estado / Provincia',
        'zip'                => 'Código Postal',
        'country'            => 'País',
    ],

    'steps' => [
        'review'             => 'Revisión',
        'payment'            => 'Pago',
        'confirmation'       => 'Confirmación',
    ],

    'buttons' => [
        'back'               => 'Volver',
        'go_to_payment'      => 'Ir al pago',
        'view_details'       => 'Ver detalles',
        'edit'               => 'Cambiar fecha o participantes',
        'close'              => 'Cerrar',
    ],

    'summary' => [
        'title'              => 'Resumen del pedido',
        'item'               => 'ítem',
        'items'              => 'ítems',
        'free_cancellation'  => 'Cancelación gratuita',
        'free_cancellation_until' => 'Antes de las :time del :date',
        'subtotal'           => 'Subtotal',
        'promo_code'         => 'Cupón',
        'total'              => 'Total',
        'taxes_included'     => 'Impuestos y cargos incluidos',
        'order_details'      => 'Detalles del pedido',
    ],

    'blocks' => [
        'pickup_meeting'     => 'Pickup / Punto de encuentro',
        'hotel'              => 'Hotel',
        'meeting_point'      => 'Punto de encuentro',
        'pickup_time'        => 'Hora de recogida',
        'add_ons'            => 'Extras',
        'duration'           => 'Duración',
        'hours'              => 'horas',
        'guide'              => 'Guía',
        'notes'              => 'Notas',
        'ref'                => 'Ref',
        'item'               => 'Item',
    ],

    'categories' => [
        'adult'              => 'Adulto',
        'kid'                => 'Niño',
        'category'           => 'Categoría',
        'qty_badge'          => ':qtyx',
        'unit_price'         => '($:price × :qty)',
        'line_total'         => '$:total',
    ],

    'accept' => [
        'label_html'         => 'He leído y acepto los <strong>Términos y Condiciones</strong>, la <strong>Política de Privacidad</strong>, y todas las <strong>Políticas de Cancelación, Devoluciones y Garantía</strong>. *',
        'error'              => 'Debes aceptar las políticas para continuar.',
    ],

    'misc' => [
        'at'                 => 'a las',
        'participant'        => 'participante',
        'participants'       => 'participantes',
    ],

    'payment' => [
        'title'              => 'Pago',
        'total'              => 'Total',
        'secure_payment'     => 'Pago Seguro',
        'powered_by'         => 'Procesado por',
        'proceed_to_payment' => 'Proceder al Pago',
        'secure_transaction' => 'Transacción Segura',
        'error_occurred'     => 'Ocurrió un error al procesar el pago. Por favor intenta nuevamente.',
        'invalid_response'   => 'La respuesta del procesador de pagos no es válida.',

        // Friendly error messages for Alignet
        'cancelled_by_user'  => 'Cancelaste el pago. Tu carrito sigue disponible.',
        'timeout'            => 'El tiempo de pago expiró. Por favor intenta nuevamente.',
        'insufficient_funds' => 'Fondos insuficientes. Por favor verifica tu saldo o usa otra tarjeta.',
        'card_declined'      => 'Tu tarjeta fue rechazada. Por favor contacta a tu banco o usa otra tarjeta.',
        'invalid_card'       => 'Los datos de la tarjeta son inválidos. Por favor verifica e intenta nuevamente.',
        'failed'             => 'El pago no pudo ser procesado. Por favor intenta nuevamente.',
        'success'            => '¡Pago exitoso! Recibirás un correo de confirmación pronto.',
        'session_expired'    => 'Tu sesión expiró. Por favor inicia sesión nuevamente.',
    ],
    'booking' => [
        'summary'   => 'Resumen de la reserva',
        'reference' => 'Referencia',
        'date'      => 'Fecha',
        'passengers' => 'Pasajeros',
    ],
    'tour' => [
        'name' => 'Tour',
    ],
];
