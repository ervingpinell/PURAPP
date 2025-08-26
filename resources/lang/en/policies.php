<?php

return [
    // Titles / headers
    'categories_title'        => 'Policy Categories',
    'sections_title'          => 'Sections — :policy',

    // Columns / common fields
    'id'                      => 'ID',
    'internal_name'           => 'Internal name',
    'title_current_locale'    => 'Title (current locale)',
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
    'delete_category_confirm' => 'Delete this category and ALL its sections?',
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
    'delete_section_confirm'  => 'Delete this section?',
    'no_sections'             => 'No sections found.',
    'edit_section'            => 'Edit section',
    'internal_key_optional'   => 'Internal key (optional)',
    'content_label'           => 'Content',

    // Public
    'page_title'              => 'Policies',
    'no_policies'             => 'There are no policies available at the moment.',
    'section'                 => 'Section',
    'cancellation_policy'     => 'Cancellation Policy',
    'refund_policy'           => 'Refund Policy',
    'no_cancellation_policy'  => 'No cancellation policy configured.',
    'no_refund_policy'        => 'No refund policy configured.',

    // Messages (categories)
    'category_created'        => 'Category created successfully.',
    'category_updated'        => 'Category updated successfully.',
    'category_activated'      => 'Category activated successfully.',
    'category_deactivated'    => 'Category deactivated successfully.',
    'category_deleted'        => 'Category deleted successfully.',

    // --- NEW KEYS (refactor / module utilities) ---
    'untitled'                => 'Untitled',
    'no_content'              => 'No content available.',
    'display_name'            => 'Display name',
    'name'                    => 'Name',
    'name_base'               => 'Base name',
    'name_base_help'          => 'Short identifier/slug for the section (internal only).',
    'translation_content'     => 'Translated content',
    'locale'                  => 'Language',
    'save'                    => 'Save',
    'name_base_label'         => 'Base name',
    'translation_name'        => 'Translated name',
    'lang_autodetect_hint'    => 'You can type in any language; it is detected automatically.',
    'bulk_edit_sections'      => 'Quick edit of sections',
    'bulk_edit_hint'          => 'Changes to all sections will be saved together with the category translation when you click “Save”.',
    'no_changes_made'         => 'No changes made.',
    'no_sections_found'       => 'No sections found.',

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
    'confirm_delete_section'      => 'Are you sure you want to delete this section?',
    'confirm_deactivate_section'  => 'Are you sure you want to deactivate this section?',
    'confirm_activate_section'    => 'Are you sure you want to activate this section?',
];
