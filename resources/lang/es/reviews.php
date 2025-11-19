<?php

return [
    'no_reviews' => 'No hay reseñas',

    'what_visitors_say' => '¿Qué opinan nuestros clientes?',
    'powered_by'        => 'Proporcionado por',

    'generic' => [
        'our_tour' => 'nuestro tour',
    ],

    // =========================
    // Comunes
    // =========================
    'common' => [
        'reviews'   => 'Reseñas',
        'provider'  => 'Proveedor',
        'status'    => 'Estado',
        'tour'      => 'Tour',
        'rating'    => 'Puntuación',
        'title'     => 'Título',
        'body'      => 'Contenido',
        'author'    => 'Autor',
        'actions'   => 'Acciones',
        'filter'    => 'Filtrar',
        'search'    => 'Buscar',
        'id'        => 'ID',
        'public'    => 'Pública',
        'private'   => 'Privada',
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
        'not_found' => 'No se encontraron resultados.',
        'clear'     => 'Limpiar',
        'language'  => 'Idioma',

        // 🔹 Claves añadidas para panel Proveedores
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
        'system_locked'   => 'Proveedor del sistema (bloqueado)',
    ],

    // =========================
    // Estados de review (moderación)
    // =========================
    'status' => [
        'pending'   => 'pendiente',
        'published' => 'publicada',
        'hidden'    => 'oculta',
        'flagged'   => 'marcada',
    ],

    // =========================
    // Admin - listado / moderación
    // =========================
    'admin' => [
        'index_title'   => 'Reviews',
        'index_titel'   => 'Reviews', // alias por typo común

        'new_local'     => 'Nueva (local)',
        'bulk_apply'    => 'Aplicar a seleccionados',

        'responded'     => '¿Respondido?',
        'last_reply'    => 'Última:',

        'filters'       => [
            'provider'  => 'Proveedor',
            'status'    => 'Estado',
            'tour_id'   => 'Tour ID',
            'stars'     => '⭐',
            'q'         => 'Buscar texto/autor...',
            'responded' => '¿Respondido?',
        ],

        'table' => [
            'date'     => 'Fecha',
            'review'   => 'Reseña',
            'client'   => 'Cliente',
            'tour'     => 'Tour',
        ],

        'messages' => [
            'created'        => 'Reseña creada.',
            'updated'        => 'Reseña actualizada.',
            'deleted'        => 'Reseña eliminada.',
            'published'      => 'Reseña publicada.',
            'hidden'         => 'Reseña ocultada.',
            'flagged'        => 'Reseña marcada.',
            'unflagged'      => 'Reseña desmarcada.',
            'bulk_published' => 'Publicadas :n reseñas.',
            'bulk_hidden'    => 'Ocultadas :n reseñas.',
            'bulk_flagged'   => 'Marcadas :n reseñas.',
            'bulk_deleted'   => 'Eliminadas :n reseñas.',
            'publish_min_rating' => 'No se puede publicar porque la calificación (:rating★) es menor que el mínimo permitido (:min★).',
            'bulk_published_partial' => 'Publicadas :ok reseñas. Omitidas :skipped por calificación menor a :min★.',
        ],
    ],

    // =========================
    // Admin - respuestas
    // =========================
    'replies' => [
        'reply'            => 'Responder',
        'title_create'     => 'Responder — Review #:id',
        'label_body'       => 'Respuesta',
        'label_is_public'  => 'Pública',
        'label_notify'     => 'Enviar email al cliente',
        'notify_to'        => 'Se enviará a: :email',
        'warn_no_email'    => 'Atención: no encontramos correo del cliente en esta reseña. La respuesta se guardará, pero no se enviará email.',
        'saved_notified'   => 'Respuesta publicada y notificada a :email.',
        'saved_no_email'   => 'Respuesta publicada. No se envió email porque no se encontró un destinatario.',
        'deleted'          => 'Respuesta eliminada.',
        'visibility_ok'    => 'Visibilidad actualizada.',
        'thread_title'     => 'Conversación — Review #:id',
        'thread_empty'     => 'Sin respuestas.',
        'last_reply'       => 'Última:',
    ],

    // =========================
    // Admin - solicitudes post-compra
    // =========================
    'requests' => [
        'index_title' => 'Solicitar reseñas',
        'subtitle'    => 'Envía links de reseña post-compra y gestiona solicitudes ya enviadas.',

        // Tabs
        'tabs' => [
            'eligible'  => 'Elegibles (reservas)',
            'requested' => 'Solicitadas (enviadas)',
        ],

        // Filtros
        'filters' => [
            'q_placeholder' => 'ID, nombre o email',
            'any_status'    => '— Cualquiera —',
            'from'          => 'Desde',
            'to'            => 'Hasta',
        ],

        'window_days'      => 'Ventana (días)',
        'date_column'      => 'Columna fecha',
        'calculated_range' => 'Rango calculado',
        'tour_id'          => 'Tour ID',
        'btn_request'      => 'Solicitar reseña',
        'no_eligible'      => 'No hay reservas elegibles.',

        'table' => [
            'booking'   => 'Reserva',
            'reference' => 'Referencia',
            'sent_at'   => 'Enviado',
            'states'    => 'Estados',
        ],

        'labels' => [
            'expires_in_days' => 'Días de expiración',
            'expires_at'      => 'Expira',
            'used_at'         => 'Usada',
        ],

        'actions' => [
            'resend'         => 'Reenviar',
            'confirm_delete' => '¿Eliminar esta solicitud?',
        ],

        'status' => [
            'active'    => 'Vigentes',
            'sent'      => 'Enviadas',
            'reminded'  => 'Reenviadas',
            'used'      => 'Usadas',
            'expired'   => 'Expiradas',
            'cancelled' => 'Canceladas',
        ],

        'status_labels' => [
            'created'   => 'creada',
            'sent'      => 'enviada',
            'reminded'  => 'reenviada',
            'fulfilled' => 'completada',
            'expired'   => 'expirada',
            'cancelled' => 'cancelada',
            'active'    => 'vigente',
        ],

        'send_ok'   => 'Solicitud de reseña enviada.',
        'resend_ok' => 'Solicitud reenviada.',
        'remind_ok' => 'Recordatorio enviado.',
        'expire_ok' => 'Solicitud expirada.',
        'deleted'   => 'Solicitud eliminada.',
        'none'      => 'No hay solicitudes.',

        'errors' => [
            'used'    => 'Esta solicitud ya fue usada.',
            'expired' => 'Esta solicitud está expirada.',
        ],
    ],

    // =========================
    // Público (formulario de reseña)
    // =========================
    'public' => [
        'form_title'   => 'Dejar una reseña',
        'labels'       => [
            'rating'       => 'Puntuación',
            'title'        => 'Título (opcional)',
            'body'         => 'Tu experiencia',
            'author_name'  => 'Tu nombre (opcional)',
            'author_email' => 'Tu correo (opcional)',
            'submit'       => 'Enviar reseña',
        ],
        'thanks'       => '¡Gracias por tu reseña! 🌿',
        'thanks_body' => 'Tu opinión es muy importante y nos ayuda a mejorar, te lo agaradecemos de corazón',
        'thanks_farewell' => "Esperamos que hayas disfrutado con nosotros y esperamos vernos pronto.\n\n🇨🇷 ¡Pura Vida mae! 🇨🇷",
        'thanks_dup'   => '¡Gracias! Ya teníamos tu reseña registrada 🙌',
        'expired'      => 'Este enlace ya expiró, pero gracias por tu intención 💚',
        'used'         => 'Esta solicitud ya fue usada.',
        'used_help'    => 'Este enlace de reseña ya fue utilizado. Si crees que es un error o quieres actualizar tu comentario, contáctanos y con gusto te ayudamos.',
        'not_found'    => 'Solicitud no encontrada.',
        'back_home'  => 'Regresar',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [

        'brand_from'   => 'Green Vacations CR',
        'contact_line' => 'Si necesitas ayuda, contáctanos en :email o al :phone. Visítanos en :url.',
        'request' => [
            'preheader_with_date' => 'Cuéntanos tu experiencia en :tour (:date). No te toma ni un minuto.',
            'preheader'           => 'Cuéntanos tu experiencia en :tour. No te toma ni un minuto.',
            'subject'   => '¿Cómo te fue en :tour?',
            'cta'       => 'Dejar mi reseña',
            'footer'    => 'Gracias por apoyar al turismo local. ¡Te esperamos pronto de vuelta! 🌿',
            'expires'   => '* Este enlace estará activo hasta: :date.',
            'greeting'  => 'Hola :name,',
            'intro'     => '¡Pura vida! 🙌 Gracias por elegirnos. Queremos saber cómo te fue en :tour.',
            'ask'       => '¿Nos regalas 1–2 minutos para dejar tu reseña? De verdad cuenta muchísimo.',
            'fallback'  => 'Si el botón no funciona, copia y pega este enlace en tu navegador:',
        ],
        'reply' => [
            'subject'  => 'Respuesta a tu reseña',
            'greeting' => 'Hola :name,',
            'intro'    => 'Nuestro equipo ha respondido a tu reseña :extra.',
            'quote'    => '“:text”',
            'sign'     => '— :admin',
        ],
        'submitted' => [
            'subject' => 'Nueva reseña recibida',
        ],
    ],

    // =========================
    // Front
    // =========================
    'front' => [
        'see_more'   => 'Ver más reseñas',
        'no_reviews' => 'Aún no hay reseñas.',
    ],

    // =========================
    // Proveedores
    // =========================
    'providers' => [
        'index_title' => 'Proveedores de reseñas',
        'system_locked' => 'Proveedor del sistema',
        'messages' => [
            'cannot_delete_local' => 'El proveedor “local” es del sistema y no puede eliminarse.',
            'created'        => 'Proveedor creado.',
            'updated'        => 'Proveedor actualizado.',
            'deleted'        => 'Proveedor eliminado.',
            'status_updated' => 'Estado actualizado.',
            'cache_flushed'  => 'Caché limpiada.',
            'test_fetched'   => 'Se obtuvieron :n reseñas.',
        ],
    ],

    // =========================
    // Sync
    // =========================
    'sync' => [
        'queued' => 'Sincronización encolada para :target.',
        'all'    => 'todos los proveedores',
    ],

    // =========================
    // Hilo/conversación
    // =========================
    'thread' => [
        'title'             => 'Hilo de la reseña #:id',
        'header'            => 'Hilo — Review #:id',
        'replies_header'    => 'Respuestas',
        'th_date'           => 'Fecha',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Visible',
        'th_body'           => 'Contenido',
        'th_actions'        => 'Acciones',
        'toggle_visibility' => 'Cambiar visibilidad',
        'delete'            => 'Eliminar',
        'confirm_delete'    => '¿Eliminar respuesta?',
        'empty'             => 'Sin respuestas aún.',
    ],

    // =========================
    // Formulario admin (crear/editar)
    // =========================
    'form' => [
        'title_edit'       => 'Editar Reseña',
        'title_new'        => 'Nueva Reseña',
        'visible_publicly' => 'Visible públicamente',
    ],

    // =========================
    // Alias para emails de respuesta (si se usan fuera de "emails")
    // =========================
    'reply' => [
        'subject'          => 'Respuesta a tu reseña',
        'greeting'         => 'Hola :name,',
        'about_html'       => 'sobre <strong>:tour</strong>',
        'about_text'       => 'sobre :tour',
        'intro'            => 'Nuestro equipo ha respondido a tu reseña :extra.',
        'quote'            => '“:text”',
        'sign'             => '— :admin',
        'closing'          => 'Si tienes dudas o quieres ampliar tu comentario, solo responde a este correo. ¡Pura vida! 🌿',
        'rights_reserved'  => 'Todos los derechos reservados',
    ],

    // Fallback para el saludo si no hay nombre
    'traveler' => 'viajero/a',

    // =====================================================================
    // ==== Compatibilidad con archivo de traducciones antiguo (legacy) ====
    // =====================================================================

    'loaded'           => 'Reseñas cargadas exitosamente.',
    'provider_error'   => 'Hubo un problema con el proveedor de reseñas.',
    'service_busy'     => 'El servicio está ocupado, por favor intenta nuevamente en breve.',
    'unexpected_error' => 'Ocurrió un error inesperado al cargar las reseñas.',
    'anonymous'        => 'Anónimo',

    'what_customers_think_about' => 'Lo que los clientes piensan sobre',
    'previous_review'            => 'Reseña anterior',
    'next_review'                => 'Siguiente reseña',
    'loading'                    => 'Cargando reseñas...',
    // 'what_visitors_say' ya existe arriba; se mantiene por compatibilidad
    'reviews_title'              => 'Reseñas de clientes',
    // 'powered_by' ya existe arriba; se mantiene por compatibilidad
    'view_on_viator'             => 'Ver :name en Viator',

    // Modal / acciones (legacy)
    'open_tour_title'    => '¿Abrir tour?',
    'open_tour_text_pre' => 'Estás a punto de abrir la página del tour',
    'open_tour_confirm'  => 'Abrir ahora',
    'open_tour_cancel'   => 'Cancelar',

    // Controles del carrusel (legacy, alias de front.see_more/less)
    'previous' => 'Anterior',
    'next'     => 'Siguiente',
    'see_more' => 'Ver más',
    'see_less' => 'Ver menos',
];
