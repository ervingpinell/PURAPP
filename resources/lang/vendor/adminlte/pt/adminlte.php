<?php

/**
 * Table of Contents
 *
 * 1. AUTHENTICATION AND REGISTRATION ........... Line 37
 * 2. HOTELS ................................... Line 57
 * 3. GENERAL NAVIGATION ....................... Line 67
 * 4. CONTENT AND PAGES ........................ Line 82
 * 5. TOURS AND REVIEWS ........................ Line 97
 * 6. SCHEDULES ................................ Line 131
 * 7. ITINERARIES .............................. Line 144
 * 8. HOTELS (DETAIL) .......................... Line 156
 * 9. CART AND BOOKINGS ........................ Line 180
 * 10. VALIDATION .............................. Line 219
 * 11. BUTTONS AND CRUD ........................ Line 225
 * 12. FOOTER .................................. Line 243
 * 13. WHATSAPP ................................ Line 247
 * 14. REVIEWS ................................. Line 257
 * 15. TRAVELERS ............................... Line 273
 * 16. CONTACT ................................. Line 286
 * 17. ERRORS .................................. Line 295
 * 18. CART LOGIN MODAL ........................ Line 298
 * 19. SWEETALERTS (ACTIONS) ................... Line 322
 * 20. SUCCESSES (USED IN CONTROLLERS) ......... Line 328
 * 21. MAIL .................................... Line 381
 * 22. DASHBOARD ............................... Line 386
 * 23. ENTITIES ................................ Line 394
 * 24. SECTIONS ................................ Line 408
 * 25. EMPTY STATES ............................ Line 414
 * 26. BUTTONS (GENERIC) ....................... Line 421
 * 27. LABELS .................................. Line 426
 */

