<?php

return [

    // =========================================================
    // [00] GENERIC
    // =========================================================
    'page_title'  => 'Policies',
    'no_policies' => 'There are no policies available at the moment.',
    'no_sections' => 'There are no sections available at the moment.',
    'propagate_to_all_langs' => 'Propagate this change to all languages (EN, FR, DE, PT)',
    'propagate_hint'         => 'It will be automatically translated from the current text and any existing translations in those languages will be overwritten.',
    'update_base_es'         => 'Also update base (ES)',
    'update_base_hint'       => 'Overwrites the name and content of the policy in the base table (Spanish). Use this only if you also want to change the original text.',

    // =========================================================
    // [01] CHECKOUT
    // =========================================================
    'checkout' => [
        'card_title'  => 'Your order',
        'details'     => 'Details',
        'must_accept' => 'You must read and accept all policies to proceed with the payment.',
        'accept_label_html' =>
            'I have read and accept the <strong>Terms and Conditions</strong>, the <strong>Privacy Policy</strong> and all <strong>Cancellation, Refund and Warranty Policies</strong>.',
        'back'       => 'Back',
        'pay'        => 'Process payment',
        'order_full' => 'Full order details',

        'titles' => [
            'terms'        => 'Terms and Conditions',
            'privacy'      => 'Privacy Policy',
            'cancellation' => 'Cancellation Policy',
            'refunds'      => 'Refund Policy',
            'warranty'     => 'Warranty Policy',
            'payments'     => 'Payment Methods',
        ],
    ],

    // =========================================================
    // [02] FIELDS
    // =========================================================
    'fields' => [
        'title'       => 'Title',
        'description' => 'Description',
        'type'        => 'Type',
        'is_active'   => 'Active',
    ],

    // =========================================================
    // [03] TYPES
    // =========================================================
    'types' => [
        'cancellation' => 'Cancellation Policy',
        'refund'       => 'Refund Policy',
        'terms'        => 'Terms and Conditions',
    ],

    // =========================================================
    // [04] MESSAGES
    // =========================================================
    'success' => [
        'created' => 'Policy created successfully.',
        'updated' => 'Policy updated successfully.',
        'deleted' => 'Policy deleted successfully.',
    ],

    'error' => [
        'create' => 'The policy could not be created.',
        'update' => 'The policy could not be updated.',
        'delete' => 'The policy could not be deleted.',
    ],
];
