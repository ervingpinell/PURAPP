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

    'customer_info' => [
        'title'              => 'Customer Information',
        'subtitle'              => 'Please provide your contact information to continue',
        'full_name'          => 'Full Name',
        'first_name'         => 'First Name',
        'last_name'          => 'Last Name',
        'email'              => 'Email',
        'phone'              => 'Phone',
        'optional'              => 'optional',
        'placeholder_name'              => 'John Doe',
        'placeholder_email'              => 'email@example.com',
        'why_need_title'              => 'Why we need this',
        'why_need_text'              => 'Your email will be used to send booking confirmation, updates, and payment links. You can optionally create an account after booking to manage your reservations.',
        'logged_in_as'              => 'Logged in as',
        'address'            => 'Address',
        'city'               => 'City',
        'state'              => 'State / Province',
        'zip'                => 'Zip Code',
        'country'            => 'Country',
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
        'accept_terms'       => 'Accept terms and conditions',
    ],

    'summary' => [
        'title'              => 'Order summary',
        'item'               => 'item',
        'items'              => 'items',
        'free_cancellation'  => 'Free cancellation',
        'free_cancellation_until' => 'Before :time on :date',
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

    'payment' => [
        'title'              => 'Payment',
        'total'              => 'Total',
        'secure_payment'     => 'Secure Payment',
        'powered_by'         => 'Powered by',
        'proceed_to_payment' => 'Proceed to Payment',
        'secure_transaction' => 'Secure Transaction',
        'error_occurred'     => 'An error occurred while processing the payment. Please try again.',
        'invalid_response'   => 'The payment processor response is invalid.',

        // Friendly error messages for Alignet
        'cancelled_by_user'  => 'You cancelled the payment.',
        'timeout'            => 'Payment time expired.',
        'insufficient_funds' => 'Insufficient funds.',
        'card_declined'      => 'Your card was declined.',
        'invalid_card'       => 'Invalid card details.',
        'failed'             => 'Payment could not be processed.',
        'success'            => 'Payment successful! You will receive a confirmation email soon.',
        'session_expired'    => 'Your session expired. Please log in again.',

        // Alignet Bank Specific Messages
        'operation_denied'   => 'Operation Denied.',
        'operation_rejected' => 'Operation Rejected.',
        'operation_cancelled' => 'Operation Cancelled.',
        'operation_authorized' => 'Operation Authorized.',

        // Debug info for bank support
        'debug_info'         => 'DEBUG - Code: :code | Auth: :auth | Message: :message',
    ],
    'booking' => [
        'summary'   => 'Booking Summary',
        'reference' => 'Reference',
        'date'      => 'Date',
        'passengers' => 'Passengers',
    ],
    'tour' => [
        'name' => 'Tour',
    ],
];
