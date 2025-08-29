<?php

/*************************************************************
 *  TRANSLATION MODULE: TOURS
 *  File: resources/lang/en/m_tours.php
 *
 *  Table of Contents (sections & starting line)
 *  [01] COMMON           -> line 19
 *  [02] AMENITY          -> line 27
 *  [03] SCHEDULE         -> line 90
 *  [04] ITINERARY_ITEM   -> line 176
 *  [05] ITINERARY        -> line 239
 *  [06] LANGUAGE         -> line 302
 *  [07] TOUR             -> line 386
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
            'create_title'   => 'Create Amenity',
            'edit_title'     => 'Edit Amenity',
            'save'           => 'Save',
            'update'         => 'Update',
            'cancel'         => 'Cancel',
            'close'          => 'Close',
            'state'          => 'Status',
            'actions'        => 'Actions',
            'delete_forever' => 'Delete Permanently',

            'processing' => 'Processing...',
            'applying'   => 'Applying...',
            'deleting'   => 'Deleting...',

            'toggle_on'  => 'Enable amenity',
            'toggle_off' => 'Disable amenity',

            'toggle_confirm_on_title'  => 'Enable amenity?',
            'toggle_confirm_off_title' => 'Disable amenity?',
            'toggle_confirm_on_html'   => 'Amenity <b>:label</b> will be enabled.',
            'toggle_confirm_off_html'  => 'Amenity <b>:label</b> will be disabled.',

            'delete_confirm_title' => 'Delete permanently?',
            'delete_confirm_html'  => '<b>:label</b> will be deleted and cannot be undone.',

            'yes_continue' => 'Yes, continue',
            'yes_delete'   => 'Yes, delete',

            'item_this' => 'this amenity',
        ],

        'success' => [
            'created'     => 'Amenity created successfully.',
            'updated'     => 'Amenity updated successfully.',
            'activated'   => 'Amenity activated successfully.',
            'deactivated' => 'Amenity deactivated successfully.',
            'deleted'     => 'Amenity permanently deleted.',
        ],

        'error' => [
            'create' => 'Could not create the amenity.',
            'update' => 'Could not update the amenity.',
            'toggle' => 'Could not change the amenity status.',
            'delete' => 'Could not delete the amenity.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Invalid name',
                'required' => 'The :attribute field is required.',
                'string'   => 'The :attribute must be a string.',
                'max'      => 'The :attribute may not be greater than :max characters.',
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
            'max_capacity'   => 'Max. capacity',
            'active'         => 'Active',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ],

        'ui' => [
            'page_title'        => 'Tour Schedules',
            'page_heading'      => 'Schedule Management',

            'general_title'     => 'General Schedules',
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

            'toggle_global_title'     => 'Enable/Disable (global)',
            'toggle_global_on_title'  => 'Enable schedule (global)?',
            'toggle_global_off_title' => 'Disable schedule (global)?',
            'toggle_global_on_html'   => '<b>:label</b> will be enabled for all tours.',
            'toggle_global_off_html'  => '<b>:label</b> will be disabled for all tours.',

            'toggle_on_tour'          => 'Enable on this tour',
            'toggle_off_tour'         => 'Disable on this tour',
            'toggle_assign_on_title'  => 'Enable on this tour?',
            'toggle_assign_off_title' => 'Disable on this tour?',
            'toggle_assign_on_html'   => 'Assignment will be <b>active</b> for <b>:tour</b>.',
            'toggle_assign_off_html'  => 'Assignment will be <b>inactive</b> for <b>:tour</b>.',

            'detach_from_tour'     => 'Detach from tour',
            'detach_confirm_title' => 'Detach from tour?',
            'detach_confirm_html'  => 'The schedule will be <b>detached</b> from <b>:tour</b>.',

            'delete_forever'       => 'Delete (global)',
            'delete_confirm_title' => 'Delete permanently?',
            'delete_confirm_html'  => '<b>:label</b> will be deleted globally and cannot be undone.',

            'yes_continue' => 'Yes, continue',
            'yes_delete'   => 'Yes, delete',
            'yes_detach'   => 'Yes, detach',

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
            'missing_fields_text'  => 'Please check required fields (start, end, and capacity).',
            'could_not_save'       => 'Could not save',
        ],

        'success' => [
            'created'                => 'Schedule created successfully.',
            'updated'                => 'Schedule updated successfully.',
            'activated_global'       => 'Schedule activated successfully (global).',
            'deactivated_global'     => 'Schedule deactivated successfully (global).',
            'attached'               => 'Schedule assigned to tour.',
            'detached'               => 'Schedule detached from tour.',
            'assignment_activated'   => 'Assignment enabled for this tour.',
            'assignment_deactivated' => 'Assignment disabled for this tour.',
            'deleted'                => 'Schedule deleted successfully.',
        ],

        'error' => [
            'create'               => 'There was a problem creating the schedule.',
            'update'               => 'There was a problem updating the schedule.',
            'toggle'               => 'Could not change the global status of the schedule.',
            'attach'               => 'Could not assign the schedule to the tour.',
            'detach'               => 'Could not detach the schedule from the tour.',
            'assignment_toggle'    => 'Could not change the assignment status.',
            'not_assigned_to_tour' => 'The schedule is not assigned to this tour.',
            'delete'               => 'There was a problem deleting the schedule.',
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

            'toggle_on'  => 'Enable item',
            'toggle_off' => 'Disable item',

            'delete_forever'       => 'Delete permanently',
            'delete_confirm_title' => 'Delete permanently?',
            'delete_confirm_html'  => '<b>:label</b> will be deleted and cannot be undone.',
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
            'deleted'     => 'Item permanently deleted.',
        ],

        'error' => [
            'create' => 'Could not create the item.',
            'update' => 'Could not update the item.',
            'toggle' => 'Could not change the item status.',
            'delete' => 'Could not delete the item.',
        ],

        'validation' => [
            'title' => [
                'required' => 'The :attribute field is required.',
                'string'   => 'The :attribute must be a string.',
                'max'      => 'The :attribute may not be greater than :max characters.',
            ],
            'description' => [
                'required' => 'The :attribute field is required.',
                'string'   => 'The :attribute must be a string.',
                'max'      => 'The :attribute may not be greater than :max characters.',
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
            'page_heading'  => 'Itinerary & Item Management',
            'new_itinerary' => 'New Itinerary',

            'assign'        => 'Assign',
            'edit'          => 'Edit',
            'save'          => 'Save',
            'cancel'        => 'Cancel',
            'close'         => 'Close',
            'create_title'  => 'Create new itinerary',
            'create_button' => 'Create',

            'toggle_on'  => 'Enable itinerary',
            'toggle_off' => 'Disable itinerary',
            'toggle_confirm_on_title'  => 'Enable itinerary?',
            'toggle_confirm_off_title' => 'Disable itinerary?',
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
            'deleted'        => 'Itinerary permanently deleted.',
            'items_assigned' => 'Items assigned successfully.',
        ],

        'error' => [
            'create'  => 'Could not create the itinerary.',
            'update'  => 'Could not update the itinerary.',
            'toggle'  => 'Could not change the itinerary status.',
            'delete'  => 'Could not delete the itinerary.',
            'assign'  => 'Could not assign items.',
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
            'delete_forever' => 'Delete Permanently',

            'processing'   => 'Processing...',
            'saving'       => 'Saving...',
            'activating'   => 'Activating...',
            'deactivating' => 'Deactivating...',
            'deleting'     => 'Deleting...',

            'toggle_on'  => 'Enable language',
            'toggle_off' => 'Disable language',
            'toggle_confirm_on_title'  => 'Enable language?',
            'toggle_confirm_off_title' => 'Disable language?',
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
            'create' => 'Could not create the language.',
            'update' => 'Could not update the language.',
            'toggle' => 'Could not change the language status.',
            'delete' => 'Could not delete the language.',
            'save'   => 'Could not save',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Invalid name',
                'required' => 'The language name is required.',
                'string'   => 'The :attribute must be a string.',
                'max'      => 'The :attribute may not be greater than :max characters.',
                'unique'   => 'A language with this name already exists.',
            ],
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'fields' => [
            'id'           => 'ID',
            'name'         => 'Name',
            'overview'     => 'Overview',
            'amenities'    => 'Amenities',
            'exclusions'   => 'Exclusions',
            'itinerary'    => 'Itinerary',
            'languages'    => 'Languages',
            'schedules'    => 'Schedules',
            'adult_price'  => 'Adult price',
            'kid_price'    => 'Child price',
            'length_hours' => 'Duration (h)',
            'max_capacity' => 'Max. capacity',
            'type'         => 'Type',
            'viator_code'  => 'Viator code',
            'status'       => 'Status',
            'actions'      => 'Actions',
        ],
        'table' => [
            'id'           => 'ID',
            'name'         => 'Name',
            'overview'     => 'Overview',
            'amenities'    => 'Amenities',
            'exclusions'   => 'Exclusions',
            'itinerary'    => 'Itinerary',
            'languages'    => 'Languages',
            'schedules'    => 'Schedules',
            'adult_price'  => 'Adult price',
            'kid_price'    => 'Child price',
            'length_hours' => 'Duration (h)',
            'max_capacity' => 'Max. capacity',
            'type'         => 'Type',
            'viator_code'  => 'Viator code',
            'status'       => 'Status',
            'actions'      => 'Actions',
        ],
        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ],
        'ui' => [
            'page_title'   => 'Tours',
            'page_heading' => 'Tour Management',

            'font_decrease_title' => 'Decrease font size',
            'font_increase_title' => 'Increase font size',

            'see_more' => 'See more',
            'see_less' => 'See less',

            'none' => [
                'amenities'       => 'No amenities',
                'exclusions'      => 'No exclusions',
                'languages'       => 'No languages',
                'itinerary'       => 'No itinerary',
                'itinerary_items' => '(No items)',
                'schedules'       => 'No schedules',
            ],

            'toggle_on'         => 'Enable',
            'toggle_off'        => 'Disable',
            'toggle_on_title'   => 'Do you want to activate this tour?',
            'toggle_off_title'  => 'Do you want to deactivate this tour?',
            'toggle_on_button'  => 'Yes, activate',
            'toggle_off_button' => 'Yes, deactivate',

            'confirm_title'   => 'Confirmation',
            'confirm_text'    => 'Confirm action?',
            'yes_confirm'     => 'Yes, confirm',
            'cancel'          => 'Cancel',

            'load_more'       => 'Load more',
            'loading'         => 'Loading...',
            'load_more_error' => 'Could not load more',
        ],
        'success' => [
            'created'     => 'Tour created successfully.',
            'updated'     => 'Tour updated successfully.',
            'activated'   => 'Tour activated successfully.',
            'deactivated' => 'Tour deactivated successfully.',
        ],
        'error' => [
            'create' => 'There was a problem creating the tour.',
            'update' => 'There was a problem updating the tour.',
            'toggle' => 'There was a problem changing the tour status.',
        ],
    ],
    // =========================================================
    // [08] IMAGES
    // =========================================================
    'image' => [
    'limit_reached_title' => 'Limit reached',
    'limit_reached_text'  => 'Image limit reached for this tour.',
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

    'ui' => [
        'page_title_pick'   => 'Tour Images — Choose tour',
        'page_heading'      => 'Tour Images',
        'choose_tour'       => 'Choose tour',
        'search_placeholder'=> 'Search by ID or name…',
        'search_button'     => 'Search',
        'no_results'        => 'No tours found.',
        'manage_images'     => 'Manage images',
        'cover_alt'         => 'Cover',
    ],
],

];
