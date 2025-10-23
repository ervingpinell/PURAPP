<?php

return [
    'title' => 'User Management',
    'add_user' => 'Add User',
    'no_role' => 'No role',
    'user_marked_verified' => 'User marked as verified successfully.',


    'filters' => [
        'role' => 'Filter by role:',
        'state' => 'Filter by status:',
        'email' => 'Filter by email:',
        'email_placeholder' => 'example@domain.com',
        'all' => '-- All --',
        'search' => 'Search',
        'clear' => 'Clear',
    ],

    'table' => [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'phone' => 'Phone',
        'status' => 'Status',
        'verified' => 'Verified',
        'locked' => 'Locked',
        'actions' => 'Actions',
    ],

    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'verified' => [
        'yes' => 'Yes',
        'no'  => 'No',
    ],

    'locked' => [
        'yes' => 'Yes',
        'no'  => 'No',
    ],

    'actions' => [
        'edit' => 'Edit',
        'deactivate' => 'Deactivate',
        'reactivate' => 'Reactivate',
        'lock' => 'Lock',
        'unlock' => 'Unlock',
        'mark_verified' => 'Mark verified',
    ],

    'dialog' => [
        'title' => 'Confirmation',
        'cancel' => 'Cancel',
        'confirm_lock' => 'Lock this user?',
        'confirm_unlock' => 'Unlock this user?',
        'confirm_deactivate' => 'Deactivate this user?',
        'confirm_reactivate' => 'Reactivate this user?',
        'confirm_mark_verified' => 'Mark as verified?',
        'action_lock' => 'Yes, lock',
        'action_unlock' => 'Yes, unlock',
        'action_deactivate' => 'Yes, deactivate',
        'action_reactivate' => 'Yes, reactivate',
        'action_mark_verified' => 'Yes, mark',
    ],

    'modals' => [
        'register_user' => 'Register User',
        'edit_user' => 'Edit User',
        'save' => 'Save',
        'update' => 'Update',
        'cancel' => 'Cancel',
        'close' => 'Close',
    ],

    'form' => [
        'full_name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'country_code' => 'Country code',
        'phone_number' => 'Phone number',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
        'toggle_password' => 'Show/Hide password',
    ],

    'password_reqs' => [
        'length'  => 'At least 8 characters',
        'special' => '1 special character (.,!@#$%^&*()_+-)',
        'number'  => '1 number',
        'match'   => 'Passwords match',
    ],

    'alert' => [
        'success' => 'Success',
        'error'   => 'Error',
    ],
];
