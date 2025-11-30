<?php

/*************************************************************
 *  TRANSLATION MODULE: TOURS
 *  File: resources/lang/en/m_tours.php
 *
 *  Index (section and start line)
 *  [01] COMMON           -> line 23
 *  [02] AMENITY          -> line 31
 *  [03] SCHEDULE         -> line 106
 *  [04] ITINERARY_ITEM   -> line 218
 *  [05] ITINERARY        -> line 288
 *  [06] LANGUAGE         -> line 364
 *  [07] TOUR             -> line 453
 *  [08] IMAGES           -> line 579
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'optional' => 'Optional',
        'success_title' => 'Success',
        'error_title'   => 'Error',
        'people' => 'people',
        'hours' => 'hours',
        'success' => 'Success',
        'error' => 'Error',
        'cancel' => 'Cancel',
        'confirm_delete' => 'Yes, delete',
        'unspecified' => 'Unspecified',
        'no_description' => 'No description',
        'required_fields_title' => 'Required fields',
        'required_fields_text' => 'Please complete the required fields: Name and Maximum Capacity.',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'notice' => 'Notice',
        'na'    => 'Not configured',
        'create' => 'Create',
        'previous' => 'Back',
        'info'               => 'Information',
        'close'              => 'Close',
        'save'              => 'Save',
        'required'           => 'This field is required.',
        'add'                => 'Add',
        'translating'        => 'Translating...',
        'error_translating'  => 'Could not translate the text.',
        'confirm' => 'Confirm',
        'yes' => 'Yes',
        'form_errors_title' => 'Please correct the following errors:',
        'delete' => 'Delete',
        'delete_all' => 'Delete All',
        'actions' => 'Actions',
        'updated_at' => 'Last Updated',
        'not_set' => 'Not specified',
        'error_deleting' => 'An error occurred while deleting. Please try again.',
        'error_saving' => 'An error occurred while saving. Please try again.',
        'crud_go_to_index' => 'Manage :element',
        'validation_title' => 'There are validation errors',
        'ok'               => 'OK',
        'confirm_delete_title' => 'Delete this item?',
        'confirm_delete_text' => 'This action cannot be undone.',
        'saving' => 'Saving...',
        'network_error' => 'Network error',
        'custom' => 'Custom',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'singular' => 'amenity',
        'plural'   => 'amenities',

        'fields' => [
            'name' => 'Name',
            'icon' => 'Icon (FontAwesome)',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ],

        'ui' => [
            'page_title'    => 'Amenities',
            'page_heading'  => 'Amenities Management',
            'list_title'    => 'Amenity List',

            'add'            => 'Add Amenity',
            'create_title'   => 'Register Amenity',
            'edit_title'     => 'Edit Amenity',
            'save'           => 'Save',
            'update'         => 'Update',
            'cancel'         => 'Cancel',
            'close'          => 'Close',
            'state'          => 'Status',
            'actions'        => 'Actions',
            'delete_forever' => 'Delete forever',

            'processing' => 'Processing...',
            'applying'   => 'Applying...',
            'deleting'   => 'Deleting...',

            'toggle_on'  => 'Activate amenity',
            'toggle_off' => 'Deactivate amenity',

            'toggle_confirm_on_title'  => 'Activate amenity?',
            'toggle_confirm_off_title' => 'Deactivate amenity?',
            'toggle_confirm_on_html'   => 'The amenity <b>:label</b> will be activated.',
            'toggle_confirm_off_html'  => 'The amenity <b>:label</b> will be deactivated.',

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
            'included_required' => 'You must select at least one included amenity.',
            'name' => [
                'title'    => 'Invalid name',
                'required' => ':attribute is required.',
                'string'   => ':attribute must be a text string.',
                'max'      => ':attribute may not exceed :max characters.',
            ],
        ],

        'hints' => [
            'fontawesome' => 'Use FontAwesome classes, for example: "fas fa-check".',
        ],

        'quick_create' => [
            'button'           => 'New amenity',
            'title'            => 'Quick Create Amenity',
            'name_label'       => 'Amenity name',
            'icon_label'       => 'Icon (optional)',
            'icon_placeholder' => 'Ex: fas fa-utensils',
            'icon_help'        => 'Use a Font Awesome icon class or leave blank.',
            'save'             => 'Save amenity',
            'cancel'           => 'Cancel',
            'saving'           => 'Saving...',
            'error_generic'    => 'Could not create the amenity. Try again.',
            'go_to_index'      => 'View all',
            'go_to_index_title' => 'Open the full amenities list',
            'success_title'    => 'Amenity created',
            'success_text'     => 'The amenity was added to the tour list.',
            'error_title'      => 'Error creating amenity',
            'error_duplicate'  => 'An amenity with that name already exists.',
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'plural'       => 'Schedules',
        'singular'     => 'Schedule',

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

            'time_range'        => 'Schedule',
            'state'             => 'Status',
            'actions'           => 'Actions',
            'schedule_state'    => 'Schedule',
            'assignment_state'  => 'Assignment',
            'no_general'        => 'No general schedules available.',
            'no_tour_schedules' => 'This tour has no schedules yet.',
            'no_label'          => 'No label',
            'assigned_count'    => 'assigned schedule(s)',

            'toggle_global_title'     => 'Activate/Deactivate (global)',
            'toggle_global_on_title'  => 'Activate schedule globally?',
            'toggle_global_off_title' => 'Deactivate schedule globally?',
            'toggle_global_on_html'   => '<b>:label</b> will be activated for all tours.',
            'toggle_global_off_html'  => '<b>:label</b> will be deactivated for all tours.',

            'toggle_on_tour'          => 'Activate on this tour',
            'toggle_off_tour'         => 'Deactivate on this tour',
            'toggle_assign_on_title'  => 'Activate on this tour?',
            'toggle_assign_off_title' => 'Deactivate on this tour?',
            'toggle_assign_on_html'   => 'Assignment will be <b>active</b> for <b>:tour</b>.',
            'toggle_assign_off_html'  => 'Assignment will be <b>inactive</b> for <b>:tour</b>.',

            'detach_from_tour'     => 'Remove from tour',
            'detach_confirm_title' => 'Remove from tour?',
            'detach_confirm_html'  => 'The schedule will be <b>detached</b> from <b>:tour</b>.',

            'delete_forever'       => 'Delete (global)',
            'delete_confirm_title' => 'Delete permanently?',
            'delete_confirm_html'  => '<b>:label</b> (global) will be deleted permanently.',

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
            'base_capacity_tour'   => 'Base tour capacity:',
            'capacity_not_defined' => 'Not defined',
            'capacity_optional'    => 'Capacity (optional)',
            'capacity_placeholder_with_value' => 'Ex: :capacity',
            'capacity_placeholder_generic'   => 'Use tour capacity',
            'capacity_hint_with_value'       => 'Leave empty → :capacity',
            'capacity_hint_generic'          => 'Leave empty → tour capacity',
            'tip_label'                      => 'Tip:',
            'capacity_tip'                   => 'You may leave capacity empty to use the general tour capacity (:capacity).',

            'new_schedule_for_tour'            => 'New schedule',
            'modal_new_for_tour_title'         => 'Create schedule for :tour',
            'modal_save'                       => 'Save schedule',
            'modal_cancel'                     => 'Cancel',
            'capacity_modal_info_with_value'   => 'The tour’s base capacity is :capacity. If left empty, this value will be used.',
            'capacity_modal_info_generic'      => 'If left empty, the tour’s general capacity will be used when defined.',
        ],

        'success' => [
            'created'                => 'Schedule created successfully.',
            'updated'                => 'Schedule updated successfully.',
            'activated_global'       => 'Schedule globally activated.',
            'deactivated_global'     => 'Schedule globally deactivated.',
            'attached'               => 'Schedule assigned to the tour.',
            'detached'               => 'Schedule successfully removed from the tour.',
            'assignment_activated'   => 'Assignment activated for this tour.',
            'assignment_deactivated' => 'Assignment deactivated for this tour.',
            'deleted'                => 'Schedule deleted successfully.',
            'created_and_attached'   => 'Schedule created and assigned to the tour.',
        ],

        'error' => [
            'create'               => 'There was an error creating the schedule.',
            'update'               => 'There was an error updating the schedule.',
            'toggle'               => 'Could not change the global status.',
            'attach'               => 'Could not assign the schedule to the tour.',
            'detach'               => 'Could not detach the schedule from the tour.',
            'assignment_toggle'    => 'Could not change the assignment status.',
            'not_assigned_to_tour' => 'The schedule is not assigned to this tour.',
            'delete'               => 'There was an error deleting the schedule.',
        ],

        'placeholders' => [
            'morning' => 'Ex: Morning',
        ],

        'validation' => [
            'no_schedule_selected' => 'You must select at least one schedule.',
            'title' => 'Schedules Validation',
            'end_after_start' => 'End time must be after start time.',
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
            'assigned_items'       => 'Items assigned to the itinerary',
            'drag_to_order'        => 'Drag items to set the order.',
            'pool_hint'            => 'Check the available items you want to include in this itinerary.',
            'register_item_hint'   => 'Register new items if you need additional steps that don’t exist yet.',
            'translations_updated' => 'Translation updated',

            'toggle_on'  => 'Activate item',
            'toggle_off' => 'Deactivate item',

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
                'required' => ':attribute is required.',
                'string'   => ':attribute must be a text string.',
                'max'      => ':attribute may not exceed :max characters.',
            ],
            'description' => [
                'required' => ':attribute is required.',
                'string'   => ':attribute must be a text string.',
                'max'      => ':attribute may not exceed :max characters.',
            ],
        ],
    ],

    // =========================================================
    // [05] ITINERARY
    // =========================================================
    'itinerary' => [
        'plural'   => 'Itineraries',
        'singular' => 'Itinerary',

        'fields' => [
            'name'                 => 'Itinerary name',
            'description'          => 'Description',
            'description_optional' => 'Description (optional)',
            'items'                => 'Items',
            'item_title'           => 'Item title',
            'item_description'     => 'Item description',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ],

        'ui' => [
            'page_title'    => 'Itineraries and Items',
            'page_heading'  => 'Itineraries and Item Management',
            'new_itinerary' => 'New Itinerary',
            'select_or_create_hint' => 'Select an existing itinerary or create a new one for this tour.',
            'save_changes'          => 'Save the itinerary to apply the changes to the tour.',
            'select_existing'       => 'Select existing itinerary',
            'create_new'            => 'Create new itinerary',
            'add_item'              => 'Add item',
            'min_one_item'          => 'There must be at least one item in the itinerary.',
            'cannot_delete_item'    => 'Cannot be deleted',
            'item_added'            => 'Item added',
            'item_added_success'    => 'The item was successfully added to the itinerary.',
            'error_creating_item'   => 'Validation error while creating the item.',
            'translations_updated' => 'Translation updated',

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
            'toggle_confirm_on_html'   => 'The itinerary <b>:label</b> will be <b>active</b>.',
            'toggle_confirm_off_html'  => 'The itinerary <b>:label</b> will be <b>inactive</b>.',
            'yes_continue' => 'Yes, continue',

            'assign_title'          => 'Assign items to :name',
            'drag_hint'             => 'Drag and drop items to set the order.',
            'drag_handle'           => 'Drag to reorder',
            'select_one_title'      => 'You must select at least one item',
            'select_one_text'       => 'Please select at least one item to continue.',
            'assign_confirm_title'  => 'Assign selected items?',
            'assign_confirm_button' => 'Yes, assign',
            'assigning'             => 'Assigning...',

            'no_items_assigned'       => 'No items are assigned to this itinerary.',
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
            'go_to_crud'              => 'Go to Module',
        ],

        'modal' => [
            'create_itinerary' => 'Create itinerary',
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
            'assign'  => 'Could not assign the items.',
        ],

        'validation' => [
            'name_required'  => 'You must provide a name for the itinerary.',
            'must_add_items' => 'You must add at least one item to the new itinerary.',
            'title' => 'Itinerary Validation',
            'name' => [
                'required' => 'The itinerary name is required.',
                'string'   => 'The name must be text.',
                'max'      => 'The name may not exceed 255 characters.',
                'unique'   => 'An itinerary with that name already exists.',
            ],
            'description' => [
                'string' => 'The description must be text.',
                'max'    => 'The description may not exceed 1000 characters.',
            ],
            'items' => [
                'item'          => 'Item',
                'required'      => 'You must select at least one item.',
                'array'         => 'The items format is invalid.',
                'min'           => 'You must select at least one item.',
                'order_integer' => 'Order must be an integer.',
                'order_min'     => 'Order cannot be negative.',
                'order_max'     => 'Order may not exceed 9999.',
            ],
        ],

        'item'  => 'Item',
        'items' => 'Items',
    ],

    // =========================================================
    // [06] LANGUAGE
    // =========================================================
    'language' => [
        'fields' => [
            'name' => 'Language',
            'code' => 'Code',
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
            'create' => 'Could not create the language.',
            'update' => 'Could not update the language.',
            'toggle' => 'Could not change the language status.',
            'delete' => 'Could not delete the language.',
            'save'   => 'Could not save.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Invalid name',
                'required' => 'The language name is required.',
                'string'   => ':attribute must be a text string.',
                'max'      => ':attribute may not exceed :max characters.',
                'unique'   => 'A language with that name already exists.',
            ],
        ],

        'hints' => [
            'iso_639_1' => 'ISO 639-1 code, for example: es, en, fr.',
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [

        'validation' => [
            // General messages
            'required' => 'This field is required.',
            'min'      => 'This field must have at least :min characters.',
            'max'      => 'This field may not exceed :max characters.',
            'number'   => 'This field must be a valid number.',
            'slug'     => 'The slug may only contain lowercase letters, numbers and hyphens.',
            'color'    => 'Please select a valid color.',
            'select'   => 'Please select an option.',

            // Field-specific messages
            'length_in_hours'   => 'Duration in hours (ex: 2, 2.5, 4)',
            'max_capacity_help' => 'Maximum number of people per tour',

            // Forms
            'form_error_title'   => 'Attention!',
            'form_error_message' => 'Please fix the errors in the form before continuing.',
            'saving'             => 'Saving...',

            // Success
            'success'           => 'Success!',
            'tour_type_created' => 'Tour type created successfully.',
            'language_created'  => 'Language created successfully.',

            // Errors
            'tour_type_error' => 'Error creating tour type.',
            'language_error'  => 'Error creating language.',
            'languages_hint' => 'Select the languages available for this tour.',
        ],

        'wizard' => [
            // General titles
            'create_new_tour' => 'Create New Tour',
            'edit_tour'       => 'Edit Tour',
            'step_number'     => 'Step :number',
            'edit_step'       => 'Edit',
            'leave_warning'   => 'You have unsaved changes on this tour. If you leave now, the draft will remain in the database. Are you sure you want to leave?',
            'cancel_title'    => 'Cancel tour setup?',
            'cancel_text'     => 'If you leave this wizard, you may lose unsaved changes in this step.',
            'cancel_confirm'  => 'Yes, discard changes',
            'cancel_cancel'   => 'No, keep editing',
            'details_validation_text' => 'Check the required fields in the details form before continuing.',
            'most_recent'     => 'Most recent',
            'last_modified'   => 'Last modified',
            'start_fresh'     => 'Start again',
            'draft_details'   => 'Draft details',
            'drafts_found'    => 'A draft has been found',
            'basic_info'      => 'Details',
            'previous'        => 'Previous',

            // Wizard steps
            'steps' => [
                'details'   => 'Basic Details',
                'itinerary' => 'Itinerary',
                'schedules' => 'Schedules',
                'amenities' => 'Amenities',
                'prices'    => 'Prices',
                'summary'   => 'Summary',
            ],

            // Actions
            'save_and_continue' => 'Save and Continue',
            'publish_tour'      => 'Publish Tour',
            'delete_draft'      => 'Delete Draft',
            'ready_to_publish'  => 'Ready to Publish?',

            // Messages
            'details_saved'    => 'Details saved successfully.',
            'itinerary_saved'  => 'Itinerary saved successfully.',
            'schedules_saved'  => 'Schedules saved successfully.',
            'amenities_saved'  => 'Amenities saved successfully.',
            'prices_saved'     => 'Prices saved successfully.',
            'published_successfully' => 'Tour published successfully!',
            'draft_cancelled'        => 'Draft deleted.',

            // States
            'draft_mode'        => 'Draft Mode',
            'draft_explanation' => 'This tour will be saved as a draft until you complete all steps and publish it.',
            'already_published' => 'This tour has already been published. Use the regular editor to modify it.',
            'cannot_cancel_published' => 'You cannot cancel a tour that is already published.',

            // Confirmations
            'confirm_cancel' => 'Are you sure you want to cancel and delete this draft?',

            // Summary
            'publish_explanation' => 'Review all the information before publishing. Once published, the tour will be available for bookings.',
            'can_edit_later'      => 'You can edit the tour later from the admin panel.',
            'incomplete_warning'  => 'Some steps are incomplete. You can publish anyway, but it’s recommended to complete all information.',

            // Checklist
            'checklist'              => 'Checklist',
            'checklist_details'      => 'Basic details completed',
            'checklist_itinerary'    => 'Itinerary configured',
            'checklist_schedules'    => 'Schedules added',
            'checklist_amenities'    => 'Amenities configured',
            'checklist_prices'       => 'Prices set',

            // Hints
            'hints' => [
                'status' => 'The status can be changed after publishing.',
            ],

            // Existing drafts modal
            'existing_drafts_title'   => 'You have unfinished tour drafts!',
            'existing_drafts_message' => 'We found :count unfinished tour draft(s).',
            'current_step'            => 'Current Step',
            'step'                    => 'Step',

            // Modal actions
            'continue_draft'      => 'Continue with this draft',
            'delete_all_drafts'   => 'Delete All Drafts',
            'create_new_anyway'   => 'Create New Tour Anyway',

            // Additional info
            'drafts_info' => 'You can continue editing an existing draft, delete it individually, delete all drafts, or create a new tour and ignore current drafts.',

            // Delete confirmations
            'confirm_delete_title'        => 'Delete this draft?',
            'confirm_delete_message'      => 'This action cannot be undone. The draft will be permanently deleted:',
            'confirm_delete_all_title'    => 'Delete all drafts?',
            'confirm_delete_all_message'  => ':count draft(s) will be permanently deleted. This action cannot be undone.',

            // Success messages
            'draft_deleted'       => 'Draft deleted successfully.',
            'all_drafts_deleted'  => ':count draft(s) deleted successfully.',
            'continuing_draft'    => 'Continuing with your draft...',

            // Error messages
            'not_a_draft' => 'This tour is no longer a draft and cannot be edited via the wizard.',
        ],

        'title' => 'Tours',

        'fields' => [
            'id'            => 'ID',
            'name'          => 'Name',
            'details'       => 'Details',
            'price'         => 'Prices',
            'overview'      => 'Overview',
            'amenities'     => 'Amenities',
            'exclusions'    => 'Exclusions',
            'itinerary'     => 'Itinerary',
            'languages'     => 'Languages',
            'schedules'     => 'Schedules',
            'adult_price'   => 'Adult Price',
            'kid_price'     => 'Child Price',
            'length_hours'  => 'Duration (hours)',
            'max_capacity'  => 'Maximum capacity',
            'type'          => 'Tour Type',
            'viator_code'   => 'Viator Code',
            'status'        => 'Status',
            'actions'       => 'Actions',
            'group_size'    => 'Group size',
        ],

        'pricing' => [
            'already_added'          => 'This category has already been added.',
            'configured_categories'  => 'Configured categories',
            'create_category'        => 'Create category',
            'note_title'             => 'Note:',
            'note_text'              => 'Define the base prices for each customer category here.',
            'manage_detailed_hint'   => ' For detailed management, use the "Manage Detailed Prices" button above.',
            'price_usd'              => 'Price (USD)',
            'min_quantity'           => 'Minimum quantity',
            'max_quantity'           => 'Maximum quantity',
            'status'                 => 'Status',
            'active'                 => 'Active',
            'no_categories'          => 'No customer categories configured.',
            'create_categories_first' => 'Create categories first',
            'page_title'             => 'Prices - :name',
            'header_title'           => 'Prices: :name',
            'back_to_tours'          => 'Back to tours',

            'configured_title'       => 'Configured categories and prices',
            'empty_title'            => 'No categories configured for this tour.',
            'empty_hint'             => 'Use the form on the right to add categories.',

            'save_changes'           => 'Save changes',
            'auto_disable_note'      => 'Prices set to $0 are automatically disabled.',

            'add_category'           => 'Add category',

            'all_assigned_title'     => 'All categories are assigned',
            'all_assigned_text'      => 'There are no more categories available for this tour.',

            'info_title'             => 'Information',
            'tour_label'             => 'Tour',
            'configured_count'       => 'Configured categories',
            'active_count'           => 'Active categories',

            'fields_title'           => 'Fields',
            'rules_title'            => 'Rules',

            'field_price'            => 'Price',
            'field_min'              => 'Minimum',
            'field_max'              => 'Maximum',
            'field_status'           => 'Status',

            'rule_min_le_max'        => 'Minimum must be less than or equal to maximum.',
            'rule_zero_disable'      => 'Prices set to $0 are automatically disabled.',
            'rule_only_active'       => 'Only active categories appear on the public site.',

            'status_active'          => 'Active',
            'add_existing_category'  => 'Add existing category',
            'choose_category_placeholder' => 'Select a category…',
            'add_button'             => 'Add',
            'add_existing_hint'      => 'Add only the customer categories needed for this tour.',
            'remove_category'        => 'Remove category',
            'category_already_added' => 'This category has already been added to the tour.',
            'no_prices_preview'      => 'No prices have been configured yet.',
            'already_added'          => 'This category has already been added to the tour.',

            // Seasonal pricing
            'wizard_description'          => 'Define prices by season and customer category',
            'add_period'                  => 'Add Price Period',
            'confirm_remove_period'       => 'Remove this price period?',
            'category_already_in_period'  => 'This category is already added to this period',
            'category'                    => 'Category',
            'age_range'                   => 'Age',
            'taxes'                       => 'Taxes',
            'category_removed_success'    => 'Category removed successfully',
            'leave_empty_no_limit'        => 'Leave empty for no limit',
            'valid_from'                  => 'Valid from',
            'valid_until'                 => 'Valid until',
            'default_price'               => 'Default Price',
            'all_year'                    => 'All Year',
            'category_added_success'      => 'Category added successfully',
            'period_removed_success'      => 'Period removed successfully',
            'period_added_success'        => 'Period added successfully',
            'overlap_not_allowed_title'   => 'Date Range Not Allowed',
            'overlap_not_allowed_text'    => 'The selected dates overlap with another pricing period. Please adjust the range to avoid conflicts.',
            'overlap_conflict_with'       => 'Conflict with the following periods:',
            'duplicate_category_title'    => 'Duplicate Category',
            'invalid_date_range_title'    => 'Invalid Date Range',
            'remove_category_confirm_text' => 'This category will be removed from the period',
            'validation_failed'           => 'Validation Failed',
            'are_you_sure'                => 'Are you sure?',
            'yes_delete'                  => 'Yes, delete',
            'cancel'                      => 'Cancel',
            'attention'                   => 'Attention',
        ],

        'modal' => [
            'create_category' => 'Create category',

            'fields' => [
                'name'          => 'Name',
                'age_from'      => 'Age from',
                'age_to'        => 'Age to',
                'age_range'     => 'Age range',
                'min'           => 'Minimum',
                'max'           => 'Maximum',
                'order'         => 'Order',
                'is_active'     => 'Active',
                'auto_translate' => 'Auto translate',
            ],

            'placeholders' => [
                'name'            => 'Ex: Adult, Child, Infant',
                'age_to_optional' => 'Leave empty for "+"',
            ],

            'hints' => [
                'age_to_empty_means_plus' => 'If you leave the "age to" field empty, it will be interpreted as "+" (for example 12+).',
                'min_le_max'              => 'Minimum must be less than or equal to maximum.',
            ],

            'errors' => [
                'min_le_max' => 'Minimum must be less than or equal to maximum.',
            ],
        ],

        'schedules_form' => [
            'available_title'        => 'Available Schedules',
            'select_hint'            => 'Select the schedules for this tour',
            'no_schedules'           => 'No schedules available.',
            'create_schedules_link'  => 'Create schedules',

            'create_new_title'       => 'Create New Schedule',
            'label_placeholder'      => 'Ex: Morning, Afternoon',
            'create_and_assign'      => 'Create this schedule and assign it to the tour',

            'info_title'             => 'Information',
            'schedules_title'        => 'Schedules',
            'schedules_text'         => 'Select one or more schedules when this tour will be available.',
            'create_block_title'     => 'Create New',
            'create_block_text'      => 'If you need a schedule that does not exist yet, you can create it here by checking "Create this schedule and assign it to the tour".',

            'current_title'          => 'Current Schedules',
            'none_assigned'          => 'No schedules assigned',
        ],

        'summary' => [
            'preview_title'        => 'Tour Preview',
            'preview_text_create'  => 'Review all information before creating the tour.',
            'preview_text_update'  => 'Review all information before updating the tour.',

            'basic_details_title'  => 'Basic Details',
            'description_title'    => 'Description',
            'prices_title'         => 'Prices by Category',
            'schedules_title'      => 'Schedules',
            'languages_title'      => 'Languages',
            'itinerary_title'      => 'Itinerary',
            'amenities_title'      => 'Amenities',

            'table' => [
                'category' => 'Category',
                'price'    => 'Price',
                'min_max'  => 'Min–Max',
                'status'   => 'Status',
            ],

            'not_specified'      => 'Not specified',
            'slug_autogenerated' => 'Will be generated automatically',
            'deactivate' => 'Deactivate',
            'manage_prices' => 'Manage prices',
            'manage_images' => 'Manage images',
            'manage_delete' => 'Delete',
            'no_description'     => 'No description',
            'no_active_prices'   => 'No active prices configured',
            'no_languages'       => 'No languages assigned',
            'none_included'      => 'Nothing included specified',
            'none_excluded'      => 'Nothing excluded specified',
            'date_range'         => 'Date Range',

            'units' => [
                'hours'  => 'hours',
                'people' => 'people',
            ],

            'create_note' => 'Schedules, prices, languages and amenities will appear here after saving the tour.',
        ],

        'alerts' => [
            'delete_title'             => 'Delete tour?',
            'delete_text'              => 'The tour will be moved to Deleted. You can restore it later.',
            'purge_title'              => 'Delete permanently?',
            'purge_text'               => 'This action is irreversible.',
            'purge_text_with_bookings' => 'This tour has :count booking(s). They will not be deleted; they will remain without an associated tour.',
            'toggle_question_active'   => 'Deactivate tour?',
            'toggle_question_inactive' => 'Activate tour?',
        ],

        'flash' => [
            'created'      => 'Tour created successfully.',
            'updated'      => 'Tour updated successfully.',
            'deleted_soft' => 'Tour moved to Deleted.',
            'restored'     => 'Tour restored successfully.',
            'purged'       => 'Tour permanently deleted.',
            'toggled_on'   => 'Tour activated.',
            'toggled_off'  => 'Tour deactivated.',
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
            'max_capacity'  => 'Max capacity',
            'type'          => 'Type',
            'viator_code'   => 'Viator Code',
            'status'        => 'Status',
            'actions'       => 'Actions',
            'slug'          => 'URL',
            'prices'        => 'Prices',
            'capacity'      => 'Capacity',
            'group_size'    => 'Max. Group',
        ],

        'status' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
            'archived' => 'Archived',
        ],

        'placeholders' => [
            'group_size' => 'Ex: 10',
        ],

        'hints' => [
            'group_size' => 'Group size per guide or general for this tour. (This value is shown in the product information.)',
        ],

        'success' => [
            'created'     => 'The tour was created successfully.',
            'updated'     => 'The tour was updated successfully.',
            'deleted'     => 'The tour was deleted.',
            'toggled'     => 'The tour status was updated.',
            'activated'   => 'Tour activated successfully.',
            'deactivated' => 'Tour deactivated successfully.',
            'archived'    => 'Tour archived successfully.',
            'restored'    => 'Tour restored successfully.',
            'purged'      => 'Tour permanently deleted.',
        ],

        'error' => [
            'create'            => 'Could not create the tour.',
            'update'            => 'Could not update the tour.',
            'delete'            => 'Could not delete the tour.',
            'toggle'            => 'Could not change the tour status.',
            'not_found'         => 'The tour does not exist.',
            'restore'           => 'Could not restore the tour.',
            'purge'             => 'Could not permanently delete the tour.',
            'purge_has_bookings' => 'Cannot permanently delete: the tour has bookings.',
        ],

        'ui' => [
            'add_tour_type'     => 'Add tour type',
            'back'              => 'Back',
            'page_title'        => 'Tour Management',
            'page_heading'      => 'Tour Management',
            'create_title'      => 'Register Tour',
            'edit_title'        => 'Edit Tour',
            'delete_title'      => 'Delete Tour',
            'cancel'            => 'Cancel',
            'save'              => 'Save',
            'save_changes'      => 'Save changes',
            'update'            => 'Update',
            'delete_confirm'    => 'Delete this tour?',
            'toggle_on'         => 'Activate',
            'toggle_off'        => 'Deactivate',
            'toggle_on_title'   => 'Activate tour?',
            'toggle_off_title'  => 'Deactivate tour?',
            'toggle_on_button'  => 'Yes, activate',
            'toggle_off_button' => 'Yes, deactivate',
            'see_more'          => 'See more',
            'see_less'          => 'Hide',
            'load_more'         => 'Load more',
            'loading'           => 'Loading...',
            'load_more_error'   => 'Could not load more tours.',
            'confirm_title'     => 'Confirmation',
            'confirm_text'      => 'Do you want to confirm this action?',
            'yes_confirm'       => 'Yes, confirm',
            'no_confirm'        => 'No, cancel',
            'add_tour'          => 'Add Tour',
            'edit_tour'         => 'Edit Tour',
            'delete_tour'       => 'Delete Tour',
            'toggle_tour'       => 'Activate/Deactivate Tour',
            'view_cart'         => 'View Cart',
            'add_to_cart'       => 'Add to Cart',
            'slug_help'         => 'Tour identifier in the URL (no spaces or accents)',
            'generate_auto'     => 'Generate automatically',
            'slug_preview_label' => 'Preview',
            'saved'             => 'Saved',

            'available_languages'    => 'Available languages',
            'default_capacity'       => 'Default capacity',
            'create_new_schedules'   => 'Create new schedules',
            'multiple_hint_ctrl_cmd' => 'Hold CTRL/CMD to select multiple.',
            'use_existing_schedules' => 'Use existing schedules',
            'add_schedule'           => 'Add schedule',
            'schedules_title'        => 'Tour Schedules',
            'amenities_included'     => 'Included amenities',
            'amenities_excluded'     => 'Not included amenities',
            'color'                  => 'Tour color',
            'remove'                 => 'Remove',
            'delete'                 => 'Delete',
            'choose_itinerary'       => 'Choose itinerary',
            'select_type'            => 'Select type',
            'empty_means_default'    => 'Default',
            'actives'                => 'Active',
            'inactives'              => 'Inactive',
            'archived'               => 'Archived',
            'all'                    => 'All',
            'help_title'             => 'Help',
            'amenities_included_hint' => 'Select what is included in the tour.',
            'amenities_excluded_hint' => 'Select what is NOT included in the tour.',
            'help_included_title'    => 'Included',
            'help_included_text'     => 'Check everything that is included in the tour price (transport, meals, tickets, equipment, guide, etc.).',
            'help_excluded_title'    => 'Not Included',
            'help_excluded_text'     => 'Check what the customer must pay separately or bring (tips, alcoholic drinks, souvenirs, etc.).',
            'select_or_create_title' => 'Select or Create Itinerary',
            'select_existing_items'  => 'Select Existing Items',
            'name_hint'              => 'Internal name for this itinerary',
            'click_add_item_hint'    => 'Click "Add Item" to create new items',
            'scroll_hint'            => 'Scroll horizontally to see more columns',
            'no_schedules'           => 'No schedules',
            'no_prices'              => 'No prices configured',

            // Price badges
            'prices_by_period' => 'Prices by Period',
            'period' => 'period',
            'periods' => 'periods',
            'all_year' => 'All year',
            'from' => 'From',
            'until' => 'Until',
            'no_prices' => 'No prices',

            'edit'                   => 'Edit',
            'slug_auto'              => 'Will be generated automatically',
            'deactivate' => 'Deactivate',
            'manage_prices' => 'Manage prices',
            'manage_images' => 'Manage images',
            'manage_delete' => 'Delete',
            'deactivate' => 'Deactivate',
            'manage_prices' => 'Manage prices',
            'manage_images' => 'Manage images',
            'manage_delete' => 'Delete',
            'added_to_cart'          => 'Added to cart',
            'add_language'           => 'Add language',
            'added_to_cart_text'     => 'The tour was added to the cart successfully.',
            'amenities_excluded_auto_hint' => 'By default, all amenities not marked as “included” are considered “not included”. You can uncheck the ones that don’t apply.',
            'quick_create_language_hint'    => 'Quickly add a new language if it does not appear in the list.',
            'quick_create_type_hint'        => 'Quickly add a new tour type if it does not appear in the list.',

            'none' => [
                'amenities'       => 'No amenities',
                'exclusions'      => 'No exclusions',
                'itinerary'       => 'No itinerary',
                'itinerary_items' => 'No items',
                'languages'       => 'No languages',
                'schedules'       => 'No schedules',
            ],

            // Archive / restore / purge
            'archive' => 'Archive',
            'restore' => 'Restore',
            'purge'   => 'Delete permanently',

            'confirm_archive_title' => 'Archive tour?',
            'confirm_archive_text'  => 'The tour will be disabled for new bookings, but existing bookings are kept.',
            'confirm_purge_title'   => 'Delete permanently',
            'confirm_purge_text'    => 'This action is irreversible and only allowed if the tour never had bookings.',

            // Filters
            'filters' => [
                'active'   => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
                'all'      => 'All',
            ],

            // Font toolbar
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
        'upload_truncated'    => 'Some files were skipped because of the per-tour limit.',
        'done'                => 'Done',
        'notice'              => 'Notice',
        'saved'               => 'Saved',
        'caption_updated'     => 'Caption updated successfully.',
        'deleted'             => 'Deleted',
        'image_removed'       => 'Image deleted successfully.',
        'invalid_order'       => 'Invalid sort order.',
        'nothing_to_reorder'  => 'Nothing to reorder.',
        'order_saved'         => 'Order saved.',
        'cover_updated_title' => 'Update cover',
        'cover_updated_text'  => 'This image is now the cover.',
        'deleting'            => 'Deleting...',

        'ui' => [
            // Tour selection page
            'page_title_pick'     => 'Tour Images',
            'page_heading'        => 'Tour Images',
            'choose_tour'         => 'Choose tour',
            'search_placeholder'  => 'Search by ID or name…',
            'search_button'       => 'Search',
            'no_results'          => 'No tours found.',
            'manage_images'       => 'Manage images',
            'cover_alt'           => 'Cover',
            'images_label'        => 'images',

            // Generic buttons
            'upload_btn'          => 'Upload',
            'delete_btn'          => 'Delete',
            'show_btn'            => 'Show',
            'close_btn'           => 'Close',
            'preview_title'       => 'Image preview',

            // General state texts
            'error_title'         => 'Error',
            'warning_title'       => 'Warning',
            'success_title'       => 'Success',
            'cancel_btn'          => 'Cancel',

            // Basic confirmations
            'confirm_delete_title' => 'Delete this image?',
            'confirm_delete_text'  => 'This action cannot be undone.',

            // Cover management (classic form)
            'cover_current_title'    => 'Current cover',
            'upload_new_cover_title' => 'Upload new cover',
            'cover_file_label'       => 'Cover file',
            'file_help_cover'        => 'JPEG/PNG/WebP, 30 MB max.',
            'id_label'               => 'ID',

            // Navigation / header on tour view
            'back_btn'          => 'Back to list',

            // Stats (top bar)
            'stats_images'      => 'Images uploaded',
            'stats_cover'       => 'Covers defined',
            'stats_selected'    => 'Selected',

            // Upload area
            'drag_or_click'     => 'Drag and drop your images here or click to select.',
            'upload_help'       => 'Allowed formats: JPG, PNG, WebP. Total maximum size 100 MB.',
            'select_btn'        => 'Select files',
            'limit_badge'       => 'Limit of :max images reached',
            'files_word'        => 'files',

            // Multi-select toolbar
            'select_all'        => 'Select all',
            'delete_selected'   => 'Delete selected',
            'delete_all'        => 'Delete all',

            // Image selector (chip)
            'select_image_title' => 'Select this image',
            'select_image_aria'  => 'Select image :id',

            // Cover (chip / per-card button)
            'cover_label'       => 'Cover',
            'cover_btn'         => 'Set as cover',

            // Saving states / JS helpers
            'caption_placeholder' => 'Caption (optional)',
            'saving_label'        => 'Saving…',
            'saving_fallback'     => 'Saving…',
            'none_label'          => 'No caption',
            'limit_word'          => 'Limit',

            // Advanced confirmations (JS)
            'confirm_set_cover_title' => 'Set as cover?',
            'confirm_set_cover_text'  => 'This image will be the main cover for the tour.',
            'confirm_btn'             => 'Yes, continue',

            'confirm_bulk_delete_title' => 'Delete selected images?',
            'confirm_bulk_delete_text'  => 'The selected images will be permanently deleted.',

            'confirm_delete_all_title'  => 'Delete all images?',
            'confirm_delete_all_text'   => 'All images for this tour will be deleted.',

            // No images view
            'no_images'           => 'There are no images for this tour yet.',
        ],

        'errors' => [
            'validation'     => 'The submitted data is invalid.',
            'upload_generic' => 'Some images could not be uploaded.',
            'update_caption' => 'Could not update the caption.',
            'delete'         => 'Could not delete the image.',
            'reorder'        => 'Could not save the order.',
            'set_cover'      => 'Could not set the cover.',
            'load_list'      => 'Could not load the list.',
            'too_large'      => 'The file exceeds the maximum allowed size. Try a smaller image.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Prices - :name',
            'header_title'       => 'Prices: :name',
            'back_to_tours'      => 'Back to tours',

            'configured_title'   => 'Configured categories and prices',
            'empty_title'        => 'No categories configured for this tour.',
            'empty_hint'         => 'Use the form on the right to add categories.',

            'save_changes'       => 'Save changes',
            'auto_disable_note'  => 'Prices set to $0 are automatically disabled.',

            'add_category'       => 'Add category',
            'period_name'        => 'Period Name',
            'period_name_placeholder' => 'Ex. High Season',

            'all_assigned_title' => 'All categories are assigned',
            'all_assigned_text'  => 'There are no more categories available for this tour.',

            'info_title'         => 'Information',
            'tour_label'         => 'Tour',
            'configured_count'   => 'Configured categories',
            'active_count'       => 'Active categories',

            'fields_title'       => 'Fields',
            'rules_title'        => 'Rules',

            'field_price'        => 'Price',
            'field_min'          => 'Minimum',
            'field_max'          => 'Maximum',
            'field_status'       => 'Status',

            'rule_min_le_max'    => 'Minimum must be less than or equal to maximum.',
            'rule_zero_disable'  => 'Prices set to $0 are automatically disabled.',
            'rule_only_active'   => 'Only active categories appear on the public site.',
        ],

        'table' => [
            'category'   => 'Category',
            'age_range'  => 'Age range',
            'price_usd'  => 'Price (USD)',
            'min'        => 'Min',
            'max'        => 'Max',
            'status'     => 'Status',
            'action'     => 'Action',
            'active'     => 'Active',
            'inactive'   => 'Inactive',
        ],

        'forms' => [
            'select_placeholder'   => '-- Select --',
            'category'             => 'Category',
            'price_usd'            => 'Price (USD)',
            'min'                  => 'Minimum',
            'max'                  => 'Maximum',
            'create_disabled_hint' => 'If the price is $0, the category will be created disabled',
            'add'                  => 'Add',
        ],

        'modal' => [
            'delete_title'   => 'Delete category',
            'delete_text'    => 'Delete this category from this tour?',
            'cancel'         => 'Cancel',
            'delete'         => 'Delete',
            'delete_tooltip' => 'Delete category',
        ],

        'flash' => [
            'success' => 'Operation completed successfully.',
            'error'   => 'An error occurred.',
        ],

        'js' => [
            'max_ge_min'            => 'Maximum must be greater than or equal to minimum.',
            'auto_disabled_tooltip' => 'Price is $0 – automatically disabled',
            'fix_errors'            => 'Fix the minimum and maximum amounts',
        ],

        'quick_category' => [
            'title'                 => 'Quick Create Category',
            'button'                => 'New category',
            'go_to_index'           => 'View all categories',
            'go_to_index_title'     => 'Open the full category list',
            'name_label'            => 'Category name',
            'age_from'              => 'Age from',
            'age_to'                => 'Age to',
            'save'                  => 'Save category',
            'cancel'                => 'Cancel',
            'saving'                => 'Saving...',
            'success_title'         => 'Category created',
            'success_text'          => 'The category was created successfully and added to the tour.',
            'error_title'           => 'Error',
            'error_generic'         => 'There was a problem creating the category.',
            'created_ok'            => 'Category created successfully.',
            'no_limit'              => 'Empty for no limit',
        ],

        'validation' => [
            'title'                 => 'Price Validation',
            'no_categories'         => 'You must add at least one price category.',
            'no_price_greater_zero' => 'There must be at least one category with a price greater than $0.00.',
            'price_required'        => 'Price is required.',
            'price_min'             => 'Price must be greater than or equal to 0.',
            'age_to_greater_equal'  => '"Age to" must be greater than or equal to "Age from".',
        ],

        'alerts' => [
            'price_updated' => 'Price updated successfully',
            'price_created' => 'Category added to period successfully',
            'price_deleted' => 'Price deleted successfully',
            'status_updated' => 'Status updated',
            'period_updated' => 'Period dates updated',
            'period_deleted' => 'Period deleted successfully',

            'error_title' => 'Error',
            'error_unexpected' => 'An unexpected error occurred',
            'error_delete_price' => 'Could not delete the price',
            'error_add_category' => 'Could not add the category',
            'error_update_period' => 'Could not update period dates',

            'attention' => 'Attention',
            'select_category_first' => 'Select a category first',
            'duplicate_category_title' => 'Duplicate Category',
            'duplicate_category_text' => 'This category is already added to this period',

            'confirm_delete_price_title' => 'Delete price?',
            'confirm_delete_price_text' => 'This action cannot be undone.',
            'confirm_delete_period_title' => 'Delete this period?',
            'confirm_delete_period_text' => 'All prices associated with this period will be deleted.',
            'confirm_yes_delete' => 'Yes, delete',
            'confirm_cancel' => 'Cancel',

            'no_categories' => 'This period has no categories',
        ],
    ],

    'ajax' => [
        'category_created'   => 'Category created successfully.',
        'category_error'     => 'Error creating category.',
        'language_created'   => 'Language created successfully.',
        'language_error'     => 'Error creating language.',
        'amenity_created'    => 'Amenity created successfully.',
        'amenity_error'      => 'Error creating amenity.',
        'schedule_created'   => 'Schedule created successfully.',
        'schedule_error'     => 'Error creating schedule.',
        'itinerary_created'  => 'Itinerary created successfully.',
        'itinerary_error'    => 'Error creating itinerary.',
        'translation_error'  => 'Error translating.',
    ],

    'modal' => [
        'create_category'  => 'Create New Category',
        'create_language'  => 'Create New Language',
        'create_amenity'   => 'Create New Amenity',
        'create_schedule'  => 'Create New Schedule',
        'create_itinerary' => 'Create New Itinerary',
    ],

    'validation' => [
        'slug_taken'     => 'This slug is already in use.',
        'slug_available' => 'Slug available.',
    ],

    'tour_type' => [
        'fields' => [
            'name' => 'Name',
            'description' => 'Description',
            'status' => 'Status',
            'duration' => 'Duration',
            'duration_hint' => 'Suggested tour duration (optional)',
            'duration_placeholder' => 'Example: 4 hours, 6 hours, etc.',
        ],
    ],
];
