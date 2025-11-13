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

        // New for index/list
        'list_title'        => 'Category List',
        'empty_list'        => 'There are no categories registered.',
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
        'age_from' => 'Age from',
        'age_to'   => 'Age to',
        'range'    => 'Range',
        'active'   => 'Active',
        'actions'  => 'Actions',

        // New used in index
        'order'    => 'Order',
        'slug'     => 'Slug',
    ],

    'form' => [
        'translations' => [
            'title'          => 'Name translations',
            'auto_translate' => 'Automatically translate the other languages (DeepL)',
            'regen_missing'  => 'Automatically fill in the empty ones (DeepL)',
            'at_least_first' => 'You must complete at least the first language.',
            'locale_hint'    => 'Translation for locale :loc.',
        ],
        'name' => [
            'label'       => 'Name',
            'placeholder' => 'Ex: Adult, Child, Infant',
            'required'    => 'Name is required',
        ],
        'slug' => [
            'label'       => 'Slug (unique identifier)',
            'placeholder' => 'Ex: adult, child, infant',
            'title'       => 'Lowercase letters, numbers, hyphens and underscores only',
            'helper'      => 'Only lowercase letters, numbers, hyphens (-) and underscores (_)',
        ],
        'age_from' => [
            'label'       => 'Age from',
            'placeholder' => 'Ex: 0, 3, 13, 65',
        ],
        'age_to' => [
            'label'         => 'Age to',
            'placeholder'   => 'Ex: 2, 12, 64 (leave empty for “no limit”)',
            'hint_no_limit' => 'leave empty for “no limit”',
        ],
        'order' => [
            'label'  => 'Display order',
            'helper' => 'Determines the order in which categories appear (lower = first)',
        ],
        'active' => [
            'label'  => 'Active category',
            'helper' => 'Only active categories are shown in booking forms',
        ],
        'min_per_booking' => [
            'label'       => 'Minimum per booking',
            'placeholder' => 'Ex: 0, 1',
        ],
        'max_per_booking' => [
            'label'       => 'Maximum per booking',
            'placeholder' => 'Ex: 10 (leave empty for “no limit”)',
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
        'warning_title'   => 'Warning',
        'warning_text'    => 'Deleting a category that is in use in tours or bookings may cause issues. It is recommended to deactivate it instead of deleting it.',
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
        'no_upper_limit_hint'   => 'Leave “Age to” empty to indicate “no upper limit”.',
        'slug_unique'           => 'The slug must be unique.',
        'order_affects_display' => 'The order determines how they are displayed in the system.',
    ],

    'help' => [
        'title'           => 'Help',
        'examples_title'  => 'Category Examples',
        'infant'          => 'Infant',
        'child'           => 'Child',
        'adult'           => 'Adult',
        'senior'          => 'Senior',
        'age_from_tip'    => 'Age from:',
        'age_to_tip'      => 'Age to:',
        'range_tip'       => 'Range:',
        'notes_title'     => 'Notes',
        'notes' => [
            'use_null_age_to' => 'Use age_to = NULL to indicate “no upper limit” (e.g. 18+ years).',
            'inactive_hidden' => 'Inactive categories are not shown in booking forms.',
        ],
    ],

    'info' => [
        'id'       => 'ID:',
        'created'  => 'Created:',
        'updated'  => 'Updated:',
        'date_fmt' => 'd/m/Y H:i',
    ],

    'validation' => [
        'age_to_gte_age_from' => 'Age to must be greater than or equal to age from.',
    ],
];
