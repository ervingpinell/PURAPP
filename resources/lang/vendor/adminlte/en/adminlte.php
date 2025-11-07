<?php

/**
 * Table of Contents
 *
 * 1. AUTHENTICATION AND REGISTRATION ........... Line 37
 * 2. HOTELS ................................... Line 57
 * 3. GENERAL NAVIGATION ....................... Line 67
 * 4. CONTENT AND PAGES ........................ Line 82
 * 5. TOURS AND REVIEWS ........................ Line 97
 * 6. SCHEDULES ................................ Line 131
 * 7. ITINERARIES .............................. Line 144
 * 8. HOTELS (DETAIL) .......................... Line 156
 * 9. CART AND BOOKINGS ........................ Line 180
 * 10. VALIDATION .............................. Line 219
 * 11. BUTTONS AND CRUD ........................ Line 225
 * 12. FOOTER .................................. Line 243
 * 13. WHATSAPP ................................ Line 247
 * 14. REVIEWS ................................. Line 257
 * 15. TRAVELERS ............................... Line 273
 * 16. CONTACT ................................. Line 286
 * 17. ERRORS .................................. Line 295
 * 18. CART LOGIN MODAL ........................ Line 298
 * 19. SWEETALERTS (ACTIONS) ................... Line 322
 * 20. SUCCESSES (USED IN CONTROLLERS) ......... Line 328
 * 21. MAIL .................................... Line 381
 * 22. DASHBOARD ............................... Line 386
 * 23. ENTITIES ................................ Line 394
 * 24. SECTIONS ................................ Line 408
 * 25. EMPTY STATES ............................ Line 414
 * 26. BUTTONS (GENERIC) ....................... Line 421
 * 27. LABELS .................................. Line 426
 */

