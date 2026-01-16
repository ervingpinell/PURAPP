<?php

return [
    'no_reviews' => 'No reviews available',

    'what_visitors_say' => 'What do our customers say?',
    'powered_by'        => 'Powered by',

    'generic' => [
        'our_tour' => 'our tour',
    ],

    // =========================
    // Common
    // =========================
    'common' => [
        'reviews'   => 'Reviews',
        'provider'  => 'Provider',
        'status'    => 'Status',
        'tour'      => 'Tour',
        'rating'    => 'Rating',
        'title'     => 'Title',
        'body'      => 'Content',
        'author'    => 'Author',
        'actions'   => 'Actions',
        'filter'    => 'Filter',
        'search'    => 'Search',
        'id'        => 'ID',
        'public'    => 'Public',
        'private'   => 'Private',
        'back'      => 'Back',
        'save'      => 'Save',
        'create'    => 'Create',
        'edit'      => 'Edit',
        'delete'    => 'Delete',
        'publish'   => 'Publish',
        'hide'      => 'Hide',
        'flag'      => 'Flag',
        'unflag'    => 'Unflag',
        'apply'     => 'Apply',
        'yes'       => 'Yes',
        'no'        => 'No',
        'not_found' => 'No results were found.',
        'clear'     => 'Clear',
        'language'  => 'Language',

        // 🔹 Keys added for Providers panel
        'new'             => 'New',
        'name'            => 'Name',
        'active'          => 'Active',
        'inactive'        => 'Inactive',
        'indexable'       => 'Indexable',
        'indexable_yes'   => 'Includes indexable/JSON-LD markup',
        'indexable_no'    => 'Not indexable',
        'activate'        => 'Activate provider',
        'deactivate'      => 'Deactivate provider',
        'activate_title'  => 'Activate provider?',
        'activate_text'   => 'The provider will be set as active.',
        'deactivate_title' => 'Deactivate provider?',
        'deactivate_text' => 'The provider will no longer be active.',
        'cancel'          => 'Cancel',
        'test'            => 'Test connection',
        'flush_cache'     => 'Flush cache',
        'delete_confirm'  => 'Delete provider?',
        'system_locked'   => 'System provider (locked)',
    ],

    // =========================
    // Review statuses (moderation)
    // =========================
    'status' => [
        'pending'   => 'pending',
        'published' => 'published',
        'hidden'    => 'hidden',
        'flagged'   => 'flagged',
    ],

    // =========================
    // Admin - list / moderation
    // =========================
    'admin' => [
        'index_title'   => 'Reviews',
        'index_titel'   => 'Reviews', // alias for common typo

        'new_local'     => 'New (local)',
        'bulk_apply'    => 'Apply to selected',

        'responded'     => 'Responded?',
        'last_reply'    => 'Last:',

        'filters'       => [
            'provider'  => 'Provider',
            'status'    => 'Status',
            'tour_id'   => 'Tour ID',
            'stars'     => '⭐',
            'q'         => 'Search text/author…',
            'responded' => 'Responded?',
        ],

        'table' => [
            'date'     => 'Date',
            'review'   => 'Review',
            'client'   => 'Client',
            'tour'     => 'Tour',
        ],

        'messages' => [
            'created'        => 'Review created.',
            'updated'        => 'Review updated.',
            'deleted'        => 'Review deleted.',
            'published'      => 'Review published.',
            'hidden'         => 'Review hidden.',
            'flagged'        => 'Review flagged.',
            'unflagged'      => 'Review unflagged.',
            'bulk_published' => ':n reviews published.',
            'bulk_hidden'    => ':n reviews hidden.',
            'bulk_flagged'   => ':n reviews flagged.',
            'bulk_deleted'   => ':n reviews deleted.',
            'publish_min_rating' => 'Cannot publish because the rating (:rating★) is lower than the allowed minimum (:min★).',
            'bulk_published_partial' => ':ok reviews published. :skipped skipped because their rating was lower than :min★.',
        ],
    ],

    // =========================
    // Admin - replies
    // =========================
    'replies' => [
        'reply'            => 'Reply',
        'title_create'     => 'Reply — Review #:id',
        'label_body'       => 'Reply',
        'label_is_public'  => 'Public',
        'label_notify'     => 'Send email to customer',
        'notify_to'        => 'It will be sent to: :email',
        'warn_no_email'    => 'Warning: we did not find an email address for the customer in this review. The reply will be saved, but no email will be sent.',
        'saved_notified'   => 'Reply published and emailed to :email.',
        'saved_no_email'   => 'Reply published. No email was sent because no recipient was found.',
        'deleted'          => 'Reply deleted.',
        'visibility_ok'    => 'Visibility updated.',
        'thread_title'     => 'Conversation — Review #:id',
        'thread_empty'     => 'No replies.',
        'last_reply'       => 'Last:',
    ],

    // =========================
    // Admin - post-purchase review requests
    // =========================
    'requests' => [
        'index_title' => 'Request reviews',
        'subtitle'    => 'Send post-purchase review links and manage already sent requests.',

        // Tabs
        'tabs' => [
            'eligible'  => 'Eligible (bookings)',
            'requested' => 'Requested (sent)',
        ],

        // Filters
        'filters' => [
            'q_placeholder' => 'ID, name or email',
            'any_status'    => '— Any —',
            'from'          => 'From',
            'to'            => 'To',
        ],

        'window_days'      => 'Window (days)',
        'date_column'      => 'Date column',
        'calculated_range' => 'Calculated range',
        'tour_id'          => 'Tour ID',
        'btn_request'      => 'Request review',
        'no_eligible'      => 'No eligible bookings.',

        'table' => [
            'booking'   => 'Booking',
            'reference' => 'Reference',
            'sent_at'   => 'Sent at',
            'states'    => 'States',
        ],

        'labels' => [
            'expires_in_days' => 'Expiration (days)',
            'expires_at'      => 'Expires at',
            'used_at'         => 'Used at',
        ],

        'actions' => [
            'resend'         => 'Resend',
            'confirm_delete' => 'Delete this request?',
        ],

        'status' => [
            'active'    => 'Active',
            'sent'      => 'Sent',
            'reminded'  => 'Reminded',
            'used'      => 'Used',
            'expired'   => 'Expired',
            'cancelled' => 'Cancelled',
        ],

        'status_labels' => [
            'created'   => 'created',
            'sent'      => 'sent',
            'reminded'  => 'reminded',
            'fulfilled' => 'completed',
            'expired'   => 'expired',
            'cancelled' => 'cancelled',
            'active'    => 'active',
        ],

        'send_ok'   => 'Review request sent.',
        'resend_ok' => 'Request resent.',
        'remind_ok' => 'Reminder sent.',
        'expire_ok' => 'Request expired.',
        'deleted'   => 'Request deleted.',
        'none'      => 'No requests.',

        'errors' => [
            'used'    => 'This request has already been used.',
            'expired' => 'This request is expired.',
        ],
    ],

    // =========================
    // Public (review form)
    // =========================
    'public' => [
        'form_title'   => 'Leave a review',
        'labels'       => [
            'rating'       => 'Rating',
            'title'        => 'Title (optional)',
            'body'         => 'Your experience',
            'author_name'  => 'Your name (optional)',
            'author_email' => 'Your email (optional)',
            'submit'       => 'Submit review',
        ],
        'thanks'       => 'Thank you for your review! 🌿',
        'thanks_body'  => 'Your opinion is very important and helps us improve. We truly appreciate it.',
        'thanks_farewell' => "We hope you enjoyed your time with us and we hope to see you again soon.\n\n🇨🇷 Pura Vida mae! 🇨🇷",
        'thanks_dup'   => 'Thank you! We already had your review on file 🙌',
        'expired'      => 'This link has expired, but thank you so much for your intention 💚',
        'used'         => 'This request has already been used.',
        'used_help'    => 'This review link has already been used. If you think this is an error or want to update your comment, contact us and we will gladly help you.',
        'not_found'    => 'Request not found.',
        'back_home'    => 'Go back',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [

        'brand_from'   => config('app.name', 'Green Vacations CR'),
        'contact_line' => 'If you need help, contact us at :email or :phone. Visit us at :url.',
        'request' => [
            'preheader_with_date' => 'Tell us about your experience on :tour (:date). It only takes a minute.',
            'preheader'           => 'Tell us about your experience on :tour. It only takes a minute.',
            'subject'   => 'How was your experience on :tour?',
            'cta'       => 'Leave my review',
            'footer'    => 'Thank you for supporting local tourism. We hope to see you back soon! 🌿',
            'expires'   => '* This link will be active until: :date.',
            'greeting'  => 'Hi :name,',
            'intro'     => 'Pura vida! 🙌 Thank you for choosing us. We would love to know how your experience on :tour was.',
            'ask'       => 'Would you give us 1–2 minutes to leave your review? It really means a lot.',
            'fallback'  => 'If the button does not work, copy and paste this link into your browser:',
        ],
        'reply' => [
            'subject'  => 'Reply to your review',
            'greeting' => 'Hi :name,',
            'intro'    => 'Our team has replied to your review :extra.',
            'quote'    => '“:text”',
            'sign'     => '— :admin',
            'closing'  => 'Wenn Sie Fragen haben oder Ihren Kommentar erweitern möchten, antworten Sie einfach auf diese E-Mail. Pura vida! 🌿',
        ],
        'submitted' => [
            'subject' => 'New review received',
        ],
    ],

    // =========================
    // Front
    // =========================
    'front' => [
        'see_more'   => 'See more reviews',
        'no_reviews' => 'There are no reviews yet.',
    ],

    // =========================
    // Providers
    // =========================
    'providers' => [
        'index_title' => 'Review providers',
        'indexable' => 'Indexable',
        'cache_ttl' => 'Cache TTL (sec)',
        'back' => 'Back',
        'actions' => 'Actions',
        'system_locked' => 'System Provider',
        'messages' => [
            'cannot_delete_local' => 'The “local” provider is a system provider and cannot be deleted.',
            'created'        => 'Provider created.',
            'updated'        => 'Provider updated.',
            'deleted'        => 'Provider deleted.',
            'status_updated' => 'Status updated.',
            'cache_flushed'  => 'Cache flushed.',
            'test_fetched'   => ':n reviews fetched.',
            'mapping_added'   => 'Mapping added successfully.',
            'mapping_updated' => 'Mapping updated successfully.',
            'mapping_deleted' => 'Mapping deleted successfully.',
        ],
        'product_map' => [
            'title' => 'Product Mapping - :provider',
        ],
        'product_mapping_title' => 'Product Mapping - :name',
        'product_mappings' => 'Product Mappings',
        'tour' => 'Tour',
        'select_tour' => 'Select tour',
        'select_tour_placeholder' => 'Select a tour...',
        'product_code' => 'Product code',
        'product_code_placeholder' => 'Ex: 12732-ABC',
        'add_mapping' => 'Add mapping',
        'no_mappings' => 'No mappings configured',
        'confirm_delete_mapping' => 'Are you sure you want to delete this mapping?',
        'help_title' => 'Help',
        'help_text' => 'Map external product codes to internal tours to sync reviews correctly.',
        'help_step_1' => 'Select a tour from the list',
        'help_step_2' => 'Enter the external provider product code',
        'help_step_3' => 'Click "Add" to create the mapping',
    ],

    // =========================
    // Sync
    // =========================
    'sync' => [
        'queued' => 'Sync queued for :target.',
        'all'    => 'all providers',
    ],

    // =========================
    // Thread / conversation
    // =========================
    'thread' => [
        'title'             => 'Review thread #:id',
        'header'            => 'Thread — Review #:id',
        'replies_header'    => 'Replies',
        'th_date'           => 'Date',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Visible',
        'th_body'           => 'Content',
        'th_actions'        => 'Actions',
        'toggle_visibility' => 'Toggle visibility',
        'delete'            => 'Delete',
        'confirm_delete'    => 'Delete reply?',
        'empty'             => 'No replies yet.',
    ],

    // =========================
    // Admin form (create/edit)
    // =========================
    'form' => [
        'title_edit'       => 'Edit review',
        'title_new'        => 'New review',
        'visible_publicly' => 'Visible publicly',
    ],

    // =========================
    // Alias for reply emails (if used outside "emails")
    // =========================
    'reply' => [
        'subject'          => 'Reply to your review',
        'greeting'         => 'Hi :name,',
        'about_html'       => 'about <strong>:tour</strong>',
        'about_text'       => 'about :tour',
        'intro'            => 'Our team has replied to your review :extra.',
        'quote'            => '“:text”',
        'sign'             => '— :admin',
        'closing'          => 'If you have any questions or would like to expand on your comment, just reply to this email. Pura vida! 🌿',
        'rights_reserved'  => 'All rights reserved',
    ],

    // Fallback for greeting if there is no name
    'traveler' => 'traveler',

    // =====================================================================
    // ==== Compatibility with old translation file (legacy) ================
    // =====================================================================

    'loaded'           => 'Reviews loaded successfully.',
    'provider_error'   => 'There was a problem with the review provider.',
    'service_busy'     => 'The service is busy, please try again shortly.',
    'unexpected_error' => 'An unexpected error occurred while loading reviews.',
    'anonymous'        => 'Anonymous',

    'what_customers_think_about' => 'What customers think about',
    'previous_review'            => 'Previous review',
    'next_review'                => 'Next review',
    'loading'                    => 'Loading reviews...',
    // 'what_visitors_say' already exists above; kept for compatibility
    'reviews_title'              => 'Customer reviews',
    // 'powered_by' already exists above; kept for compatibility
    'view_on_viator'             => 'View :name on Viator',

    // Modal / actions (legacy)
    'open_tour_title'    => 'Open tour page?',
    'open_tour_text_pre' => 'You are about to open the tour page for',
    'open_tour_confirm'  => 'Open now',
    'open_tour_cancel'   => 'Cancel',

    // Carousel controls (legacy, alias of front.see_more/less)
    'previous' => 'Previous',
    'next'     => 'Next',
    'see_more' => 'See more',
    'see_less' => 'See less',
];
