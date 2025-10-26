<?php

return [

    'messages' => [
        'date_no_longer_available' => 'The date :date is no longer available for booking (minimum: :min).',
        'limited_seats_available' => 'Only :available seats left for ":tour" on :date.',
        'bookings_created_from_cart' => 'Your bookings were successfully created from the cart.',
        'capacity_exceeded' => 'Capacity Exceeded',
        'meeting_point_hint' => 'Only the name of the point will be shown in the list.',
    ],

    'availability' => [
        'fields' => [
            'tour'        => 'Tour',
            'date'        => 'Date',
            'start_time'  => 'Start Time',
            'end_time'    => 'End Time',
            'available'   => 'Available',
            'is_active'   => 'Active',
        ],

        'success' => [
            'created'     => 'Availability successfully created.',
            'updated'     => 'Availability successfully updated.',
            'deactivated' => 'Availability successfully deactivated.',
        ],

        'error' => [
            'create'     => 'Could not create availability.',
            'update'     => 'Could not update availability.',
            'deactivate' => 'Could not deactivate availability.',
        ],

        'ui' => [
            'page_title'           => 'Availability',
            'page_heading'         => 'Availability',
            'blocked_page_title'   => 'Blocked Tours',
            'blocked_page_heading' => 'Blocked Tours',
        ],

        'states' => [
            'available' => 'Available',
            'blocked'   => 'Blocked',
        ],

        'buttons' => [
            'mark_all'         => 'Select All',
            'unmark_all'       => 'Unselect All',
            'block_all'        => 'Block All',
            'unblock_all'      => 'Unblock All',
            'block_selected'   => 'Block Selected',
            'unblock_selected' => 'Unblock Selected',
            'back'             => 'Back',
            'open'             => 'Open',
            'cancel'           => 'Cancel',
            'block'            => 'Block',
            'unblock'          => 'Unblock',
        ],
    ],

    'bookings' => [
        'ui' => [
            'page_title'         => 'Bookings',
            'page_heading'       => 'Booking Management',
            'register_booking'   => 'Register Booking',
            'add_booking'        => 'Add Booking',
            'edit_booking'       => 'Edit Booking',
            'booking_details'    => 'Booking Details',
            'download_receipt'   => 'Download Receipt',
            'actions'            => 'Actions',
            'view_details'       => 'View Details',
            'click_to_view'      => 'Click to view details',
            'zoom_in'            => 'Zoom In',
            'zoom_out'           => 'Zoom Out',
            'zoom_reset'         => 'Reset Zoom',
        ],

        'fields' => [
            'booking_id'        => 'Booking ID',
            'status'            => 'Status',
            'booking_date'      => 'Booking Date',
            'booking_origin'    => 'Booking Date (Origin)',
            'reference'         => 'Reference',
            'customer'          => 'Customer',
            'email'             => 'Email',
            'phone'             => 'Phone',
            'tour'              => 'Tour',
            'language'          => 'Language',
            'tour_date'         => 'Tour Date',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Other Hotel Name',
            'meeting_point'     => 'Meeting Point',
            'pickup_location'   => 'Pickup Location',
            'schedule'          => 'Schedule',
            'type'              => 'Type',
            'adults'            => 'Adults',
            'adults_quantity'   => 'Number of Adults',
            'children'          => 'Children',
            'children_quantity' => 'Number of Children',
            'promo_code'        => 'Promo Code',
            'total'             => 'Total',
            'total_to_pay'      => 'Total to Pay',
            'adult_price'       => 'Adult Price',
            'child_price'       => 'Child Price',
            'notes'             => 'Notes',
        ],

        'placeholders' => [
            'select_customer'  => 'Select Customer',
            'select_tour'      => 'Select Tour',
            'select_schedule'  => 'Select Schedule',
            'select_language'  => 'Select Language',
            'select_hotel'     => 'Select Hotel',
            'select_point'     => 'Select Meeting Point',
            'select_status'    => 'Select Status',
            'enter_hotel_name' => 'Enter Hotel Name',
            'enter_promo_code' => 'Enter Promo Code',
            'other'            => 'Other…',
        ],

        'statuses' => [
            'pending'   => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
        ],

        'buttons' => [
            'save'            => 'Save',
            'cancel'          => 'Cancel',
            'edit'            => 'Edit',
            'delete'          => 'Delete',
            'confirm_changes' => 'Confirm Changes',
            'apply'           => 'Apply',
            'update'          => 'Update',
            'close'           => 'Close',
            'customer_locked' => 'The customer is locked and cannot be edited.',
        ],

        'meeting_point' => [
            'time'     => 'Time:',
            'view_map' => 'View Map',
        ],

        'pricing' => [
            'title' => 'Price Summary',
        ],

        'optional' => 'optional',

        'messages' => [
            'past_booking_warning'  => 'This booking corresponds to a past date and cannot be edited.',
            'tour_archived_warning' => 'The tour for this booking has been deleted/archived and could not be loaded. Please select a tour to view its schedules.',
            'no_schedules'          => 'No schedules available',
            'deleted_tour'          => 'Deleted Tour',
            'deleted_tour_snapshot' => 'Deleted Tour (:name)',
            'tour_archived'         => '(archived)',
            'meeting_point_hint'    => 'Only the name of the point will be shown in the list.',
        ],

        'alerts' => [
            'error_summary' => 'Please correct the following errors:',
        ],

        'validation' => [
            'past_date'      => 'You cannot book for past dates.',
            'promo_required' => 'Please enter a promo code first.',
            'promo_checking' => 'Checking code…',
            'promo_invalid'  => 'Invalid promo code.',
            'promo_error'    => 'Could not validate code.',
        ],

        'promo' => [
            'applied'         => 'Code applied',
            'applied_percent' => 'Code applied: -:percent%',
            'applied_amount'  => 'Code applied: -$:amount',
        ],

        'loading' => [
            'saving'     => 'Saving...',
            'validating' => 'Validating...',
            'updating'   => 'Updating...',
        ],

        'success' => [
            'created'          => 'Booking successfully created.',
            'updated'          => 'Booking successfully updated.',
            'deleted'          => 'Booking successfully deleted.',
            'status_updated'   => 'Booking status successfully updated.',
            'status_confirmed' => 'Booking successfully confirmed.',
            'status_cancelled' => 'Booking successfully cancelled.',
            'status_pending'   => 'Booking successfully set to pending.',
        ],

        'errors' => [
            'create'                => 'Could not create booking.',
            'update'                => 'Could not update booking.',
            'delete'                => 'Could not delete booking.',
            'status_update_failed'  => 'Could not update booking status.',
            'detail_not_found'      => 'Booking details not found.',
            'schedule_not_found'    => 'Schedule not found.',
            'insufficient_capacity' => 'Cannot confirm booking. Insufficient capacity for :tour on :date at :time. Requested: :requested people, Available: :available/:max.',
        ],

        'confirm' => [
            'delete' => 'Are you sure you want to delete this booking?',
        ],
    ],

    'actions' => [
        'confirm'        => 'Confirm',
        'cancel'         => 'Cancel Booking',
        'confirm_cancel' => 'Are you sure you want to cancel this booking?',
    ],

    'filters' => [
        'advanced_filters' => 'Advanced Filters',
        'dates'            => 'Dates',
        'booked_from'      => 'Booked From',
        'booked_until'     => 'Booked Until',
        'tour_from'        => 'Tour From',
        'tour_until'       => 'Tour Until',
        'all'              => 'All',
        'apply'            => 'Apply',
        'clear'            => 'Clear',
        'close_filters'    => 'Close Filters',
        'search_reference' => 'Search Reference...',
        'enter_reference'  => 'Enter Booking Reference',
    ],

    'reports' => [
        'excel_title'          => 'Booking Export',
        'pdf_title'            => 'Booking Report - Green Vacations CR',
        'general_report_title' => 'General Booking Report - Green Vacations Costa Rica',
        'download_pdf'         => 'Download PDF',
        'export_excel'         => 'Export to Excel',
        'coupon'               => 'Coupon',
        'adjustment'           => 'Adjustment',
        'totals'               => 'Totals',
        'adults_qty'           => 'Adults (x:qty)',
        'kids_qty'             => 'Kids (x:qty)',
        'people'               => 'People',
        'subtotal'             => 'Subtotal',
        'discount'             => 'Discount',
        'surcharge'            => 'Surcharge',
        'original_price'       => 'Original Price',
        'total_adults'         => 'Total Adults',
        'total_kids'           => 'Total Kids',
        'total_people'         => 'Total People',
    ],

    'receipt' => [
        'title'         => 'Booking Receipt',
        'company'       => 'Green Vacations CR',
        'code'          => 'Code',
        'client'        => 'Client',
        'tour'          => 'Tour',
        'booking_date'  => 'Booking Date',
        'tour_date'     => 'Tour Date',
        'schedule'      => 'Schedule',
        'hotel'         => 'Hotel',
        'meeting_point' => 'Meeting Point',
        'status'        => 'Status',
        'adults_x'      => 'Adults (x:count)',
        'kids_x'        => 'Kids (x:count)',
        'people'        => 'People',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Discount',
        'surcharge'     => 'Surcharge',
        'total'         => 'TOTAL',
        'no_schedule'   => 'No Schedule',
        'qr_alt'        => 'QR Code',
        'qr_scan'       => 'Scan to view booking',
        'thanks'        => 'Thank you for choosing :company!',
    ],

    'details' => [
        'booking_info'  => 'Booking Information',
        'customer_info' => 'Customer Information',
        'tour_info'     => 'Tour Information',
        'pricing_info'  => 'Pricing Information',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Discount',
    ],

];
