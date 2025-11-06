<?php

return [

    // =========================================================
    // [00] GENERIC
    // =========================================================
    'page_title'  => 'Policies',
    'no_policies' => 'No policies available at the moment.',
    'no_sections' => 'No sections available at the moment.',

    // =========================================================
    // [01] CHECKOUT
    // =========================================================
    'checkout' => [
        'card_title'  => 'Your order',
        'details'     => 'Details',
        'must_accept' => 'You must read and accept all policies to proceed with payment.',
        'accept_label_html' =>
            'I have read and accept the <strong>Terms & Conditions</strong>, the <strong>Privacy Policy</strong>, and all <strong>Cancellation, Refund and Warranty Policies</strong>.',
        'back'       => 'Back',
        'pay'        => 'Proceed to payment',
        'order_full' => 'Full order details',

        'version' => [
            'terms'   => 'v1',
            'privacy' => 'v1',
        ],

        'titles' => [
            'terms'        => 'Terms & Conditions',
            'privacy'      => 'Privacy Policy',
            'cancellation' => 'Cancellation Policy',
            'refunds'      => 'Refund Policy',
            'warranty'     => 'Warranty Policy',
            'payments'     => 'Payment Methods',
        ],

        'bodies' => [
        'terms_html' => <<<HTML
<p>These terms govern the purchase of tours and services offered by Green Vacations CR.</p>
<ul>
<li><strong>Scope:</strong> The purchase applies exclusively to the services listed for the selected dates and times.</li>
<li><strong>Prices and fees:</strong> Prices are shown in USD and include taxes where applicable. Any additional charges will be disclosed before payment.</li>
<li><strong>Capacity and availability:</strong> Bookings are subject to availability and capacity validations.</li>
<li><strong>Changes:</strong> Date/time changes are subject to availability and may result in fare differences.</li>
<li><strong>Liability:</strong> Services are provided in accordance with the applicable Costa Rican regulations.</li>
</ul>
HTML,
        'privacy_html' => <<<HTML
<p>We process personal data in accordance with applicable regulations. We collect only the data necessary to manage bookings, payments, and customer communications.</p>
<ul>
<li><strong>Use of information:</strong> Purchase management, customer support, operational notifications, and legal compliance.</li>
<li><strong>Sharing:</strong> We do not sell or trade personal data.</li>
<li><strong>Rights:</strong> You may exercise rights of access, rectification, objection, and erasure through our contact channels.</li>
</ul>
HTML,
        'cancellation_html' => <<<HTML
<p>You may request cancellation before the service starts according to the following deadlines:</p>
<ul>
<li>Up to 2 hours before: <strong>full refund</strong>.</li>
<li>Between 2 hours and 1 hour before: <strong>50% refund</strong>.</li>
<li>Less than 1 hour: <strong>non-refundable</strong>.</li>
</ul>
<p>Refunds are issued to the <strong>same card</strong> used for the purchase. Posting times depend on the issuing bank.</p>
<p>Please provide your <strong>order number</strong> and <strong>full name</strong> when requesting a cancellation. Deadlines may vary by tour if indicated on the product page.</p>
HTML,
        'refunds_html' => <<<HTML
<p>Where applicable, refunds are issued to the <strong>same card</strong> used for the purchase. Timeframes depend on the payment method’s issuer.</p>
<p>To request a refund: info@greenvacationscr.com / (+506) 2479 1471.</p>
HTML,
        'warranty_html' => <<<HTML
<p>Applies to services not provided or provided in a manner substantially different from what was offered. You have <strong>7 days</strong> to report incidents. The warranty applies to tourism services sold by Green Vacations CR.</p>
HTML,
        'payments_html' => <<<HTML
<p>Payment is made via Alignet Payment Link with Visa/Mastercard/Amex cards enabled for online purchases.</p>
HTML,
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
        'terms'        => 'Terms & Conditions',
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
        'create' => 'Could not create the policy.',
        'update' => 'Could not update the policy.',
        'delete' => 'Could not delete the policy.',
    ],
];
