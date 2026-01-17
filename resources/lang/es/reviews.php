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
        'edit'      => 'Edit',
        'delete'    => 'Delete',
        'publish'   => 'Publish',
        'hide'      => 'Hide',
        'flag'      => 'Flag',
        'unflag'    => 'Unflag',
        'apply'     => 'Apply',
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

        'responded'     => 'Responded?',
        'last_reply'    => 'Last:',

        'filters'       => [
            'provider'  => 'Proveedor',
            'status'    => 'Estado',
            'tour_id'   => 'ID del Tour',
            'stars'     => '⭐',
            'q'         => 'Buscar texto/autor…',
            'responded' => '¿Respondido?',
        ],

        'table' => [
            'date'     => 'Date',
            'review'   => 'Review',
            'client'   => 'Client',
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
        'warn_no_email'    => 'Warning: we did not find an email address for the customer in this review. The reply will be saved, but no email will be sent.',
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
        'calculated_range' => 'Rango calculado',
        'tour_id'          => 'ID del Tour',
        'btn_request'      => 'Solicitar reseña',
        'no_eligible'      => 'No hay reservas elegibles.',

        'table' => [
            'booking'   => 'Reserva',
            'reference' => 'Referencia',
            'sent_at'   => 'Enviado el',
            'states'    => 'Estados',
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
        'form_title'   => 'Leave a review',
        'labels'       => [
            'rating'       => 'Rating',
            'title'        => 'Title (optional)',
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
        'used'         => 'This request has already been used.',
        'used_help'    => 'This review link has already been used. If you think this is an error or want to update your comment, contact us and we will gladly help you.',
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
            'preheader_with_date' => 'Tell us about your experience on :tour (:date). It only takes a minute.',
            'preheader'           => 'Tell us about your experience on :tour. It only takes a minute.',
            'subject'   => 'How was your experience on :tour?',
            'cta'       => 'Leave my review',
            'footer'    => 'Thank you for supporting local tourism. We hope to see you back soon! 🌿',
            'expires'   => '* This link will be active until: :date.',
            'greeting'  => 'Hi :name,',
            'intro'     => 'Pura vida! 🙌 Thank you for choosing us. We would love to know how your experience on :tour was.',
            'ask'       => 'Would you give us 1–2 minutes to leave your review? It really means a lot.',
            'fallback'  => 'If the button does not work, copy and paste this link into your browser:',
        ],
        'reply' => [
            'subject'  => 'Reply to your review',
            'greeting' => 'Hi :name,',
            'intro'    => 'Our team has replied to your review :extra.',
            'quote'    => '“:text”',
            'sign'     => '— :admin',
            'closing'  => 'Si tienes alguna duda o quieres ampliar tu comentario, solo responde a este correo. ¡Pura vida! 🌿',
        ],
        'submitted' => [
            'subject' => 'New review received',
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
        'title'             => 'Review thread #:id',
        'header'            => 'Thread — Review #:id',
        'replies_header'    => 'Respuestas',
        'th_date'           => 'Date',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Visible',
        'th_body'           => 'Content',
        'th_actions'        => 'Actions',
        'toggle_visibility' => 'Toggle visibility',
        'delete'            => 'Delete',
        'confirm_delete'    => 'Delete reply?',
        'empty'             => 'No replies yet.',
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
        'subject'          => 'Reply to your review',
        'greeting'         => 'Hi :name,',
        'about_html'       => 'about <strong>:tour</strong>',
        'about_text'       => 'about :tour',
        'intro'            => 'Our team has replied to your review :extra.',
        'quote'            => '“:text”',
        'sign'             => '— :admin',
        'closing'          => 'If you have any questions or would like to expand on your comment, just reply to this email. Pura vida! 🌿',
        'rights_reserved'  => 'All rights reserved',
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
