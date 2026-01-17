<?php

return [
    'no_reviews' => 'No reviews available',

    'what_visitors_say' => 'What do our customers say?',
    'powered_by'        => 'Powered by',

    'generic' => [
        'our_tour' => 'our tour',
    ],

    // =========================
    // Common
    // =========================
    'common' => [
        'reviews'   => 'Reviews',
        'provider'  => 'Provider',
        'status'    => 'Status',
        'tour'      => 'Tour',
        'rating'    => 'Calificación',
        'title'     => 'Título',
        'body'      => 'Contenido',
        'author'    => 'Autor',
        'actions'   => 'Acciones',
        'filter'    => 'Filtrar',
        'search'    => 'Buscar',
        'id'        => 'ID',
        'public'    => 'Público',
        'private'   => 'Privado',
        'back'      => 'Volver',
        'save'      => 'Guardar',
        'create'    => 'Crear',
        'edit'      => 'Editar',
        'delete'    => 'Eliminar',
        'publish'   => 'Publicar',
        'hide'      => 'Ocultar',
        'flag'      => 'Marcar',
        'unflag'    => 'Desmarcar',
        'apply'     => 'Aplicar',
        'yes'       => 'Sí',
        'no'        => 'No',
        'not_found' => 'No results were found.',
        'clear'     => 'Clear',
        'language'  => 'Idioma',

        // 🔹 Keys added for Providers panel
        'new'             => 'Nuevo',
        'name'            => 'Nombre',
        'active'          => 'Activo',
        'inactive'        => 'Inactivo',
        'indexable'       => 'Indexable',
        'indexable_yes'   => 'Incluye marcado indexable/JSON-LD',
        'indexable_no'    => 'No indexable',
        'activate'        => 'Activar proveedor',
        'deactivate'      => 'Desactivar proveedor',
        'activate_title'  => '¿Activar proveedor?',
        'activate_text'   => 'El proveedor quedará activo.',
        'deactivate_title' => '¿Desactivar proveedor?',
        'deactivate_text' => 'El proveedor dejará de estar activo.',
        'cancel'          => 'Cancelar',
        'test'            => 'Probar conexión',
        'flush_cache'     => 'Vaciar caché',
        'delete_confirm'  => '¿Eliminar proveedor?',
        'system_locked'   => 'Proveedor de sistema (bloqueado)',
    ],

    // =========================
    // Review statuses (moderation)
    // =========================
    'status' => [
        'pending'   => 'pending',
        'published' => 'published',
        'hidden'    => 'hidden',
        'flagged'   => 'flagged',
    ],

    // =========================
    // Admin - list / moderation
    // =========================
    'admin' => [
        'index_title'   => 'Reviews',
        'index_titel'   => 'Reviews', // alias for common typo

        'new_local'     => 'Nueva (local)',
        'bulk_apply'    => 'Aplicar a la selección',
        'external_provider_note' => 'Nota del proveedor externo',

        'responded'     => '¿Respondido?',
        'last_reply'    => 'Última:',

        'filters'       => [
            'provider'  => 'Proveedor',
            'status'    => 'Estado',
            'tour_id'   => 'ID del Tour',
            'stars'     => '⭐',
            'q'         => 'Buscar texto/autor…',
            'responded' => '¿Respondido?',
        ],

        'table' => [
            'date'     => 'Fecha',
            'review'   => 'Reseña',
            'client'   => 'Cliente',
            'tour'     => 'Tour',
        ],

        'messages' => [
            'created'        => 'Review created.',
            'updated'        => 'Review updated.',
            'deleted'        => 'Review deleted.',
            'published'      => 'Review published.',
            'hidden'         => 'Review hidden.',
            'flagged'        => 'Review flagged.',
            'unflagged'      => 'Review unflagged.',
            'bulk_published' => ':n reviews published.',
            'bulk_hidden'    => ':n reviews hidden.',
            'bulk_flagged'   => ':n reviews flagged.',
            'bulk_deleted'   => ':n reviews deleted.',
            'publish_min_rating' => 'Cannot publish because the rating (:rating★) is lower than the allowed minimum (:min★).',
            'bulk_published_partial' => ':ok reviews published. :skipped skipped because their rating was lower than :min★.',
        ],
        'sweetalert' => [
            'delete_title'   => '¿Eliminar reseña?',
            'delete_text'    => 'Esta acción no se puede deshacer.',
            'delete_confirm' => 'Sí, eliminar',
            'delete_cancel'  => 'Cancelar',
        ],

        // New form fields
        'booking_ref'      => 'Ref. Reserva',
        'user_email'       => 'Email de Usuario',
        'optional_parens'  => '(Opcional)',
    ],

    // =========================
    // Admin - replies
    // =========================
    'replies' => [
        'reply'            => 'Responder',
        'title_create'     => 'Reply — Review #:id',
        'label_body'       => 'Reply',
        'label_is_public'  => 'Public',
        'label_notify'     => 'Send email to customer',
        'notify_to'        => 'It will be sent to: :email',
        'warn_no_email'    => 'Advertencia: no se encontró una dirección de correo electrónico para el cliente en esta reseña. La respuesta se guardará, pero no se enviará ningún correo.',
        'saved_notified'   => 'Reply published and emailed to :email.',
        'saved_no_email'   => 'Reply published. No email was sent because no recipient was found.',
        'deleted'          => 'Reply deleted.',
        'visibility_ok'    => 'Visibility updated.',
        'thread_title'     => 'Conversation — Review #:id',
        'thread_empty'     => 'No replies.',
        'last_reply'       => 'Última:',

    ],

    // =========================
    // Admin - post-purchase review requests
    // =========================
    'requests' => [
        'index_title' => 'Solicitar reseña',
        'subtitle'    => 'Enviar enlaces de reseña post-compra y gestionar solicitudes ya enviadas.',

        // Tabs
        'tabs' => [
            'eligible'  => 'Elegibles (reservas)',
            'requested' => 'Solicitadas (enviadas)',
        ],

        // Filters
        'filters' => [
            'q_placeholder' => 'ID, nombre o correo',
            'any_status'    => '— Cualquiera —',
            'from'          => 'Desde',
            'to'            => 'Hasta',
        ],

        'window_days'      => 'Ventana (días)',
        'date_column'      => 'Columna de fecha',
        'date_options'     => [
            'created_at' => 'Reserva creada',
            'tour_date'  => 'Fecha del tour',
        ],
        'calculated_range' => 'Rango calculado',
        'tour_id'          => 'ID del Tour',
        'btn_request'      => 'Solicitar reseña',
        'no_eligible'      => 'No hay reservas elegibles.',

        'table' => [
            'booking'   => 'Reserva',
            'reference' => 'Referencia',
            'sent_at'   => 'Enviado el',
            'states'    => 'Estados',
            'expires_days' => 'Expira (días)',
        ],

        'labels' => [
            'expires_in_days' => 'Expiración (días)',
            'expires_at'      => 'Expira el',
            'used_at'         => 'Usado el',
        ],

        'actions' => [
            'resend'         => 'Reenviar',
            'confirm_delete' => '¿Eliminar esta solicitud?',
        ],

        'sweetalert' => [
            'delete_title'   => '¿Eliminar Solicitud de Reseña?',
            'delete_text'    => 'Esta acción no se puede deshacer.',
            'delete_confirm' => 'Sí, eliminar',
            'delete_cancel'  => 'Cancelar',
        ],

        'status' => [
            'active'    => 'Activo',
            'sent'      => 'Enviado',
            'reminded'  => 'Recordado',
            'used'      => 'Usado',
            'expired'   => 'Expirado',
            'cancelled' => 'Cancelado',
        ],

        'status_labels' => [
            'created'   => 'creado',
            'sent'      => 'enviado',
            'reminded'  => 'recordado',
            'fulfilled' => 'completado',
            'expired'   => 'expirado',
            'cancelled' => 'cancelado',
            'active'    => 'activo',
        ],

        'send_ok'   => 'Solicitud de reseña enviada.',
        'resend_ok' => 'Solicitud reenviada.',
        'remind_ok' => 'Recordatorio enviado.',
        'expire_ok' => 'Solicitud expirada.',
        'deleted'   => 'Solicitud eliminada.',
        'none'      => 'No hay solicitudes.',

        'errors' => [
            'used'    => 'Esta solicitud ya ha sido usada.',
            'expired' => 'Esta solicitud ha expirado.',
        ],
        'no_requests' => 'No se encontraron solicitudes.',
    ],

    // =========================
    // Public (review form)
    // =========================
    'public' => [
        'form_title'        => 'Dejar una reseña',
        'form_heading'      => 'Comparte Tu Experiencia',
        'form_subheading'   => 'Cuéntanos sobre tu experiencia en :tour',
        'form_description'  => 'Tu opinión nos ayuda a mejorar y ayuda a otros viajeros',
        'booking_date'      => 'Fecha del Tour',
        'participants'      => 'Participantes',
        'adults'            => 'adultos',
        'children'          => 'niños',
        'booking_code'      => 'Reserva',
        'help_title'        => '¿Por qué dejar una reseña?',
        'help_text'         => 'Tu opinión honesta nos ayuda a mejorar nuestros servicios y ayuda a otros viajeros a tomar decisiones informadas.',
        'error_title'       => 'Por favor corrige los siguientes errores:',
        'optional'          => 'opcional',
        'rating_help'       => 'Haz clic en las estrellas para calificar tu experiencia',
        'title_placeholder' => 'Resume tu experiencia en pocas palabras',
        'body_placeholder'  => 'Cuéntanos sobre tu experiencia... ¿Qué fue lo que más disfrutaste? ¿Qué podría mejorar?',
        'body_help'         => 'Mínimo 10 caracteres, máximo 1000',
        'privacy_note'      => 'Tu reseña podría ser publicada después de moderación',
        'labels'       => [
            'rating'       => 'Rating',
            'title'        => 'Título de la Reseña',
            'body'         => 'Your experience',
            'author_name'  => 'Your name (optional)',
            'author_email' => 'Your email (optional)',
            'submit'       => 'Submit review',
        ],
        'thanks'       => 'Thank you for your review! 🌿',
        'thanks_body'  => 'Your opinion is very important and helps us improve. We truly appreciate it.',
        'thanks_farewell' => "We hope you enjoyed your time with us and we hope to see you again soon.\n\n🇨🇷 Pura Vida mae! 🇨🇷",
        'thanks_dup'   => 'Thank you! We already had your review on file 🙌',
        'expired'      => 'This link has expired, but thank you so much for your intention 💚',
        'used'         => 'Esta solicitud ya ha sido utilizzada.',
        'used_help'    => 'Este enlace de reseña ya se utilizó. Si crees que es un error o deseas actualizar tu comentario, contáctanos y con gusto te ayudaremos.',
        'not_found'    => 'Request not found.',
        'back_home'    => 'Go back',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [

        'brand_from'   => config('app.name', 'Green Vacations CR'),
        'contact_line' => 'If you need help, contact us at :email or :phone. Visit us at :url.',
        'request' => [
            'preheader_with_date' => 'Cuéntanos sobre tu experiencia en :tour (:date). Solo toma un minuto.',
            'preheader'           => 'Cuéntanos sobre tu experiencia en :tour. Solo toma un minuto.',
            'subject'   => '¿Cómo fue tu experiencia en :tour?',
            'cta'       => 'Dejar mi reseña',
            'footer'    => 'Gracias por apoyar el turismo local. ¡Esperamos verte de nuevo pronto! 🌿',
            'expires'   => '* Este enlace estará activo hasta: :date.',
            'greeting'  => 'Hola :name,',
            'intro'     => '¡Pura vida! 🙌 Gracias por elegirnos. Nos encantaría saber cómo te fue en :tour.',
            'ask'       => '¿Nos regalas 1–2 minutos para dejar tu reseña? ¡Nos ayuda muchísimo!',
            'fallback'  => 'Si el botón no funciona, copia y pega este enlace en tu navegador:',
        ],
        'reply' => [
            'subject'  => 'Respuesta a tu reseña',
            'greeting' => 'Hola :name,',
            'intro'    => 'Nuestro equipo ha respondido a tu reseña :extra.',
            'quote'    => '“:text”',
            'sign'     => '— :admin',
            'closing'  => 'Si tienes alguna duda o quieres ampliar tu comentario, solo responde a este correo. ¡Pura vida! 🌿',
        ],
        'submitted' => [
            'subject' => 'New review received',
        ],
        'booking' => [
            'cancelled_subject' => 'Reserva Cancelada - Pago No Recibido #:ref',
            'payment_success_subject' => '¡Pago Confirmado! #:ref',
            'payment_reminder_subject' => 'Recordatorio de Pago - ¡Tu Tour se Acerca! #:ref',
        ],
    ],

    // =========================
    // Front
    // =========================
    'front' => [
        'see_more'   => 'See more reviews',
        'no_reviews' => 'There are no reviews yet.',
    ],

    // =========================
    // Providers
    // =========================
    'providers' => [
        'index_title' => 'Proveedores de reseñas',
        'indexable' => 'Indexable',
        'cache_ttl' => 'TTL Caché (seg)',
        'back' => 'Volver',
        'actions' => 'Acciones',
        'system_locked' => 'Proveedor del Sistema',
        'messages' => [
            'cannot_delete_local' => 'El proveedor “local” es de sistema y no puede eliminarse.',
            'created'        => 'Proveedor creado.',
            'updated'        => 'Proveedor actualizado.',
            'deleted'        => 'Proveedor eliminado.',
            'status_updated' => 'Estado actualizado.',
            'cache_flushed'  => 'Caché vaciada.',
            'test_fetched'   => ':n reseñas obtenidas.',
            'mapping_added'   => 'Mapeo agregado correctamente.',
            'mapping_updated' => 'Mapeo actualizado correctamente.',
            'mapping_deleted' => 'Mapeo eliminado correctamente.',
        ],
        'product_map' => [
            'title' => 'Mapeo de Productos - :provider',
        ],
        'product_mapping_title' => 'Mapeo de Productos - :name',
        'product_mappings' => 'Mapeos de Productos',
        'tour' => 'Tour',
        'select_tour' => 'Seleccionar tour',
        'select_tour_placeholder' => 'Selecciona un tour...',
        'product_code' => 'Código de producto',
        'product_code_placeholder' => 'Ej: 12732-ABC',
        'add_mapping' => 'Agregar mapeo',
        'no_mappings' => 'No hay mapeos configurados',
        'confirm_delete_mapping' => '¿Estás seguro de eliminar este mapeo?',
        'help_title' => 'Ayuda',
        'help_text' => 'Mapea códigos de productos externos a tours internos para sincronizar reseñas correctamente.',
        'help_step_1' => 'Selecciona un tour de la lista',
        'help_step_2' => 'Ingresa el código de producto del proveedor externo',
        'help_step_3' => 'Haz clic en "Agregar" para crear el mapeo',
    ],

    // =========================
    // Sync
    // =========================
    'sync' => [
        'queued' => 'Sync queued for :target.',
        'all'    => 'all providers',
    ],

    // =========================
    // Thread / conversation
    // =========================
    'thread' => [
        'title'             => 'Hilo de reseña #:id',
        'header'            => 'Hilo — Reseña #:id',
        'replies_header'    => 'Respuestas',
        'th_date'           => 'Fecha',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Visible',
        'th_body'           => 'Contenido',
        'th_actions'        => 'Acciones',
        'toggle_visibility' => 'Cambiar visibilidad',
        'delete'            => 'Eliminar',
        'confirm_delete'    => '¿Eliminar respuesta?',
        'empty'             => 'No hay respuestas aún.',
    ],

    // =========================
    // Admin form (create/edit)
    // =========================
    'form' => [
        'title_edit'       => 'Edit review',
        'title_new'        => 'Crear reseña',
        'visible_publicly' => 'Visible publicly',
    ],

    // =========================
    // Alias for reply emails (if used outside "emails")
    // =========================
    'reply' => [
        'subject'          => 'Respuesta a tu reseña',
        'greeting'         => 'Hola :name,',
        'about_html'       => 'sobre <strong>:tour</strong>',
        'about_text'       => 'sobre :tour',
        'intro'            => 'Nuestro equipo ha respondido a tu reseña :extra.',
        'quote'            => '“:text”',
        'sign'             => '— :admin',
        'closing'          => 'Si tienes alguna duda o quieres ampliar tu comentario, solo responde a este correo. ¡Pura vida! 🌿',
        'rights_reserved'  => 'Todos los derechos reservados',
    ],

    // Fallback for greeting if there is no name
    'traveler' => 'traveler',

    // =====================================================================
    // ==== Compatibility with old translation file (legacy) ================
    // =====================================================================

    'loaded'           => 'Reviews loaded successfully.',
    'provider_error'   => 'There was a problem with the review provider.',
    'service_busy'     => 'The service is busy, please try again shortly.',
    'unexpected_error' => 'An unexpected error occurred while loading reviews.',
    'anonymous'        => 'Anonymous',

    'what_customers_think_about' => 'What customers think about',
    'previous_review'            => 'Previous review',
    'next_review'                => 'Next review',
    'loading'                    => 'Loading reviews...',
    // 'what_visitors_say' already exists above; kept for compatibility
    'reviews_title'              => 'Customer reviews',
    // 'powered_by' already exists above; kept for compatibility
    'view_on_viator'             => 'View :name on Viator',

    // Modal / actions (legacy)
    'open_tour_title'    => 'Open tour page?',
    'open_tour_text_pre' => 'You are about to open the tour page for',
    'open_tour_confirm'  => 'Open now',
    'open_tour_cancel'   => 'Cancel',

    // Carousel controls (legacy, alias of front.see_more/less)
    'previous' => 'Previous',
    'next'     => 'Next',
    'see_more' => 'See more',
    'see_less' => 'See less',
];
