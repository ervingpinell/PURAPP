<?php

return [

        /*
    |--------------------------------------------------------------------------
    | Tamaño máximo por imagen (en KB)
    |--------------------------------------------------------------------------
    | Equivalente a ~30MB por archivo.
    */
    'max_image_kb' => 30720,

    /*
    |--------------------------------------------------------------------------
    | Cantidad máxima de imágenes por product
    |--------------------------------------------------------------------------
    */
    'max_images_per_product' => 20,
    /*
    |--------------------------------------------------------------------------
    | Límite de Drafts por Usuario
    |--------------------------------------------------------------------------
    |
    | Cantidad máxima de borradores que un usuario puede tener activos
    | simultáneamente. Esto ayuda a mantener la base de datos limpia y
    | fomenta que los usuarios completen o eliminen borradores antiguos.
    |
    */

    'max_drafts_per_user' => env('PRODUCTS_MAX_DRAFTS_PER_USER', 5),

    /*
    |--------------------------------------------------------------------------
    | Días para Auto-limpieza de Drafts
    |--------------------------------------------------------------------------
    |
    | Los borradores sin actividad durante este período serán marcados para
    | eliminación automática por el comando products:clean-old-drafts
    |
    */

    'draft_auto_delete_days' => env('PRODUCTS_DRAFT_AUTO_DELETE_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Días para Notificación de Drafts Pendientes
    |--------------------------------------------------------------------------
    |
    | Si un borrador tiene más de estos días sin actividad, el usuario
    | recibirá una notificación recordándole completarlo o eliminarlo.
    |
    */

    'draft_notification_days' => env('PRODUCTS_DRAFT_NOTIFICATION_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Auditoría
    |--------------------------------------------------------------------------
    |
    | Configuración del sistema de auditoría de products
    |
    */

    'audit' => [
        // Habilitar/deshabilitar sistema de auditoría
        'enabled' => env('PRODUCTS_AUDIT_ENABLED', true),

        // Días de retención de logs de auditoría (null = infinito)
        'retention_days' => env('PRODUCTS_AUDIT_RETENTION_DAYS', 365),

        // Auditar cambios en campos específicos
        'track_fields' => [
            'name',
            'slug',
            'overview',
            'length',
            'max_capacity',
            'group_size',
            'color',
            'product_type_id',
            'is_active',
            'is_draft',
            'current_step',
        ],

        // Excluir campos de auditoría
        'exclude_fields' => [
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración del Wizard
    |--------------------------------------------------------------------------
    |
    | Opciones de comportamiento del wizard de creación de products
    |
    */

    'wizard' => [
        // Permitir saltar pasos (no recomendado)
        'allow_skip_steps' => env('PRODUCTS_WIZARD_ALLOW_SKIP', false),

        // Requerir guardar antes de avanzar al siguiente paso
        'require_save_before_next' => env('PRODUCTS_WIZARD_REQUIRE_SAVE', true),

        // Auto-guardar progreso (cada X segundos, 0 = deshabilitado)
        'autosave_interval' => env('PRODUCTS_WIZARD_AUTOSAVE_INTERVAL', 0),

        // Mostrar advertencia al salir sin guardar
        'warn_on_exit' => env('PRODUCTS_WIZARD_WARN_ON_EXIT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notificaciones
    |--------------------------------------------------------------------------
    |
    | Configuración de notificaciones relacionadas con products
    |
    */

    'notifications' => [
        // Notificar al crear un product
        'notify_on_create' => env('PRODUCTS_NOTIFY_ON_CREATE', false),

        // Notificar al publicar un product
        'notify_on_publish' => env('PRODUCTS_NOTIFY_ON_PUBLISH', true),

        // Notificar sobre drafts pendientes
        'notify_pending_drafts' => env('PRODUCTS_NOTIFY_PENDING_DRAFTS', true),

        // Frecuencia de notificación de drafts pendientes
        // Opciones: daily, weekly, biweekly, monthly
        'pending_drafts_frequency' => env('PRODUCTS_PENDING_DRAFTS_FREQUENCY', 'weekly'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Permisos y Seguridad
    |--------------------------------------------------------------------------
    |
    | Configuración de permisos y restricciones de seguridad
    |
    */

    'permissions' => [
        // Solo el creador puede editar sus drafts
        'creator_only_edit_drafts' => env('PRODUCTS_CREATOR_ONLY_EDIT_DRAFTS', true),

        // Requerir aprobación para publicar products
        'require_approval_to_publish' => env('PRODUCTS_REQUIRE_APPROVAL', false),

        // Roles que pueden aprobar products
        'approver_roles' => ['admin', 'manager'],

        // Roles que pueden ver auditoría completa
        'audit_viewer_roles' => ['admin', 'auditor'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Exportación
    |--------------------------------------------------------------------------
    |
    | Opciones para exportar datos de products y auditoría
    |
    */

    'export' => [
        // Formatos disponibles para exportar
        'allowed_formats' => ['csv', 'xlsx', 'pdf'],

        // Límite máximo de registros por exportación
        'max_records' => env('PRODUCTS_EXPORT_MAX_RECORDS', 10000),

        // Incluir campos sensibles en exportación
        'include_sensitive_data' => env('PRODUCTS_EXPORT_SENSITIVE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance y Caché
    |--------------------------------------------------------------------------
    |
    | Configuración de optimización y caché
    |
    */

    'performance' => [
        // Cachear listados de products (en minutos, 0 = sin caché)
        'cache_listings_minutes' => env('PRODUCTS_CACHE_LISTINGS', 60),

        // Eager load automático de relaciones
        'eager_load_relations' => [
            'productType',
            'languages',
            'amenities',
            'schedules',
            'prices.category',
        ],

        // Paginación por defecto
        'per_page' => env('PRODUCTS_PER_PAGE', 25),
    ],

    /*
    |--------------------------------------------------------------------------
    | Desarrollo y Debug
    |--------------------------------------------------------------------------
    |
    | Opciones útiles durante desarrollo
    |
    */

    'debug' => [
        // Mostrar queries de SQL en logs
        'log_queries' => env('PRODUCTS_LOG_QUERIES', false),

        // Mostrar información de debug en wizard
        'show_debug_info' => env('PRODUCTS_DEBUG_INFO', false),

        // Permitir acciones peligrosas en producción
        'allow_dangerous_actions' => env('PRODUCTS_ALLOW_DANGEROUS', false),
    ],

];
