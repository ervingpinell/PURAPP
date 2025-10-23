<?php

/*************************************************************
 *  TRANSLATION MODULE: TOURS
 *  File: resources/lang/en/m_tours.php
 *
 *  Index (section and starting line)
 *  [01] COMMON           -> line 23
 *  [02] AMENITY          -> line 31
 *  [03] SCHEDULE         -> line 106
 *  [04] ITINERARY_ITEM   -> line 218
 *  [05] ITINERARY        -> line 288
 *  [06] LANGUAGE         -> line 364
 *  [07] TOUR             -> line 454
 *  [08] IMAGES           -> line 578
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title' => 'Success',
        'error_title'   => 'Error',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'fields' => [
            'name' => 'Name',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ],

        'ui' => [
            'page_title'    => 'Amenities',
            'page_heading'  => 'Amenities Management',
            'list_title'    => 'Amenities List',

            'add'            => 'Add Amenity',
            'create_title'   => 'Register Amenity',
            'edit_title'     => 'Edit Amenity',
            'save'           => 'Save',
            'update'         => 'Update',
            'cancel'         => 'Cancel',
            'close'          => 'Close',
            'state'          => 'Status',
            'actions'        => 'Actions',
            'delete_forever' => 'Delete permanently',

            'processing' => 'Processing...',
            'applying'   => 'Applying...',
            'deleting'   => 'Deleting...',

            'toggle_on'  => 'Activate amenity',
            'toggle_off' => 'Deactivate amenity',

            'toggle_confirm_on_title'  => 'Activate amenity?',
            'toggle_confirm_off_title' => 'Deactivate amenity?',
            'toggle_confirm_on_html'   => 'Amenity <b>:label</b> will be active.',
            'toggle_confirm_off_html'  => 'Amenity <b>:label</b> will be inactive.',

            'delete_confirm_title' => 'Delete permanently?',
            'delete_confirm_html'  => '<b>:label</b> will be deleted and you won’t be able to undo this.',

            'yes_continue' => 'Yes, continue',
            'yes_delete'   => 'Yes, delete',

            'item_this' => 'this amenity',
        ],

        'success' => [
            'created'     => 'Amenity created successfully.',
            'updated'     => 'Amenity updated successfully.',
            'activated'   => 'Amenity activated successfully.',
            'deactivated' => 'Amenity deactivated successfully.',
            'deleted'     => 'Amenity deleted permanently.',
        ],

        'error' => [
            'create' => 'The amenity could not be created.',
            'update' => 'The amenity could not be updated.',
            'toggle' => 'The amenity status could not be changed.',
            'delete' => 'The amenity could not be deleted.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Invalid name',
                'required' => 'The :attribute is required.',
                'string'   => 'The :attribute must be a string.',
                'max'      => 'The :attribute may not exceed :max characters.',
            ],
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'fields' => [
            'start_time'     => 'Start',
            'end_time'       => 'End',
            'label'          => 'Label',
            'label_optional' => 'Label (optional)',
            'max_capacity'   => 'Max capacity',
            'active'         => 'Active',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ],

        'ui' => [
            'page_title'        => 'Tour Schedules',
            'page_heading'      => 'Schedule Management',

            'general_title'     => 'General schedules',
            'new_schedule'      => 'New schedule',
            'new_general_title' => 'New general schedule',
            'new'               => 'New',
            'edit_schedule'     => 'Edit schedule',
            'edit_global'       => 'Edit (global)',

            'assign_existing'    => 'Assign existing',
            'assign_to_tour'     => 'Assign schedule to ":tour"',
            'select_schedule'    => 'Select a schedule',
            'choose'             => 'Choose',
            'assign'             => 'Assign',
            'new_for_tour_title' => 'New schedule for ":tour"',

            'time_range'        => 'Time range',
            'state'             => 'Status',
            'actions'           => 'Actions',
            'schedule_state'    => 'Schedule',
            'assignment_state'  => 'Assignment',
            'no_general'        => 'No general schedules.',
            'no_tour_schedules' => 'This tour has no schedules yet.',
            'no_label'          => 'No label',
            'assigned_count'    => 'assigned schedule(s)',

            'toggle_global_title'     => 'Activate/Deactivate (global)',
            'toggle_global_on_title'  => 'Activate schedule (global)?',
            'toggle_global_off_title' => 'Deactivate schedule (global)?',
            'toggle_global_on_html'   => '<b>:label</b> will be activated for all tours.',
            'toggle_global_off_html'  => '<b>:label</b> will be deactivated for all tours.',

            'toggle_on_tour'          => 'Activate on this tour',
            'toggle_off_tour'         => 'Deactivate on this tour',
            'toggle_assign_on_title'  => 'Activate on this tour?',
            'toggle_assign_off_title' => 'Deactivate on this tour?',
            'toggle_assign_on_html'   => 'The assignment will be <b>active</b> for <b>:tour</b>.',
            'toggle_assign_off_html'  => 'The assignment will be <b>inactive</b> for <b>:tour</b>.',

            'detach_from_tour'     => 'Remove from tour',
            'detach_confirm_title' => 'Remove from tour?',
            'detach_confirm_html'  => 'The schedule will be <b>unassigned</b> from <b>:tour</b>.',

            'delete_forever'       => 'Delete (global)',
            'delete_confirm_title' => 'Delete permanently?',
            'delete_confirm_html'  => '<b>:label</b> (global) will be deleted and you won’t be able to undo this.',

            'yes_continue' => 'Yes, continue',
            'yes_delete'   => 'Yes, delete',
            'yes_detach'   => 'Yes, remove',

            'this_schedule' => 'this schedule',
            'this_tour'     => 'this tour',

            'processing'     => 'Processing...',
            'applying'       => 'Applying...',
            'deleting'       => 'Deleting...',
            'removing'       => 'Removing...',
            'saving_changes' => 'Saving changes...',
            'save'           => 'Save',
            'save_changes'   => 'Save changes',
            'cancel'         => 'Cancel',

            'missing_fields_title' => 'Missing data',
            'missing_fields_text'  => 'Check required fields (start, end, and capacity).',
            'could_not_save'       => 'Could not save',
        ],

        'success' => [
            'created'                => 'Schedule created successfully.',
            'updated'                => 'Schedule updated successfully.',
            'activated_global'       => 'Schedule activated successfully (global).',
            'deactivated_global'     => 'Schedule deactivated successfully (global).',
            'attached'               => 'Schedule assigned to the tour.',
            'detached'               => 'Schedule removed from the tour.',
            'assignment_activated'   => 'Assignment activated for this tour.',
            'assignment_deactivated' => 'Assignment deactivated for this tour.',
            'deleted'                => 'Schedule deleted successfully.',
        ],

        'error' => [
            'create'               => 'There was an issue creating the schedule.',
            'update'               => 'There was an issue updating the schedule.',
            'toggle'               => 'Could not change the global status of the schedule.',
            'attach'               => 'Could not assign the schedule to the tour.',
            'detach'               => 'Could not unassign the schedule from the tour.',
            'assignment_toggle'    => 'Could not change the assignment status.',
            'not_assigned_to_tour' => 'The schedule is not assigned to this tour.',
            'delete'               => 'There was an issue deleting the schedule.',
        ],
    ],

    // =========================================================
    // [04] ITINERARY_ITEM
    // =========================================================
    'itinerary_item' => [
        'fields' => [
            'title'       => 'Title',
            'description' => 'Description',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ],

        'ui' => [
            'list_title'    => 'Itinerary Items',
            'add_item'      => 'Add Item',
            'register_item' => 'Register Item',
            'edit_item'     => 'Edit Item',
            'save'          => 'Save',
            'update'        => 'Update',
            'cancel'        => 'Cancel',
            'state'         => 'Status',
            'actions'       => 'Actions',
            'see_more'      => 'See more',
            'see_less'      => 'See less',

            'toggle_on'  => 'Activate item',
            'toggle_off' => 'Deactivate item',

            'delete_forever'       => 'Delete permanently',
            'delete_confirm_title' => 'Delete permanently?',
            'delete_confirm_html'  => '<b>:label</b> will be deleted and you won’t be able to undo this.',
            'yes_delete'           => 'Yes, delete',
            'item_this'            => 'this item',

            'processing' => 'Processing...',
            'applying'   => 'Applying...',
            'deleting'   => 'Deleting...',
        ],

        'success' => [
            'created'     => 'Itinerary item created successfully.',
            'updated'     => 'Item updated successfully.',
            'activated'   => 'Item activated successfully.',
            'deactivated' => 'Item deactivated successfully.',
            'deleted'     => 'Item deleted permanently.',
        ],

        'error' => [
            'create' => 'The item could not be created.',
            'update' => 'The item could not be updated.',
            'toggle' => 'The item status could not be changed.',
            'delete' => 'The item could not be deleted.',
        ],

        'validation' => [
            'title' => [
                'required' => 'The :attribute is required.',
                'string'   => 'The :attribute must be a string.',
                'max'      => 'The :attribute may not exceed :max characters.',
            ],
            'description' => [
                'required' => 'The :attribute is required.',
                'string'   => 'The :attribute must be a string.',
                'max'      => 'The :attribute may not exceed :max characters.',
            ],
        ],
    ],

    // =========================================================
    // [05] ITINERARY
    // =========================================================
    'itinerary' => [
        'fields' => [
            'name'                 => 'Itinerary name',
            'description'          => 'Description',
            'description_optional' => 'Description (optional)',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ],

        'ui' => [
            'page_title'    => 'Itineraries & Items',
            'page_heading'  => 'Itineraries and Item Management',
            'new_itinerary' => 'New Itinerary',

            'assign'        => 'Assign',
            'edit'          => 'Edit',
            'save'          => 'Save',
            'cancel'        => 'Cancel',
            'close'         => 'Close',
            'create_title'  => 'Create new itinerary',
            'create_button' => 'Create',

            'toggle_on'  => 'Activate itinerary',
            'toggle_off' => 'Deactivate itinerary',
            'toggle_confirm_on_title'  => 'Activate itinerary?',
            'toggle_confirm_off_title' => 'Deactivate itinerary?',
            'toggle_confirm_on_html'   => 'Itinerary <b>:label</b> will be <b>active</b>.',
            'toggle_confirm_off_html'  => 'Itinerary <b>:label</b> will be <b>inactive</b>.',
            'yes_continue' => 'Yes, continue',

            'assign_title'          => 'Assign items to :name',
            'drag_hint'             => 'Drag and drop items to set the order.',
            'drag_handle'           => 'Drag to reorder',
            'select_one_title'      => 'Select at least one item',
            'select_one_text'       => 'Please select at least one item to continue.',
            'assign_confirm_title'  => 'Assign selected items?',
            'assign_confirm_button' => 'Yes, assign',
            'assigning'             => 'Assigning...',

            'no_items_assigned'       => 'No items assigned to this itinerary.',
            'itinerary_this'          => 'this itinerary',
            'processing'              => 'Processing...',
            'saving'                  => 'Saving...',
            'activating'              => 'Activating...',
            'deactivating'            => 'Deactivating...',
            'applying'                => 'Applying...',
            'deleting'                => 'Deleting...',
            'flash_success_title'     => 'Success',
            'flash_error_title'       => 'Error',
            'validation_failed_title' => 'Could not process',
        ],

        'success' => [
            'created'        => 'Itinerary created successfully.',
            'updated'        => 'Itinerary updated successfully.',
            'activated'      => 'Itinerary activated successfully.',
            'deactivated'    => 'Itinerary deactivated successfully.',
            'deleted'        => 'Itinerary deleted permanently.',
            'items_assigned' => 'Items assigned successfully.',
        ],

        'error' => [
            'create'  => 'The itinerary could not be created.',
            'update'  => 'The itinerary could not be updated.',
            'toggle'  => 'The itinerary status could not be changed.',
            'delete'  => 'The itinerary could not be deleted.',
            'assign'  => 'Items could not be assigned.',
        ],
    ],

    // =========================================================
    // [06] LANGUAGE
    // =========================================================
    'language' => [
        'fields' => [
            'name' => 'Language',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ],

        'ui' => [
            'page_title'   => 'Tour Languages',
            'page_heading' => 'Language Management',
            'list_title'   => 'Language List',

            'table' => [
                'id'      => 'ID',
                'name'    => 'Language',
                'state'   => 'Status',
                'actions' => 'Actions',
            ],

            'add'            => 'Add Language',
            'create_title'   => 'Register Language',
            'edit_title'     => 'Edit Language',
            'save'           => 'Save',
            'update'         => 'Update',
            'cancel'         => 'Cancel',
            'close'          => 'Close',
            'actions'        => 'Actions',
            'delete_forever' => 'Delete permanently',

            'processing'   => 'Processing...',
            'saving'       => 'Saving...',
            'activating'   => 'Activating...',
            'deactivating' => 'Deactivating...',
            'deleting'     => 'Deleting...',

            'toggle_on'  => 'Activate language',
            'toggle_off' => 'Deactivate language',
            'toggle_confirm_on_title'  => 'Activate language?',
            'toggle_confirm_off_title' => 'Deactivate language?',
            'toggle_confirm_on_html'   => 'Language <b>:label</b> will be <b>active</b>.',
            'toggle_confirm_off_html'  => 'Language <b>:label</b> will be <b>inactive</b>.',
            'edit_confirm_title'       => 'Save changes?',
            'edit_confirm_button'      => 'Yes, save',

            'yes_continue' => 'Yes, continue',
            'yes_delete'   => 'Yes, delete',
            'item_this'    => 'this language',

            'flash' => [
                'activated_title'   => 'Language Activated',
                'deactivated_title' => 'Language Deactivated',
                'updated_title'     => 'Language Updated',
                'created_title'     => 'Language Registered',
                'deleted_title'     => 'Language Deleted',
            ],
        ],

        'success' => [
            'created'     => 'Language created successfully.',
            'updated'     => 'Language updated successfully.',
            'activated'   => 'Language activated successfully.',
            'deactivated' => 'Language deactivated successfully.',
            'deleted'     => 'Language deleted successfully.',
        ],

        'error' => [
            'create' => 'The language could not be created.',
            'update' => 'The language could not be updated.',
            'toggle' => 'The language status could not be changed.',
            'delete' => 'The language could not be deleted.',
            'save'   => 'Could not save',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Invalid name',
                'required' => 'The language name is required.',
                'string'   => 'The :attribute must be a string.',
                'max'      => 'The :attribute may not exceed :max characters.',
                'unique'   => 'A language with that name already exists.',
            ],
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'title' => 'Tours',

        'fields' => [
            'id'            => 'ID',
            'name'          => 'Name',
            'overview'      => 'Overview',
            'amenities'     => 'Amenities',
            'exclusions'    => 'Exclusions',
            'itinerary'     => 'Itinerary',
            'languages'     => 'Languages',
            'schedules'     => 'Schedules',
            'adult_price'   => 'Adult Price',
            'kid_price'     => 'Child Price',
            'length_hours'  => 'Duration (hours)',
            'max_capacity'  => 'Max capacity',
            'type'          => 'Tour Type',
            'viator_code'   => 'Viator Code',
            'status'        => 'Status',
            'actions'       => 'Actions',
        ],

        'table' => [
            'id'            => 'ID',
            'name'          => 'Name',
            'overview'      => 'Overview',
            'amenities'     => 'Amenities',
            'exclusions'    => 'Exclusions',
            'itinerary'     => 'Itinerary',
            'languages'     => 'Languages',
            'schedules'     => 'Schedules',
            'adult_price'   => 'Adult Price',
            'kid_price'     => 'Child Price',
            'length_hours'  => 'Duration (h)',
            'max_capacity'  => 'Max Cap.',
            'type'          => 'Type',
            'viator_code'   => 'Viator Code',
            'status'        => 'Status',
            'actions'       => 'Actions',
            'slug'          => 'URL',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
            'archived' => 'Archived',
        ],

        'success' => [
            'created'     => 'Tour created successfully.',
            'updated'     => 'Tour updated successfully.',
            'deleted'     => 'Tour deleted.',
            'toggled'     => 'Tour status updated.',
            'activated'   => 'Tour activated successfully.',
            'deactivated' => 'Tour deactivated successfully.',
            // NEW
            'archived'    => 'Tour archived successfully.',
            'restored'    => 'Tour restored successfully.',
            'purged'      => 'Tour permanently deleted.',
        ],

        'error' => [
            'create'    => 'There was a problem creating the tour.',
            'update'    => 'There was a problem updating the tour.',
            'delete'    => 'There was a problem deleting the tour.',
            'toggle'    => 'There was a problem changing the tour status.',
            'not_found' => 'The tour does not exist.',
            // NEW
            'restore'            => 'The tour could not be restored.',
            'purge'              => 'The tour could not be permanently deleted.',
            'purge_has_bookings' => 'Cannot permanently delete: this tour has bookings.',
        ],

        'ui' => [
            'page_title'       => 'Tour Management',
            'page_heading'     => 'Tour Management',
            'create_title'     => 'Create Tour',
            'edit_title'       => 'Edit Tour',
            'delete_title'     => 'Delete Tour',
            'cancel'           => 'Cancel',
            'save'             => 'Save',
            'update'           => 'Update',
            'delete_confirm'   => 'Delete this tour?',
            'toggle_on'        => 'Activate',
            'toggle_off'       => 'Deactivate',
            'toggle_on_title'  => 'Activate tour?',
            'toggle_off_title' => 'Deactivate tour?',
            'toggle_on_button' => 'Yes, activate',
            'toggle_off_button'=> 'Yes, deactivate',
            'see_more'         => 'See more',
            'see_less'         => 'Hide',
            'load_more'        => 'Load more',
            'loading'          => 'Loading...',
            'load_more_error'  => 'Could not load more tours.',
            'confirm_title'    => 'Confirmation',
            'confirm_text'     => 'Do you want to confirm this action?',
            'yes_confirm'      => 'Yes, confirm',
            'no_confirm'       => 'No, cancel',
            'add_tour'         => 'Add Tour',
            'edit_tour'        => 'Edit Tour',
            'delete_tour'      => 'Delete Tour',
            'toggle_tour'      => 'Activate/Deactivate Tour',
            'view_cart'        => 'View Cart',
            'add_to_cart'      => 'Add to Cart',
            'slug_help'        => 'Unique URL slug for the tour (no spaces or special characters).',
            'generate_auto'       => 'Generate automatically',
            'slug_preview_label'  => 'Preview',
            'saved'               => 'Saved successfully.',
            'available_languages'    => 'Available languages',
            'default_capacity'       => 'Default capacity',
            'create_new_schedules'   => 'Create new schedules',
            'multiple_hint_ctrl_cmd' => 'Hold CTRL/CMD to select multiple',
            'use_existing_schedules' => 'Use existing schedules',
            'add_schedule'           => 'Add schedule',
            'schedules_title'        => 'Tour schedules',
            'amenities_included'     => 'Included amenities',
            'amenities_excluded'     => 'Excluded amenities',
            'color'                  => 'Tour color',
            'remove'                 => 'Remove',
            'choose_itinerary'       => 'Choose itinerary',
            'select_type'            => 'Select type',
            'empty_means_default'    => 'Default',
            'actives'                 => 'Actives',
            'inactives'               => 'Inactives',
            'archived'                => 'Trash',
            'all'                     => 'All',

            'none' => [
                'amenities'       => 'No amenities',
                'exclusions'      => 'No exclusions',
                'itinerary'       => 'No itinerary',
                'itinerary_items' => 'No items',
                'languages'       => 'No languages',
                'schedules'       => 'No schedules',
            ],

            // NEW: archive / restore / purge
            'archive' => 'Archive',
            'restore' => 'Restore',
            'purge'   => 'Permanently delete',

            'confirm_archive_title' => 'Archive tour?',
            'confirm_archive_text'  => 'The tour will be disabled for new bookings, but existing bookings will be preserved.',
            'confirm_purge_title'   => 'Permanently delete',
            'confirm_purge_text'    => 'This action is irreversible and only allowed if the tour never had bookings.',

            // NEW: state filters
            'filters' => [
                'active'   => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
                'all'      => 'All',
            ],

            // NEW: font toolbar (used in tourlist.blade.php)
            'font_decrease_title' => 'Decrease font size',
            'font_increase_title' => 'Increase font size',
        ],
    ],

    // =========================================================
    // [08] IMAGES
    // =========================================================
    'image' => [

        'limit_reached_title' => 'Limit reached',
        'limit_reached_text'  => 'The image limit for this tour has been reached.',
        'upload_success'      => 'Images uploaded successfully.',
        'upload_none'         => 'No images were uploaded.',
        'upload_truncated'    => 'Some files were skipped due to the per-tour limit.',
        'done'                => 'Done',
        'notice'              => 'Notice',
        'saved'               => 'Saved',
        'caption_updated'     => 'Caption updated successfully.',
        'deleted'             => 'Deleted',
        'image_removed'       => 'Image removed successfully.',
        'invalid_order'       => 'Invalid order payload.',
        'nothing_to_reorder'  => 'Nothing to reorder.',
        'order_saved'         => 'Order saved.',
        'cover_updated_title' => 'Cover updated',
        'cover_updated_text'  => 'This image is now the cover.',
        'deleting'            => 'Deleting...',

        'ui' => [
            'page_title_pick'     => 'Tour Images — Choose Tour',
            'page_heading'        => 'Tour Images',
            'choose_tour'         => 'Choose tour',
            'search_placeholder'  => 'Search by ID or name…',
            'search_button'       => 'Search',
            'no_results'          => 'No tours found.',
            'manage_images'       => 'Manage images',
            'cover_alt'           => 'Cover',
            'images_label'        => 'images',
            'upload_btn'          => 'Upload',
            'caption_placeholder' => 'Caption (optional)',
            'set_cover_btn'       => 'Set as cover',
            'no_images'           => 'No images yet for this tour.',
            'delete_btn'          => 'Delete',
            'show_btn'            => 'Show',
            'close_btn'           => 'Close',
            'preview_title'       => 'Image Preview',

            'error_title'         => 'Error',
            'warning_title'       => 'Warning',
            'success_title'       => 'Success',
            'cancel_btn'          => 'Cancel',
            'confirm_delete_title'=> 'Delete this image?',
            'confirm_delete_text' => 'This action cannot be undone.',
        ],

        'errors' => [
            'validation'     => 'The submitted data is not valid.',
            'upload_generic' => 'Some images could not be uploaded.',
            'update_caption' => 'The caption could not be updated.',
            'delete'         => 'The image could not be deleted.',
            'reorder'        => 'The order could not be saved.',
            'set_cover'      => 'The cover could not be set.',
            'load_list'      => 'The list could not be loaded.',
            'too_large'      => 'The file exceeds the maximum allowed size. Please try a smaller image.',
        ],
    ],

];
