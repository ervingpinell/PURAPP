<?php

return [

    'hotels' => [

        'title'             => 'Hotel List',
        'header'            => 'Registered Hotels',
        'sort_alpha'        => 'Sort alphabetically',

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
        'name_placeholder'  => 'Ex.: Arenal Springs Hotel',

        'confirm_activate_title'    => 'Activate hotel?',
        'confirm_activate_text'     => 'Are you sure you want to activate ":name"?',
        'confirm_deactivate_title'  => 'Deactivate hotel?',
        'confirm_deactivate_text'   => 'Are you sure you want to deactivate ":name"?',
        'confirm_delete_title'      => 'Delete permanently?',
        'confirm_delete_text'       => '":name" will be deleted. This action cannot be undone.',

        'created_success'    => 'Hotel created successfully.',
        'updated_success'    => 'Hotel updated successfully.',
        'deleted_success'    => 'Hotel deleted successfully.',
        'activated_success'  => 'Hotel activated successfully.',
        'deactivated_success'=> 'Hotel deactivated successfully.',
        'sorted_success'     => 'Hotels sorted alphabetically.',
        'unexpected_error'   => 'An unexpected error occurred. Please try again.',

        'validation' => [
            'name_required' => 'The name is required.',
            'name_unique'   => 'That hotel already exists in the list.',
            'name_max'      => 'The name cannot exceed 255 characters.',
        ],
        'error_title' => 'Error',

        'edit_title' => 'Edit hotel',
    ],

    'meeting_point' => [

        'ui' => [
            'page_title'   => 'Meeting Points',
            'page_heading' => 'Meeting Points',
        ],

        'badges' => [
            'count_badge' => ':count records',
            'active'      => 'Active',
            'inactive'    => 'Inactive',
        ],

        'create' => [
            'title' => 'Add meeting point',
        ],

        'list' => [
            'title' => 'List',
            'empty' => 'No records found. Create the first one above.',
        ],

        'labels' => [
            'time'       => 'Time',
            'sort_order' => 'Order',
        ],

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

            'name_base'               => 'Name (base)',
            'description_base'        => 'Description (base)',
            'locale'                  => 'Locale',
            'name_translation'        => 'Name (translation)',
            'description_translation' => 'Description (translation)',
        ],

        'placeholders' => [
            'name'        => 'La Fortuna Central Park',
            'pickup_time' => '7:10 AM',
            'description' => 'Downtown La Fortuna',
            'map_url'     => 'https://maps.google.com/...',
            'search'      => 'Search…',
            'optional'    => 'Optional',
        ],

        'hints' => [
            'name_example'   => 'Ex: "La Fortuna Central Park".',
            'name_base_sync' => 'If unchanged, it will be kept. Language-specific names are edited below.',
            'fallback_sync'  => 'If you choose the <strong>:fallback</strong> locale, it will also sync with the base fields.',
        ],

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
        ],

        'actions' => [
            'view_map'    => 'View map',
            'view_on_map' => 'View on map',
            'edit'        => 'Edit',
            'delete'      => 'Delete',
            'activate'    => 'Activate',
            'deactivate'  => 'Deactivate',
        ],

        'confirm' => [
            'create_title'             => 'Create new meeting point?',
            'create_text_with_name'    => '":name" will be created.',
            'create_text'              => 'A new meeting point will be created.',
            'save_title'               => 'Save changes?',
            'save_text'                => 'The meeting point and its translation will be updated.',
            'deactivate_title'         => 'Deactivate meeting point?',
            'deactivate_title_short'   => 'Deactivate?',
            'deactivate_text'          => '":name" will be inactive.',
            'activate_title'           => 'Activate meeting point?',
            'activate_title_short'     => 'Activate?',
            'activate_text'            => '":name" will be active.',
            'delete_title'             => 'Delete meeting point?',
            'delete_title_short'       => 'Delete?',
            'delete_text'              => '":name" will be permanently deleted. This action cannot be undone.',
        ],

        'validation' => [
            'title'                         => 'Validation errors',
            'missing_translated_name_title' => 'Missing translated name',
            'missing_translated_name_text'  => 'Please complete the translated name field.',
        ],

        'toasts' => [
            'success_title' => 'Success',
            'error_title'   => 'Error',
        ],
    ],

];
