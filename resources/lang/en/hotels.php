<?php

return [
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
    'name_placeholder'  => 'E.g.: Arenal Springs Hotel',

    'confirm_activate_title'    => 'Activate hotel?',
    'confirm_activate_text'     => 'Are you sure you want to activate ":name"?',
    'confirm_deactivate_title'  => 'Deactivate hotel?',
    'confirm_deactivate_text'   => 'Are you sure you want to deactivate ":name"?',
    'confirm_delete_title'      => 'Delete permanently?',
    'confirm_delete_text'       => '":name" will be deleted. This action cannot be undone.',

    'created_success'   => 'Hotel created successfully.',
    'updated_success'   => 'Hotel updated successfully.',
    'deleted_success'   => 'Hotel deleted successfully.',
    'activated_success' => 'Hotel activated successfully.',
    'deactivated_success'=> 'Hotel deactivated successfully.',
    'sorted_success'    => 'Hotels sorted alphabetically.',
    'unexpected_error'  => 'An unexpected error occurred. Please try again.',

    'validation_errors' => 'Please review the highlighted fields.',
    'validation' => [
    'name_required' => 'The name is required.',
    'name_unique'   => 'This hotel already exists in the list.',
    'name_max'      => 'The name cannot exceed 255 characters.',
],

    'error_title'       => 'Error',

    'edit_title'        => 'Edit hotel',
];
