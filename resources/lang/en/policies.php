<?php
return [
    // Titles / headings
    'categories_title'         => 'Policy Categories',
    'sections_title'           => 'Sections — :policy',

    // Common columns / fields
    'id'                       => 'ID',
    'internal_name'            => 'Internal name',
    'title_current_locale'     => 'Title (' . strtoupper(app()->getLocale()) . ')',
    'validity_range'           => 'Validity range',
    'valid_from'               => 'Valid from',
    'valid_to'                 => 'Valid to',
    'status'                   => 'Status',
    'sections'                 => 'Sections',
    'actions'                  => 'Actions',
    'active'                   => 'Active',
    'inactive'                 => 'Inactive',

    // Category list actions
    'new_category'             => 'New category',
    'view_sections'            => 'View sections',
    'edit'                     => 'Edit',
    'activate_category'        => 'Activate category',
    'deactivate_category'      => 'Deactivate category',
    'delete'                   => 'Delete',
    'delete_category_confirm'  => 'Delete this category and ALL its sections?',
    'no_categories'            => 'No categories found.',
    'edit_category'            => 'Edit category',

    // Forms (category)
    'title_label'              => 'Title',
    'description_label'        => 'Description',
    'register'                 => 'Create',
    'save_changes'             => 'Save changes',
    'close'                    => 'Close',

    // Sections list / actions
    'back_to_categories'       => 'Back to categories',
    'new_section'              => 'New section',
    'key'                      => 'Key',
    'order'                    => 'Order',
    'activate_section'         => 'Activate section',
    'deactivate_section'       => 'Deactivate section',
    'delete_section_confirm'   => 'Delete this section?',
    'no_sections'              => 'No sections found.',
    'edit_section'             => 'Edit section',
    'internal_key_optional'    => 'Internal key (optional)',
    'content_label'            => 'Content',

    'page_title'  => 'Policies',
    'no_policies' => 'No policies available at the moment.',
    'section'     => 'Section',
    'cancellation_policy'    => 'Cancellation Policy',
    'refund_policy'          => 'Refund Policy',
    'no_cancellation_policy' => 'No cancellation policy configured.',
    'no_refund_policy'       => 'No refund policy configured.',

        'category_created'     => 'Category created successfully.',
    'category_updated'     => 'Category updated successfully.',
    'category_activated'   => 'Category activated.',
    'category_deactivated' => 'Category deactivated.',
    'category_deleted'     => 'Category deleted.',
];
