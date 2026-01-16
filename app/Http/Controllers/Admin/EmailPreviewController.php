<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Review;
use App\Models\ReviewRequest;
use App\Mail\BookingCreatedMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingUpdatedMail;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingCancelledExpiry;
use App\Mail\PaymentSuccessMail;
use App\Mail\PaymentFailedMail;
use App\Mail\PaymentReminderMail;
use App\Mail\PasswordSetupMail;
use App\Mail\UserWelcomeMail;
use App\Mail\ReviewRequestLink;
use App\Mail\ReviewReplyNotification;
use App\Mail\BookingCreatedAdminMail;
use App\Mail\NewPaidBookingAdmin;
use App\Mail\BookingExpiringAdmin;
use App\Mail\DailyOperationsReportMail;
use App\Mail\ReviewSubmittedNotification;
use App\Mail\ContactMessage;
use App\Mail\TestEmail;
use Illuminate\Http\Request;

use App\Notifications\AccountLockedNotification;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\PasswordUpdatedNotification;
use App\Notifications\EmailChangeVerificationNotification;
use App\Notifications\EmailChangeCompletedNotification;
use Illuminate\Notifications\Messages\MailMessage;

class EmailPreviewController extends Controller
{
    /**
     * Display a listing of all available email previews.
     */
    public function index()
    {
        $emailTypes = [
            'bookings' => [
                'label' => 'Customer Bookings',
                'icon' => 'fas fa-calendar-check',
                'items' => [
                    'booking-created' => 'Booking Created',
                    'booking-confirmed' => 'Booking Confirmed',
                    'booking-updated' => 'Booking Updated',
                    'booking-cancelled' => 'Booking Cancelled',
                    'booking-expired' => 'Booking Expired (Unpaid)',
                    'payment-success' => 'Payment Success',
                    'payment-failed' => 'Payment Failed',
                    'payment-reminder' => 'Payment Reminder',
                ]
            ],
            'reviews' => [
                'label' => 'Reviews',
                'icon' => 'fas fa-star',
                'items' => [
                    'review-request' => 'Review Request Link',
                    'review-reply' => 'Review Reply Notification',
                    'review-submitted' => 'Review Submitted (Admin Notification)',
                ]
            ],
            'auth' => [
                'label' => 'User & Account',
                'icon' => 'fas fa-user-shield',
                'items' => [
                    'welcome' => 'Welcome Email',
                    'password-setup' => 'Account Setup (Set Password)',
                    'verify-email' => 'Verify Email Address',
                    'reset-password' => 'Reset Password Request',
                    'password-updated' => 'Password Updated Notice',
                    'account-locked' => 'Account Locked Notification',
                    'email-change-verification' => 'Email Change Verification',
                    'email-change-completed' => 'Email Change Completed',
                ]
            ],
            'admin' => [
                'label' => 'Admin & Reports',
                'icon' => 'fas fa-cogs',
                'items' => [
                    'admin-booking-created' => 'New Booking Notification',
                    'admin-paid-booking' => 'Paid Booking Notification',
                    'admin-booking-expiring' => 'Booking Expiring Alert',
                    'admin-daily-report' => 'Daily Operations Report',
                    'contact-message' => 'Contact Form Message',
                ]
            ],
        ];

        return view('admin.email-preview.index', compact('emailTypes'));
    }

    /**
     * Preview a specific email type.
     */
    public function show(string $type, Request $request)
    {
        if ($request->has('locale')) {
            app()->setLocale($request->get('locale'));
        }

        $mailable = $this->getMailable($type);

        if (!$mailable) {
            abort(404, 'Email preview not found');
        }

        // Handle Notifications (which typically return a MailMessage)
        if ($mailable instanceof \Illuminate\Notifications\Notification) {
             // Mock notifiable entity (User)
            $user = $this->createMockUser();
            
            // If the notification returns a MailMessage via toMail, we ensure we return its view
            if (method_exists($mailable, 'toMail')) {
                $mailMessage = $mailable->toMail($user);
                
                // If it returns a MailMessage object, render it
                if ($mailMessage instanceof MailMessage) {
                    return $mailMessage->render(); // Correctly renders the MailMessage view
                }
            }
        }

        return $mailable;
    }

