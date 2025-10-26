<?php
/*************************************************************
 *  Index (searchable anchors)
 *  [01] POLICIES LINE 20
 *  [02] TOURTYPES LINE 141
 *  [03] FAQ LINE 200
 *  [04] TRANSLATIONS LINE 251
 *  [05] PROMOCODE LINE 361
 *  [06] CUT-OFF LINE 438
 *************************************************************/

return [

    // =========================================================
    // ==== POLICIES ===========================================
    // =========================================================
    'policies' => [
        // Titles / headers
        'categories_title'        => 'Policy Categories',
        'sections_title'          => 'Sections — :policy',

        // Columns / common fields
        'id'                      => 'ID',
        'internal_name'           => 'Internal name',
        'title_current_locale'    => 'Title',
        'validity_range'          => 'Validity range',
        'valid_from'              => 'Valid from',
        'valid_to'                => 'Valid to',
        'status'                  => 'Status',
        'sections'                => 'Sections',
        'actions'                 => 'Actions',
        'active'                  => 'Active',
        'inactive'                => 'Inactive',
        'slug'                    => 'URL',
        'slug_hint'               => 'optional',
        'slug_auto_hint'          => 'Will be automatically generated from the name if left blank',
        'slug_edit_hint'          => 'Changes the policy URL. Use only lowercase letters, numbers, and hyphens.',
        'updated'                  => 'Policy updated successfully.',


        // Category list: actions
        'new_category'            => 'New category',
        'view_sections'           => 'View sections',
        'edit'                    => 'Edit',
        'activate_category'       => 'Activate category',
        'deactivate_category'     => 'Deactivate category',
        'delete'                  => 'Delete',
        'delete_category_confirm' => 'Delete this category and ALL its sections?<br>This action cannot be undone.',
        'no_categories'           => 'No categories found.',
        'edit_category'           => 'Edit category',

        // Forms (category)
        'title_label'             => 'Title',
        'description_label'       => 'Description',
        'register'                => 'Create',
        'save_changes'            => 'Save changes',
        'close'                   => 'Close',

        // Sections
        'back_to_categories'      => 'Back to categories',
        'new_section'             => 'New section',
        'key'                     => 'Key',
        'order'                   => 'Order',
        'activate_section'        => 'Activate section',
        'deactivate_section'      => 'Deactivate section',
        'delete_section_confirm'  => 'Are you sure you want to delete this section?<br>This action cannot be undone.',
        'no_sections'             => 'No sections found.',
        'edit_section'            => 'Edit section',
        'internal_key_optional'   => 'Internal key (optional)',
        'content_label'           => 'Content',
        'section_content'         => 'Content',
        'base_content_hint'       => 'This is the main text of the policy. It will be automatically translated into other languages when created, but you can edit each translation afterwards.',

        // Public
        'page_title'              => 'Policies',
        'no_policies'             => 'No policies available at the moment.',
        'section'                 => 'Section',
        'cancellation_policy'     => 'Cancellation policy',
        'refund_policy'           => 'Refund policy',
        'no_cancellation_policy'  => 'No cancellation policy configured.',
        'no_refund_policy'        => 'No refund policy configured.',

        // Messages (categories)
        'category_created'        => 'Category created successfully.',
        'category_updated'        => 'Category updated successfully.',
        'category_activated'      => 'Category activated successfully.',
        'category_deactivated'    => 'Category deactivated successfully.',
        'category_deleted'        => 'Category deleted successfully.',

        // --- New keys (refactor / utilities) ---
        'untitled'                => 'Untitled',
        'no_content'              => 'No content available.',
        'display_name'            => 'Display name',
        'name'                    => 'Name',
        'name_base'               => 'Base name',
        'name_base_help'          => 'Short identifier/slug of the section (internal only).',
        'translation_content'     => 'Content',
        'locale'                  => 'Language',
        'save'                    => 'Save',
        'name_base_label'         => 'Base name',
        'translation_name'        => 'Translated name',
        'lang_autodetect_hint'    => 'You can write in any language; it will be detected automatically.',
        'bulk_edit_sections'      => 'Quick edit of sections',
        'bulk_edit_hint'          => 'Changes to all sections will be saved together with the category translation when you click "Save".',
        'no_changes_made'         => 'No changes made.',
        'no_sections_found'       => 'No sections found.',
        'editing_locale'          => 'Editing',

        // Messages (sections)
        'section_created'         => 'Section created successfully.',
        'section_updated'         => 'Section updated successfully.',
        'section_activated'       => 'Section activated successfully.',
        'section_deactivated'     => 'Section deactivated successfully.',
        'section_deleted'         => 'Section deleted successfully.',

        // Generic module messages
        'created_success'         => 'Created successfully.',
        'updated_success'         => 'Updated successfully.',
        'deleted_success'         => 'Deleted successfully.',
        'activated_success'       => 'Activated successfully.',
        'deactivated_success'     => 'Deactivated successfully.',
        'unexpected_error'        => 'An unexpected error occurred.',

        // Buttons / common SweetAlert texts
        'create'                  => 'Create',
        'activate'                => 'Activate',
        'deactivate'              => 'Deactivate',
        'cancel'                  => 'Cancel',
        'ok'                      => 'OK',
        'validation_errors'       => 'There are validation errors',
        'error_title'             => 'Error',

        // Section-specific confirmations
        'confirm_create_section'      => 'Create this section?',
        'confirm_edit_section'        => 'Save changes to this section?',
        'confirm_deactivate_section'  => 'Are you sure you want to deactivate this section?',
        'confirm_activate_section'    => 'Are you sure you want to activate this section?',
        'confirm_delete_section'      => 'Are you sure you want to delete this section?<br>This action cannot be undone.',
    ],

    // =========================================================
    // ==== TOURTYPES ==========================================
    // =========================================================
    'tourtypes' => [
        // Titles / headers
        'title'                   => 'Tour Types',
        'new'                     => 'Add Tour Type',

        // Columns / fields
        'id'                      => 'ID',
        'name'                    => 'Name',
        'description'             => 'Description',
        'duration'                => 'Duration',
        'status'                  => 'Status',
        'actions'                 => 'Actions',
        'active'                  => 'Active',
        'inactive'                => 'Inactive',

        // Buttons / actions
        'register'                => 'Save',
        'update'                  => 'Update',
        'save'                    => 'Save',
        'close'                   => 'Close',
        'cancel'                  => 'Cancel',
        'edit'                    => 'Edit',
        'delete'                  => 'Delete',
        'activate'                => 'Activate',
        'deactivate'              => 'Deactivate',

        // Modal titles
        'edit_title'              => 'Edit Tour Type',
        'create_title'            => 'Create Tour Type',

        // Placeholders / help
        'examples_placeholder'    => 'e.g., Adventure, Nature, Relax',
        'duration_placeholder'    => 'e.g., 4 hours, 8 hours',
        'suggested_duration_hint' => 'Suggested format: "4 hours", "8 hours".',
        'keep_default_hint'       => 'Leave "4 hours" if applicable; you can change it.',
        'optional'                => 'optional',

        // Confirmations
        'confirm_delete'          => 'Delete ":name"? This action cannot be undone.',
        'confirm_activate'        => 'Activate ":name"?',
        'confirm_deactivate'      => 'Deactivate ":name"?',

        // Messages (flash)
        'created_success'         => 'Tour type created successfully.',
        'updated_success'         => 'Tour type updated successfully.',
        'deleted_success'         => 'Tour type deleted successfully.',
        'activated_success'       => 'Tour type activated successfully.',
        'deactivated_success'     => 'Tour type deactivated successfully.',
        'in_use_error'            => 'Could not delete: this tour type is in use.',
        'unexpected_error'        => 'An unexpected error occurred. Please try again.',

        // Validation / generic
        'validation_errors'       => 'Please check the highlighted fields.',
        'error_title'             => 'Error',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Title / header
        'title'            => 'Frequently Asked Questions',

        // Fields / columns
        'question'         => 'Question',
        'answer'           => 'Answer',
        'status'           => 'Status',
        'actions'          => 'Actions',
        'active'           => 'Active',
        'inactive'         => 'Inactive',

        // Buttons / actions
        'new'              => 'New question',
        'create'           => 'Create',
        'save'             => 'Save',
        'edit'             => 'Edit',
        'delete'           => 'Delete',
        'activate'         => 'Activate',
        'deactivate'       => 'Deactivate',
        'cancel'           => 'Cancel',
        'close'            => 'Close',
        'ok'               => 'OK',

        // UI
        'read_more'        => 'Read more',
        'read_less'        => 'Read less',

        // Confirmations
        'confirm_create'   => 'Create this FAQ?',
        'confirm_edit'     => 'Save changes to this FAQ?',
        'confirm_delete'   => 'Are you sure you want to delete this FAQ?<br>This action cannot be undone.',
        'confirm_activate' => 'Are you sure you want to activate this FAQ?',
        'confirm_deactivate'=> 'Are you sure you want to deactivate this FAQ?',

        // Validation / errors
        'validation_errors'=> 'There are validation errors',
        'error_title'      => 'Error',

        // Messages (flash)
        'created_success'      => 'FAQ created successfully.',
        'updated_success'      => 'FAQ updated successfully.',
        'deleted_success'      => 'FAQ deleted successfully.',
        'activated_success'    => 'FAQ activated successfully.',
        'deactivated_success'  => 'FAQ deactivated successfully.',
        'unexpected_error'     => 'An unexpected error occurred.',
    ],

    // =========================================================
    // ==== TRANSLATIONS =======================================
    // =========================================================
    'translations' => [
        // Titles / general texts
        'title'                 => 'Translations Management',
        'index_title'           => 'Translations management',
        'select_entity_title'   => 'Select :entity to translate',
        'edit_title'            => 'Edit translation',
        'main_information'      => 'Main information',
        'ok'                    => 'OK',
        'save'                  => 'Save',
        'validation_errors'     => 'There are validation errors',
        'updated_success'       => 'Translation updated successfully.',
        'unexpected_error'      => 'Could not update the translation.',
                'editing'         => 'Editing',
        'policy_name'     => 'Policy title',
        'policy_content'  => 'Content',
        'policy_sections' => 'Policy sections',
        'section'         => 'Section',
        'section_name'    => 'Section name',
        'section_content' => 'Section content',

        // Language selector (screen and helpers)
        'choose_locale_title'   => 'Select language',
        'choose_locale_hint'    => 'Choose the language you want to translate this item into.',
        'select_language_title' => 'Select language',
        'select_language_intro' => 'Choose the language you want to translate this item into.',
        'languages' => [
            'es' => 'Spanish',
            'en' => 'English',
            'fr' => 'French',
            'pt' => 'Portuguese',
            'de' => 'German',
        ],

        // Listings / buttons
        'select'                => 'Select',
        'id_unavailable'        => 'ID unavailable',
        'no_items'              => 'No :entity available to translate.',

        // Common translation form fields
        'name'                  => 'Name',
        'description'           => 'Description',
        'content'               => 'Content',
        'overview'              => 'Overview',
        'itinerary'             => 'Itinerary',
        'itinerary_name'        => 'Itinerary name',
        'itinerary_description' => 'Itinerary description',
        'itinerary_items'       => 'Itinerary items',
        'item'                  => 'Item',
        'item_title'            => 'Item title',
        'item_description'      => 'Item description',
        'sections'              => 'Sections',
        'edit'                  => 'Edit',
        'close'                 => 'Close',
        'actions'               => 'Actions',

        // === Modular field labels =============================
        'fields' => [
            'name'                  => 'Name',
            'title'                 => 'Title',
            'overview'              => 'Overview',
            'description'           => 'Description',
            'content'               => 'Content',
            'duration'              => 'Duration',
            'question'              => 'Question',
            'answer'                => 'Answer',

            // Itinerary / items (tours partial)
            'itinerary'             => 'Itinerary',
            'itinerary_name'        => 'Itinerary name',
            'itinerary_description' => 'Itinerary description',
            'item'                  => 'Item',
            'item_title'            => 'Item title',
            'item_description'      => 'Item description',
        ],

        // === Overrides by ENTITY and FIELD (optional) =========
        'entity_fields' => [
            'tour_types' => [
                'duration' => 'Suggested duration',
                'name'     => 'Tour type name',
            ],
            'faqs' => [
                'question' => 'Question (customer-facing)',
                'answer'   => 'Answer (customer-facing)',
            ],
        ],

        // Entity names (plural)
        'entities' => [
            'tours'            => 'Tours',
            'itineraries'      => 'Itineraries',
            'itinerary_items'  => 'Itinerary items',
            'amenities'        => 'Amenities',
            'faqs'             => 'FAQs',
            'policies'         => 'Policies',
            'tour_types'       => 'Tour types',
        ],

        // Entity names (singular)
        'entities_singular' => [
            'tours'            => 'tour',
            'itineraries'      => 'itinerary',
            'itinerary_items'  => 'itinerary item',
            'amenities'        => 'amenity',
            'faqs'             => 'FAQ',
            'policies'         => 'policy',
            'tour_types'       => 'tour type',
        ],
    ],

    // =========================================================
    // ==== PROMOCODE ==========================================
    // =========================================================
    'promocode' => [
        'title'        => 'Promo Codes',
        'create_title' => 'Generate new promo code',
        'list_title'   => 'Existing promo codes',

        'success_title' => 'Success',
        'error_title'   => 'Error',

        'fields' => [
            'code'        => 'Code',
            'discount'    => 'Discount',
            'type'        => 'Type',
            'operation'   => 'Operation',
            'valid_from'  => 'Valid from',
            'valid_until' => 'Valid until',
            'usage_limit' => 'Usage limit',
            'promocode_hint'        => 'After applying, the coupon will be saved when submitting the form and the history snapshots will be updated.',
        ],

        'types' => [
            'percent' => '%',
            'amount'  => '$',
        ],

        'symbols' => [
            'percent'  => '%',
            'currency' => '$',
        ],

        'table' => [
            'code'         => 'Code',
            'discount'     => 'Discount',
            'operation'    => 'Operation',
            'validity'     => 'Validity',
            'date_status'  => 'Date status',
            'usage'        => 'Uses',
            'usage_status' => 'Usage status',
            'actions'      => 'Actions',
        ],

        'status' => [
            'used'      => 'Used',
            'available' => 'Available',
        ],

        'date_status' => [
            'scheduled' => 'Scheduled',
            'active'    => 'Active',
            'expired'   => 'Expired',
        ],

        'actions' => [
            'generate'         => 'Generate',
            'delete'           => 'Delete',
            'toggle_operation' => 'Switch Add/Subtract',
        ],

        'labels' => [
            'unlimited_placeholder' => 'Empty = unlimited',
            'unlimited_hint'        => 'Leave empty for unlimited uses. Set 1 for single-use.',
            'no_limit'              => '(no limit)',
            'remaining'             => 'remaining',
        ],

        'confirm_delete' => 'Are you sure you want to delete this code?',
        'empty'          => 'No promo codes available.',

        'messages' => [
            'created_success'        => 'Promo code created successfully.',
            'deleted_success'        => 'Promo code deleted successfully.',
            'percent_over_100'       => 'Percentage cannot be greater than 100.',
            'code_exists_normalized' => 'This code (ignoring spaces and case) already exists.',
            'invalid_or_used'        => 'Invalid or already used code.',
            'valid'                  => 'Valid code.',
            'server_error'           => 'Server error, please try again.',
            'operation_updated'      => 'Operation updated successfully.',
        ],

        'operations' => [
            'add'           => 'Add',
            'subtract'      => 'Subtract',
            'make_add'      => 'Switch to “Add”',
            'make_subtract' => 'Switch to “Subtract”',
            'surcharge'     => 'Surcharge',
            'discount'      => 'Discount',
        ],
    ],


    // =========================================================
    // ==== CUTOFF =============================================
    // =========================================================
    'cut-off' => [
        // Titles / headers
        'title'       => 'Cut-off Settings',
        'header'      => 'Booking Settings',
        'server_time' => 'Server time (:tz)',

        // Tabs
        'tabs' => [
            'global'   => 'Global (default)',
            'tour'     => 'Tour Lock',
            'schedule' => 'Schedule Lock',
            'summary'  => 'Summary',
            'help'     => 'Help',
        ],

        // Fields
        'fields' => [
            'cutoff_hour'       => 'Cut-off time (24h)',
            'cutoff_hour_short' => 'Cut-off (24h)',
            'lead_days'         => 'Lead days',
            'timezone'          => 'Time zone',
            'tour'              => 'Tour',
            'schedule'          => 'Schedule',
        ],

        // Selects / placeholders
        'selects' => [
            'tour' => '— Select a tour —',
            'time' => '— Select a time —',
        ],

        // Labels
        'labels' => [
            'status' => 'Status',
        ],

        // Badges / chips
        'badges' => [
            'inherits'            => 'Inherits Global',
            'override'            => 'Lock',
            'inherit_tour_global' => 'Inherits from Tour/Global',
            'schedule'            => 'Schedule',
            'tour'                => 'Tour',
            'global'              => 'Global',
        ],

        // Actions
        'actions' => [
            'save_global'   => 'Save global',
            'save_tour'     => 'Save tour lock',
            'save_schedule' => 'Save schedule lock',
            'clear'         => 'Clear lock',
            'confirm'       => 'Confirm',
            'cancel'        => 'Cancel',
            'actions'      => 'Actions',

        ],

        // Confirmations (modals)
        'confirm' => [
            'tour' => [
                'title' => 'Save tour lock?',
                'text'  => 'A specific lock will be applied for this tour. Leave empty to inherit.',
            ],
            'schedule' => [
                'title' => 'Save schedule lock?',
                'text'  => 'A specific lock will be applied for this schedule. Leave empty to inherit.',
            ],
        ],

        // Summary
        'summary' => [
            'tour_title'            => 'Tour Locks',
            'no_tour_overrides'     => 'No tour-level locks.',
            'schedule_title'        => 'Schedule Locks',
            'no_schedule_overrides' => 'No schedule-level locks.',
            'search_placeholder'    => 'Search tour or schedule…',
        ],

        // Flash / toasts
        'flash' => [
            'success_title' => 'Success',
            'error_title'   => 'Error',
        ],

        // Help
        'help' => [
            'title'      => 'How does it work?',
            'global'     => 'Default value for the entire site.',
            'tour'       => 'If a tour has cut-off/lead days, it takes precedence over Global.',
            'schedule'   => 'If a tour schedule has a lock, it takes precedence over the Tour.',
            'precedence' => 'Precedence',
        ],

        // Hints
        'hints' => [
            // Global
            'cutoff_example'    => 'e.g., :ex. After this time, “today” is no longer available.',
            'pattern_24h'       => '24h format HH:MM (e.g., 09:30, 18:00).',
            'cutoff_behavior'   => 'If the cut-off time has passed, the soonest available date moves to the next day.',
            'lead_days'         => 'Minimum days in advance (0 allows booking today if the cut-off has not passed).',
            'lead_days_detail'  => 'Allowed range: 0–30. 0 allows same-day booking if the cut-off has not passed.',
            'timezone_source'   => 'Taken from config(\'app.timezone\').',

            // Tour
            'pick_tour'             => 'Select a tour first; then define its lock (optional).',
            'tour_override_explain' => 'If you set only one (cut-off or days), the other inherits from Global.',
            'clear_button_hint'     => 'Use “Clear lock” to return to inheritance.',
            'leave_empty_inherit'   => 'Leave empty to inherit.',

            // Schedule
            'pick_schedule'             => 'Then select the tour’s schedule.',
            'schedule_override_explain' => 'Values here take precedence over the Tour. Leave empty to inherit.',
            'schedule_precedence_hint'  => 'Precedence: Schedule → Tour → Global.',

            // Summary
            'dash_means_inherit' => 'The “—” symbol indicates that the value is inherited.',
        ],
    ],

];
