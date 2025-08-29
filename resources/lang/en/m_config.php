<?php
/*************************************************************
 *  CONFIG MODULE – TRANSLATIONS (EN)
 *  File: resources/lang/en/m_config.php
 *
 *  Index (searchable anchors)
 *  [01] POLICIES LINE 16
 *  [02] TOURTYPES LINE 134
 *  [03] FAQ LINE 193
 *  [04] TRANSLATIONS LINE 244
 *  [05] PROMOCODE LINE 354
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

        // Public
        'page_title'              => 'Policies',
        'no_policies'             => 'There are no policies available at the moment.',
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

        // --- NEW KEYS (refactor / utilities) ---
        'untitled'                => 'Untitled',
        'no_content'              => 'No content available.',
        'display_name'            => 'Display name',
        'name'                    => 'Name',
        'name_base'               => 'Base name',
        'name_base_help'          => 'Short identifier/slug for the section (internal only).',
        'translation_content'     => 'Content',
        'locale'                  => 'Language',
        'save'                    => 'Save',
        'name_base_label'         => 'Base name',
        'translation_name'        => 'Translated name',
        'lang_autodetect_hint'    => 'You can write in any language; it will be detected automatically.',
        'bulk_edit_sections'      => 'Bulk edit sections',
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

        // Buttons / common texts (SweetAlert)
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

        // Placeholders / hints
        'examples_placeholder'    => 'E.g.: Adventure, Nature, Relax',
        'duration_placeholder'    => 'E.g.: 4 hours, 8 hours',
        'suggested_duration_hint' => 'Suggested format: "4 hours", "8 hours".',
        'keep_default_hint'       => 'Leave "4 hours" if it applies; you can change it.',
        'optional'                => 'optional',

        // Confirmations
        'confirm_delete'          => 'Are you sure you want to delete ":name"? This action cannot be undone.',
        'confirm_activate'        => 'Are you sure you want to activate ":name"?',
        'confirm_deactivate'      => 'Are you sure you want to deactivate ":name"?',

        // Messages (flash)
        'created_success'         => 'Tour type created successfully.',
        'updated_success'         => 'Tour type updated successfully.',
        'deleted_success'         => 'Tour type deleted successfully.',
        'activated_success'       => 'Tour type activated successfully.',
        'deactivated_success'     => 'Tour type deactivated successfully.',
        'in_use_error'            => 'Cannot delete: this tour type is in use.',
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
        'title'                 => 'Translation Management',
        'index_title'           => 'Translation management',
        'select_entity_title'   => 'Select :entity to translate',
        'edit_title'            => 'Edit translation',
        'main_information'      => 'Main information',
        'ok'                    => 'OK',
        'save'                  => 'Save',
        'validation_errors'     => 'There are validation errors',
        'updated_success'       => 'Translation updated successfully.',
        'unexpected_error'      => 'The translation could not be updated.',

        // Language selector (screen and helpers)
        'choose_locale_title'   => 'Choose language',
        'choose_locale_hint'    => 'Select the language you want to translate this item into.',
        'select_language_title' => 'Choose language',
        'select_language_intro' => 'Select the language you want to translate this item into.',
        'languages' => [
            'es' => 'Spanish',
            'en' => 'English',
            'fr' => 'French',
            'pt' => 'Portuguese',
            'de' => 'German',
        ],

        // Listings / buttons
        'select'                => 'Select',
        'id_unavailable'        => 'ID not available',
        'no_items'              => 'No :entity available to translate.',

        // Common form fields (direct compatibility with existing blades)
        'name'                  => 'Name',
        'description'           => 'Description',
        'content'               => 'Content',
        'overview'              => 'Overview',
        'itinerary'             => 'Itinerary',
        'itinerary_name'        => 'Itinerary Name',
        'itinerary_description' => 'Itinerary Description',
        'itinerary_items'       => 'Itinerary Items',
        'item'                  => 'Item',
        'item_title'            => 'Item Title',
        'item_description'      => 'Item Description',
        'sections'              => 'Sections',
        'edit'                  => 'Edit',
        'close'                 => 'Close',
        'actions'               => 'Actions',

        // === Modular field labels =============================
        // Use as: __('m_config.translations.fields.<field>')
        'fields' => [
            // Generic
            'name'                  => 'Name',
            'title'                 => 'Title',
            'overview'              => 'Overview',
            'description'           => 'Description',
            'content'               => 'Content',
            'duration'              => 'Duration',
            'question'              => 'Question',
            'answer'                => 'Answer',

            // Itinerary / items (tour partial)
            'itinerary'             => 'Itinerary',
            'itinerary_name'        => 'Itinerary name',
            'itinerary_description' => 'Itinerary description',
            'item'                  => 'Item',
            'item_title'            => 'Item title',
            'item_description'      => 'Item description',
        ],

        // === Entity-based field overrides (optional) ==========
        // In the blade: try entity_fields.<type>.<field> first,
        // if not present, use fields.<field>.
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
        'title'         => 'Promotional Codes',
        'create_title'  => 'Generate new promo code',
        'list_title'    => 'Existing promo codes',

        'success_title' => 'Success',
        'error_title'   => 'Error',

        'fields' => [
            'code'     => 'Code',
            'discount' => 'Discount',
            'type'     => 'Type',
        ],

        'types' => [
            'percent'  => '%',
            'amount'   => '$',
        ],

        'symbols' => [
            'percent'  => '%',
            'currency' => '$',
        ],

        'table' => [
            'code'     => 'Code',
            'discount' => 'Discount',
            'status'   => 'Status',
            'actions'  => 'Actions',
        ],

        'status' => [
            'used'       => 'Used',
            'available'  => 'Available',
        ],

        'actions' => [
            'generate' => 'Generate',
            'delete'   => 'Delete',
        ],

        'confirm_delete' => 'Are you sure you want to delete this code?',
        'empty'          => 'There are no promo codes available.',

        'messages' => [
    'created_success'       => 'Promo code created successfully.',
    'deleted_success'       => 'Promo code deleted successfully.',
    'percent_over_100'      => 'The percentage cannot be greater than 100.',
    'code_exists_normalized'=> 'This code (ignoring spaces and capitalization) already exists.',
    'invalid_or_used'       => 'Invalid or already used code.',
    'valid'                 => 'Valid code.',
    'server_error'          => 'Server error, please try again.',
],
    ],

];
