<?php

return [

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

];