return [

    // 1. AUTHENTICATION AND REGISTRATION
    'hello' => 'Hello',
    'full_name' => 'Full name',
    'email' => 'Email',
    'password' => 'Password',
    'phone' => 'Phone',
    'retype_password' => 'Retype password',
    'remember_me' => 'Remember me',
    'remember_me_hint' => 'Keep the session open indefinitely or until manually signed out',
    'register' => 'Register',
    'promo_invalid' => 'Invalid promo code.',
    'promo_already_used' => 'That promo code has already been used on another booking.',
    'no_past_dates' => 'You can’t book for dates earlier than today.',
    'dupe_submit_cart' => 'A similar booking is already being processed. Please try again in a few seconds.',
    'schedule_not_available' => 'The schedule is not available for this tour (inactive or not assigned).',
    'date_blocked' => 'The selected date is blocked for this tour.',
    'capacity_left' => 'Only :available spots left for this time.',
    'booking_created_success' => 'Booking created successfully.',
    'booking_updated_success' => 'Booking updated successfully.',
    'two_factor_authentication' => 'Two-factor authentication (2FA)',

    // 2. HOTELS
    'hotel_name_required' => 'Hotel name is required.',
    'hotel_name_unique'   => 'A hotel with that name already exists.',
    'hotel_name_max'      => 'Hotel name may not exceed :max characters.',
    'hotel_created_success' => 'Hotel created successfully.',
    'hotel_updated_success' => 'Hotel updated successfully.',
    'is_active_required'  => 'Status is required.',
    'is_active_boolean'   => 'Status must be true or false.',
    'outside_list' => 'This hotel is outside our list. Please contact us to verify if we can offer transportation.',

    // 3. GENERAL NAVIGATION
    'back' => 'Back',
    'home' => 'Home',
    'dashboard_menu' => 'Dashboard',
    'profile' => 'Profile',
    'settings' => 'Settings',
    'users' => 'Users',
    'roles' => 'Roles',
    'notifications' => 'Notifications',
    'messages' => 'Messages',
    'help' => 'Help',
    'language' => 'Language',
    'support' => 'Support',
    'admin_panel' => 'Admin panel',

    // 4. CONTENT AND PAGES
    'faq' => 'Frequently Asked Questions',
    'faqpage' => 'Frequently Asked Questions',
    'no_faqs_available' => 'No FAQs available.',
    'contact' => 'Contact',
    'about' => 'About us',
    'privacy_policy' => 'Privacy policy',
    'terms_and_conditions' => 'Terms and conditions',
    'all_policies' => 'All our policies',
    'cancellation_and_refunds_policies' => 'Cancellation and refund policies',
    'reports' => 'Reports',
    'footer_text'=> 'Green Vacations CR',
    'quick_links'=> 'Quick links',
    'rights_reserved' => 'All rights reserved',

    // 5. TOURS AND REVIEWS
    'tours' => 'Tours',
    'tour' => 'Tour',
    'tour_name' => 'Tour name',
    'overview' => 'Overview',
    'duration' => 'Duration',
    'price' => 'Price',
    'type' => 'Tour type',
    'languages_available' => 'Available languages',
    'amenities_included' => 'Included services',
    'excluded_amenities' => 'Excluded services',
    'tour_details' => 'Tour details',
    'select_tour' => 'Select a tour',
    'reviews' => 'Reviews',
    'hero_title' => 'Discover the magic of Costa Rica',
    'hero_subtext' => 'Explore our unique tours and live the adventure.',
    'book_now' => 'Book now',
    'our_tours' => 'Our tours',
    'half_day' => 'Half day',
    'full_day' => 'Full day',
    'full_day_description' => 'Perfect for those seeking a full experience in one day',
    'half_day_description' => 'Ideal for a quick adventure if you’re short on time.',
    'full_day_tours' => 'Full-day tours',
    'half_day_tours' => 'Half-day tours',
    'see_tour' => 'View tour',
    'see_tours' => 'View tours',
    'what_visitors_say' => 'What our visitors say',
    'quote_1' => 'An unforgettable experience!',
    'guest_1' => 'Carlos M.',
    'quote_2' => 'I’ll definitely come back.',
    'guest_2' => 'Ana G.',
    'tour_information'=> 'Tour information',
    'group_size'=> 'Group size',
    'no_prices_available' => 'No prices available',
    'no_prices_configured' => 'This tour has no prices configured',
    'total_persons' => 'Total persons',
    'quantity' => 'Quantity',
    'decrease' => 'Decrease',
    'increase' => 'Increase',
    'max_persons_reached' => 'Maximum :max persons per booking',
    'min_category_required' => 'At least :min required in :category',
    'max_category_exceeded' => 'Maximum :max allowed in :category',
    'max_persons_exceeded' => 'Maximum :max persons in total',
    'min_one_person' => 'At least one person is required',
    'persons_max' => 'people max',
    'or' => 'Or',
    'open_map' => 'View location',

    // 6. SCHEDULES
    'schedule' => 'Schedule',
    'schedule_am' => 'AM schedule',
    'schedule_pm' => 'PM schedule',
    'start_time' => 'Start time',
    'end_time' => 'End time',
    'select_date' => 'Select a date',
    'select_time' => 'Select a time',
    'select_language' => 'Select a language',
    'schedules' => 'Schedules',
    'horas' => 'hours',
    'hours' => 'hours',

    // 7. ITINERARIES
    'itinerary' => 'Itinerary',
    'itineraries' => 'Itineraries',
    'new_itinerary' => 'New itinerary',
    'itinerary_items' => 'Itinerary items',
    'item_title' => 'Item title',
    'item_description' => 'Item description',
    'add_item' => 'Add item',
    'edit_itinerary' => 'Edit itinerary',
    'no_itinerary_info' => 'No itinerary information.',
    'whats_included' => 'What’s included',

    // 8. HOTELS (DETAIL)
    'hotels' => 'Hotels',
    'hotel' => 'Hotel',
    'select_hotel' => 'Hotel',
    'hotel_other' => 'Other (specify manually)',
    'hotel_name' => 'Hotel name',
    'other_hotel' => 'Other hotel (specify)',
    'hotel_pickup' => 'Hotel pickup',
    'outside_area' => 'This hotel is outside the coverage area. Please contact us to review your options.',
    'pickup_valid' => 'The selected hotel is valid! Once you confirm the booking, we will contact you to coordinate the pickup time.',
    'pickup_details' => 'Pickup details',
    'pickup_note' => 'Free pickups apply only for hotels in the La Fortuna area...',
    'pickup_points' => 'Pickup points',
    'select_pickup' => 'Select a pickup point',
    'type_to_search' => 'Type to search...',
    'no_pickup_available' => 'No pickup points available.',
    'pickup_not_found' => 'Hotel not found.',
    'meeting_points' => 'Meeting points',
    'select_meeting' => 'Select a meeting point',
    'meeting_point_details' => 'Meeting point details',
    'meeting_not_found' => 'Meeting point not found.',
    'main_street_entrance' => 'Main street entrance',
    'example_address' => 'Example address 123',
    'hotels_meeting_points' => 'Hotels and meeting points',
    'meeting_valid' => 'The selected meeting point is valid! Once you confirm your booking, we’ll send you instructions and the exact meeting time.',
    'meeting_point' => 'Meeting point',
    'meetingPoint'  => 'Meeting point',
    'selectHotelHelp' => 'Select your hotel from the list.',
    'selectFromList'      => 'Select an item from the list',
    'fillThisField'       => 'Fill out this field',
    'pickupRequiredTitle' => 'Pickup required',
    'pickupRequiredBody'  => 'You must select a hotel or a meeting point to continue.',
    'ok'                  => 'OK',
    'pickup_time' => 'Pickup time',
    'pickupTime'  => 'Pickup time',
    'meeting_time' => 'Meeting time',
    'open_map' => 'Open map',
    'openMap'  => 'Open map',

    // 9. CART AND BOOKINGS
    'cart' => 'Cart',
    'myCart' => 'My cart',
    'my_reservations' => 'My bookings',
    'your_cart' => 'Your cart',
    'add_to_cart' => 'Add to cart',
    'remove_from_cart' => 'Remove from cart',
    'confirm_reservation' => 'Confirm booking',
    'confirmBooking' => 'Confirm booking',
    'cart_updated' => 'Cart updated successfully.',
    'itemUpdated' => 'Cart item updated successfully.',
    'cartItemAdded' => 'Tour added to cart successfully.',
    'cartItemDeleted' => 'Tour removed from cart successfully.',
    'emptyCart' => 'Your cart is empty.',
    'no_items_in_cart' => 'Your cart is empty.',
    'reservation_success' => 'Booking completed successfully!',
    'reservation_failed' => 'There was an error processing the booking.',
    'booking_reference' => 'Booking reference',
    'booking_date' => 'Booking date',
    'reservation_status' => 'Booking status',
    'blocked_date_for_tour' => 'The date :date is blocked for ":tour".',
    'tourCapacityFull' => 'The maximum capacity for this tour is already full.',
    'totalEstimated' => 'Estimated total',
    'total_price' => 'Total price',
    'total' => 'Total',
    'date'=> 'Date',
    'status' => 'Status',
    'actions' => 'Actions',
    'active'=> 'Active',
    'delete'=> 'Delete',
    'promoCode' => 'Do you have a promo code?',
    'promoCodePlaceholder' => 'Enter your promo code',
    'apply' => 'Apply',
    'remove' => 'Remove',
    'deleteItemTitle' => 'Delete item',
    'deleteItemText' => 'Are you sure you want to delete this item? This action cannot be undone.',
    'deleteItemConfirm' => 'Delete',
    'deleteItemCancel' => 'Cancel',
    'selectOption' => 'Select an option',
    'breakdown' => 'Breakdown',
    'subtotal'  => 'Subtotal',
    'senior'    => 'Senior',
    'student'   => 'Student',

    // 10. VALIDATION
    'required_field' => 'This field is required.',
    'invalid_email' => 'Invalid email.',
    'invalid_date' => 'Invalid date.',
    'select_option' => 'Select an option',

    // 11. BUTTONS AND CRUD
    'create' => 'Create',
    'edit' => 'Edit',
    'update' => 'Update',
    'activate' => 'Activate',
    'deactivate' => 'Deactivate',
    'confirm' => 'Confirm',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'save_changes' => 'Save changes',
    'are_you_sure' => 'Are you sure?',
    'optional' => 'Optional',
    'edit_profile' => 'Edit profile',
    'read_more' => 'Read more',
    'read_less' => 'Read less',
    'switch_view' => 'Switch view',
    'close' => 'Close',

    // 12. FOOTER
    'contact_us' => 'Contact us',
    'location' => 'San José, Costa Rica',

    // 13. WHATSAPP
    'whatsapp_title' => 'Green Vacations CR',
    'whatsapp_subtitle' => 'Usually replies instantly',
    'whatsapp_attention_schedule' => 'Monday to Sunday, 7:30 a.m. – 7:30 p.m. (GMT-6)',
    'whatsapp_attention_language' => 'Support in Spanish and English only',
    'whatsapp_greeting' => '👋 Hi! How can we help you plan your adventure in Costa Rica?',
    'whatsapp_placeholder' => 'Hi, I’m interested in one of your tours. Could you give me more information?',
    'whatsapp_button' => 'Send message',
    'whatsapp_footer' => 'Connected via WhatsApp Business',

    // 14. REVIEWS
    'what_customers_thinks_about' => 'What our customers think about',
    'loading_reviews' => 'Loading reviews',
    'redirect_to_tour' => 'Redirect to tour',
    'would_you_like_to_visit' => 'Would you like to visit ',
    'this_tour' => 'this tour',
    'no_reviews_found' => 'No reviews found for this tour.',
    'no_reviews_available' => 'No reviews available.',
    'error_loading_reviews' => 'Error loading reviews.',
    'anonymous_user' => 'Anonymous',
    'see_more' => 'See more',
    'see_less' => 'See less',
    'powered_by_viator' => 'Powered by Viator',
    'go_to_tour' => 'Do you want to go to the tour ":name"?',
    'view_in_viator' => 'View :name on Viator',

    // 15. TRAVELERS
    'select_travelers' => 'Select travelers',
    'max_travelers_info' => 'You can select up to 12 people in total.',
    'adult' => 'Adult',
    'adults' => 'Adults',
    'adults_quantity' => 'Number of adults',
    'kid' => 'Child',
    'kids' => 'Children',
    'kids_quantity' => 'Number of children',
    'age_10_plus' => 'Age 10+',
    'age_4_to_9' => 'Age 4–9',
    'max_limits_info' => 'Max. 12 travelers, max. 2 children.',
    'total_persons' => 'Total people',
    'or' => 'or',
    'min' => 'Min',

    // 16. CONTACT
    'name' => 'Name',
    'subject' => 'Subject',
    'message' => 'Message',
    'send_message' => 'Send message',
    'message_sent' => 'Message sent',
    'business_hours' => 'Business hours',
    'business_schedule' => 'Monday to Sunday, 7:30 a.m. – 7:30 p.m.',

    // 17. ERRORS
    'access_denied' => 'Access denied',

    // 18. CART LOGIN MODAL
    'login' => 'Log in',
    'view_cart' => 'View cart',
    'login_required_title' => 'You need to log in',
    'login_required_text' => 'You must log in to add to cart.',
    'login_required_text_confirm' => 'You must log in to add to cart. Go to log in?',
    'pax' => 'pax',
    'remove_item_title' => 'Remove from cart',
    'remove_item_text' => 'Do you want to remove this tour from the cart?',
    'success' => 'Success',
    'error' => 'Error',
    'validation_error' => 'Incomplete data',
    'editItem' => 'Edit item',
    // (without duplicating "close")
    'scheduleHelp' => 'If the tour doesn’t require a time slot, leave it blank.',
    'customHotel' => 'Custom hotel…',
    'otherHotel' => 'Use custom hotel',
    'customHotelName' => 'Custom hotel name',
    'customHotelHelp' => 'If you enter a custom hotel, the selection from the list will be ignored.',
    'inactive' => 'Inactive',
    'notSpecified' => 'Not specified',
    'saving' => 'Saving…',

    // 19. SWEETALERTS (ACTIONS)
    'confirmReservationTitle' => 'Are you sure?',
    'confirmReservationText' => 'Your booking will be confirmed',
    'confirmReservationConfirm' => 'Yes, confirm',
    'confirmReservationCancel' => 'Cancel',

    // 20. SUCCESSES (USED IN CONTROLLERS)
    'edit_profile_of' => 'Edit profile',
    'profile_information' => 'Profile information',
    'new_password_optional' => 'New password (optional)',
    'leave_blank_if_no_change' => 'Leave blank if you don’t want to change it',
    'confirm_new_password_placeholder' => 'Confirm new password',

    'policies' => 'Policies',
    'no_reservations_yet' => 'You don’t have any bookings yet!',
    'no_reservations_message' => 'It looks like you haven’t booked an adventure with us yet. Why not explore our amazing tours?',
    'view_available_tours' => 'View available tours',
    'pending_reservations' => 'Pending bookings',
    'confirmed_reservations' => 'Confirmed bookings',
    'cancelled_reservations' => 'Cancelled bookings',
    'reservations_generic' => 'Bookings',
    'generic_tour' => 'Generic tour',
    'unknown_tour' => 'Unknown tour',
    'tour_date' => 'Tour date',
    'participants' => 'Participants',
    'children' => 'Children',
    'not_specified' => 'Not specified',
    'status_pending' => 'Pending',
    'status_confirmed' => 'Confirmed',
    'status_cancelled' => 'Cancelled',
    'status_unknown' => 'Unknown',

    'view_receipt' => 'View receipt',

    'validation.unique' => 'This email is already in use',

    'validation' => [
        'too_many_attempts' => 'Too many failed attempts. Try again in :seconds seconds.',
    ],

    'open_tour'          => 'Go to the tour?',
    'open_tour_text_pre' => 'You’re about to open the tour page',
    'open_tour_confirm'  => 'Go now',
    'open_tour_cancel'   => 'Cancel',

    // Success messages (used in controllers)
    'show_password' => 'Show password',
    'user_registered_successfully'   => 'User registered successfully.',
    'user_updated_successfully'      => 'User updated successfully.',
    'user_reactivated_successfully'  => 'User reactivated successfully.',
    'user_deactivated_successfully'  => 'User deactivated successfully.',
    'profile_updated_successfully'   => 'Profile updated successfully.',
    'user_unlocked_successfully' => 'Your account has been unlocked. You can now log in.',
    'user_locked_successfully' => 'User locked successfully.',
    'auth_required_title' => 'You must log in to book',
    'auth_required_body'  => 'Log in or register to start your purchase. Fields are locked until you log in.',
    'login_now'           => 'Log in',
    'back_to_login'       => 'Back to log in',

    // 21. MAIL
    'mail' => [
        'trouble_clicking' => 'If you’re having trouble clicking the ":actionText" button, copy and paste the URL below into your web browser',
    ],

    // 22. DASHBOARD (section)
    'dashboard' => [
        'title'      => 'Dashboard',
        'greeting'   => 'Hi :name! 👋',
        'welcome_to' => 'Welcome to the :app admin dashboard.',
        'hint'       => 'Use the side menu to start managing content.',
    ],

    // 23. ENTITIES
    'entities' => [
        'users'        => 'Users',
        'tours'        => 'Tours',
        'tour_types'   => 'Tour types',
        'languages'    => 'Languages',
        'schedules'    => 'Schedules',
        'amenities'    => 'Amenities',
        'bookings'     => 'Bookings',
        'total_bookings'=> 'Total bookings',
        'itineraries'  => 'Itineraries',
        'items'        => 'Items',
    ],

    // 24. SECTIONS
    'sections' => [
        'available_tours' => 'Available tours',
        'upcoming_bookings'     => 'Upcoming bookings',
    ],

    // 25. EMPTY STATES
    'empty' => [
        'itinerary_items'   => 'This itinerary has no items yet.',
        'itineraries'       => 'No itineraries found.',
        'upcoming_bookings' => 'No upcoming bookings.',
    ],

    // 26. BUTTONS (GENERIC)
    'buttons' => [
        'view' => 'View',
    ],

    'persons' => [
        'count' => '{0} 0 people|{1} 1 person|[2,*] :count people',
        'title'            => 'People',
        'pax'              => 'PAX',
        'adults'           => 'Adults',
        'kids'             => 'Children',
        'seniors'          => 'Seniors',
        'infants'          => 'Infants',
        'students'         => 'Students',
        'guides'           => 'Guides',
        'drivers'          => 'Drivers',
        'free'             => 'Free',
        'other'            => 'Others',
        'category'         => 'Category',
        'categories'       => 'Categories',
        'quantity'         => 'Quantity',
        'min'              => 'Min',
        'max'              => 'Max',
        'per_person'       => 'per person',
        'price'            => 'Price',
        'subtotal'         => 'Subtotal',
        'total'            => 'Total',
        'add_category'     => 'Add category',
        'remove_category'  => 'Remove',
        'select_category'  => 'Select a category',
        'required'         => 'Required',
        'optional'         => 'Optional',
        'min_required'     => 'Minimum required: :min',
        'max_allowed'      => 'Maximum allowed: :max',
        'invalid_quantity' => 'Invalid quantity for ":category".',
    ],

    // 27. LABELS
    'labels' => [
        'reference' => 'Reference',
        'date'      => 'Date',
    ],
    'pickup'      => 'Pickup place',

];
