<?php

return [
    'title'                  => 'Payment',
    'panels' => [
        'terms_title'        => 'Policies & Terms',
        'secure_subtitle'    => 'Checkout is fast and secure',
        'required_title'     => 'Required fields',
        'required_read_accept' => 'You must read and accept all policies to proceed with payment',
        'terms_block_title'  => 'Terms, Conditions & Policies',
        'version'            => 'v',
        'no_policies_configured' => 'No policies configured. Please contact the administrator.',
    ],

    'steps' => [
        'review'             => 'Review',
        'payment'            => 'Payment',
        'confirmation'       => 'Confirmation',
    ],

    'buttons' => [
        'back'               => 'Back',
        'go_to_payment'      => 'Go to payment',
        'view_details'       => 'View details',
        'edit'               => 'Change date or participants',
        'close'              => 'Close',
    ],

    'summary' => [
        'title'              => 'Order summary',
        'item'               => 'item',
        'items'              => 'items',
        'free_cancellation'  => 'Free cancellation',
        'free_cancellation_until' => 'Until :time on :date',
        'subtotal'           => 'Subtotal',
        'promo_code'         => 'Promo code',
        'total'              => 'Total',
        'taxes_included'     => 'All taxes and fees included',
        'order_details'      => 'Order Details',
    ],

    'blocks' => [
        'pickup_meeting'     => 'Pickup / Meeting point',
        'hotel'              => 'Hotel',
        'meeting_point'      => 'Meeting point',
        'pickup_time'        => 'Pickup time',
        'add_ons'            => 'Add-ons',
        'duration'           => 'Duration',
        'hours'              => 'hours',
        'guide'              => 'Guide',
        'notes'              => 'Notes',
        'ref'                => 'Ref',
        'item'               => 'Item',
    ],

    'categories' => [
        'adult'              => 'Adult',
        'kid'                => 'Kid',
        'category'           => 'Category',
        'qty_badge'          => ':qtyx',
        'unit_price'         => '($:price × :qty)',
        'line_total'         => '$:total',
    ],

    'accept' => [
        'label_html'         => 'I have read and accept the <strong>Terms & Conditions</strong>, the <strong>Privacy Policy</strong>, and all <strong>Cancellation, Refunds & Warranty Policies</strong>. *',
        'error'              => 'You must accept the policies to continue.',
    ],

    'misc' => [
        'at'                 => 'at',
        'participant'        => 'participant',
        'participants'       => 'participants',
    ],
];
