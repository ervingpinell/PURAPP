<?php

return [

    'hotels' => [

        // Title / headers
        'title'             => 'Hotel List',
        'header'            => 'Registered Hotels',
        'sort_alpha'        => 'Sort alphabetically',

        // Fields / columns / actions
        'name'              => 'Name',
        'status'            => 'Status',
        'actions'           => 'Actions',
        'active'            => 'Active',
        'inactive'          => 'Inactive',
        'add'               => 'Add',
        'edit'              => 'Edit',
        'delete'            => 'Delete',
        'activate'          => 'Activate',
        'deactivate'        => 'Deactivate',
        'save_changes'      => 'Save changes',
        'cancel'            => 'Cancel',
        'close'             => 'Close',
        'no_records'        => 'No hotels registered.',
        'name_placeholder'  => 'E.g.: Hotel Arenal Springs',

        // Confirmations
        'confirm_activate_title'    => 'Activate hotel?',
        'confirm_activate_text'     => 'Are you sure you want to activate ":name"?',
        'confirm_deactivate_title'  => 'Deactivate hotel?',
        'confirm_deactivate_text'   => 'Are you sure you want to deactivate ":name"?',
        'confirm_delete_title'      => 'Delete permanently?',
        'confirm_delete_text'       => '":name" will be deleted. This action cannot be undone.',

        // Messages (flash)
        'created_success'    => 'Hotel created successfully.',
        'updated_success'    => 'Hotel updated successfully.',
        'deleted_success'    => 'Hotel deleted successfully.',
        'activated_success'  => 'Hotel activated successfully.',
        'deactivated_success' => 'Hotel deactivated successfully.',
        'sorted_success'     => 'Hotels sorted alphabetically.',
        'unexpected_error'   => 'An unexpected error occurred. Please try again.',

        // Validation / generic
        'validation' => [
            'name_required' => 'The name is required.',
            'name_unique'   => 'That hotel already exists in the list.',
            'name_max'      => 'The name may not be greater than 255 characters.',
        ],
        'error_title' => 'Error',

        // Modals
        'edit_title' => 'Edit hotel',
    ],

    'meeting_point' => [

        // UI
        'ui' => [
            'page_title'   => 'Meeting Points',
            'page_heading' => 'Meeting Points',
        ],

        // Badges
        'badges' => [
            'count_badge' => ':count records',
            'active'      => 'Active',
            'inactive'    => 'Inactive',
        ],

        // Create
        'create' => [
            'title' => 'Add meeting point',
        ],

        // List
        'list' => [
            'title' => 'Listing',
            'empty' => 'There are no records. Create the first one above.',
        ],

        // Compact labels on cards
        'labels' => [
            'time'       => 'Time',
            'sort_order' => 'Order',
        ],

        // Fields
        'fields' => [
            'name'                    => 'Name',
            'pickup_time'             => 'Pickup time',
            'sort_order'              => 'Order',
            'description'             => 'Description',
            'map_url'                 => 'Map URL',
            'active'                  => 'Active',
            'time_short'              => 'Time',
            'map'                     => 'Map',
            'status'                  => 'Status',
            'actions'                 => 'Actions',
            'instructions'            => 'Instructions',

            // Editing / translations
            'name_base'               => 'Name (base)',
            'description_base'        => 'Description (base)',
            'instructions_base'       => 'Instructions (base)',
            'locale'                  => 'Language',
            'name_translation'        => 'Name (translation)',
            'description_translation' => 'Description (translation)',
            'instructions_translation' => 'Instructions (translation)',
        ],

        // Placeholders
        'placeholders' => [
            'name'         => 'La Fortuna Central Park',
            'pickup_time'  => '7:10 AM',
            'description'  => 'La Fortuna downtown',
            'instructions' => 'Please meet us in front of the church at the central park. Our guide will be wearing a green shirt with the :company logo.',
            'map_url'      => 'https://maps.google.com/...',
            'search'       => 'Search…',
            'optional'     => 'Optional',
        ],

        // Hints
        'hints' => [
            'name_example'   => 'E.g.: "La Fortuna Central Park".',
            'name_base_sync' => 'If you do not change it, it stays the same. The language-specific name is edited below.',
            'fallback_sync'  => 'If you choose the <strong>:fallback</strong> locale, it will also sync with the base fields.',
        ],

        // Buttons
        'buttons' => [
            'reload'       => 'Reload',
            'save'         => 'Save',
            'clear'        => 'Clear',
            'create'       => 'Create',
            'cancel'       => 'Cancel',
            'save_changes' => 'Save changes',
            'close'        => 'Close',
            'ok'           => 'OK',
            'confirm'      => 'Yes, continue',
            'delete'       => 'Delete',
            'activate'     => 'Activate',
            'deactivate'   => 'Deactivate',
            'back'         => 'Back',
        ],

        // Actions (titles / tooltips)
        'actions' => [
            'view_map'    => 'View map',
            'view_on_map' => 'View on map',
            'title'       => 'Edit meeting point',
            'edit'        => 'Edit',
            'delete'      => 'Delete',
            'activate'    => 'Activate',
            'deactivate'  => 'Deactivate',
        ],

        // Confirm
        'confirm' => [
            'create_title'             => 'Create new meeting point?',
            'create_text_with_name'    => '":name" will be created.',
            'create_text'              => 'A new meeting point will be created.',

            'save_title'               => 'Save changes?',
            'save_text'                => 'The meeting point and the selected translation will be updated.',

            'deactivate_title'         => 'Deactivate meeting point?',
            'deactivate_title_short'   => 'Deactivate?',
            'deactivate_text'          => '":name" will be set to inactive.',

            'activate_title'           => 'Activate meeting point?',
            'activate_title_short'     => 'Activate?',
            'activate_text'            => '":name" will be set to active.',

            'delete_title'             => 'Delete meeting point?',
            'delete_title_short'       => 'Delete?',
            'delete_text'              => '« :name » will be permanently deleted. This action cannot be undone.',

            'restore_title'            => 'Restore meeting point?',
            'restore_text'             => 'The point will return to the active list.',

            'force_delete_title'       => 'Delete permanently?',
            'force_delete_text'        => 'This action cannot be undone.',
        ],

        // Validation / Toastr / SweetAlert
        'validation' => [
            'title'                         => 'Validation errors',
            'missing_translated_name_title' => 'Translated name missing',
            'missing_translated_name_text'  => 'Please fill in the translated name field.',
        ],

        'toasts' => [
            'success_title'        => 'Success',
            'error_title'          => 'Error',
            'created_success'      => 'Meeting point created successfully.',
            'updated_success'      => 'Meeting point updated successfully.',
            'deleted_success'      => 'Meeting point moved to trash.',
            'activated_success'    => 'Meeting point activated successfully.',
            'deactivated_success'  => 'Meeting point deactivated successfully.',
        ],

        // Trash management
        'trash' => [
            'title'                => 'Trash',
            'empty'                => 'No deleted meeting points.',
            'deleted_by'           => 'Deleted by',
            'deleted_at'           => 'Deleted on',
            'auto_delete_in'       => 'Will be deleted in',
            'days'                 => '{1} :count day|[2,*] :count days',
            'restore_success'      => 'Meeting point restored successfully.',
            'force_delete_success' => 'Meeting point permanently deleted.',
        ],
    ],

];
