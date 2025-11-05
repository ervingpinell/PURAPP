<?php

return [
    'ui' => [
        'page_title_index'  => 'Customer Categories',
        'page_title_create' => 'New Customer Category',
        'page_title_edit'   => 'Edit Category',
        'header_index'      => 'Customer Categories',
        'header_create'     => 'New Customer Category',
        'header_edit'       => 'Edit Category: :name',
        'info_card_title'   => 'Information',
        'list_title'        => 'Category List',
        'empty_list'        => 'No categories registered.',
    ],

    'buttons' => [
        'new_category' => 'New Category',
        'save'         => 'Save',
        'update'       => 'Update',
        'cancel'       => 'Cancel',
        'back'         => 'Back',
        'delete'       => 'Delete',
        'edit'         => 'Edit',
    ],

    'table' => [
        'name'     => 'Name',
        'age_from' => 'Age From',
        'age_to'   => 'Age To',
        'range'    => 'Range',
        'active'   => 'Active',
        'actions'  => 'Actions',
        'order'    => 'Order',
        'slug'     => 'Slug',
    ],

    'form' => [
        'name' => [
            'label'       => 'Name',
            'placeholder' => 'E.g.: Adult, Child, Infant',
            'required'    => 'The name is required',
        ],
        'slug' => [
            'label'       => 'Slug (unique identifier)',
            'placeholder' => 'E.g.: adult, child, infant',
            'title'       => 'Only lowercase letters, numbers, hyphens, and underscores',
            'helper'      => 'Only lowercase letters, numbers, hyphens (-) and underscores (_)',
        ],
        'age_from' => [
            'label'       => 'Age From',
            'placeholder' => 'E.g.: 0, 3, 13, 65',
        ],
        'age_to' => [
            'label'         => 'Age To',
            'placeholder'   => 'E.g.: 2, 12, 64 (leave empty for “no limit”)',
            'hint_no_limit' => 'leave empty for “no limit”',
        ],
        'order' => [
            'label'  => 'Display Order',
            'helper' => 'Determines the order in which categories appear (lower = first)',
        ],
        'active' => [
            'label'  => 'Active Category',
            'helper' => 'Only active categories appear in booking forms',
        ],
        'min_per_booking' => [
            'label'       => 'Minimum per Booking',
            'placeholder' => 'E.g.: 0, 1',
        ],
        'max_per_booking' => [
            'label'       => 'Maximum per Booking',
            'placeholder' => 'E.g.: 10 (leave empty for “no limit”)',
        ],
    ],

    'states' => [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ],

    'alerts' => [
        'success_created' => 'Category created successfully.',
        'success_updated' => 'Category updated successfully.',
        'success_deleted' => 'Category deleted successfully.',
        'warning_title'  => 'Warning',
        'warning_text'   => 'Deleting a category used in tours or bookings may cause issues. It is recommended to deactivate it instead of deleting it.',
    ],

    'dialogs' => [
        'delete' => [
            'title'   => 'Confirm Deletion',
            'text'    => 'Are you sure you want to delete the category :name?',
            'caution' => 'This action cannot be undone.',
        ],
    ],

    'rules' => [
        'title'                 => 'Important Rules',
        'no_overlap'            => 'Age ranges cannot overlap between active categories.',
        'no_upper_limit_hint'   => 'Leave “Age To” empty to indicate “no upper limit”.',
        'slug_unique'           => 'The slug must be unique.',
        'order_affects_display' => 'Order determines how they are displayed in the system.',
    ],

    'help' => [
        'title'           => 'Help',
        'examples_title'  => 'Category Examples',
        'infant'          => 'Infant',
        'child'           => 'Child',
        'adult'           => 'Adult',
        'senior'          => 'Senior',
        'age_from_tip'    => 'Age From:',
        'age_to_tip'      => 'Age To:',
        'range_tip'       => 'Range:',
        'notes_title'     => 'Notes',
        'notes' => [
            'use_null_age_to' => 'Use age_to = NULL to indicate "no upper limit" (e.g.: 18+ years).',
            'inactive_hidden' => 'Inactive categories are not shown in booking forms.',
        ],
    ],

    'info' => [
        'id'        => 'ID:',
        'created'   => 'Created:',
        'updated'   => 'Updated:',
        'date_fmt'  => 'Y-m-d H:i',
    ],

    'validation' => [
        'age_to_gte_age_from' => '“Age To” must be greater than or equal to “Age From”.',
    ],
];
