<?php

/*************************************************************
 *  TRANSLATION MODULE: TOURS
 *  File: resources/lang/en/m_tours.php
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title'      => 'Success',
        'error_title'        => 'Error',
        'people'             => 'people',
        'hours'              => 'hours',
        'success'            => 'Success',
        'error'              => 'Error',
        'cancel'             => 'Cancel',
        'confirm_delete'     => 'Yes, delete',
        'unspecified'        => 'Not specified',
        'no_description'     => 'No description',
        'required_fields_title' => 'Required fields',
        'required_fields_text'  => 'Please complete the required fields: Name and Maximum Capacity',
        'active'             => 'Active',
        'inactive'           => 'Inactive',
        'notice'             => 'Notice',
        'na'                 => 'Not configured',
        'create'             => 'Create',
        'info'               => 'Information',
        'close'              => 'Close',
        'required'           => 'This field is required.',
        'add'                => 'Add',
        'translating'        => 'Translating...',
        'error_translating'  => 'The text could not be translated.',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
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
            'toggle_confirm_on_html'   => 'Amenity <b>:label</b> will be activated.',
            'toggle_confirm_off_html'  => 'Amenity <b>:label</b> will be deactivated.',

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
            'deleted'     => 'Amenity permanently deleted.',
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
                'required' => ':attribute is required.',
                'string'   => ':attribute must be a text string.',
                'max'      => ':attribute may not be greater than :max characters.',
            ],
        ],

        'hints' => [
            'fontawesome' => 'Use FontAwesome classes, for example: "fas fa-check".',
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

            'general_title'     => 'Global schedules',
            'new_schedule'      => 'New schedule',
            'new_general_title' => 'New global schedule',
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
            'no_general'        => 'There are no global schedules.',
            'no_tour_schedules' => 'This tour does not have any schedules yet.',
            'no_label'          => 'No label',
            'assigned_count'    => 'assigned schedule(s)',

            'toggle_global_title'     => 'Activate/Deactivate (global)',
            'toggle_global_on_title'  => 'Activate schedule (global)?',
            'toggle_global_off_title' => 'Deactivate schedule (global)?',
            'toggle_global_on_html'   => '<b>:label</b> will be activated for all tours.',
            'toggle_global_off_html'  => '<b>:label</b> will be deactivated for all tours.',

            'toggle_on_tour'          => 'Activate for this tour',
            'toggle_off_tour'         => 'Deactivate for this tour',
            'toggle_assign_on_title'  => 'Activate for this tour?',
            'toggle_assign_off_title' => 'Deactivate for this tour?',
            'toggle_assign_on_html'   => 'The assignment will be <b>active</b> for <b>:tour</b>.',
            'toggle_assign_off_html'  => 'The assignment will be <b>inactive</b> for <b>:tour</b>.',

            'detach_from_tour'     => 'Detach from tour',
            'detach_confirm_title' => 'Detach from tour?',
            'detach_confirm_html'  => 'The schedule will be <b>detached</b> from <b>:tour</b>.',

            'delete_forever'       => 'Delete (global)',
            'delete_confirm_title' => 'Delete permanently?',
            'delete_confirm_html'  => '<b>:label</b> (global) will be deleted and you won’t be able to undo this.',

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
            'missing_fields_text'  => 'Check the required fields (start, end and capacity).',
            'could_not_save'       => 'Could not be saved',
        ],

        'success' => [
            'created'                => 'Schedule created successfully.',
            'updated'                => 'Schedule updated successfully.',
            'activated_global'       => 'Schedule activated successfully (global).',
            'deactivated_global'     => 'Schedule deactivated successfully (global).',
            'attached'               => 'Schedule assigned to the tour.',
            'detached'               => 'Schedule detached from the tour successfully.',
            'assignment_activated'   => 'Assignment activated for this tour.',
            'assignment_deactivated' => 'Assignment deactivated for this tour.',
            'deleted'                => 'Schedule deleted successfully.',
        ],

        'error' => [
            'create'               => 'There was a problem creating the schedule.',
            'update'               => 'There was a problem updating the schedule.',
            'toggle'               => 'The global status of the schedule could not be changed.',
            'attach'               => 'The schedule could not be assigned to the tour.',
            'detach'               => 'The schedule could not be detached from the tour.',
            'assignment_toggle'    => 'The assignment status could not be changed.',
            'not_assigned_to_tour' => 'The schedule is not assigned to this tour.',
            'delete'               => 'There was a problem deleting the schedule.',
        ],

        'placeholders' => [
            'morning' => 'E.g.: Morning',
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
            'deleted'     => 'Item permanently deleted.',
        ],

        'error' => [
            'create' => 'The item could not be created.',
            'update' => 'The item could not be updated.',
            'toggle' => 'The item status could not be changed.',
            'delete' => 'The item could not be deleted.',
        ],

        'validation' => [
            'title' => [
                'required' => ':attribute is required.',
                'string'   => ':attribute must be a text string.',
                'max'      => ':attribute may not be greater than :max characters.',
            ],
            'description' => [
                'required' => ':attribute is required.',
                'string'   => ':attribute must be a text string.',
                'max'      => ':attribute may not be greater than :max characters.',
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
            'drag_hint'             => 'Drag and drop items to define the order.',
            'drag_handle'           => 'Drag to reorder',
            'select_one_title'      => 'You must select at least one item',
            'select_one_text'       => 'Please select at least one item to continue.',
            'assign_confirm_title'  => 'Assign selected items?',
            'assign_confirm_button' => 'Yes, assign',
            'assigning'             => 'Assigning...',

            'no_items_assigned'       => 'There are no items assigned to this itinerary.',
            'itinerary_this'          => 'this itinerary',
            'processing'              => 'Processing...',
            'saving'                  => 'Saving...',
            'activating'              => 'Activating...',
            'deactivating'            => 'Deactivating...',
            'applying'                => 'Applying...',
            'deleting'                => 'Deleting...',
            'flash_success_title'     => 'Success',
            'flash_error_title'       => 'Error',
            'validation_failed_title' => 'Could not be processed',
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
            'create'  => 'The itinerary could not be created.',
            'update'  => 'The itinerary could not be updated.',
            'toggle'  => 'The itinerary status could not be changed.',
            'delete'  => 'The itinerary could not be deleted.',
            'assign'  => 'The items could not be assigned.',
        ],

        'validation' => [
            'name_required' => 'You must provide a name for the itinerary.',
            'name' => [
                'required' => 'The itinerary name is required.',
                'string'   => 'The name must be text.',
                'max'      => 'The name may not be greater than 255 characters.',
                'unique'   => 'An itinerary with that name already exists.',
            ],
            'description' => [
                'string' => 'The description must be text.',
                'max'    => 'The description may not be greater than 1000 characters.',
            ],
            'items' => [
                'required'      => 'You must select at least one item.',
                'array'         => 'The items format is not valid.',
                'min'           => 'You must select at least one item.',
                'order_integer' => 'Order must be an integer.',
                'order_min'     => 'Order cannot be negative.',
                'order_max'     => 'Order may not be greater than 9999.',
            ],
        ],

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
            'create' => 'The language could not be created.',
            'update' => 'The language could not be updated.',
            'toggle' => 'The language status could not be changed.',
            'delete' => 'The language could not be deleted.',
            'save'   => 'Could not be saved',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Invalid name',
                'required' => 'The language name is required.',
                'string'   => ':attribute must be a text string.',
                'max'      => ':attribute may not be greater than :max characters.',
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
            'max_capacity'  => 'Max. capacity',
            'type'          => 'Tour Type',
            'viator_code'   => 'Viator Code',
            'status'        => 'Status',
            'actions'       => 'Actions',
            'group_size'    => 'Group size',
        ],

        'pricing' => [
            'configured_categories' => 'Configured categories',
            'create_category'       => 'Create category',
            'note_title'            => 'Note:',
            'note_text'             => 'Define the base prices for each customer category here.',
            'manage_detailed_hint'  => 'For detailed management, use the "Manage Detailed Prices" button above.',
            'price_usd'             => 'Price (USD)',
            'min_quantity'          => 'Minimum quantity',
            'max_quantity'          => 'Maximum quantity',
            'status'                => 'Status',
            'active'                => 'Active',
            'no_categories'         => 'There are no customer categories configured.',
            'create_categories_first' => 'Create categories first',
            'page_title'            => 'Prices - :name',
            'header_title'          => 'Prices: :name',
            'back_to_tours'         => 'Back to tours',

            'configured_title'      => 'Configured categories and prices',
            'empty_title'           => 'There are no categories configured for this tour.',
            'empty_hint'            => 'Use the form on the right to add categories.',

            'save_changes'          => 'Save changes',
            'auto_disable_note'     => 'Prices of $0 are automatically disabled',

            'add_category'          => 'Add category',

            'all_assigned_title'    => 'All categories are assigned',
            'all_assigned_text'     => 'There are no more categories available for this tour.',

            'info_title'            => 'Information',
            'tour_label'            => 'Tour',
            'configured_count'      => 'Configured categories',
            'active_count'          => 'Active categories',

            'fields_title'          => 'Fields',
            'rules_title'           => 'Rules',

            'field_price'           => 'Price',
            'field_min'             => 'Minimum',
            'field_max'             => 'Maximum',
            'field_status'          => 'Status',

            'rule_min_le_max'       => 'Minimum must be less than or equal to maximum',
            'rule_zero_disable'     => 'Prices of $0 are automatically disabled',
            'rule_only_active'      => 'Only active categories appear on the public site',

            'status_active'         => 'Active',
            'add_existing_category'       => 'Add existing category',
            'choose_category_placeholder' => 'Select a category…',
            'add_button'                  => 'Add',
            'add_existing_hint'           => 'Add only the customer categories needed for this tour.',
            'remove_category'             => 'Remove category',
            'category_already_added'      => 'This category has already been added to the tour.',
            'no_prices_preview'           => 'There are no prices configured yet.',
        ],

        'modal' => [
            'create_category' => 'Create category',

            'fields' => [
                'name'           => 'Name',
                'age_from'       => 'Age from',
                'age_to'         => 'Age to',
                'age_range'      => 'Age range',
                'min'            => 'Minimum',
                'max'            => 'Maximum',
                'order'          => 'Order',
                'is_active'      => 'Active',
                'auto_translate' => 'Translate automatically',
            ],

            'placeholders' => [
                'name'            => 'E.g.: Adult, Child, Infant',
                'age_to_optional' => 'Leave empty for "+"',
            ],

            'hints' => [
                'age_to_empty_means_plus' => 'If you leave the maximum age empty, it will be interpreted as "+" (for example 12+).',
                'min_le_max'              => 'Minimum must be less than or equal to maximum.',
            ],

            'errors' => [
                'min_le_max' => 'Minimum must be less than or equal to maximum.',
            ],
        ],

        'schedules_form' => [
            'available_title'        => 'Available Schedules',
            'select_hint'            => 'Select schedules for this tour',
            'no_schedules'           => 'There are no schedules available.',
            'create_schedules_link'  => 'Create schedules',

            'create_new_title'       => 'Create New Schedule',
            'label_placeholder'      => 'E.g.: Morning, Afternoon',
            'create_and_assign'      => 'Create this schedule and assign it to the tour',

            'info_title'             => 'Information',
            'schedules_title'        => 'Schedules',
            'schedules_text'         => 'Select one or more schedules when this tour will be available.',
            'create_block_title'     => 'Create New',
            'create_block_text'      => 'If you need a schedule that does not exist, you can create it here by checking the box "Create this schedule and assign it to the tour".',

            'current_title'          => 'Current Schedules',
            'none_assigned'          => 'No schedules assigned',
        ],

        'summary' => [
            'preview_title'        => 'Tour Preview',
            'preview_text_create'  => 'Review all the information before creating the tour.',
            'preview_text_update'  => 'Review all the information before updating the tour.',

            'basic_details_title'  => 'Basic Details',
            'description_title'    => 'Description',
            'prices_title'         => 'Prices by Category',
            'schedules_title'      => 'Schedules',
            'languages_title'      => 'Languages',
            'itinerary_title'      => 'Itinerary',

            'table' => [
                'category' => 'Category',
                'price'    => 'Price',
                'min_max'  => 'Min–Max',
            ],

            'not_specified'        => 'Not specified',
            'slug_autogenerated'   => 'Will be generated automatically',
            'no_description'       => 'No description',
            'no_active_prices'     => 'No active prices configured',
            'no_languages'         => 'No languages assigned',
            'none_included'        => 'Nothing marked as included',
            'none_excluded'        => 'Nothing marked as excluded',

            'units' => [
                'hours'  => 'hours',
                'people' => 'people',
            ],

            'create_note' => 'Schedules, prices, languages and amenities will be shown here after saving the tour.',
        ],

        'alerts' => [
            'delete_title' => 'Delete tour?',
            'delete_text'  => 'The tour will be moved to Deleted. You will be able to restore it later.',
            'purge_title'  => 'Delete permanently?',
            'purge_text'   => 'This action is irreversible.',
            'purge_text_with_bookings' => 'This tour has :count booking(s). They will not be deleted; they will remain without an associated tour.',
            'toggle_question_active'   => 'Deactivate tour?',
            'toggle_question_inactive' => 'Activate tour?',
        ],

        'flash' => [
            'created'       => 'Tour created successfully.',
            'updated'       => 'Tour updated successfully.',
            'deleted_soft'  => 'Tour moved to Deleted.',
            'restored'      => 'Tour restored successfully.',
            'purged'        => 'Tour permanently deleted.',
            'toggled_on'    => 'Tour activated.',
            'toggled_off'   => 'Tour deactivated.',
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
            'max_capacity'  => 'Max. capacity',
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
            'group_size' => 'E.g.: 10',
        ],

        'hints' => [
            'group_size' => 'Recommended capacity/group size for this tour.',
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
            'create'    => 'The tour could not be created.',
            'update'    => 'The tour could not be updated.',
            'delete'    => 'The tour could not be deleted.',
            'toggle'    => 'The tour status could not be changed.',
            'not_found' => 'The tour does not exist.',
            'restore'            => 'The tour could not be restored.',
            'purge'              => 'The tour could not be permanently deleted.',
            'purge_has_bookings' => 'It cannot be permanently deleted: the tour has bookings.',
        ],

        'ui' => [
            'page_title'       => 'Tour Management',
            'page_heading'     => 'Tour Management',
            'create_title'     => 'Register Tour',
            'edit_title'       => 'Edit Tour',
            'delete_title'     => 'Delete Tour',
            'cancel'           => 'Cancel',
            'save'             => 'Save',
            'save_changes'     => 'Save changes',
            'update'           => 'Update',
            'delete_confirm'   => 'Delete this tour?',
            'toggle_on'        => 'Activate',
            'toggle_off'       => 'Deactivate',
            'toggle_on_title'  => 'Activate tour?',
            'toggle_off_title' => 'Deactivate tour?',
            'toggle_on_button'  => 'Yes, activate',
            'toggle_off_button' => 'Yes, deactivate',
            'see_more'         => 'See more',
            'see_less'         => 'Hide',
            'load_more'        => 'Load more',
            'loading'          => 'Loading...',
            'load_more_error'  => 'More tours could not be loaded.',
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
            'slug_help'        => 'Tour identifier in the URL (no spaces or accents)',
            'generate_auto'       => 'Generate automatically',
            'slug_preview_label'  => 'Preview',
            'saved'               => 'Saved',

            'available_languages'    => 'Available languages',
            'default_capacity'       => 'Default capacity',
            'create_new_schedules'   => 'Create new schedules',
            'multiple_hint_ctrl_cmd' => 'Hold CTRL/CMD to select multiple',
            'use_existing_schedules' => 'Use existing schedules',
            'add_schedule'           => 'Add schedule',
            'schedules_title'        => 'Tour Schedules',
            'amenities_included'     => 'Included amenities',
            'amenities_excluded'     => 'Not included amenities',
            'color'                  => 'Tour color',
            'remove'                 => 'Remove',
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
            'help_included_title'     => 'Included',
            'help_included_text'      => 'Mark everything that is included in the tour price (transportation, meals, entrance fees, equipment, guide, etc.).',
            'help_excluded_title'     => 'Not Included',
            'help_excluded_text'      => 'Mark what the customer must pay separately or bring (tips, alcoholic drinks, souvenirs, etc.).',
            'select_or_create_title' => 'Select or Create Itinerary',
            'select_existing_items'  => 'Select Existing Items',
            'name_hint'              => 'Identifier name for this itinerary',
            'click_add_item_hint'    => 'Click "Add Item" to create new items',
            'scroll_hint'            => 'Scroll horizontally to see more columns',
            'no_schedules'           => 'No schedules',
            'no_prices'              => 'No prices configured',
            'edit'                   => 'Edit',
            'slug_auto'              => 'Will be generated automatically',
            'added_to_cart'          => 'Added to cart',
            'added_to_cart_text'     => 'The tour was added to the cart successfully.',

            'none' => [
                'amenities'       => 'No amenities',
                'exclusions'      => 'No exclusions',
                'itinerary'       => 'No itinerary',
                'itinerary_items' => 'No items',
                'languages'       => 'No languages',
                'schedules'       => 'No schedules',
            ],

            'archive' => 'Archive',
            'restore' => 'Restore',
            'purge'   => 'Delete permanently',

            'confirm_archive_title' => 'Archive tour?',
            'confirm_archive_text'  => 'The tour will be disabled for new bookings, but existing bookings are kept.',
            'confirm_purge_title'   => 'Delete permanently',
            'confirm_purge_text'    => 'This action is irreversible and is only allowed if the tour never had bookings.',

            'filters' => [
                'active'   => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
                'all'      => 'All',
            ],

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
        'saved'               => 'Save',
        'caption_updated'     => 'Caption updated successfully.',
        'deleted'             => 'Deleted',
        'image_removed'       => 'Image removed successfully.',
        'invalid_order'       => 'Invalid order payload.',
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

            // Generic status texts
            'error_title'         => 'Error',
            'warning_title'       => 'Warning',
            'success_title'       => 'Success',
            'cancel_btn'          => 'Cancel',

            // Basic confirmations
            'confirm_delete_title' => 'Delete this image?',
            'confirm_delete_text'  => 'This action cannot be undone.',

            // Cover management via classic form
            'cover_current_title'    => 'Current cover',
            'upload_new_cover_title' => 'Upload new cover',
            'cover_file_label'       => 'Cover file',
            'file_help_cover'        => 'JPEG/PNG/WebP, max. 30 MB.',
            'id_label'               => 'ID',

            // Navigation / header in a single tour view
            'back_btn'          => 'Back to list',

            // Stats (top bar)
            'stats_images'      => 'Images uploaded',
            'stats_cover'       => 'Covers defined',
            'stats_selected'    => 'Selected',

            // Upload zone
            'drag_or_click'     => 'Drag and drop your images or click to select.',
            'upload_help'       => 'Allowed formats: JPG, PNG, WebP. Max total size 100 MB.',
            'select_btn'        => 'Select files',
            'limit_badge'       => 'Limit of :max images reached',
            'files_word'        => 'files',

            // Multi-selection toolbar
            'select_all'        => 'Select all',
            'delete_selected'   => 'Delete selected',
            'delete_all'        => 'Delete all',

            // Per-image selector (chip)
            'select_image_title' => 'Select this image',
            'select_image_aria'  => 'Select image :id',

            // Cover (chip / card button)
            'cover_label'       => 'Cover',
            'cover_btn'         => 'Make cover',

            // Save states / JS helpers
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

            // Empty state
            'no_images'           => 'There are no images for this tour yet.',
        ],

        'errors' => [
            'validation'     => 'The data sent is not valid.',
            'upload_generic' => 'Some images could not be uploaded.',
            'update_caption' => 'The caption could not be updated.',
            'delete'         => 'The image could not be deleted.',
            'reorder'        => 'The order could not be saved.',
            'set_cover'      => 'The cover could not be set.',
            'load_list'      => 'The list could not be loaded.',
            'too_large'      => 'The file exceeds the maximum allowed size. Try a lighter image.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Prices - :name',
            'header_title'       => 'Prices: :name',
            'back_to_tours'      => 'Back to tours',

            'configured_title'   => 'Configured categories and prices',
            'empty_title'        => 'There are no categories configured for this tour.',
            'empty_hint'         => 'Use the form on the right to add categories.',

            'save_changes'       => 'Save changes',
            'auto_disable_note'  => 'Prices of $0 are automatically disabled',

            'add_category'       => 'Add category',

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

            'rule_min_le_max'    => 'Minimum must be less than or equal to maximum',
            'rule_zero_disable'  => 'Prices of $0 are automatically disabled',
            'rule_only_active'   => 'Only active categories appear on the public site',
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
            'create_disabled_hint' => 'If the price is $0, the category will be created as disabled',
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
            'max_ge_min'            => 'Maximum must be greater than or equal to minimum',
            'auto_disabled_tooltip' => 'Price $0 – automatically disabled',
            'fix_errors'            => 'Fix the minimum and maximum quantities',
        ],
    ],

    'ajax' => [
        'category_created' => 'Category created successfully',
        'category_error'   => 'Error creating the category',
        'language_created' => 'Language created successfully',
        'language_error'   => 'Error creating the language',
        'amenity_created'  => 'Amenity created successfully',
        'amenity_error'    => 'Error creating the amenity',
        'schedule_created' => 'Schedule created successfully',
        'schedule_error'   => 'Error creating the schedule',
        'itinerary_created' => 'Itinerary created successfully',
        'itinerary_error'   => 'Error creating the itinerary',
        'translation_error' => 'Error translating',
    ],

    'modal' => [
        'create_category'  => 'Create New Category',
        'create_language'  => 'Create New Language',
        'create_amenity'   => 'Create New Amenity',
        'create_schedule'  => 'Create New Schedule',
        'create_itinerary' => 'Create New Itinerary',
    ],

    'validation' => [
        'slug_taken'     => 'This slug is already in use',
        'slug_available' => 'Slug available',
    ],

];
