<?php

return [

    'messages' => [
        'date_no_longer_available'   => 'The date :date is no longer available for booking (minimum: :min).',
        'limited_seats_available'    => 'Only :available seats remain for ":tour" on :date.',
        'bookings_created_from_cart' => 'Your bookings were successfully created from the cart.',
        'capacity_exceeded'          => 'Capacity Exceeded',
        'meeting_point_hint'         => 'Only the name of the meeting point is displayed in the list.',
    ],

    'validation' => [
        'max_persons_exceeded'  => 'Maximum :max persons per booking in total.',
        'min_adults_required'   => 'A minimum of :min adults per booking is required.',
        'max_kids_exceeded'     => 'Maximum :max children per booking.',
        'no_active_categories'  => 'This tour has no active customer categories.',
        'min_category_not_met'  => 'A minimum of :min persons is required in the category ":category".',
        'max_category_exceeded' => 'Maximum :max persons allowed in the category ":category".',
        'min_one_person_required' => 'At least one person is required in the booking.',
        'category_not_available' => 'The category with ID :category_id is not available for this tour.',
        'max_persons_label' => 'Maximum number of people allowed per booking',
        'date_range_hint' => 'Select a date between :from — :to',
    ],

    // =========================================================
    // [01] AVAILABILITY
    // =========================================================
    'availability' => [
        'fields' => [
            'tour'       => 'Tour',
            'date'       => 'Date',
            'start_time' => 'Start Time',
            'end_time'   => 'End Time',
            'available'  => 'Available',
            'is_active'  => 'Active',
        ],

        'success' => [
            'created'     => 'Availability created successfully.',
            'updated'     => 'Availability updated successfully.',
            'deactivated' => 'Availability deactivated successfully.',
        ],

        'error' => [
            'create'     => 'Could not create availability.',
            'update'     => 'Could not update availability.',
            'deactivate' => 'Could not deactivate availability.',
        ],

        'validation' => [
            'tour_id' => [
                'required' => 'The :attribute is required.',
                'integer'  => 'The :attribute must be an integer.',
                'exists'   => 'The selected :attribute does not exist.',
            ],
            'date' => [
                'required'    => 'The :attribute is required.',
                'date_format' => 'The :attribute must be in YYYY-MM-DD format.',
            ],
            'start_time' => [
                'date_format'   => 'The :attribute must be in HH:MM (24h) format.',
                'required_with' => 'The :attribute is required when end time is specified.',
            ],
            'end_time' => [
                'date_format'    => 'The :attribute must be in HH:MM (24h) format.',
                'after_or_equal' => 'The :attribute must be after or equal to the start time.',
            ],
            'available' => [
                'boolean' => 'The :attribute field is invalid.',
            ],
            'is_active' => [
                'boolean' => 'The :attribute is invalid.',
            ],
        ],

        'ui' => [
            'page_title'           => 'Availability',
            'page_heading'         => 'Availability',
            'blocked_page_title'   => 'Blocked Tours',
            'blocked_page_heading' => 'Blocked Tours',
            'tours_count'          => '( :count tours )',
            'blocked_count'        => '( :count blocked )',
        ],

        'filters' => [
            'date'               => 'Date',
            'days'               => 'Days',
            'product'            => 'Product',
            'search_placeholder' => 'Search tour…',
            'update_state'       => 'Update state',
            'view_blocked'       => 'View blocked',
            'tip'                => 'Tip: mark rows and use a menu action.',
        ],

        'blocks' => [
            'am_tours'    => 'AM Tours (all tours starting before 12:00pm)',
            'pm_tours'    => 'PM Tours (all tours starting after 12:00pm)',
            'am_blocked'  => 'AM Blocked',
            'pm_blocked'  => 'PM Blocked',
            'empty_block' => 'No tours in this block.',
            'empty_am'    => 'No blocked tours in AM.',
            'empty_pm'    => 'No blocked tours in PM.',
            'no_data'     => 'No data for selected filters.',
            'no_blocked'  => 'No tours blocked in the selected range.',
        ],

        'states' => [
            'available' => 'Available',
            'blocked'   => 'Blocked',
        ],

        'buttons' => [
            'mark_all'         => 'Mark All',
            'unmark_all'       => 'Unmark All',
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

        'confirm' => [
            'view_blocked_title'    => 'View blocked tours',
            'view_blocked_text'     => 'The view with blocked tours will open to unblock them.',
            'block_title'           => 'Block tour?',
            'block_html'            => '<b>:label</b> will be blocked for date <b>:day</b>.',
            'block_btn'             => 'Yes, block',
            'unblock_title'         => 'Unblock tour?',
            'unblock_html'          => '<b>:label</b> will be unblocked for date <b>:day</b>.',
            'unblock_btn'           => 'Yes, unblock',
            'bulk_title'            => 'Confirm action',
            'bulk_items_html'       => 'Items to be affected: <b>:count</b>.',
            'bulk_block_day_html'   => 'Block all available for day <b>:day</b>',
            'bulk_block_block_html' => 'Block all available in block <b>:block</b> on <b>:day</b>',
        ],

        'toasts' => [
            'applying_filters'   => 'Applying filters…',
            'searching'          => 'Searching…',
            'updating_range'     => 'Updating range…',
            'invalid_date_title' => 'Invalid date',
            'invalid_date_text'  => 'Past dates are not allowed.',
            'marked_n'           => 'Marked :n',
            'unmarked_n'         => 'Unmarked :n',
            'updated'            => 'Change applied',
            'updated_count'      => 'Updated: :count',
            'unblocked_count'    => 'Unblocked: :count',
            'no_selection_title' => 'No selection',
            'no_selection_text'  => 'Please mark at least one tour.',
            'no_changes_title'   => 'No changes',
            'no_changes_text'    => 'There are no applicable items.',
            'error_generic'      => 'Could not complete the update.',
            'error_update'       => 'Could not update.',
        ],
    ],

    // =========================================================
    // [02] BOOKINGS
    // =========================================================
    'bookings' => [
        'ui' => [
            'page_title'               => 'Bookings',
            'page_heading'             => 'Bookings Management',
            'register_booking'         => 'Register Booking',
            'add_booking'              => 'Add Booking',
            'edit_booking'             => 'Edit Booking',
            'booking_details'          => 'Booking Details',
            'download_receipt'         => 'Download Receipt',
            'actions'                  => 'Actions',
            'view_details'             => 'View Details',
            'click_to_view'            => 'Click to view details',
            'zoom_in'                  => 'Zoom In',
            'zoom_out'                 => 'Zoom Out',
            'zoom_reset'               => 'Reset Zoom',
            'no_promo'                 => 'No promotional code applied',
            'create_booking'           => 'Create Booking',
            'booking_info'             => 'Booking Information',
            'select_customer'          => 'Select customer',
            'select_tour'              => 'Select tour',
            'select_tour_first'        => 'Select a tour first',
            'select_option'            => 'Select',
            'select_tour_to_see_categories' => 'Select a tour to view categories',
            'loading'                  => 'Loading...',
            'no_results'               => 'No results',
            'error_loading'            => 'Error loading data',
            'tour_without_categories'  => 'This tour has no categories set up',
            'verifying'                => 'Verifying…',
        ],

        'fields' => [
            'booking_id'        => 'Booking ID',
            'status'            => 'Status',
            'booking_date'      => 'Booking Date',
            'booking_origin'    => 'Booking Date (origin)',
            'reference'         => 'Reference',
            'customer'          => 'Customer',
            'email'             => 'Email',
            'phone'             => 'Phone',
            'tour'              => 'Tour',
            'language'          => 'Language',
            'tour_date'         => 'Tour Date',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Other hotel name',
            'meeting_point'     => 'Meeting Point',
            'pickup_location'   => 'Pickup Location',
            'schedule'          => 'Schedule',
            'type'              => 'Type',
            'adults'            => 'Adults',
            'adults_quantity'   => 'Adults Quantity',
            'children'          => 'Children',
            'children_quantity' => 'Children Quantity',
            'promo_code'        => 'Promotional Code',
            'total'             => 'Total',
            'total_to_pay'      => 'Total to Pay',
            'adult_price'       => 'Adult Price',
            'child_price'       => 'Child Price',
            'notes'             => 'Notes',
            'hotel_name'        => 'Hotel Name',
            'travelers'         => 'Travelers',
            'subtotal'          => 'Subtotal',
            'discount'          => 'Discount',
            'total_persons'     => 'Persons',
        ],

        'placeholders' => [
            'select_customer'  => 'Select customer',
            'select_tour'      => 'Select a tour',
            'select_schedule'  => 'Select a schedule',
            'select_language'  => 'Select language',
            'select_hotel'     => 'Select hotel',
            'select_point'     => 'Select meeting point',
            'select_status'    => 'Select status',
            'enter_hotel_name' => 'Enter hotel name',
            'enter_promo_code' => 'Enter promotional code',
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
            'confirm_changes' => 'Confirm changes',
            'apply'           => 'Apply',
            'update'          => 'Update',
            'close'           => 'Close',
            'back'            => 'Back',
        ],

        'meeting_point' => [
            'time'     => 'Time:',
            'view_map' => 'View map',
        ],

        'pricing' => [
            'title' => 'Price Summary',
        ],

        'optional' => 'optional',

        'messages' => [
            'past_booking_warning'   => 'This booking corresponds to a past date and cannot be edited.',
            'tour_archived_warning'  => 'The tour for this booking was deleted/archived and could not be loaded. Please select a tour to view its schedules.',
            'no_schedules'           => 'No schedules available',
            'deleted_tour'           => 'Tour deleted',
            'deleted_tour_snapshot'  => 'Tour Deleted (:name)',
            'tour_archived'          => '(archived)',
            'meeting_point_hint'     => 'Only the name of the meeting point is displayed in the list.',
            'customer_locked'        => 'The customer is locked and cannot be edited.',
            'promo_applied_subtract' => 'Discount applied:',
            'promo_applied_add'      => 'Charge applied:',
            'hotel_locked_by_meeting_point' => 'A meeting point has been selected; cannot select hotel.',
            'meeting_point_locked_by_hotel' => 'A hotel has been selected; cannot select meeting point.',
            'promo_removed'          => 'Promotional code removed',
        ],

        'alerts' => [
            'error_summary' => 'Please correct the following errors:',
        ],

        'validation' => [
            'past_date'          => 'You cannot book for dates prior to today.',
            'promo_required'     => 'Enter a promotional code first.',
            'promo_checking'     => 'Checking code…',
            'promo_invalid'      => 'Invalid promotional code.',
            'promo_error'        => 'Could not validate the code.',
            'promo_empty'        => 'Enter a code first.',
            'promo_needs_subtotal' => 'Add at least 1 passenger to calculate the discount.',
        ],

        'promo' => [
            'applied'         => 'Code applied',
            'applied_percent' => 'Code applied: -:percent%',
            'applied_amount'  => 'Code applied: -$:amount',
        ],

        'loading' => [
            'saving'     => 'Saving…',
            'validating' => 'Validating…',
            'updating'   => 'Updating…',
        ],

        'success' => [
            'created'          => 'Booking created successfully.',
            'updated'          => 'Booking updated successfully.',
            'deleted'          => 'Booking deleted successfully.',
            'status_updated'   => 'Booking status updated successfully.',
            'status_confirmed' => 'Booking confirmed successfully.',
            'status_cancelled' => 'Booking cancelled successfully.',
            'status_pending'   => 'Booking set to pending successfully.',
        ],

        'errors' => [
            'create'               => 'Could not create the booking.',
            'update'               => 'Could not update the booking.',
            'delete'               => 'Could not delete the booking.',
            'status_update_failed' => 'Could not update booking status.',
            'detail_not_found'     => 'Booking details not found.',
            'schedule_not_found'   => 'Schedule not found.',
            'insufficient_capacity'=> 'There is insufficient capacity for ":tour" on :date at :time. Requested: :requested, available: :available (max: :max).',
        ],

        'confirm' => [
            'delete' => 'Are you sure you want to delete this booking?',
        ],
    ],

    // =========================================================
    // [03] ACTIONS
    // =========================================================
    'actions' => [
        'confirm'        => 'Confirm',
        'cancel'         => 'Cancel Booking',
        'confirm_cancel' => 'Are you sure you want to cancel this booking?',
    ],

    // =========================================================
    // [04] FILTERS
    // =========================================================
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
        'close_filters'    => 'Close filters',
        'search_reference' => 'Search reference…',
        'enter_reference'  => 'Enter booking reference',
    ],

    // =========================================================
    // [05] REPORTS
    // =========================================================
    'reports' => [
        'excel_title'           => 'Bookings Export',
        'pdf_title'             => 'Bookings Report – Green Vacations CR',
        'general_report_title'  => 'General Bookings Report – Green Vacations Costa Rica',
        'download_pdf'          => 'Download PDF',
        'export_excel'          => 'Export Excel',
        'coupon'                => 'Coupon',
        'adjustment'            => 'Adjustment',
        'totals'                => 'Totals',
        'adults_qty'            => 'Adults (x:qty)',
        'kids_qty'              => 'Children (x:qty)',
        'people'                => 'People',
        'subtotal'              => 'Subtotal',
        'discount'              => 'Discount',
        'surcharge'             => 'Surcharge',
        'original_price'        => 'Original Price',
        'total_adults'          => 'Total Adults',
        'total_kids'            => 'Total Children',
        'total_people'          => 'Total Persons',
    ],

    // =========================================================
    // [06] RECEIPT
    // =========================================================
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
        'kids_x'        => 'Children (x:count)',
        'people'        => 'People',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Discount',
        'surcharge'     => 'Surcharge',
        'total'         => 'TOTAL',
        'no_schedule'   => 'No schedule',
        'qr_alt'        => 'QR Code',
        'qr_scan'       => 'Scan to view booking',
        'thanks'        => 'Thank you for choosing :company!',
    ],

    // =========================================================
    // [07] DETAILS MODAL
    // =========================================================
    'details' => [
        'booking_info'  => 'Booking Information',
        'customer_info' => 'Customer Information',
        'tour_info'     => 'Tour Information',
        'pricing_info'  => 'Pricing Information',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Discount',
    ],

    // =========================================================
    // [08] TRAVELERS (MODAL)
    // =========================================================
    'travelers' => [
        'title_warning'        => 'Attention',
        'title_info'           => 'Info',
        'title_error'          => 'Error',
        'max_persons_reached'  => 'Maximum :max people per booking.',
        'max_category_reached' => 'The maximum for this category is :max.',
        'invalid_quantity'     => 'Invalid quantity. Please enter a valid number.',
        'age_between'          => 'Age :min-:max',
        'age_from'             => 'Age :min+',
        'age_to'               => 'Up to :max years',
    ],

    // =========================================================
    // [09] EXCLUDED DATES / AVAILABILITY & CAPACITY
    // =========================================================
    'excluded_dates' => [

        'ui' => [
            'page_title'           => 'Availability & Capacity Management',
            'page_heading'         => 'Availability & Capacity Management',
            'tours_count'          => 'tours',
            'blocked_page_title'   => 'Blocked Tours',
            'blocked_page_heading' => 'Blocked Tours',
            'blocked_count'        => ':count blocked tours',
        ],

        'legend' => [
            'title'                 => 'Capacity Legend',
            'base_tour'             => 'Base Tour',
            'override_schedule'     => 'Override Schedule',
            'override_day'          => 'Override Day',
            'override_day_schedule' => 'Override Day+Schedule',
            'blocked'               => 'Blocked',
        ],

        'filters' => [
            'date'               => 'Date',
            'days'               => 'Days',
            'product'            => 'Search Tour',
            'search_placeholder' => 'Tour name…',
            'bulk_actions'       => 'Bulk Actions',
            'update_state'       => 'Update state',
        ],

        'blocks' => [
            'am'          => 'AM TOURS',
            'pm'          => 'PM TOURS',
            'am_blocked'  => 'AM TOURS (blocked)',
            'pm_blocked'  => 'PM TOURS (blocked)',
            'empty_am'    => 'No tours in this block',
            'empty_pm'    => 'No tours in this block',
            'no_data'     => 'No data to display',
            'no_blocked'  => 'No tours blocked for selected range',
        ],

        'buttons' => [
            'mark_all'          => 'Mark All',
            'unmark_all'        => 'Unmark All',
            'block_all'         => 'Block All',
            'unblock_all'       => 'Unblock All',
            'block_selected'    => 'Block Selected',
            'unblock_selected'  => 'Unblock Selected',
            'set_capacity'      => 'Adjust Capacity',
            'capacity'          => 'Capacity',
            'view_blocked'      => 'View Blocked',
            'capacity_settings' => 'Capacity Settings',
            'block'             => 'Block',
            'unblock'           => 'Unblock',
            'apply'             => 'Apply',
            'save'              => 'Save',
            'cancel'            => 'Cancel',
            'back'              => 'Back',
        ],

        'states' => [
            'available' => 'Available',
            'blocked'   => 'Blocked',
        ],

        'badges' => [
            'tooltip_prefix' => 'Occupied/Capacity -',
        ],

        'modals' => [
            'capacity_title'           => 'Adjust Capacity',
            'selected_capacity_title'  => 'Adjust Capacity of Selected',
            'date'                     => 'Date:',
            'hierarchy_title'          => 'Capacity Hierarchy:',
            'new_capacity'             => 'New Capacity',
            'hint_zero_blocks'         => 'Leave at 0 to fully block',
            'selected_count'           => 'Capacity will be updated for :count selected items.',
            'capacity_day_title'       => 'Adjust capacity for the day',
            'capacity_day_subtitle'    => 'All schedules of the day',
        ],

        'confirm' => [
            'block_title'       => 'Block?',
            'unblock_title'     => 'Unblock?',
            'block_html'        => '<strong>:label</strong><br>Date: :day',
            'unblock_html'      => '<strong>:label</strong><br>Date: :day',
            'block_btn'         => 'Block',
            'unblock_btn'       => 'Unblock',
            'bulk_title'        => 'Confirm bulk operation',
            'bulk_items_html'   => ':count items will be affected',
            'block_day_title'   => 'Block whole day',
            'block_block_title' => 'Block block :block on :day',
        ],

        'toasts' => [
            'invalid_date_title' => 'Invalid date',
            'invalid_date_text'  => 'You cannot select past dates',
            'searching'          => 'Searching…',
            'applying_filters'   => 'Applying filters…',
            'updating_range'     => 'Updating range…',
            'no_selection_title' => 'No selection',
            'no_selection_text'  => 'You must select at least one item',
            'no_changes_title'   => 'No changes',
            'no_changes_text'    => 'There are no items to update',
            'marked_n'           => 'Marked :n items',
            'unmarked_n'         => 'Unmarked :n items',
            'error_generic'      => 'Could not complete the operation',
            'updated'            => 'Updated',
            'updated_count'      => ':count items updated',
            'unblocked_count'    => ':count items unblocked',
            'blocked'            => 'Blocked',
            'unblocked'          => 'Unblocked',
            'capacity_updated'   => 'Capacity updated',
        ],
    ],

    // =========================================================
    // [10] CAPACITY SETTINGS
    // =========================================================
    'capacity' => [

        'ui' => [
            'page_title'   => 'Capacity Management',
            'page_heading' => 'Capacity Management',
        ],

        'tabs' => [
            'global'        => 'Global',
            'by_tour'       => 'By Tour + Schedule',
            'day_schedules' => 'Day + Schedule Overrides',
        ],

        'alerts' => [
            'global_info'       => '<strong>Global capacities:</strong> Define the base limit for each tour (all days and times).',
            'by_tour_info'      => '<strong>By Tour + Schedule:</strong> Specific capacity override for each schedule of every tour. These overrides take precedence over the global tour capacity.',
            'day_schedules_info'=> '<strong>Day + Schedule:</strong> Highest priority override for a specific day and schedule. These are managed from the "Availability and Capacity" view.',
        ],

        'tables' => [
            'global' => [
                'tour'     => 'Tour',
                'type'     => 'Type',
                'capacity' => 'Global Capacity',
                'level'    => 'Level',
            ],
            'by_tour' => [
                'schedule'     => 'Schedule',
                'capacity'     => 'Capacity Override',
                'level'        => 'Level',
                'no_schedules' => 'This tour has no assigned schedules',
            ],
            'day_schedules' => [
                'date'         => 'Date',
                'tour'         => 'Tour',
                'schedule'     => 'Schedule',
                'capacity'     => 'Capacity',
                'actions'      => 'Actions',
                'no_overrides' => 'No day + schedule overrides found',
            ],
        ],

        'badges' => [
            'base'      => 'Base',
            'override'  => 'Override',
            'global'    => 'Global',
            'blocked'   => 'BLOCKED',
            'unlimited' => '∞',
        ],

        'buttons' => [
            'save'   => 'Save',
            'delete' => 'Delete',
            'back'   => 'Back',
            'apply'  => 'Apply',
            'cancel' => 'Cancel',
        ],

        'messages' => [
            'empty_placeholder' => 'Empty = uses global capacity (:capacity)',
            'deleted_confirm'   => 'Delete this override?',
            'no_day_overrides'  => 'No day + schedule overrides available.',
        ],

        'toasts' => [
            'success_title' => 'Success',
            'error_title'   => 'Error',
        ],
    ],

];