return [
    'contact_throttled' => 'Has enviado demasiados mensajes en poco tiempo. Por favor, espera un momento antes de volver a intentarlo.',

    'pending_email_title'  => 'Cambio de correo pendiente',
    'pending_email_notice' => 'Has solicitado cambiar tu correo de <strong>:current</strong> a <strong>:pending</strong>. Te enviamos un enlace a tu nuevo correo para confirmar el cambio. Hasta que lo confirmes, seguiremos usando tu correo actual.',
    'email_change_warning' => 'Si cambias tu correo, te enviaremos un enlace de confirmación al nuevo correo. Tu correo actual seguirá activo hasta que confirmes el cambio.',
    'profile_updated_email_change_pending' => 'Tu perfil se ha actualizado. Te hemos enviado un enlace a tu nuevo correo para confirmar el cambio. Hasta que lo confirmes, seguiremos usando tu correo actual.',
    'email_change_confirmed' => 'Tu correo electrónico ha sido actualizado y verificado correctamente.',

    'no_slots_for_date' => 'no hay espacios disponibles para esta fecha',
    // 1. AUTHENTICATION AND REGISTRATION
    'hello' => 'Hola',
    'full_name' => 'Nombre completo',
    'email' => 'Correo electrónico',
    'password' => 'Contraseña',
    'phone' => 'Teléfono',
    'address' => 'Dirección',
    'city' => 'Ciudad',
    'state' => 'Provincia/Estado',
    'zip' => 'Código Postal',
    'country' => 'País',
    'retype_password' => 'Repetir contraseña',
    'remember_me' => 'Recuérdame',
    'remember_me_hint' => 'Mantener la sesión abierta indefinidamente o hasta que se cierre manualmente',
    'register' => 'Registrarse',
    'i_already_have_a_membership' => 'Ya tengo una cuenta',
    'promo_invalid' => 'Código promocional inválido.',
    'promo_already_used' => 'Ese código promocional ya ha sido utilizado en otra reserva.',
    'no_past_dates' => 'No puedes reservar para fechas anteriores a hoy.',
    'dupe_submit_cart' => 'Ya se está procesando una reserva similar. Por favor, inténtalo de nuevo en unos segundos.',
    'schedule_not_available' => 'El horario no está disponible para este tour (inactivo o no asignado).',
    'date_blocked' => 'La fecha seleccionada está bloqueada para este tour.',
    'capacity_left' => 'Solo quedan :available lugares para este horario.',
    'booking_created_success' => 'Reserva creada exitosamente.',
    'booking_updated_success' => 'Reserva actualizada exitosamente.',
    'two_factor_authentication' => 'Autenticación en dos pasos (2FA)',
    'enable_2fa_to_continue' => 'Debes configurar la autenticación de dos factores (2FA) para acceder al panel de administración.',

    // 2. HOTELS
    'hotel_name_required' => 'El nombre del hotel es obligatorio.',
    'hotel_name_unique'   => 'Ya existe un hotel con ese nombre.',
    'hotel_name_max'      => 'El nombre del hotel no puede exceder de :max caracteres.',
    'hotel_created_success' => 'Hotel creado exitosamente.',
    'hotel_updated_success' => 'Hotel actualizado exitosamente.',
    'is_active_required'  => 'El estado es obligatorio.',
    'is_active_boolean'   => 'El estado debe ser verdadero o falso.',
    'outside_list' => 'Este hotel está fuera de nuestra lista. Por favor contáctanos para verificar si podemos ofrecerte transporte.',

    // 3. GENERAL NAVIGATION
    'back' => 'Atrás',
    'home' => 'Inicio',
    'dashboard_menu' => 'Panel', // renombrado para no colisionar con la sección 'dashboard'
    'profile' => 'Perfil',
    'settings' => 'Configuración',
    'users' => 'Usuarios',
    'roles' => 'Roles',
    'notifications' => 'Notificaciones',
    'messages' => 'Mensajes',
    'help' => 'Ayuda',
    'language' => 'Idioma',
    'support' => 'Soporte',
    'admin_panel' => 'Panel de administración',

    // 4. CONTENIDO Y PÁGINAS
    'faq' => 'Preguntas frecuentes',
    'faqpage' => 'Preguntas frecuentes',
    'no_faqs_available' => 'No hay preguntas frecuentes disponibles.',
    'contact' => 'Contacto',
    'about' => 'Sobre nosotros',
    'privacy_policy' => 'Política de privacidad',
    'terms_and_conditions' => 'Términos y condiciones',
    'all_policies' => 'Todas nuestras políticas',
    'cancellation_and_refunds_policies' => 'Políticas de cancelación y reembolso',
    'reports' => 'Reportes',
    'footer_text' => config('app.name', 'Green Vacations CR'),
    'quick_links' => 'Enlaces rápidos',
    'rights_reserved' => 'Todos los derechos reservados',

    // 5. TOURS Y RESEÑAS
    'tours' => 'Tours',
    'tour' => 'Tour',
    'tour_name' => 'Nombre del tour',
    'overview' => 'Resumen',
    'duration' => 'Duración',
    'price' => 'Precio',
    'type' => 'Tipo de tour',
    'languages_available' => 'Idiomas disponibles',
    'amenities_included' => 'Servicios incluidos',
    'excluded_amenities' => 'Servicios no incluidos',
    'tour_details' => 'Detalles del tour',
    'select_tour' => 'Selecciona un tour',
    'reviews' => 'Reseñas',
    'hero_title' => 'Descubre la magia de Costa Rica',
    'hero_subtext' => 'Explora nuestros tours únicos y vive la aventura.',
    'book_now' => 'Reservar ahora',
    'our_tours' => 'Nuestros tours',
    'half_day' => 'Medio día',
    'full_day' => 'Día completo',
    'full_day_description' => 'Perfecto para quienes buscan una experiencia completa en un día',
    'half_day_description' => 'Tours ideales para una aventura rápida para quienes tienen poco tiempo.',
    'full_day_tours' => 'Tours de día completo',
    'half_day_tours' => 'Tours de medio día',
    'see_tour' => 'Ver tour',
    'see_tours' => 'Ver tours',
    'see_tour_details' => 'Ver detalles del tour',
    'what_visitors_say' => 'Lo que dicen nuestros visitantes',
    'quote_1' => '¡Una experiencia inolvidable!',
    'guest_1' => 'Carlos M.',
    'quote_2' => 'Definitivamente volveré.',
    'guest_2' => 'Ana G.',
    'tour_information' => 'Información del tour',
    'group_size' => 'Tamaño del grupo',
    'no_prices_available' => 'No hay precios disponibles',
    'no_prices_configured' => 'Este tour no tiene precios configurados',
    'total_persons' => 'Total personas',
    'quantity' => 'Cantidad',
    'decrease' => 'Disminuir',
    'increase' => 'Aumentar',
    'max_persons_reached' => 'Máximo :max personas por reserva',
    'min_category_required' => 'Se requieren mínimo :min en :category',
    'max_category_exceeded' => 'Máximo :max permitidos en :category',
    'max_persons_exceeded' => 'Máximo :max personas en total',
    'min_one_person' => 'Debe haber al menos una persona',
    'persons_max' => 'personas máximo',
    'or' => 'O',
    'open_map' => 'Ver ubicación',


    // 6. HORARIOS
    'schedule' => 'Horario',
    'schedule_am' => 'Horario AM',
    'schedule_pm' => 'Horario PM',
    'start_time' => 'Hora de inicio',
    'end_time' => 'Hora de finalización',
    'select_date' => 'Selecciona una fecha',
    'select_time' => 'Selecciona una hora',
    'select_language' => 'Selecciona un idioma',
    'schedules' => 'Horarios',
    'horas' => 'horas',
    'hours' => 'horas',

    // 7. ITINERARIOS
    'itinerary' => 'Itinerario',
    'itineraries' => 'Itinerarios',
    'new_itinerary' => 'Nuevo itinerario',
    'itinerary_items' => 'Elementos del itinerario',
    'item_title' => 'Título del elemento',
    'item_description' => 'Descripción del elemento',
    'add_item' => 'Agregar elemento',
    'edit_itinerary' => 'Editar itinerario',
    'no_itinerary_info' => 'Sin información de itinerario.',
    'whats_included' => 'Qué está incluido',

    // 8. HOTELES (DETALLE)
    'hotels' => 'Hoteles',
    'hotel' => 'Hotel',
    'select_hotel' => 'Hotel',
    'hotel_other' => 'Otro (especificar manualmente)',
    'hotel_name' => 'Nombre del hotel',
    'other_hotel' => 'Otro hotel (especificar)',
    'hotel_pickup' => 'Recogida en hotel',
    'outside_area' => 'Este hotel está fuera del área de cobertura. Por favor contáctanos para revisar tus opciones.',
    'pickup_valid' => '¡El hotel seleccionado es válido! Una vez confirmes la reserva, te contactaremos para coordinar la hora de recogida.',
    'pickup_details' => 'Detalles de recogida',
    'pickup_note' => 'Las recogidas gratuitas aplican solo para hoteles en el área de La Fortuna...',
    'pickup_points' => 'Puntos de recogida',
    'select_pickup' => 'Selecciona un punto de recogida',
    'type_to_search' => 'Escribe para buscar...',
    'no_pickup_available' => 'No hay puntos de recogida disponibles.',
    'pickup_not_found' => 'Hotel no encontrado.',
    'meeting_points' => 'Puntos de encuentro',
    'select_meeting' => 'Selecciona un punto de encuentro',
    'meeting_point_details' => 'Detalles del punto de encuentro',
    'meeting_not_found' => 'Punto de encuentro no encontrado.',
    'main_street_entrance' => 'Entrada calle principal',
    'example_address' => 'Dirección de ejemplo 123',
    'hotels_meeting_points' => 'Hoteles y puntos de encuentro',
    'meeting_valid' => '¡El punto de encuentro seleccionado es válido! Una vez confirmes tu reserva, te enviaremos las instrucciones y la hora exacta del encuentro.',
    'meeting_point' => 'Punto de encuentro',
    'meetingPoint'  => 'Punto de encuentro',
    'selectHotelHelp' => 'Selecciona tu hotel de la lista.',
    'selectFromList'      => 'Selecciona un elemento de la lista',
    'fillThisField'       => 'Completa este campo',
    'pickupRequiredTitle' => 'Recogida obligatoria',
    'pickupRequiredBody'  => 'Debes seleccionar un hotel o un punto de encuentro para continuar.',
    'ok'                  => 'Aceptar',
    'pickup_time' => 'Hora de recogida',
    'pickupTime'  => 'Hora de recogida',
    'meeting_time' => 'Hora de encuentro',
    'open_map' => 'Abrir mapa',
    'openMap'  => 'Abrir mapa',

    'openMap'  => 'Abrir mapa',
    'select_pickup_type' => 'Selecione a preferência de recolha',
    'no_pickup' => 'Não preciso de recolha',
    'other_hotel_option' => 'O meu hotel não está na lista',
    'custom_pickup_notice' => 'Selecionou um local personalizado. Entre em contacto connosco para verificar se a recolha é possível aí, uma vez que está fora da nossa lista padrão.',
    // 9. CARRITO Y RESERVAS
    'cart' => 'Carrito',
    'myCart' => 'Mi carrito',
    'my_reservations' => 'Mis reservas',
    'your_cart' => 'Tu carrito',
    'add_to_cart' => 'Agregar al carrito',
    'remove_from_cart' => 'Quitar del carrito',
    'confirm_reservation' => 'Confirmar reserva',
    'confirmBooking' => 'Confirmar reserva',
    'cart_updated' => 'Carrito actualizado exitosamente.',
    'itemUpdated' => 'Elemento del carrito actualizado exitosamente.',
    'cartItemAdded' => 'Tour agregado al carrito exitosamente.',
    'cartItemDeleted' => 'Tour eliminado del carrito exitosamente.',
    'emptyCart' => 'Tu carrito está vacío.',
    'no_items_in_cart' => 'Tu carrito está vacío.',
    'reservation_success' => '¡Reserva completada exitosamente!',
    'reservation_failed' => 'Hubo un error al realizar la reserva.',
    'booking_reference' => 'Referencia de reserva',
    'booking_date' => 'Fecha de reserva',
    'reservation_status' => 'Estado de la reserva',
    'blocked_date_for_tour' => 'La fecha :date está bloqueada para ":tour".',
    'tourCapacityFull' => 'La capacidad máxima para este tour ya está llena.',
    'totalEstimated' => 'Total estimado',
    'total_price' => 'Precio total',
    'total' => 'Total',
    'date' => 'Fecha',
    'status' => 'Estado',
    'actions' => 'Acciones',
    'active' => 'Activo',
    'delete' => 'Eliminar',
    'promoCode' => '¿Tienes un código promocional?',
    'promoCodePlaceholder' => 'Ingresa tu código promocional',
    'apply' => 'Aplicar',
    'remove' => 'Eliminar',
    'deleteItemTitle' => 'Eliminar elemento',
    'deleteItemText' => '¿Estás seguro de que deseas eliminar este elemento? Esta acción no se puede deshacer.',
    'deleteItemConfirm' => 'Eliminar',
    'deleteItemCancel' => 'Cancelar',
    'selectOption' => 'Selecciona una opción',
    'breakdown' => 'Desglose',
    'subtotal'  => 'Subtotal',
    'senior'    => 'Adulto mayor',
    'student'   => 'Estudiante',
    'custom' => 'Personalizado',
    'notes'             => 'Notas',
    'notes_placeholder' => '¿Algo que debamos saber? (alergias, movilidad, celebraciones, etc.)',
    'notes_help'        => 'Estas notas se enviarán a nuestro equipo junto con tu reserva.',


    // 10. VALIDACIÓN
    'required_field' => 'Este campo es obligatorio.',
    'invalid_email' => 'Correo electrónico inválido.',
    'invalid_date' => 'Fecha inválida.',
    'select_option' => 'Selecciona una opción',

    // 11. BOTONES Y CRUD
    'create' => 'Crear',
    'edit' => 'Editar',
    'update' => 'Actualizar',
    'activate' => 'Activar',
    'deactivate' => 'Desactivar',
    'confirm' => 'Confirmar',
    'cancel' => 'Cancelar',
    'save' => 'Guardar',
    'save_changes' => 'Guardar cambios',
    'are_you_sure' => '¿Estás seguro?',
    'optional' => 'Opcional',
    'edit_profile' => 'Editar perfil',
    'read_more' => 'Leer más',
    'read_less' => 'Leer menos',
    'switch_view' => 'Cambiar vista',
    'close' => 'Cerrar',

    // 12. PIE DE PÁGINA
    'contact_us' => 'Contáctanos',
    'location' => 'San José, Costa Rica',

    // 13. WHATSAPP
    'whatsapp_title' => config('app.name', 'Green Vacations CR'),
    'whatsapp_subtitle' => 'Normalmente responde al instante',
    'whatsapp_attention_schedule' => 'Lunes a domingo, de 7:30 a.m. a 7:30 p.m. (GMT-6)',
    'whatsapp_attention_language' => 'Atención solo en español e inglés',
    'whatsapp_greeting' => '👋 ¡Hola! ¿Cómo podemos ayudarte a planear tu aventura en Costa Rica?',
    'whatsapp_placeholder' => 'Hola, estoy interesado en uno de sus tours. ¿Podrían darme más información?',
    'whatsapp_button' => 'Enviar mensaje',
    'whatsapp_footer' => 'Conectado por WhatsApp Business',

    // 14. RESEÑAS
    'what_customers_thinks_about' => 'Lo que nuestros clientes piensan sobre',
    'loading_reviews' => 'Cargando reseñas',
    'redirect_to_tour' => 'Redirigir al tour',
    'would_you_like_to_visit' => '¿Te gustaría visitar ',
    'this_tour' => 'este tour',
    'no_reviews_found' => 'No se encontraron reseñas para este tour.',
    'no_reviews_available' => 'No hay reseñas disponibles.',
    'error_loading_reviews' => 'Error al cargar las reseñas.',
    'anonymous_user' => 'Anónimo',
    'see_more' => 'Ver más',
    'see_less' => 'Ver menos',
    'powered_by_viator' => 'Desarrollado por Viator',
    'go_to_tour' => '¿Quieres ir al tour ":name"?',
    'view_in_viator' => 'Ver :name en Viator',

    // 15. VIAJEROS
    'select_travelers' => 'Selecciona viajeros',
    'max_travelers_info' => 'Puedes seleccionar hasta 12 personas en total.',
    'adult' => 'Adulto',
    'adults' => 'Adultos',
    'adults_quantity' => 'Cantidad de adultos',
    'kid' => 'Niño',
    'kids' => 'Niños',
    'kids_quantity' => 'Cantidad de niños',
    'age_10_plus' => 'Edad 10+',
    'age_4_to_9' => 'Edad 4-9',
    'max_limits_info' => 'Máx. 12 viajeros, máx. 2 niños.',
    'total_persons' => 'Total de personas',
    'or' => 'o',
    'min' => 'Mín',

    // 16. CONTACTO
    'name' => 'Nombre',
    'subject' => 'Asunto',
    'message' => 'Mensaje',
    'send_message' => 'Enviar mensaje',
    'message_sent' => 'Mensaje enviado',
    'business_hours' => 'Horario de atención',
    'business_schedule' => 'Lunes a domingo, de 7:30 a.m. a 7:30 p.m.',
    'field_required'              => 'Este campo es obligatorio.',
    'email_invalid'               => 'Ingresa un correo electrónico válido.',
    'contact_spam_success' => 'Tu mensaje ha sido enviado.',
    'contact_success'      => 'Tu mensaje se ha enviado correctamente. Nos pondremos en contacto contigo muy pronto.',
    'contact_error'        => 'Ocurrió un error al enviar tu mensaje. Por favor, inténtalo de nuevo en unos minutos.',


    // Placeholders
    'contact_name_placeholder'    => 'Tu nombre completo',
    'contact_email_placeholder'   => 'tucorreo@ejemplo.com',
    'contact_subject_placeholder' => '¿En qué podemos ayudarte?',
    'contact_message_placeholder' => 'Cuéntanos en qué podemos ayudarte...',

    // SweetAlert
    'validation_error'            => 'Revisa los campos marcados.',
    'swal_ok'                     => 'Aceptar',

    // 17. ERRORES
    'access_denied' => 'Acceso denegado',
    'need_language' => 'Por favor, selecciona un idioma.',
    'need_pickup'   => 'Por favor, selecciona un hotel o un punto de encuentro.',
    'need_schedule_title' => 'Horario obligatorio',
    'need_schedule'       => 'Por favor, selecciona una hora.',
    'need_language_title' => 'Idioma obligatorio',
    'need_pickup_title'   => 'Recogida obligatoria',
    'no_slots_title'      => 'Sin horarios disponibles',
    'no_slots_text'       => 'No hay horarios disponibles para la fecha seleccionada. Por favor, elige otra fecha.',

    // 18. MODAL CARRITO LOGIN
    'login' => 'Iniciar sesión',
    'view_cart' => 'Ver carrito',
    'login_required_title' => 'Necesitas iniciar sesión',
    'login_required_text' => 'Para agregar al carrito debes iniciar sesión.',
    'login_required_text_confirm' => 'Para agregar al carrito debes iniciar sesión. ¿Ir a iniciar sesión?',
    'pax' => 'pax',
    'remove_item_title' => 'Quitar del carrito',
    'remove_item_text' => '¿Deseas quitar este tour del carrito?',
    'success' => 'Éxito',
    'error' => 'Error',
    'validation_error' => 'Datos incompletos',
    'editItem' => 'Editar elemento',
    // (sin duplicar 'close')
    'scheduleHelp' => 'Si el tour no requiere horario, déjalo en blanco.',
    'customHotel' => 'Hotel personalizado…',
    'otherHotel' => 'Usar hotel personalizado',
    'customHotelName' => 'Nombre del hotel personalizado',
    'customHotelHelp' => 'Si ingresas un hotel personalizado, la selección de la lista será ignorada.',
    'inactive' => 'Inactivo',
    'notSpecified' => 'No especificado',
    'noItemsSelected' => 'No hay elementos seleccionados',
    'saving' => 'Guardando…',

    // 19. SWEETALERTS (ACCIONES)
    'confirmReservationTitle' => '¿Estás seguro?',
    'confirmReservationText' => 'Tu reserva será confirmada',
    'confirmReservationConfirm' => 'Sí, confirmar',
    'confirmReservationCancel' => 'Cancelar',

    // 20. ÉXITOS (USADOS EN CONTROLADORES)
    'edit_profile_of' => 'Editar perfil',
    'profile_information' => 'Información del perfil',
    'new_password_optional' => 'Nueva contraseña (opcional)',
    'leave_blank_if_no_change' => 'Déjalo en blanco si no deseas cambiarla',
    'confirm_new_password_placeholder' => 'Confirmar nueva contraseña',

    'policies' => 'Políticas',
    'no_reservations_yet' => '¡Aún no tienes reservas!',
    'no_reservations_message' => 'Parece que aún no has reservado ninguna aventura con nosotros. ¿Por qué no exploras nuestros increíbles tours?',
    'view_available_tours' => 'Ver tours disponibles',
    'pending_reservations' => 'Reservas pendientes',
    'confirmed_reservations' => 'Reservas confirmadas',
    'cancelled_reservations' => 'Reservas canceladas',
    'reservations_generic' => 'Reservas',
    'generic_tour' => 'Tour genérico',
    'unknown_tour' => 'Tour desconocido',
    'tour_date' => 'Fecha del tour',
    'participants' => 'Participantes',
    'children' => 'Niños',
    'not_specified' => 'No especificado',
    'status_pending' => 'Pendiente',
    'status_confirmed' => 'Confirmada',
    'status_cancelled' => 'Cancelada',
    'status_unknown' => 'Desconocido',

    'view_receipt' => 'Ver recibo',

    'validation.unique' => 'Este correo electrónico ya está en uso',

    'validation' => [
        'too_many_attempts' => 'Demasiados intentos fallidos. Inténtalo de nuevo en :seconds segundos.',
    ],

    'open_tour'          => '¿Ir al tour?',
    'open_tour_text_pre' => 'Estás a punto de abrir la página del tour',
    'open_tour_confirm'  => 'Ir ahora',
    'open_tour_cancel'   => 'Cancelar',

    // Éxitos (usados en controladores)
    'show_password' => 'Mostrar contraseña',
    'user_registered_successfully'   => 'Usuario registrado exitosamente.',
    'user_updated_successfully'      => 'Usuario actualizado exitosamente.',
    'user_reactivated_successfully'  => 'Usuario reactivado exitosamente.',
    'user_deactivated_successfully'  => 'Usuario desactivado exitosamente.',
    'profile_updated_successfully'   => 'Perfil actualizado exitosamente.',
    'user_unlocked_successfully' => 'Tu cuenta ha sido desbloqueada. Ya puedes iniciar sesión.',
    'user_locked_successfully' => 'Usuario bloqueado exitosamente.',
    'auth_required_title' => 'Debes iniciar sesión para reservar',
    'auth_required_body'  => 'Inicia sesión o regístrate para comenzar tu compra. Los campos están bloqueados hasta que inicies sesión.',
    'login_now'           => 'Iniciar sesión',
    'login_to_book'       => 'Inicia sesión para reservar',
    'back_to_login'       => 'Volver a iniciar sesión',

    // 21. CORREO
    'mail' => [
        'trouble_clicking' => 'Si tienes problemas para hacer clic en el botón ":actionText", copia y pega la URL de abajo en tu navegador web',
    ],

    // 22. DASHBOARD (sección)
    'dashboard' => [
        'title'      => 'Panel',
        'greeting'   => '¡Hola :name! 👋',
        'welcome_to' => 'Bienvenido al panel de administración de :app.',
        'hint'       => 'Usa el menú lateral para comenzar a gestionar el contenido.',
    ],

    // 23. ENTIDADES
    'entities' => [
        'users'        => 'Usuarios',
        'tours'        => 'Tours',
        'tour_types'   => 'Tipos de tour',
        'languages'    => 'Idiomas',
        'schedules'    => 'Horarios',
        'amenities'    => 'Servicios',
        'bookings'     => 'Reservas',
        'total_bookings' => 'Reservas totales',
        'itineraries'  => 'Itinerarios',
        'items'        => 'Elementos',
    ],

    // 24. SECCIONES
    'sections' => [
        'available_tours' => 'Tours disponibles',
        'upcoming_bookings'     => 'Próximas reservas',
    ],

    // 25. ESTADOS VACÍOS
    'empty' => [
        'itinerary_items'   => 'Este itinerario aún no tiene elementos.',
        'itineraries'       => 'No se encontraron itinerarios.',
        'upcoming_bookings' => 'No hay próximas reservas.',
    ],

    // 26. BOTONES (GENÉRICOS)
    'buttons' => [
        'view' => 'Ver',
    ],

    'persons' => [
        'count' => '{0} 0 personas|{1} 1 persona|[2,*] :count personas',
        'title'            => 'Personas',
        'pax'              => 'PAX',
        'adults'           => 'Adultos',
        'kids'             => 'Niños',
        'seniors'          => 'Adultos mayores',
        'infants'          => 'Infantes',
        'students'         => 'Estudiantes',
        'guides'           => 'Guías',
        'drivers'          => 'Choferes',
        'free'             => 'Gratis',
        'other'            => 'Otros',
        'category'         => 'Categoría',
        'categories'       => 'Categorías',
        'quantity'         => 'Cantidad',
        'min'              => 'Mín',
        'max'              => 'Máx',
        'per_person'       => 'por persona',
        'price'            => 'Precio',
        'subtotal'         => 'Subtotal',
        'total'            => 'Total',
        'add_category'     => 'Agregar categoría',
        'remove_category'  => 'Quitar',
        'select_category'  => 'Selecciona una categoría',
        'required'         => 'Requerido',
        'optional'         => 'Opcional',
        'min_required'     => 'Mínimo requerido: :min',
        'max_allowed'      => 'Máximo permitido: :max',
        'invalid_quantity' => 'Cantidad inválida para ":category".',
    ],

    // 27. ETIQUETAS
    'labels' => [
        'reference' => 'Referencia',
        'date'      => 'Fecha',
    ],
    'pickup'      => 'Lugar Recogida',
    'filters_title'            => 'Filtrar resultados',
    'filters_subtitle'         => 'Combina búsqueda por texto y categoría para encontrar el tour ideal.',
    'search_tours_placeholder' => 'Buscar por nombre o descripción…',

    'all_categories'           => 'Todas las categorías',
    'category_label'           => 'Categoría',

    'filters_active'           => 'Filtros activos',
    'clear_filters'            => 'Limpiar filtros',
    'clear_short'              => 'Limpiar',
    'all_tours_title'       => 'Todos los tours',
    'all_tours_subtitle'    => 'Explora todas nuestras experiencias disponibles y encuentra tu próxima aventura.',
    'filters_btn' => 'Filtrar',
    'more_categories' => 'Más categorías',
    'more_tags' => '+ ver más',
    'less_tags' => 'ver menos',

    'tours_index_title'     => 'Tours',
    'tours_index_subtitle'  => 'Descubre nuestras experiencias y actividades disponibles.',

    // Para trans_choice
    'tours_count' => '1 tour disponible|:count tours disponibles',

    // Quantities
    'quantities' => 'Cantidades',
    'quantitiesHelp' => 'Ajusta las cantidades según necesites. Puedes dejar en 0 las categorías que no uses.',
    'no_tours_found' => 'No se encontraron tours.',

    // COOKIES (Cookie Consent)
    'cookies' => [
        'banner_aria' => 'Aviso de cookies',
        'title' => 'Usamos cookies',
        'message' => 'Este sitio utiliza cookies para mejorar tu experiencia. Puedes aceptar todas, rechazar las no esenciales o personalizar tus preferencias.',
        'accept_all' => 'Aceptar todas',
        'reject' => 'Rechazar',
        'customize' => 'Personalizar',
        'customize_title' => 'Personalizar cookies',
        'save_preferences' => 'Guardar preferencias',
        'change_preferences' => 'Preferencias de cookies',
        'close' => 'Cerrar',
        'always_active' => 'Siempre activas',
        'learn_more' => 'Más información sobre cookies',

        'essential' => 'Cookies esenciales',
        'essential_desc' => 'Necesarias para el funcionamiento básico del sitio (login, carrito, seguridad)',

        'functional' => 'Cookies funcionales',
        'functional_desc' => 'Recuerdan tus preferencias como idioma, moneda o tema',

        'analytics' => 'Cookies analíticas',
        'analytics_desc' => 'Nos ayudan a entender cómo usas el sitio para mejorarlo (Google Analytics)',

        'marketing' => 'Cookies de marketing',
        'marketing_desc' => 'Permiten mostrarte anuncios relevantes y medir campañas (Facebook Pixel)',
    ],

    'meta' => [
        'home_title' => 'Green Vacations Costa Rica | Tours y Aventuras en La Fortuna',
        'home_description' => 'Explora los mejores tours en La Fortuna y Volcán Arenal. Aventuras sostenibles, caminatas y más con Green Vacations Costa Rica.',
        'tours_title' => 'Tours y Actividades en La Fortuna | Green Vacations',
        'tours_description' => 'Descubre nuestra selección de tours en La Fortuna. Desde caminatas al volcán hasta actividades acuáticas. ¡Reserva tu aventura hoy!',
        'contact_title' => 'Contáctanos | Green Vacations Costa Rica',
        'contact_description' => '¿Tienes preguntas? Contáctanos para planificar tu viaje a Costa Rica. Estamos aquí para ayudarte con tu reserva de tours y transporte.',
        'faq_description' => 'Encuentra respuestas a preguntas frecuentes sobre nuestros tours en La Fortuna, proceso de reserva, cancelaciones y más. Planifica tu aventura en Costa Rica con facilidad.',
    ],
    'faq_more_questions' => '¿Tiene más preguntas?',

    // 28. EMAILS_TRANSACTIONAL
    'email_templates' => [
        'payment_reminder' => [
            'title' => 'Recordatorio de Pago',
            'intro' => 'Este es un recordatorio sobre su próxima reserva.',
            'auto_charge_warning' => 'Su tarjeta será cargada automáticamente en <strong>:days días</strong> el :date.',
            'pay_now_intro' => 'También puede pagar ahora usando el siguiente enlace:',
            'pay_now' => 'Pagar Ahora',
        ],
        'payment_failed' => [
            'title' => 'Pago Fallido',
            'intro' => 'No pudimos procesar su pago para la siguiente reserva:',
            'update_payment' => 'Por favor actualice su método de pago o intente nuevamente para asegurar su reserva.',
            'try_again' => 'Intentar de nuevo',
            'support_footer' => 'Si continúa teniendo problemas, por favor contacte a nuestro equipo de soporte.',
        ],
        'payment_success' => [
            'title' => 'Pago Exitoso',
            'intro' => '¡Gracias! Su pago ha sido recibido.',
            'confirmed_message' => 'Su reserva está confirmada. Le enviaremos más detalles cuando se acerque la fecha del tour.',
            'create_account_view_booking' => 'Crear Cuenta y Ver Reserva',
            'view_my_bookings' => 'Ver Mis Reservas',
        ],
        'booking_expired' => [
            'title' => 'Reserva Expirada',
            'intro' => 'Su solicitud de reserva ha expirado porque el pago no se completó a tiempo.',
            'rebook_message' => 'Si desea continuar con este tour, por favor cree una nueva reserva.',
            'book_again' => 'Reservar de Nuevo',
        ],
        'booking_confirmed_subject' => 'Reserva Confirmada #:reference',
        'booking_created_subject' => 'Solicitud de Reserva #:reference',
        'booking_cancelled_subject' => 'Reserva Cancelada #:reference',
        'booking_updated_subject' => 'Reserva Actualizada #:reference',
        'booking_details' => 'Detalles de la Reserva',
        'amount_due' => 'Monto Pendiente',
        'amount_paid' => 'Monto Pagado',
        'payment_date' => 'Fecha de Pago',
        'important' => 'Importante',
        'reference' => 'Referencia',
        'tour' => 'Tour',
        'date' => 'Fecha',
        'amount_paid' => 'Monto Pagado',
        'payment_date' => 'Fecha de Pago',
        'important' => 'Importante',
        'reference' => 'Referencia',
        'tour' => 'Tour',
        'date' => 'Fecha',
        'thanks' => 'Gracias',
        'contact_footer' => 'Si tiene alguna pregunta, no dude en contactarnos.',
    ],

    // 29. EMAIL PREVIEW
    'email_preview' => [
        'page_title' => 'Previsualización de Correos',
        'title' => 'Sistema de Previsualización',
        'description' => 'Previsualice todas las plantillas de correo sin enviarlas. Se utilizan datos reales de reservas cuando están disponibles, o datos de muestra.',
        'labels' => [
            'bookings' => 'Reservas de Clientes',
            'reviews' => 'Reseñas',
            'auth' => 'Usuario y Cuenta',
            'admin' => 'Admin y Reportes',
        ],
        'types' => [
            'booking-created' => 'Reserva Creada',
            'booking-confirmed' => 'Reserva Confirmada',
            'booking-updated' => 'Reserva Actualizada',
            'booking-cancelled' => 'Reserva Cancelada',
            'booking-expired' => 'Reserva Expirada (Impago)',
            'payment-success' => 'Pago Exitoso',
            'payment-failed' => 'Pago Fallido',
            'payment-reminder' => 'Recordatorio de Pago',
            'review-request' => 'Enlace Solicitud Reseña',
            'review-reply' => 'Notificación Respuesta Reseña',
            'review-submitted' => 'Reseña Enviada (Admin)',
            'welcome' => 'Bienvenida Usuario',
            'password-setup' => 'Configurar Cuenta (Password)',
            'verify-email' => 'Verificar Email',
            'reset-password' => 'Solicitud Reset Password',
            'password-updated' => 'Aviso Password Actualizado',
            'account-locked' => 'Notificación Cuenta Bloqueada',
            'email-change-verification' => 'Verificación Cambio Email',
            'email-change-completed' => 'Cambio Email Completado',
            'admin-booking-created' => 'Nueva Reserva (Admin)',
            'admin-paid-booking' => 'Reserva Pagada (Admin)',
            'admin-booking-expiring' => 'Alerta Reserva Expirando',
            'admin-daily-report' => 'Reporte Operativo Diario',
            'contact-message' => 'Mensaje Formulario Contacto',
        ],
        'tools_title' => 'Herramientas de Email',
        'view_telescope' => 'Ver Correos Enviados (Telescope)',
        'config_button' => 'Configuración de Email',
        'tip_title' => 'Consejo:',
        'tip_text' => 'Haga clic en cualquier enlace para abrirlo en una nueva pestaña. La previsualización usa el mismo diseño y estilos que los correos reales.',
        'config_title' => 'Configuración de Direcciones',
        'table' => [
            'purpose' => 'Propósito',
            'address' => 'Dirección',
            'env_var' => 'Variable de Entorno',
            'from' => 'De (No-Reply)',
            'reply_to' => 'Responder a (Support)',
            'admin_notify' => 'Notificaciones Admin',
            'booking_notify' => 'Notificaciones Reservas',
        ],
    ],
    'follow_us' => 'Siga-nos',
];
