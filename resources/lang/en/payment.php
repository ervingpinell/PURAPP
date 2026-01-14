<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in payment-related views
    |
    */

    // Payment Page
    'payment' => 'Payment',
    'stripe_description' => 'Credit/Debit Card Payment',
    'paypal_description' => 'PayPal Payment',
    'tilopay_description' => 'Credit/Debit Card Payment (Tilopay)',
    'banco_nacional_description' => 'Banco Nacional Transfer',
    'bac_description' => 'BAC Credomatic Transfer',
    'bcr_description' => 'Banco de Costa Rica Transfer',
    'payment_information' => 'Payment Information',
    'secure_payment' => 'Secure Payment',
    'select_payment_method' => 'Select Payment Method',
    'payment_secure_encrypted' => 'Your payment is secure and encrypted',
    'powered_by_stripe' => 'Powered by Stripe. Your card information is never stored on our servers.',
    'pay' => 'Pay',
    'back' => 'Back',
    'processing' => 'Processing...',
    'terms_agreement' => 'By completing this payment, you agree to our terms and conditions.',
    'terms_agreement_checkbox' => 'I have read and agree to the <a href=":url" target="_blank">Terms and Conditions</a>.',
    'terms_required' => 'You must accept the terms and conditions to continue.',

    // Order Summary
    'order_summary' => 'Order Summary',
    'subtotal' => 'Subtotal',
    'total' => 'Total',
    'participants' => 'participants',
    'free_cancellation' => 'Free cancellation available',

    // Confirmation Page
    'payment_successful' => 'Payment Successful!',
    'booking_confirmed' => 'Your booking has been confirmed',
    'booking_reference' => 'Booking Reference',
    'what_happens_next' => 'What happens next?',
    'view_my_bookings' => 'View My Bookings',
    'back_to_home' => 'Back to Home',

    // Next Steps
    'next_step_email' => 'You will receive a confirmation email with all the details of your booking',
    'next_step_confirmed' => 'Your tour is confirmed for the selected date and time',
    'next_step_manage' => 'You can view and manage your booking in "My Bookings"',
    'next_step_support' => 'If you have any questions, please contact our support team',

    // Countdown Timer
    'time_remaining' => 'Time Remaining',
    'complete_payment_in' => 'Complete your payment in',
    'payment_expires_in' => 'Payment expires in',
    'session_expired' => 'Your payment session has expired',
    'session_expired_message' => 'Please return to your cart and try again.',

    // Errors
    'payment_failed' => 'Payment Failed',
    'payment_error' => 'An error occurred while processing your payment',
    'payment_declined' => 'Your payment was declined',
    'try_again' => 'Please try again',
    'no_pending_bookings' => 'No pending bookings found',
    'bookings_not_found' => 'Bookings not found',
    'payment_not_successful' => 'Payment was not successful. Please try again.',
    'payment_confirmation_error' => 'An error occurred while confirming your payment.',
    'error_title' => 'Error',

    // Progress Steps
    'checkout' => 'Checkout',
    'confirmation' => 'Confirmation',

    // Messages
    'complete_payment_message' => 'Please complete payment to confirm your booking',
    'payment_cancelled' => 'Payment was cancelled. You can try again when ready.',
    'redirect_paypal' => 'Click Pay to be redirected to PayPal and complete your payment securely.',
    'redirect_external_gateway' => 'You will be redirected to the external payment gateway to complete your transaction.',
    'alignet_description' => 'Secure payment with Credit/Debit Card (Banco Nacional)',
    'no_cart_data' => 'No cart data found',
    'gateway_error' => 'Payment gateway connection error. Please check your internet connection and try again.',

    // Admin / Management (merged from m_payments)
    'ui' => [
        'page_title' => 'Payments',
        'page_heading' => 'Payment Management',
        'payment_details' => 'Payment Details',
        'payments_list' => 'Payments List',
        'filters' => 'Filters',
        'actions' => 'Actions',
        'quick_actions' => 'Quick Actions',
    ],

    'statistics' => [
        'total_revenue' => 'Total Revenue',
        'completed_payments' => 'Completed Payments',
        'pending_payments' => 'Pending Payments',
        'failed_payments' => 'Failed Payments',
    ],

    'fields' => [
        'payment_id' => 'Payment ID',
        'booking_ref' => 'Booking Ref',
        'customer' => 'Customer',
        'tour' => 'Tour',
        'amount' => 'Amount',
        'gateway' => 'Gateway',
        'status' => 'Status',
        'date' => 'Date',
        'payment_method' => 'Payment Method',
        'tour_date' => 'Tour Date',
        'booking_status' => 'Booking Status',
    ],

    'filters' => [
        'search' => 'Search',
        'search_placeholder' => 'Booking ref, email, name...',
        'status' => 'Status',
        'gateway' => 'Gateway',
        'date_from' => 'Date From',
        'date_to' => 'Date To',
        'all' => 'All',
    ],

    'statuses' => [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
    ],

    'buttons' => [
        'export_csv' => 'Export CSV',
        'view_details' => 'View Details',
        'view_booking' => 'View Booking',
        'process_refund' => 'Process Refund',
        'back_to_list' => 'Back to List',
    ],

    'messages' => [
        'no_payments_found' => 'No payments found',
        'booking_deleted' => 'Booking was permanently deleted',
        'booking_deleted_on' => 'Booking was permanently deleted on',
    ],

    'info' => [
        'payment_information' => 'Payment Information',
        'booking_information' => 'Booking Information',
        'gateway_response' => 'Gateway Response',
        'payment_timeline' => 'Payment Timeline',
        'payment_created' => 'Payment Created',
        'payment_completed' => 'Payment Completed',
    ],

    'pagination' => [
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'results' => 'results',
    ],

    // Alignet Payment Page
    'alignet' => [
        'page_title' => 'Secure Payment',
        'processed_by' => 'Processed by',
        'booking_summary' => 'Booking Summary',
        'reference' => 'Reference',
        'tour' => 'Tour',
        'date' => 'Date',
        'passengers' => 'Passengers',
        'total' => 'Total',
        'proceed_payment' => 'Proceed to Payment',
        'loading_module' => 'Loading payment module...',
        'secure_transaction' => 'Secure and encrypted transaction',
        'error_module_unavailable' => 'Error: Payment module is not available.',
        'error_loading' => 'Error loading payment module',
        'error_config' => 'Configuration Error',
        'error_payment_system' => 'Could not load payment system.',
        'reload' => 'Reload',
        'payment_successful' => 'Payment Successful!',
        'payment_cancelled' => 'Payment process was cancelled.',
    ],
];