    /**
     * Get the mailable instance for a given type.
     */
    protected function getMailable(string $type)
    {
        // Get sample booking with all relations
        $booking = $this->getSampleBooking();
        $user = $booking->user ?? $this->createMockUser();

        return match ($type) {
            // Bookings (Customer)
            'booking-created' => new BookingCreatedMail($booking),
            'booking-confirmed' => new BookingConfirmedMail($booking),
            'booking-updated' => new BookingUpdatedMail($booking),
            'booking-cancelled' => new BookingCancelledMail($booking),
            'booking-expired' => new BookingCancelledExpiry($booking),
            'payment-success' => new PaymentSuccessMail(tap($booking, function($b) {
                $b->paid_at = $b->paid_at ?? now();
                $b->paid_amount = $b->paid_amount > 0 ? $b->paid_amount : $b->total;
            })),
            'payment-failed' => new PaymentFailedMail($booking),
            'payment-reminder' => new PaymentReminderMail(tap($booking, function($b) {
                 $b->auto_charge_at = now()->addDays(2);
            })),

            // Reviews
            'review-request' => new ReviewRequestLink($this->getSampleReviewRequest()),
            'review-reply' => $this->getReviewReplyMailable(),
            'review-submitted' => new ReviewSubmittedNotification($this->getSampleReview()), // Admin

            // Auth / User
            'welcome' => new UserWelcomeMail($user),
            'password-setup' => new PasswordSetupMail($user, 'sample-token-123', $booking->booking_reference),
            'verify-email' => new VerifyEmail(),
            'reset-password' => new ResetPasswordNotification('sample-reset-token'),
            'password-updated' => new PasswordUpdatedNotification(),
            'account-locked' => new AccountLockedNotification(route('login')), // Mock unlock URL
            'email-change-verification' => new EmailChangeVerificationNotification('new-email@example.com', 'sample-code-123'),
            'email-change-completed' => new EmailChangeCompletedNotification('old-email@example.com'),

            // Admin
            'admin-booking-created' => new BookingCreatedAdminMail($booking),
            'admin-paid-booking' => new NewPaidBookingAdmin(tap($booking, function($b) {
                $b->paid_at = $b->paid_at ?? now();
                $b->paid_amount = $b->paid_amount > 0 ? $b->paid_amount : $b->total;
            })),
            'admin-booking-expiring' => new BookingExpiringAdmin(tap($booking, function($b) {
                $b->pending_expires_at = $b->pending_expires_at ?? now()->addHours(12);
            })),
            'admin-daily-report' => new DailyOperationsReportMail(
                1, // confirmed count
                0, // pending count
                0, // cancelled count
                '/tmp/dummy_report.xlsx' // dummy attachment
            ),
            'contact-message' => new ContactMessage([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subject' => 'Question about tours',
                'message' => 'I would like to know more about your volcano tours. Do you offer private tours?',
                'locale' => 'en',
            ]),

            default => null,
        };
    }

    /**
     * Get a sample booking with all necessary relations.
     */
    protected function getSampleBooking(): Booking
    {
        // Try to get a real booking first
        $booking = Booking::with([
            'user',
            'tour',
            'tourLanguage',
            'hotel',
            'payments',
            'details.tour',
            'details.hotel',
            'details.schedule',
            'details.tourLanguage',
            'details.meetingPoint',
            'details.meetingPoint.translations',
            'redemption.promoCode',
        ])->latest()->first();

        // If no bookings exist, create a mock one
        if (!$booking) {
            $booking = $this->createMockBooking();
        }

        return $booking;
    }

    /**
     * Create a mock booking for preview purposes.
     */
    protected function createMockBooking(): Booking
    {
        // This creates a temporary booking object (not saved to DB)
        $booking = new Booking([
            'booking_reference' => 'PREVIEW-' . strtoupper(substr(md5(time()), 0, 8)),
            'booking_id' => 99999,
            'status' => 'pending',
            'tour_date' => now()->addDays(7),
            'subtotal' => 150.00,
            'taxes' => 19.50,
            'total' => 169.50,
            'paid_amount' => 169.50, // Added for paid booking preview
            'notes' => 'Sample booking for email preview',
            'tour_language_id' => 1, // Required field
            'checkout_token' => 'preview-token-' . time(),
            'checkout_token_expires_at' => now()->addHours(48),
            'pending_expires_at' => now()->addHours(12), // Added for expiring preview
            'paid_at' => now()->subMinutes(15), // Added for paid preview
        ]);

        // Mark as existing to prevent save attempts
        $booking->exists = true;

        // Mock user with password to prevent token generation
        $booking->setRelation('user', $this->createMockUser());

        return $booking;
    }

    protected function createMockUser(): User
    {
        $user = new User();
        $user->forceFill([
            'user_id' => 99999,
            'name' => 'John Doe',
            'email' => 'preview@example.com',
            'phone' => '+506 1234 5678',
            'password' => 'dummy-hash',
        ]);
        $user->exists = true;
        
        return $user;
    }

    /**
     * Get a sample review request.
     */
    protected function getSampleReviewRequest(): ReviewRequest
    {
        $reviewRequest = ReviewRequest::with(['booking', 'booking.user', 'booking.tour'])
            ->latest()
            ->first();

        if (!$reviewRequest) {
            // Create mock review request
            $reviewRequest = new ReviewRequest([
                'token' => 'sample-review-token-' . time(),
                'email' => 'preview@example.com',
                'customer_name' => 'John Doe',
                'status' => 'pending',
            ]);

            $reviewRequest->setRelation('booking', $this->getSampleBooking());
        }

        return $reviewRequest;
    }

    /**
     * Get a sample review.
     */
    protected function getSampleReview(): Review
    {
        $review = Review::with(['booking', 'booking.user', 'booking.tour'])
            ->latest()
            ->first();

        if (!$review) {
            // Create mock review
            $review = new Review([
                'customer_name' => 'John Doe',
                'customer_email' => 'preview@example.com',
                'rating' => 5,
                'comment' => 'Amazing experience! The tour was well organized and our guide was very knowledgeable.',
                'status' => 'approved',
            ]);

            $review->setRelation('booking', $this->getSampleBooking());
        }

        return $review;
    }

    /**
     * Get review reply mailable (creates a mock ReviewReply).
     */
    protected function getReviewReplyMailable()
    {
        $review = $this->getSampleReview();

        // Create a mock ReviewReply
        $reply = new \App\Models\ReviewReply([
            'reply_text' => 'Thank you so much for your wonderful feedback! We are thrilled to hear you enjoyed the tour. We look forward to welcoming you again soon!',
            'admin_name' => 'Green Vacations Team',
        ]);

        $reply->setRelation('review', $review);

        return new ReviewReplyNotification($reply, $reply->admin_name ?? 'Admin', $review->booking->tour->name ?? 'Tour Name', $review->customer_name);
    }
}
