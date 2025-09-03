<?php

return [

    // =========================================================
    // [01] AVAILABILITY
    // =========================================================
    'availability' => [
    'fields' => [
        'tour'        => 'Tour',
        'date'        => 'Date',
        'start_time'  => 'Start time',
        'end_time'    => 'End time',
        'available'   => 'Available',
        'is_active'   => 'Active',
    ],

    'success' => [
        'created'     => 'Availability created successfully.',
        'updated'     => 'Availability updated successfully.',
        'deactivated' => 'Availability deactivated successfully.',
    ],

    'error' => [
        'create'     => 'Could not create the availability.',
        'update'     => 'Could not update the availability.',
        'deactivate' => 'Could not deactivate the availability.',
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
            'required_with' => 'The :attribute is required when the end time is present.',
        ],
        'end_time' => [
            'date_format'    => 'The :attribute must be in HH:MM (24h) format.',
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
        'tip'                => 'Tip: select rows and use a menu action.',
    ],

    'blocks' => [
        'am_tours'    => 'AM tours (tours starting before 12:00pm)',
        'pm_tours'    => 'PM tours (tours starting after 12:00pm)',
        'am_blocked'  => 'AM blocked',
        'pm_blocked'  => 'PM blocked',
        'empty_block' => 'No tours in this block.',
        'empty_am'    => 'No blocked in AM.',
        'empty_pm'    => 'No blocked in PM.',
        'no_data'     => 'No data for the selected filters.',
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
        'view_blocked_text'     => 'The view with blocked tours will open so you can unblock them.',
        'block_title'           => 'Block tour?',
        'block_html'            => 'It will block <b>:label</b> for the date <b>:day</b>.',
        'block_btn'             => 'Yes, block',
        'unblock_title'         => 'Unblock tour?',
        'unblock_html'          => 'It will unblock <b>:label</b> for the date <b>:day</b>.',
        'unblock_btn'           => 'Yes, unblock',
        'bulk_title'            => 'Confirm action',
        'bulk_items_html'       => 'Items to affect: <b>:count</b>.',
        'bulk_block_day_html'   => 'Block all available on <b>:day</b>',
        'bulk_block_block_html' => 'Block all available in the <b>:block</b> block on <b>:day</b>',
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
        'no_selection_text'  => 'Select at least one tour.',
        'no_changes_title'   => 'No changes',
        'no_changes_text'    => 'There are no applicable items.',
        'error_generic'      => 'Unable to complete the update.',
        'error_update'       => 'Could not update.',
    ],
],

];
