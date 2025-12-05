<?php

return [

    'messages' => [
        'date_no_longer_available'   => 'The date :date is no longer available for booking (minimum: :min).',
        'limited_seats_available'    => 'Only :available spots left for ":tour" on :date.',
        'bookings_created_from_cart' => 'Your bookings were successfully created from the cart.',
        'capacity_exceeded'          => 'Capacity Exceeded',
        'meeting_point_hint'         => 'Only the point name is shown in the list.',
    ],

    'validation' => [
        'max_persons_exceeded'  => 'Maximum :max people per booking in total.',
        'min_adults_required'   => 'At least :min adults are required per booking.',
        'max_kids_exceeded'     => 'Maximum :max children per booking.',
        'no_active_categories'  => 'This tour has no active customer categories.',
        'min_category_not_met'  => 'At least :min people are required in the ":category" category.',
        'max_category_exceeded' => 'Maximum :max people allowed in the ":category" category.',
        'min_one_person_required' => 'There must be at least one person in the booking.',
        'category_not_available'  => 'The category with ID :category_id is not available for this tour.',
        'max_persons_label'       => 'Maximum number of people allowed per booking',
        'date_range_hint'         => 'Select a date between :from — :to',
    ],

    // =========================================================
    // [01] AVAILABILITY
    // =========================================================
    'availability' => [
        'fields' => [
            'tour'       => 'Tour',
            'date'       => 'Date',
            'start_time' => 'Start time',
            'end_time'   => 'End time',
            'available'  => 'Available',
            'is_active'  => 'Active',
        ],

        'success' => [
            'created'     => 'Availability created successfully.',
            'updated'     => 'Availability updated successfully.',
            'deactivated' => 'Availability deactivated successfully.',
        ],

        'error' => [
            'create'     => 'The availability could not be created.',
            'update'     => 'The availability could not be updated.',
            'deactivate' => 'The availability could not be deactivated.',
        ],

        'validation' => [
            'tour_id' => [
                'required' => 'The :attribute is required.',
                'integer'  => 'The :attribute must be an integer.',
                'exists'   => 'The selected :attribute does not exist.',
            ],
            'date' => [
                'required'    => 'The :attribute is required.',
                'date_format' => 'The :attribute must use the YYYY-MM-DD format.',
            ],
            'start_time' => [
                'date_format'   => 'The :attribute must use the HH:MM format (24h).',
                'required_with' => 'The :attribute is required when the end time is specified.',
            ],
            'end_time' => [
                'date_format'    => 'The :attribute must use the HH:MM format (24h).',
                'after_or_equal' => 'The :attribute must be greater than or equal to the start time.',
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
            'blocked_page_title'   => 'Blocked tours',
            'blocked_page_heading' => 'Blocked tours',
            'tours_count'          => '( :count tours )',
            'blocked_count'        => '( :count blocked )',
        ],

        'filters' => [
            'date'               => 'Date',
            'days'               => 'Days',
            'product'            => 'Product',
            'search_placeholder' => 'Search tour...',
            'update_state'       => 'Update status',
            'view_blocked'       => 'View blocked',
            'tip'                => 'Tip: mark rows and use an action from the menu.',
        ],

        'blocks' => [
            'am_tours'    => 'AM Tours (all tours starting before 12:00pm)',
            'pm_tours'    => 'PM Tours (all tours starting after 12:00pm)',
            'am_blocked'  => 'AM blocked',
            'pm_blocked'  => 'PM blocked',
            'empty_block' => 'There are no tours in this block.',
            'empty_am'    => 'No blocked tours in AM.',
            'empty_pm'    => 'No blocked tours in PM.',
            'no_data'     => 'There is no data for the selected filters.',
            'no_blocked'  => 'There are no blocked tours in the selected range.',
        ],

        'states' => [
            'available' => 'Available',
            'blocked'   => 'Blocked',
        ],

        'buttons' => [
            'mark_all'         => 'Mark all',
            'unmark_all'       => 'Unmark all',
            'block_all'        => 'Block all',
            'unblock_all'      => 'Unblock all',
            'block_selected'   => 'Block selected',
            'unblock_selected' => 'Unblock selected',
            'back'             => 'Back',
            'open'             => 'Open',
            'cancel'           => 'Cancel',
            'block'            => 'Block',
            'unblock'          => 'Unblock',
        ],

        'confirm' => [
            'view_blocked_title'    => 'View blocked tours',
            'view_blocked_text'     => 'The blocked tours view will open so you can unblock them.',
            'block_title'           => 'Block tour?',
            'block_html'            => '<b>:label</b> will be blocked for the date <b>:day</b>.',
            'block_btn'             => 'Yes, block',
            'unblock_title'         => 'Unblock tour?',
            'unblock_html'          => '<b>:label</b> will be unblocked for the date <b>:day</b>.',
            'unblock_btn'           => 'Yes, unblock',
            'bulk_title'            => 'Confirm action',
            'bulk_items_html'       => 'Items to affect: <b>:count</b>.',
            'bulk_block_day_html'   => 'Block all available for the day <b>:day</b>',
            'bulk_block_block_html' => 'Block all available in block <b>:block</b> on <b>:day</b>',
        ],

        'toasts' => [
            'applying_filters'   => 'Applying filters...',
            'searching'          => 'Searching...',
            'updating_range'     => 'Updating range...',
            'invalid_date_title' => 'Invalid date',
            'invalid_date_text'  => 'Past dates are not allowed.',
            'marked_n'           => 'Marked :n',
            'unmarked_n'         => 'Unmarked :n',
            'updated'            => 'Change applied',
            'updated_count'      => 'Updated: :count',
            'unblocked_count'    => 'Unblocked: :count',
            'no_selection_title' => 'No selection',
            'no_selection_text'  => 'Mark at least one tour.',
            'no_changes_title'   => 'No changes',
            'no_changes_text'    => 'There are no applicable items.',
            'error_generic'      => 'The update could not be completed.',
            'error_update'       => 'Could not update.',
        ],
    ],

    // =========================================================
    // [02] BOOKINGS
    // =========================================================
    'bookings' => [
        'singular' => 'Booking',
        'plural' => 'Bookings',
        'customer' => 'Customer',
        'payment_link_info' => 'Customer payment link',
        'regenerate_warning' => 'Warning: Regenerating the link will invalidate the previous one.',
        'steps' => [
            'customer' => 'Customer',
            'select_tour_date' => 'Select Tour & Date',
            'select_schedule_language' => 'Select Schedule & Language',
            'select_participants' => 'Select Participants',
            'customer_details' => 'Customer & Details',
        ],
        'ui' => [
            'page_title'        => 'Bookings',
            'page_heading'      => 'Booking Management',
            'register_booking'  => 'Register Booking',
            'add_booking'       => 'Add Booking',
            'edit_booking'      => 'Edit Booking',
            'booking_details'   => 'Booking Details',
            'download_receipt'  => 'Download receipt',
            'actions'           => 'Actions',
            'view_details'      => 'View details',
            'click_to_view'     => 'Click to view details',
            'zoom_in'           => 'Zoom in',
            'zoom_out'          => 'Zoom out',
            'zoom_reset'        => 'Reset zoom',
            'no_promo'          => 'No promotional code applied',
            'create_booking'    => 'Create Booking',
            'create_title'      => 'Create New Booking',
            'booking_info'      => 'Booking Information',
            'select_customer'   => 'Select customer',
            'select_tour'       => 'Select tour',
            'select_tour_first' => 'Select a tour first',
            'select_option'     => 'Select',
            'select_tour_to_see_categories' => 'Select a tour to see the categories',
            'loading'           => 'Loading...',
            'no_results'        => 'No results',
            'error_loading'     => 'Error loading data',
            'tour_without_categories' => 'This tour has no categories configured',
            'verifying'         => 'Verifying...',
            'min'               => 'Minimum',
            'max'               => 'Maximum',
            'confirm_booking' => 'Confirm Booking',
            'subtotal' => 'Subtotal',
            'total' => 'Total',
            'select_meeting_point' => 'Select meeting point',
            'no_pickup' => 'No pickup',
            'hotel' => 'Hotel',
            'meeting_point' => 'Meeting Point',
            'surcharge' => 'Surcharge',
            'discount' => 'Discount',
            'participants' => 'Participants',
            'price_breakdown' => 'Price Breakdown',
            'enter_promo' => 'Enter promo code',
            'select_hotel' => 'Select hotel',
        ],

        'fields' => [
            'booking_id'        => 'Booking ID',
            'status'            => 'Status',
            'booking_date'      => 'Booking date',
            'booking_origin'    => 'Booking date (origin)',
            'reference'         => 'Reference',
            'booking_reference' => 'Booking Reference',
            'customer'          => 'Customer',
            'email'             => 'Email',
            'phone'             => 'Phone',
            'tour'              => 'Tour',
            'language'          => 'Language',
            'tour_date'         => 'Tour date',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Other hotel name',
            'meeting_point'     => 'Meeting point',
            'pickup_location'   => 'Pickup location',
            'schedule'          => 'Schedule',
            'type'              => 'Type',
            'adults'            => 'Adults',
            'adults_quantity'   => 'Number of adults',
            'children'          => 'Children',
            'children_quantity' => 'Number of children',
            'promo_code'        => 'Promotional code',
            'total'             => 'Total',
            'total_to_pay'      => 'Total to pay',
            'adult_price'       => 'Adult price',
            'child_price'       => 'Child price',
            'notes'             => 'Notes',
            'hotel_name'        => 'Hotel name',
            'travelers'         => 'Travelers',
            'subtotal'          => 'Subtotal',
            'discount'          => 'Discount',
            'total_persons'     => 'Persons',
            'pickup_place'      => 'Pickup Place',
            'date'              => 'Date',
            'category'          => 'Category',
            'quantity'          => 'Quantity',
            'price'             => 'Price',
            'pickup'            => 'Pickup',
            'pickup_time'       => 'Pickup time',
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
            'enter_promo_code' => 'Enter promo code',
            'other'            => 'Other…',
            'select_hotel'     => 'Select hotel',
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
            'past_booking_warning'  => 'This booking corresponds to a past date and cannot be edited.',
            'tour_archived_warning' => 'The tour for this booking was deleted/archived and could not be loaded. Select a tour to see its schedules.',
            'no_schedules'          => 'No schedules available',
            'deleted_tour'          => 'Deleted tour',
            'deleted_tour_snapshot' => 'Deleted Tour (:name)',
            'tour_archived'         => '(archived)',
            'meeting_point_hint'    => 'Only the point name is shown in the list.',
            'customer_locked'       => 'The customer is locked and cannot be edited.',
            'promo_applied_subtract' => 'Discount applied:',
            'promo_applied_add'     => 'Surcharge applied:',
            'hotel_locked_by_meeting_point' => 'A meeting point was selected; a hotel cannot be selected.',
            'meeting_point_locked_by_hotel' => 'A hotel was selected; a meeting point cannot be selected.',
            'promo_removed'         => 'Promotional code removed',
        ],

        'alerts' => [
            'error_summary' => 'Please correct the following errors:',
        ],

        'validation' => [
            'past_date'          => 'You cannot book for dates earlier than today.',
            'promo_required'     => 'Enter a promotional code first.',
            'promo_checking'     => 'Checking code…',
            'promo_invalid'      => 'Invalid promotional code.',
            'promo_error'        => 'The code could not be validated.',
            'promo_apply_required' => 'Please click Apply to validate your promo code first.',
            'promo_empty'        => 'Enter a code first.',
            'promo_needs_subtotal' => 'Add at least 1 passenger to calculate the discount.',
        ],

        'promo' => [
            'applied'         => 'Code applied',
            'applied_percent' => 'Code applied: -:percent%',
            'applied_amount'  => 'Code applied: -$:amount',
        ],

        'loading' => [
            'saving'     => 'Saving...',
            'validating' => 'Validating…',
            'updating'   => 'Updating...',
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
            'create'               => 'The booking could not be created.',
            'update'               => 'The booking could not be updated.',
            'delete'               => 'The booking could not be deleted.',
            'status_update_failed' => 'The booking status could not be updated.',
            'detail_not_found'     => 'Booking details not found.',
            'schedule_not_found'   => 'Schedule not found.',
            'insufficient_capacity' => 'Not enough capacity for ":tour" on :date at :time. Requested: :requested, available: :available (max: :max).',
        ],

        'confirm' => [
            'delete' => 'Are you sure you want to delete this booking?',
        ],

        // SoftDelete & Trash
        'trash' => [
            'active_bookings' => 'Active Bookings',
            'trash' => 'Trash',
            'restore_booking' => 'Restore booking',
            'permanently_delete' => 'Permanently delete',
            'force_delete_title' => 'PERMANENT DELETE',
            'force_delete_warning' => 'This action CANNOT be undone!',
            'force_delete_message' => 'will be permanently deleted.',
            'force_delete_data_loss' => 'All related data will be lost forever.',
            'force_delete_confirm' => 'Yes, DELETE FOREVER',
            'booking_deleted' => 'Booking deleted.',
            'booking_restored' => 'Booking restored successfully.',
            'booking_force_deleted' => 'Booking permanently deleted. Payment records preserved for audit.',
            'force_delete_failed' => 'Failed to permanently delete booking.',
            'deleted_booking_indicator' => '(DELETED)',
        ],

        // Checkout Links (for admin-created bookings)
        'checkout_link_label' => 'Customer Payment Link',
        'checkout_link_description' => 'Send this link to the customer so they can complete payment for their booking.',
        'checkout_link_copy' => 'Copy Link',
        'checkout_link_copied' => 'Link copied!',
        'checkout_link_copy_failed' => 'Could not copy link. Please copy it manually.',
        'checkout_link_valid_until' => 'Valid until',
        'checkout_link_expired' => 'This payment link has expired or is no longer valid.',
        'booking_already_paid' => 'This booking has already been paid',
        'payment_link_regenerated' => 'Payment link regenerated successfully',
        'regenerate_payment_link' => 'Regenerate Link',
        'payment_link_expired_label' => 'Link expired',
        'checkout_link_accessed' => 'Customer accessed checkout',
    ],

    // =========================================================
    // [03] ACTIONS
    // =========================================================
    'actions' => [
        'confirm'        => 'Confirm',
        'cancel'         => 'Cancel',
        'confirm_cancel' => 'Are you sure you want to cancel this booking?',
        'remove' => 'Remove',
        'confirm_create' => 'Confirm & Create',
        'review_booking' => 'Review Booking',
        'apply'          => 'Apply',
    ],

    // =========================================================
    // [04] FILTERS
    // =========================================================
    'filters' => [
        'advanced_filters' => 'Advanced Filters',
        'dates'            => 'Dates',
        'booked_from'      => 'Booked from',
        'booked_until'     => 'Booked until',
        'tour_from'        => 'Tour from',
        'tour_until'       => 'Tour until',
        'all'              => 'All',
        'apply'            => 'Apply',
        'clear'            => 'Clear',
        'close_filters'    => 'Close filters',
        'search_reference' => 'Search reference...',
        'enter_reference'  => 'Enter booking reference',
    ],

    // =========================================================
    // [05] REPORTS
    // =========================================================
    'reports' => [
        'excel_title'          => 'Bookings Export',
        'pdf_title'            => 'Bookings Report - Green Vacations CR',
        'general_report_title' => 'General Bookings Report - Green Vacations Costa Rica',
        'download_pdf'         => 'Download PDF',
        'export_excel'         => 'Export Excel',
        'coupon'               => 'Coupon',
        'adjustment'           => 'Adjustment',
        'totals'               => 'Totals',
        'adults_qty'           => 'Adults (x:qty)',
        'kids_qty'             => 'Children (x:qty)',
        'people'               => 'People',
        'subtotal'             => 'Subtotal',
        'discount'             => 'Discount',
        'surcharge'            => 'Surcharge',
        'original_price'       => 'Original price',
        'total_adults'         => 'Total Adults',
        'total_kids'           => 'Total Children',
        'total_people'         => 'Total People',
    ],

    // =========================================================
    // [06] RECEIPT
    // =========================================================
    'receipt' => [
        'title'         => 'Booking Receipt',
        'company'       => 'Green Vacations CR',
        'code'          => 'Code',
        'client'        => 'Customer',
        'tour'          => 'Tour',
        'booking_date'  => 'Booking date',
        'tour_date'     => 'Tour date',
        'schedule'      => 'Schedule',
        'hotel'         => 'Hotel',
        'meeting_point' => 'Meeting point',
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
        'qr_scan'       => 'Scan to view the booking',
        'thanks'        => 'Thank you for choosing :company!',
        'payment_status' => 'Payment Status:',
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
        'total_persons' => 'Total people',
        'pickup_place'   => 'Pickup place',
    ],

    // =========================================================
    // [08] TRAVELERS (MODAL)
    // =========================================================
    'travelers' => [
        'title_warning'        => 'Attention',
        'title_info'           => 'Information',
        'title_error'          => 'Error',
        'max_persons_reached'  => 'Maximum :max people per booking.',
        'max_category_reached' => 'The maximum for this category is :max.',
        'invalid_quantity'     => 'Invalid quantity. Enter a valid number.',
        'age_between'          => 'Age :min-:max',
        'age_from'             => 'Age :min+',
        'age_to'               => 'Up to :max years',
    ],

    'excluded_dates' => [

        'ui' => [
            'page_title'           => 'Availability and Capacity Management',
            'page_heading'         => 'Availability and Capacity Management',
            'tours_count'          => 'tours',
            'blocked_page_title'   => 'Blocked tours',
            'blocked_page_heading' => 'Blocked tours',
            'blocked_count'        => ':count blocked tours',
        ],

        'legend' => [
            'title'                 => 'Capacity Legend',
            'base_tour'             => 'Base Tour',
            'override_schedule'     => 'Schedule Override',
            'override_day'          => 'Day Override',
            'override_day_schedule' => 'Day+Schedule Override',
            'blocked'               => 'Blocked',
        ],

        'filters' => [
            'date'               => 'Date',
            'days'               => 'Days',
            'product'            => 'Search Tour',
            'search_placeholder' => 'Tour name…',
            'bulk_actions'       => 'Bulk Actions',
            'update_state'       => 'Update status',
        ],

        'blocks' => [
            'am'          => 'AM TOURS',
            'pm'          => 'PM TOURS',
            'am_blocked'  => 'AM TOURS (blocked)',
            'pm_blocked'  => 'PM TOURS (blocked)',
            'empty_am'    => 'There are no tours in this block',
            'empty_pm'    => 'There are no tours in this block',
            'no_data'     => 'There is no data to display',
            'no_blocked'  => 'There are no blocked tours for the selected range',
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
            'capacity_title'          => 'Adjust Capacity',
            'selected_capacity_title' => 'Adjust Capacity of Selected',
            'date'                    => 'Date:',
            'hierarchy_title'         => 'Capacity hierarchy:',
            'new_capacity'            => 'New Capacity',
            'hint_zero_blocks'        => 'Set to 0 to completely block',
            'selected_count'          => 'Capacity will be updated for :count selected items.',
            'capacity_day_title'      => 'Adjust capacity for the day',
            'capacity_day_subtitle'   => 'All schedules of the day',
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
            'block_day_title'   => 'Block all day',
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
            'marked_n'           => ':n items marked',
            'unmarked_n'         => ':n items unmarked',
            'error_generic'      => 'The operation could not be completed',
            'updated'            => 'Updated',
            'updated_count'      => ':count items updated',
            'unblocked_count'    => ':count items unblocked',
            'blocked'            => 'Blocked',
            'unblocked'          => 'Unblocked',
            'capacity_updated'   => 'Capacity updated',
        ],

    ],

    'capacity' => [

        // =========================================================
        // [01] UI TITLES & HEADINGS
        // =========================================================
        'ui' => [
            'page_title'   => 'Capacity Management',
            'page_heading' => 'Capacity Management',
        ],

        // =========================================================
        // [02] TABS
        // =========================================================
        'tabs' => [
            'global'        => 'Global',
            'by_tour'       => 'By Tour + Schedule',
            'day_schedules' => 'Day + Schedule Overrides',
        ],

        // =========================================================
        // [03] ALERTS
        // =========================================================
        'alerts' => [
            'global_info'        => '<strong>Global capacities:</strong> Define the base limit for each tour (all days and schedules).',
            'by_tour_info'       => '<strong>By Tour + Schedule:</strong> Capacity override specific to each schedule of each tour. These overrides take precedence over the tour’s global capacity.',
            'day_schedules_info' => '<strong>Day + Schedule:</strong> Highest priority override for a specific day and schedule. These are managed from the "Availability and Capacity" view.',
        ],

        // =========================================================
        // [04] TABLE HEADERS
        // =========================================================
        'tables' => [
            'global' => [
                'tour'     => 'Tour',
                'type'     => 'Type',
                'capacity' => 'Global Capacity',
                'level'    => 'Level',
            ],
            'by_tour' => [
                'schedule'    => 'Schedule',
                'capacity'    => 'Override Capacity',
                'level'       => 'Level',
                'no_schedules' => 'This tour has no assigned schedules',
            ],
            'day_schedules' => [
                'date'        => 'Date',
                'tour'        => 'Tour',
                'schedule'    => 'Schedule',
                'capacity'    => 'Capacity',
                'actions'     => 'Actions',
                'no_overrides' => 'There are no day + schedule overrides',
            ],
        ],

        // =========================================================
        // [05] BADGES / LABELS
        // =========================================================
        'badges' => [
            'base'      => 'Base',
            'override'  => 'Override',
            'global'    => 'Global',
            'blocked'   => 'BLOCKED',
            'unlimited' => '∞',
        ],

        // =========================================================
        // [06] BUTTONS
        // =========================================================
        'buttons' => [
            'save'   => 'Save',
            'delete' => 'Delete',
            'back'   => 'Back',
            'apply'  => 'Apply',
            'cancel' => 'Cancel',
        ],

        // =========================================================
        // [07] MESSAGES
        // =========================================================
        'messages' => [
            'empty_placeholder' => 'Empty = use global capacity (:capacity)',
            'deleted_confirm'   => 'Delete this override?',
            'no_day_overrides'  => 'There are no day + schedule overrides.',
        ],

        // =========================================================
        // [08] TOASTS (SweetAlert2)
        // =========================================================
        'toasts' => [
            'success_title' => 'Success',
            'error_title'   => 'Error',
        ],
    ],

];
