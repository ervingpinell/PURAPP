<?php

return [

    'what_visitors_say' => 'What do our visitors say?',
    'powered_by'        => 'Powered by',

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
        'not_found' => 'No results found.',
        'clear'     => 'Clear',
        'language'  => 'Language',

        // Provider panel additions
        'new'              => 'New',
        'name'             => 'Name',
        'active'           => 'Active',
        'inactive'         => 'Inactive',
        'indexable'        => 'Indexable',
        'indexable_yes'    => 'Includes indexable markup/JSON-LD',
        'indexable_no'     => 'Not indexable',
        'activate'         => 'Activate provider',
        'deactivate'       => 'Deactivate provider',
        'activate_title'   => 'Activate provider?',
        'activate_text'    => 'The provider will be activated.',
        'deactivate_title' => 'Deactivate provider?',
        'deactivate_text'  => 'The provider will be deactivated.',
        'cancel'           => 'Cancel',
        'test'             => 'Test connection',
        'flush_cache'      => 'Clear cache',
        'delete_confirm'   => 'Delete provider?',
        'system_locked'    => 'System provider (locked)',
    ],

    // =========================
    // Review status (moderation)
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
        'index_title' => 'Reviews',
        'index_titel' => 'Reviews', // legacy alias for common typo

        'new_local'  => 'New (local)',
        'bulk_apply' => 'Apply to selected',

        'responded'  => 'Responded?',
        'last_reply' => 'Last:',

        'filters' => [
            'provider'  => 'Provider',
            'status'    => 'Status',
            'tour_id'   => 'Tour ID',
            'stars'     => '⭐',
            'q'         => 'Search text/author...',
            'responded' => 'Responded?',
        ],

        'table' => [
            'date'   => 'Date',
            'review' => 'Review',
            'client' => 'Client',
            'tour'   => 'Tour',
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
            'bulk_published_partial' => ':ok reviews published. Skipped :skipped due to rating lower than :min★.',
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
        'label_notify'     => 'Send email to the customer',
        'notify_to'        => 'Will be sent to: :email',
        'warn_no_email'    => 'Attention: we could not find the customer’s email on this review. The reply will be saved, but no email will be sent.',
        'saved_notified'   => 'Reply posted and emailed to :email.',
        'saved_no_email'   => 'Reply posted. No email was sent because no recipient was found.',
        'deleted'          => 'Reply deleted.',
        'visibility_ok'    => 'Visibility updated.',
        'thread_title'     => 'Conversation — Review #:id',
        'thread_empty'     => 'No replies.',
        'last_reply'       => 'Last:',
    ],

    // =========================
    // Admin - post-purchase requests
    // =========================
    'requests' => [
        'index_title' => 'Request reviews',
        'subtitle'    => 'Send post-purchase review links and manage requests already sent.',

        'tabs' => [
            'eligible'  => 'Eligible (bookings)',
            'requested' => 'Requested (sent)',
        ],

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
            'sent_at'   => 'Sent',
            'states'    => 'States',
        ],

        'labels' => [
            'expires_in_days' => 'Expiration days',
            'expires_at'      => 'Expires',
            'used_at'         => 'Used',
        ],

        'actions' => [
            'resend'         => 'Resend',
            'confirm_delete' => 'Delete this request?',
        ],

        'status' => [
            'active'    => 'Active',
            'sent'      => 'Sent',
            'reminded'  => 'Resent',
            'used'      => 'Used',
            'expired'   => 'Expired',
            'cancelled' => 'Cancelled',
        ],

        'status_labels' => [
            'created'   => 'created',
            'sent'      => 'sent',
            'reminded'  => 'resent',
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
        'thanks_dup'   => 'Thanks! We already had your review on file 🙌',
        'expired'      => 'This link has expired, but thank you for your intention 💚',
        'used'         => 'This request has already been used.',
        'used_help'    => 'This review link has already been used. If you think this is an error or want to update your comment, contact us and we’ll gladly help.',
        'not_found'    => 'Request not found.',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [
        'brand_from'   => 'Green Vacations CR',
        'contact_line' => 'If you need help, contact us at :email or :phone. Visit us at :url.',
        'request' => [
            'subject'   => 'How was your experience on :tour?',
            'cta'       => 'Leave my review',
            'footer'    => 'Thanks for supporting local tourism. We hope to see you again soon! 🌿',
            'expires'   => '* This link will be active until: :date.',
            'greeting'  => 'Hi :name,',
            'intro'     => 'Pura vida! 🙌 Thanks for choosing us. We’d love to know how it went on :tour.',
            'ask'       => 'Could you spare 1–2 minutes to leave your review? It truly helps a lot.',
            'fallback'  => 'If the button does not work, copy and paste this link in your browser:',
        ],
        'reply' => [
            'subject'  => 'Reply to your review',
            'greeting' => 'Hi :name,',
            'intro'    => 'Our team has replied to your review :extra.',
            'quote'    => '“:text”',
            'sign'     => '— :admin',
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
        'system_locked' => 'System provider',
        'messages' => [
            'cannot_delete_local' => 'The “local” provider is a system record and cannot be deleted.',
            'created'        => 'Provider created.',
            'updated'        => 'Provider updated.',
            'deleted'        => 'Provider deleted.',
            'status_updated' => 'Status updated.',
            'cache_flushed'  => 'Cache cleared.',
            'test_fetched'   => 'Fetched :n reviews.',
        ],
    ],

    // =========================
    // Sync
    // =========================
    'sync' => [
        'queued' => 'Sync enqueued for :target.',
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
        'title_edit'       => 'Edit Review',
        'title_new'        => 'New Review',
        'visible_publicly' => 'Publicly visible',
    ],

    // =========================
    // Reply email alias (if used outside "emails")
    // =========================
    'reply' => [
        'subject'          => 'Reply to your review',
        'greeting'         => 'Hi :name,',
        'about_html'       => 'about <strong>:tour</strong>',
        'about_text'       => 'about :tour',
        'intro'            => 'Our team has replied to your review :extra.',
        'quote'            => '“:text”',
        'sign'             => '— :admin',
        'closing'          => 'If you have questions or want to expand your comment, just reply to this email. Pura vida! 🌿',
        'rights_reserved'  => 'All rights reserved',
    ],

    'traveler' => 'traveler',

    // =========================
    // Legacy compatibility
    // =========================
    'loaded'           => 'Reviews loaded successfully.',
    'provider_error'   => 'There was a problem with the reviews provider.',
    'service_busy'     => 'The service is busy, please try again shortly.',
    'unexpected_error' => 'An unexpected error occurred while loading reviews.',
    'anonymous'        => 'Anonymous',

    'what_customers_think_about' => 'What customers think about',
    'previous_review'            => 'Previous review',
    'next_review'                => 'Next review',
    'loading'                    => 'Loading reviews...',
    'reviews_title'              => 'Customer reviews',
    'view_on_viator'             => 'View :name on Viator',

    'open_tour_title'    => 'Open tour?',
    'open_tour_text_pre' => 'You’re about to open the tour page',
    'open_tour_confirm'  => 'Open now',
    'open_tour_cancel'   => 'Cancel',

    'previous' => 'Previous',
    'next'     => 'Next',
    'see_more' => 'See more',
    'see_less' => 'See less',
];
