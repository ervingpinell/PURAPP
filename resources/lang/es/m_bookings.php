<?php

return [

    'messages' => [
        'date_no_longer_available' => 'La fecha :date ya no está disponible para reservar (mínimo: :min).',
        'limited_seats_available' => 'Solo quedan :available espacios para ":tour" el :date.',
        'bookings_created_from_cart' => 'Tus reservas fueron creadas exitosamente desde el carrito.',
        'capacity_exceeded' => 'Capacidad Excedida',
        'meeting_point_hint' => 'Solo se muestra el nombre del punto en la lista.',
    ],

    'validation' => [
        'max_persons_exceeded' => 'Máximo :max personas por reserva en total.',
        'min_adults_required' => 'Se requieren mínimo :min adultos por reserva.',
        'max_kids_exceeded' => 'Máximo :max niños por reserva.',
        'no_active_categories' => 'Este tour no tiene categorías de clientes activas.',
        'min_category_not_met' => 'Se requieren mínimo :min personas en la categoría ":category".',
        'max_category_exceeded' => 'Máximo :max personas permitidas en la categoría ":category".',
        'min_one_person_required' => 'Debe haber al menos una persona en la reserva.',
        'category_not_available' => 'La categoría con ID :category_id no está disponible para este tour.',
        'max_persons_label' => 'Máximo de personas permitidas por reserva',
        'date_range_hint' => 'Selecciona una fecha entre :from — :to',

    ],

    // =========================================================
    // [01] DISPONIBILIDAD
    // =========================================================
    'availability' => [
        'fields' => [
            'tour'        => 'Tour',
            'date'        => 'Fecha',
            'start_time'  => 'Hora de inicio',
            'end_time'    => 'Hora de fin',
            'available'   => 'Disponible',
            'is_active'   => 'Activo',
        ],

        'success' => [
            'created'     => 'Disponibilidad creada exitosamente.',
            'updated'     => 'Disponibilidad actualizada exitosamente.',
            'deactivated' => 'Disponibilidad desactivada exitosamente.',
        ],

        'error' => [
            'create'     => 'No se pudo crear la disponibilidad.',
            'update'     => 'No se pudo actualizar la disponibilidad.',
            'deactivate' => 'No se pudo desactivar la disponibilidad.',
        ],

        'validation' => [
            'tour_id' => [
                'required' => 'El :attribute es requerido.',
                'integer'  => 'El :attribute debe ser un número entero.',
                'exists'   => 'El :attribute seleccionado no existe.',
            ],
            'date' => [
                'required'    => 'La :attribute es requerida.',
                'date_format' => 'La :attribute debe tener el formato AAAA-MM-DD.',
            ],
            'start_time' => [
                'date_format'   => 'La :attribute debe tener el formato HH:MM (24h).',
                'required_with' => 'La :attribute es requerida cuando se especifica la hora de fin.',
            ],
            'end_time' => [
                'date_format'    => 'La :attribute debe tener el formato HH:MM (24h).',
                'after_or_equal' => 'La :attribute debe ser mayor o igual a la hora de inicio.',
            ],
            'available' => [
                'boolean' => 'El campo :attribute es inválido.',
            ],
            'is_active' => [
                'boolean' => 'El :attribute es inválido.',
            ],
        ],

        'ui' => [
            'page_title'           => 'Disponibilidad',
            'page_heading'         => 'Disponibilidad',
            'blocked_page_title'   => 'Tours bloqueados',
            'blocked_page_heading' => 'Tours bloqueados',
            'tours_count'          => '( :count tours )',
            'blocked_count'        => '( :count bloqueados )',
        ],

        'filters' => [
            'date'               => 'Fecha',
            'days'               => 'Días',
            'product'            => 'Producto',
            'search_placeholder' => 'Buscar tour...',
            'update_state'       => 'Actualizar estado',
            'view_blocked'       => 'Ver bloqueados',
            'tip'                => 'Consejo: marca las filas y usa una acción del menú.',
        ],

        'blocks' => [
            'am_tours'    => 'Tours AM (todos los tours que inician antes de las 12:00pm)',
            'pm_tours'    => 'Tours PM (todos los tours que inician después de las 12:00pm)',
            'am_blocked'  => 'AM bloqueados',
            'pm_blocked'  => 'PM bloqueados',
            'empty_block' => 'No hay tours en este bloque.',
            'empty_am'    => 'No hay bloqueados en AM.',
            'empty_pm'    => 'No hay bloqueados en PM.',
            'no_data'     => 'No hay datos para los filtros seleccionados.',
            'no_blocked'  => 'No hay tours bloqueados en el rango seleccionado.',
        ],

        'states' => [
            'available' => 'Disponible',
            'blocked'   => 'Bloqueado',
        ],

        'buttons' => [
            'mark_all'         => 'Marcar todos',
            'unmark_all'       => 'Desmarcar todos',
            'block_all'        => 'Bloquear todos',
            'unblock_all'      => 'Desbloquear todos',
            'block_selected'   => 'Bloquear seleccionados',
            'unblock_selected' => 'Desbloquear seleccionados',
            'back'             => 'Volver',
            'open'             => 'Abrir',
            'cancel'           => 'Cancelar',
            'block'            => 'Bloquear',
            'unblock'          => 'Desbloquear',
        ],

        'confirm' => [
            'view_blocked_title'    => 'Ver tours bloqueados',
            'view_blocked_text'     => 'Se abrirá la vista con tours bloqueados para desbloquearlos.',
            'block_title'           => '¿Bloquear tour?',
            'block_html'            => '<b>:label</b> será bloqueado para la fecha <b>:day</b>.',
            'block_btn'             => 'Sí, bloquear',
            'unblock_title'         => '¿Desbloquear tour?',
            'unblock_html'          => '<b>:label</b> será desbloqueado para la fecha <b>:day</b>.',
            'unblock_btn'           => 'Sí, desbloquear',
            'bulk_title'            => 'Confirmar acción',
            'bulk_items_html'       => 'Elementos a afectar: <b>:count</b>.',
            'bulk_block_day_html'   => 'Bloquear todos los disponibles para el día <b>:day</b>',
            'bulk_block_block_html' => 'Bloquear todos los disponibles en el bloque <b>:block</b> el <b>:day</b>',
        ],

        'toasts' => [
            'applying_filters'   => 'Aplicando filtros...',
            'searching'          => 'Buscando...',
            'updating_range'     => 'Actualizando rango...',
            'invalid_date_title' => 'Fecha inválida',
            'invalid_date_text'  => 'No se permiten fechas pasadas.',
            'marked_n'           => 'Marcados :n',
            'unmarked_n'         => 'Desmarcados :n',
            'updated'            => 'Cambio aplicado',
            'updated_count'      => 'Actualizados: :count',
            'unblocked_count'    => 'Desbloqueados: :count',
            'no_selection_title' => 'Sin selección',
            'no_selection_text'  => 'Marca al menos un tour.',
            'no_changes_title'   => 'Sin cambios',
            'no_changes_text'    => 'No hay elementos aplicables.',
            'error_generic'      => 'No se pudo completar la actualización.',
            'error_update'       => 'No se pudo actualizar.',
        ],
    ],

    // =========================================================
    // [02] RESERVAS
    // =========================================================
    'bookings' => [
        'singular' => 'Reserva',
        'plural' => 'Reservas',
        'customer' => 'Cliente',
        'payment_link_info' => 'Enlace de pago para el cliente',
        'regenerate_warning' => 'Advertencia: Al regenerar el enlace, el anterior dejará de funcionar.',
        'steps' => [
            'customer' => 'Cliente',
            'select_tour_date' => 'Seleccionar Tour y Fecha',
            'select_schedule_language' => 'Seleccionar Horario e Idioma',
            'select_participants' => 'Seleccionar Participantes',
            'customer_details' => 'Cliente y Detalles',
        ],
        'ui' => [
            'page_title'         => 'Reservas',
            'page_heading'       => 'Gestión de Reservas',
            'register_booking'   => 'Registrar Reserva',
            'add_booking'        => 'Agregar Reserva',
            'edit_booking'       => 'Editar Reserva',
            'booking_details'    => 'Detalles de la Reserva',
            'download_receipt'   => 'Descargar recibo',
            'actions'            => 'Acciones',
            'view_details'       => 'Ver Detalles',
            'click_to_view'      => 'Haz clic para ver detalles',
            'zoom_in'            => 'Acercar',
            'zoom_out'           => 'Alejar',
            'zoom_reset'         => 'Restablecer Zoom',
            'no_promo'        => 'No hay código promocional aplicado',
            'create_booking'    => 'Crear reserva',
            'create_title'      => 'Crear Nueva Reserva',
            'booking_info'      => 'Información de la reserva',
            'select_customer'                => 'Seleccionar cliente',
            'select_tour'                    => 'Seleccionar tour',
            'select_tour_first'              => 'Seleccione un tour primero',
            'select_option'                  => 'Seleccionar',
            'select_tour_to_see_categories'  => 'Selecciona un tour para ver las categorías',
            'loading'                        => 'Cargando...',
            'no_results'                     => 'Sin resultados',
            'error_loading'                  => 'Error cargando datos',
            'tour_without_categories'        => 'Este tour no tiene categorías configuradas',
            'verifying' => 'Verificando...',
            'min' => 'Mínimo',
            'max' => 'Máximo',
            'confirm_booking' => 'Confirmar Reserva',
            'subtotal' => 'Subtotal',
            'total' => 'Total',
            'select_meeting_point' => 'Seleccionar punto de encuentro',
            'no_pickup' => 'Sin recogida',
            'hotel' => 'Hotel',
            'meeting_point' => 'Punto de Encuentro',
            'surcharge' => 'Recargo',
            'discount' => 'Descuento',
            'participants' => 'Participantes',
            'price_breakdown' => 'Desglose de Precios',
            'enter_promo' => 'Ingresa código promocional',
            'select_hotel' => 'Seleccionar hotel',
            'payment_link' => 'Link de Pago',
            'view_payment' => 'Ver Pago',
            'hotel_pickup' => 'Recogida en Hotel',
            'meeting_point_pickup' => 'Punto de Encuentro',
            'no_pickup' => 'Sin recogida',
            'optional' => '(Opcional)',
            'pickup_info' => 'Establece la hora de recogida para esta reserva.',
            'confirm_booking_alert' => 'Confirmar esta reserva enviará un correo de confirmación al cliente.',
            'regenerating' => 'Regenerando...',
            'copied' => '¡Copiado!',
            'copy_failed' => 'Error al copiar',
            'pickup_warning' => 'Advertencia: Hora recogida :pickup pero tour :tour. Verificar.',
        ],

        'fields' => [
            'booking_id'        => 'ID de Reserva',
            'status'            => 'Estado',
            'booking_date'      => 'Fecha de Reserva',
            'booking_origin'    => 'Fecha de Reserva (origen)',
            'reference'         => 'Referencia',
            'booking_reference' => 'Referencia de Reserva',
            'customer'          => 'Cliente',
            'email'             => 'Correo',
            'phone'             => 'Teléfono',
            'tour'              => 'Tour',
            'language'          => 'Idioma',
            'tour_date'         => 'Fecha del Tour',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Nombre de otro hotel',
            'meeting_point'     => 'Punto de Encuentro',
            'pickup_location'   => 'Ubicación de Recogida',
            'schedule'          => 'Horario',
            'type'              => 'Tipo',
            'adults'            => 'Adultos',
            'adults_quantity'   => 'Cantidad de Adultos',
            'children'          => 'Niños',
            'children_quantity' => 'Cantidad de Niños',
            'promo_code'        => 'Código promocional',
            'total'             => 'Total',
            'total_to_pay'      => 'Total a Pagar',
            'adult_price'       => 'Precio Adulto',
            'child_price'       => 'Precio Niño',
            'notes'             => 'Notas',
            'hotel_name'     => 'Nombre del Hotel',
            'travelers'      => 'Viajeros',
            'subtotal'       => 'Subtotal',
            'discount'       => 'Descuento',
            'total_persons'  => 'Personas',
            'pickup_place'   => 'Lugar de Recogida',
            'date'           => 'Fecha',
            'category'       => 'Categoría',
            'quantity'       => 'Cantidad',
            'price'          => 'Precio',
            'pickup'         => 'Recogida',
            'pickup_time' => 'Hora de recogida',
        ],

        'placeholders' => [
            'select_customer'  => 'Seleccionar cliente',
            'select_tour'      => 'Seleccionar un tour',
            'select_schedule'  => 'Seleccionar un horario',
            'select_language'  => 'Seleccionar idioma',
            'select_hotel'     => 'Seleccionar hotel',
            'select_point'     => 'Seleccionar punto de encuentro',
            'select_status'    => 'Seleccionar estado',
            'enter_hotel_name' => 'Ingresa el nombre del hotel',
            'enter_promo_code' => 'Ingresa el código promocional',
            'other'            => 'Otro…',
        ],

        'statuses' => [
            'pending'   => 'Pendiente',
            'confirmed' => 'Confirmada',
            'cancelled' => 'Cancelada',
        ],

        'buttons' => [
            'save'            => 'Guardar',
            'cancel'          => 'Cancelar',
            'edit'            => 'Editar',
            'delete'          => 'Eliminar',
            'confirm_changes' => 'Confirmar cambios',
            'apply'           => 'Aplicar',
            'update'          => 'Actualizar',
            'close'           => 'Cerrar',
            'back'     => 'Volver',
            'remove_promo' => 'Quitar cupón',
        ],

        'meeting_point' => [
            'time'     => 'Hora:',
            'view_map' => 'Ver mapa',
        ],

        'pricing' => [
            'title' => 'Resumen de Precios',
        ],

        'optional' => 'opcional',

        'messages' => [
            'past_booking_warning'  => 'Esta reserva corresponde a una fecha pasada y no puede ser editada.',
            'tour_archived_warning' => 'El tour de esta reserva fue eliminado/archivado y no pudo ser cargado. Selecciona un tour para ver sus horarios.',
            'no_schedules'          => 'No hay horarios disponibles',
            'deleted_tour'          => 'Tour eliminado',
            'deleted_tour_snapshot' => 'Tour Eliminado (:name)',
            'tour_archived'         => '(archivado)',
            'meeting_point_hint'    => 'Solo se muestra el nombre del punto en la lista.',
            'customer_locked'       => 'El cliente está bloqueado y no puede ser editado.',
            'promo_applied_subtract' => 'Descuento aplicado:',
            'promo_applied_add'      => 'Cargo aplicado:',
            'hotel_locked_by_meeting_point'   => 'Se seleccionó un punto de encuentro; no se puede seleccionar hotel.',
            'meeting_point_locked_by_hotel'   => 'Se seleccionó un hotel; no se puede seleccionar punto de encuentro.',
            'promo_removed' => 'Código promocional eliminado',
        ],

        'alerts' => [
            'error_summary' => 'Por favor corrige los siguientes errores:',
        ],

        'validation' => [
            'past_date'      => 'No puedes reservar para fechas anteriores a hoy.',
            'promo_required' => 'Ingresa un código promocional primero.',
            'promo_checking' => 'Verificando código…',
            'promo_invalid'  => 'Código promocional inválido.',
            'promo_error'    => 'No se pudo validar el código.',
            'promo_apply_required' => 'Por favor haz clic en Aplicar para validar tu código promocional primero.',
            'promo_empty'          => 'Ingresa un código primero.',
            'promo_needs_subtotal' => 'Agrega al menos 1 pasajero para calcular el descuento.',

            // Capacity warnings
            'capacity_warning' => 'Advertencia de Capacidad',
            'capacity_exceeded_confirm' => 'Capacidad excedida (Disponible: :available, Solicitado: :requested). ¿Deseas forzar esta reserva de todos modos?',
            'max_persons_confirm' => 'Límite global excedido (Máximo: :max, Solicitado: :requested). ¿Deseas forzar esta reserva de todos modos?',
            'limits_exceeded_confirm' => 'Límites excedidos: :errors. ¿Deseas forzar esta reserva de todos modos?',
            'force_question' => '¿Deseas forzar esta reserva de todos modos?',
            'error_title' => '¡Error!',
        ],

        'promo' => [
            'applied'         => 'Código aplicado',
            'applied_percent' => 'Código aplicado: -:percent%',
            'applied_amount'  => 'Código aplicado: -$:amount',
        ],

        'loading' => [
            'saving'     => 'Guardando...',
            'validating' => 'Validando…',
            'updating'   => 'Actualizando...',
        ],

        'success' => [
            'created'          => 'Reserva creada exitosamente.',
            'updated'          => 'Reserva actualizada exitosamente.',
            'deleted'          => 'Reserva eliminada exitosamente.',
            'status_updated'   => 'Estado de reserva actualizado exitosamente.',
            'status_confirmed' => 'Reserva confirmada exitosamente.',
            'status_cancelled' => 'Reserva cancelada exitosamente.',
            'status_pending'   => 'Reserva establecida como pendiente exitosamente.',
        ],

        'errors' => [
            'create'                => 'No se pudo crear la reserva.',
            'update'                => 'No se pudo actualizar la reserva.',
            'delete'                => 'No se pudo eliminar la reserva.',
            'status_update_failed'  => 'No se pudo actualizar el estado de la reserva.',
            'detail_not_found'      => 'Detalles de la reserva no encontrados.',
            'schedule_not_found'    => 'Horario no encontrado.',
            'insufficient_capacity'  => 'No hay capacidad suficiente para ":tour" el :date a las :time. Solicitado: :requested, disponible: :available (máx: :max).',
            'payment_link_status'    => 'El link de pago solo está disponible para reservas pendientes.',
        ],

        'confirm' => [
            'delete' => '¿Estás seguro de que deseas eliminar esta reserva?',
        ],

        // SoftDelete & Trash
        'trash' => [
            'active_bookings' => 'Reservas Activas',
            'trash' => 'Papelera',
            'restore_booking' => 'Restaurar reserva',
            'permanently_delete' => 'Eliminar permanentemente',
            'force_delete_title' => 'ELIMINACIÓN PERMANENTE',
            'force_delete_warning' => '¡Esta acción NO se puede deshacer!',
            'force_delete_message' => 'La reserva será eliminada permanentemente.',
            'force_delete_data_loss' => 'Todos los datos relacionados se perderán para siempre.',
            'force_delete_confirm' => 'Sí, ELIMINAR PARA SIEMPRE',
            'booking_deleted' => 'Reserva eliminada.',
            'booking_restored' => 'Reserva restaurada exitosamente.',
            'booking_force_deleted' => 'Reserva eliminada permanentemente. Registros de pago preservados para auditoría.',
            'force_delete_failed' => 'No se pudo eliminar permanentemente la reserva.',
            'deleted_booking_indicator' => '(ELIMINADA)',
        ],

        // Checkout Links (for admin-created bookings)
        'checkout_link_label' => 'Link de Pago para el Cliente',
        'checkout_link_description' => 'Envía este link al cliente para que pueda completar el pago de su reserva.',
        'checkout_link_copy' => 'Copiar Link',
        'checkout_link_copied' => '¡Link copiado!',
        'checkout_link_copy_failed' => 'No se pudo copiar el link. Por favor cópialo manualmente.',
        'checkout_link_valid_until' => 'Válido hasta',
        'checkout_link_expired' => 'El enlace de pago ha expirado',
        'booking_already_paid' => 'Esta reserva ya ha sido pagada',
        'payment_link_regenerated' => 'Enlace de pago regenerado exitosamente',
        'regenerate_payment_link' => 'Regenerar Enlace de Pago',
        'confirm_regenerate_payment_link' => '¿Estás seguro de que deseas regenerar el enlace de pago? El enlace anterior dejará de funcionar.',
        'payment_link_expired_label' => 'Enlace expirado',
        'checkout_link_accessed' => 'Cliente accedió al checkout',

        // Payment Status
        'payment_status' => [
            'label' => 'Estado de Pago',
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'failed' => 'Fallido',
            'refunded' => 'Reembolsado',
        ],
    ],

    // =========================================================
    // [03] ACCIONES
    // =========================================================
    'actions' => [
        'confirm'        => 'Confirmar',
        'cancel'         => 'Cancelar',
        'confirm_cancel' => '¿Estás seguro de que deseas cancelar esta reserva?',
        'remove' => 'Quitar',
        'confirm_create' => 'Confirmar y Crear',
        'review_booking' => 'Revisar Reserva',
        'apply'          => 'Aplicar',
        'yes_force'      => 'Sí, Forzar Reserva',
    ],

    // =========================================================
    // [04] FILTROS
    // =========================================================
    'filters' => [
        'advanced_filters' => 'Filtros Avanzados',
        'dates'            => 'Fechas',
        'booked_from'      => 'Reservado desde',
        'booked_until'     => 'Reservado hasta',
        'tour_from'        => 'Tour desde',
        'tour_until'       => 'Tour hasta',
        'all'              => 'Todos',
        'apply'            => 'Aplicar',
        'clear'            => 'Limpiar',
        'close_filters'    => 'Cerrar filtros',
        'search_reference' => 'Buscar referencia...',
        'enter_reference'  => 'Ingresa referencia de reserva',
    ],

    // =========================================================
    // [05] REPORTES
    // =========================================================
    'reports' => [
        'excel_title'          => 'Exportación de Reservas',
        'pdf_title'            => 'Reporte de Reservas - Green Vacations CR',
        'general_report_title' => 'Reporte General de Reservas - Green Vacations Costa Rica',
        'download_pdf'         => 'Descargar PDF',
        'export_excel'         => 'Exportar Excel',
        'coupon'               => 'Cupón',
        'adjustment'           => 'Ajuste',
        'totals'               => 'Totales',
        'adults_qty'           => 'Adultos (x:qty)',
        'kids_qty'             => 'Niños (x:qty)',
        'people'               => 'Personas',
        'subtotal'             => 'Subtotal',
        'discount'             => 'Descuento',
        'surcharge'            => 'Recargo',
        'original_price'       => 'Precio original',
        'total_adults'         => 'Total Adultos',
        'total_kids'           => 'Total Niños',
        'total_people'         => 'Total Personas',
    ],

    // =========================================================
    // [06] RECIBO
    // =========================================================
    'receipt' => [
        'title'         => 'Recibo de Reserva',
        'company'       => 'Green Vacations CR',
        'code'          => 'Código',
        'client'        => 'Cliente',
        'tour'          => 'Tour',
        'booking_date'  => 'Fecha de Reserva',
        'tour_date'     => 'Fecha del Tour',
        'schedule'      => 'Horario',
        'hotel'         => 'Hotel',
        'meeting_point' => 'Punto de Encuentro',
        'status'        => 'Estado',
        'adults_x'      => 'Adultos (x:count)',
        'kids_x'        => 'Niños (x:count)',
        'people'        => 'Personas',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Descuento',
        'surcharge'     => 'Recargo',
        'total'         => 'TOTAL',
        'no_schedule'   => 'Sin horario',
        'qr_alt'        => 'Código QR',
        'qr_scan'       => 'Escanea para ver la reserva',
        'thanks'        => '¡Gracias por elegir :company!',
        'payment_status' => 'Estado de pago:',
        'persons'       => '{1} persona|[2,*] personas',
    ],

    // =========================================================
    // [07] MODAL DE DETALLES
    // =========================================================
    'details' => [
        'booking_info'  => 'Información de la Reserva',
        'customer_info' => 'Información del Cliente',
        'tour_info'     => 'Información del Tour',
        'pricing_info'  => 'Información de Precios',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Descuento',
        'total_persons' => 'Total de personas',
    ],

    // =========================================================
    // [08] VIAJEROS (MODAL)
    // =========================================================
    'travelers' => [
        'title_warning'        => 'Atención',
        'title_info'           => 'Información',
        'title_error'          => 'Error',
        'max_persons_reached'  => 'Máximo :max personas por reserva.',
        'max_category_reached' => 'El máximo para esta categoría es :max.',
        'invalid_quantity'     => 'Cantidad inválida. Ingresa un número válido.',
        'age_between'          => 'Edad :min-:max',
        'age_from'             => 'Edad :min+',
        'age_to'               => 'Hasta :max años',
    ],


    'excluded_dates' => [

        'ui' => [
            'page_title'           => 'Gestión de Disponibilidad y Capacidad',
            'page_heading'         => 'Gestión de Disponibilidad y Capacidad',
            'tours_count'          => 'tours',
            'blocked_page_title'   => 'Tours bloqueados',
            'blocked_page_heading' => 'Tours bloqueados',
            'blocked_count'        => ':count tours bloqueados',
        ],

        'legend' => [
            'title'                  => 'Leyenda de Capacidades',
            'base_tour'              => 'Base Tour',
            'override_schedule'      => 'Override Horario',
            'override_day'           => 'Override Día',
            'override_day_schedule'  => 'Override Día+Horario',
            'blocked'                => 'Bloqueado',
        ],

        'filters' => [
            'date'               => 'Fecha',
            'days'               => 'Días',
            'product'            => 'Buscar Tour',
            'search_placeholder' => 'Nombre del tour…',
            'bulk_actions'       => 'Acciones Masivas',
            'update_state'       => 'Actualizar estado',
        ],

        'blocks' => [
            'am'          => 'TOURS AM',
            'pm'          => 'TOURS PM',
            'am_blocked'  => 'TOURS AM (bloqueados)',
            'pm_blocked'  => 'TOURS PM (bloqueados)',
            'empty_am'    => 'No hay tours en este bloque',
            'empty_pm'    => 'No hay tours en este bloque',
            'no_data'     => 'No hay datos para mostrar',
            'no_blocked'  => 'No hay tours bloqueados para el rango seleccionado',
        ],

        'buttons' => [
            'mark_all'         => 'Marcar Todos',
            'unmark_all'       => 'Desmarcar Todos',
            'block_all'        => 'Bloquear Todos',
            'unblock_all'      => 'Desbloquear Todos',
            'block_selected'   => 'Bloquear Seleccionados',
            'unblock_selected' => 'Desbloquear Seleccionados',
            'set_capacity'     => 'Ajustar Capacidad',
            'capacity'         => 'Capacidad',
            'view_blocked'     => 'Ver Bloqueados',
            'capacity_settings' => 'Configuración Capacidades',
            'block'            => 'Bloquear',
            'unblock'          => 'Desbloquear',
            'apply'            => 'Aplicar',
            'save'             => 'Guardar',
            'cancel'           => 'Cancelar',
            'back'             => 'Volver',
        ],

        'states' => [
            'available' => 'Disponible',
            'blocked'   => 'Bloqueado',
        ],

        'badges' => [
            'tooltip_prefix' => 'Ocupados/Capacidad -',
        ],

        'modals' => [
            'capacity_title'            => 'Ajustar Capacidad',
            'selected_capacity_title'   => 'Ajustar Capacidad de Seleccionados',
            'date'                      => 'Fecha:',
            'hierarchy_title'           => 'Jerarquía de capacidades:',
            'new_capacity'              => 'Nueva Capacidad',
            'hint_zero_blocks'          => 'Dejar en 0 para bloquear completamente',
            'selected_count'            => 'Se actualizará la capacidad de :count ítems seleccionados.',
            'capacity_day_title'        => 'Ajustar capacidad para el día',
            'capacity_day_subtitle'     => 'Todos los horarios del día',
        ],

        'confirm' => [
            'block_title'        => '¿Bloquear?',
            'unblock_title'      => '¿Desbloquear?',
            'block_html'         => '<strong>:label</strong><br>Fecha: :day',
            'unblock_html'       => '<strong>:label</strong><br>Fecha: :day',
            'block_btn'          => 'Bloquear',
            'unblock_btn'        => 'Desbloquear',
            'bulk_title'         => 'Confirmar operación masiva',
            'bulk_items_html'    => ':count ítems serán afectados',
            'block_day_title'    => 'Bloquear todo el día',
            'block_block_title'  => 'Bloquear bloque :block del :day',
        ],

        'toasts' => [
            'invalid_date_title' => 'Fecha inválida',
            'invalid_date_text'  => 'No puedes seleccionar fechas pasadas',
            'searching'          => 'Buscando…',
            'applying_filters'   => 'Aplicando filtros…',
            'updating_range'     => 'Actualizando rango…',
            'no_selection_title' => 'Sin selección',
            'no_selection_text'  => 'Debes seleccionar al menos un item',
            'no_changes_title'   => 'Sin cambios',
            'no_changes_text'    => 'No hay ítems para actualizar',
            'marked_n'           => 'Marcados :n ítems',
            'unmarked_n'         => 'Desmarcados :n ítems',
            'error_generic'      => 'No se pudo completar la operación',
            'updated'            => 'Actualizado',
            'updated_count'      => ':count ítems actualizados',
            'unblocked_count'    => ':count ítems desbloqueados',
            'blocked'            => 'Bloqueado',
            'unblocked'          => 'Desbloqueado',
            'capacity_updated'   => 'Capacidad actualizada',
        ],

    ],

    'capacity' => [

        // =========================================================
        // [01] UI TITLES & HEADINGS
        // =========================================================
        'ui' => [
            'page_title'   => 'Gestión de Capacidades',
            'page_heading' => 'Gestión de Capacidades',
        ],

        // =========================================================
        // [02] TABS
        // =========================================================
        'tabs' => [
            'global'         => 'Globales',
            'by_tour'        => 'Por Tour + Horario',
            'day_schedules'  => 'Overrides Día + Horario',
        ],

        // =========================================================
        // [03] ALERTS
        // =========================================================
        'alerts' => [
            'global_info' => '<strong>Capacidades globales:</strong> Define el límite base para cada tour (todos los días y horarios).',
            'by_tour_info' => '<strong>Por Tour + Horario:</strong> Override de capacidad específico para cada horario de cada tour. Estos overrides tienen prioridad sobre la capacidad global del tour.',
            'day_schedules_info' => '<strong>Día + Horario:</strong> Override de máxima prioridad para un día y horario específico. Estos se gestionan desde la vista de "Disponibilidad y Capacidad".',
        ],

        // =========================================================
        // [04] TABLE HEADERS
        // =========================================================
        'tables' => [
            'global' => [
                'tour'       => 'Tour',
                'type'       => 'Tipo',
                'capacity'   => 'Capacidad Global',
                'level'      => 'Nivel',
            ],
            'by_tour' => [
                'schedule'   => 'Horario',
                'capacity'   => 'Capacidad Override',
                'level'      => 'Nivel',
                'no_schedules' => 'Este tour no tiene horarios asignados',
            ],
            'day_schedules' => [
                'date'       => 'Fecha',
                'tour'       => 'Tour',
                'schedule'   => 'Horario',
                'capacity'   => 'Capacidad',
                'actions'    => 'Acciones',
                'no_overrides' => 'No hay overrides de día + horario',
            ],
        ],

        // =========================================================
        // [05] BADGES / LABELS
        // =========================================================
        'badges' => [
            'base'       => 'Base',
            'override'   => 'Override',
            'global'     => 'Global',
            'blocked'    => 'BLOQUEADO',
            'unlimited'  => '∞',
        ],

        // =========================================================
        // [06] BUTTONS
        // =========================================================
        'buttons' => [
            'save'      => 'Guardar',
            'delete'    => 'Eliminar',
            'back'      => 'Volver',
            'apply'     => 'Aplicar',
            'cancel'    => 'Cancelar',
        ],

        // =========================================================
        // [07] MESSAGES
        // =========================================================
        'messages' => [
            'empty_placeholder' => 'Vacío = usa capacidad global (:capacity)',
            'deleted_confirm'   => '¿Eliminar este override?',
            'no_day_overrides'  => 'No hay overrides de día + horario.',
        ],

        // =========================================================
        // [08] TOASTS (SweetAlert2)
        // =========================================================
        'toasts' => [
            'success_title' => 'Éxito',
            'error_title'   => 'Error',
        ],
    ],

];
