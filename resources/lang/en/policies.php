<?php

return [

        'page_title' => 'Policies',
        'no_policies' => 'No policies available at the moment.',
        'no_sections' => 'No sections available at the moment.',
    // =========================================================
    // [01] FIELDS
    // =========================================================
    'fields' => [
        'title'       => 'Title',
        'description' => 'Description',
        'type'        => 'Type',
        'is_active'   => 'Active',
    ],

    // =========================================================
    // [02] TYPES
    // =========================================================
    'types' => [
        'cancellation' => 'Cancellation Policy',
        'refund'       => 'Refund Policy',
        'terms'        => 'Terms and Conditions',
    ],

    // =========================================================
    // [03] MESSAGES
    // =========================================================
    'success' => [
        'created'   => 'Policy created successfully.',
        'updated'   => 'Policy updated successfully.',
        'deleted'   => 'Policy deleted successfully.',
    ],

    'error' => [
        'create' => 'Could not create the policy.',
        'update' => 'Could not update the policy.',
        'delete' => 'Could not delete the policy.',
    ],
];
