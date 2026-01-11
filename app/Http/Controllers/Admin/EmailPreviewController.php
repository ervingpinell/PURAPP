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

class EmailPreviewController extends Controller
{
    /**
     * Display a listing of all available email previews.
     */
    public function index()
    {
        $emailTypes = [
            'customer' => [
                'booking-created' => 'Booking Created (Customer)',
                'booking-confirmed' => 'Booking Confirmed',
                'booking-updated' => 'Booking Updated',
                'booking-cancelled' => 'Booking Cancelled',
                'booking-expired' => 'Booking Expired (Unpaid)',
                'payment-success' => 'Payment Success',
                'payment-failed' => 'Payment Failed',
                'payment-reminder' => 'Payment Reminder',
                'password-setup' => 'Password Setup',
                'welcome' => 'Welcome Email',
                'review-request' => 'Review Request',
                'review-reply' => 'Review Reply Notification',
            ],
            'admin' => [
                'admin-booking-created' => 'New Booking (Admin)',
                'admin-paid-booking' => 'Paid Booking (Admin)',
                'admin-booking-expiring' => 'Booking Expiring (Admin)',
                'admin-daily-report' => 'Daily Operations Report',
                'admin-review-submitted' => 'Review Submitted (Admin)',
            ],
            'other' => [
                'contact-message' => 'Contact Form Message',
                'test' => 'Test Email',
            ],
        ];

        return view('admin.email-preview.index', compact('emailTypes'));
    }

    /**
     * Preview a specific email type.
     */
    public function show(string $type)
    {
        $mailable = $this->getMailable($type);

        if (!$mailable) {
            abort(404, 'Email preview not found');
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
        $user = $booking->user;

        return match ($type) {
            // Customer emails
            'booking-created' => new BookingCreatedMail($booking),
            'booking-confirmed' => new BookingConfirmedMail($booking),
            'booking-updated' => new BookingUpdatedMail($booking),
            'booking-cancelled' => new BookingCancelledMail($booking),
            'booking-expired' => new BookingCancelledExpiry($booking),
            'payment-success' => new PaymentSuccessMail($booking),
            'payment-failed' => new PaymentFailedMail($booking),
            'payment-reminder' => new PaymentReminderMail($booking),
            'password-setup' => new PasswordSetupMail($user, 'sample-token-123', $booking->booking_reference),
            'welcome' => new UserWelcomeMail($user),
            'review-request' => new ReviewRequestLink($this->getSampleReviewRequest()),
            'review-reply' => $this->getReviewReplyMailable(),

            // Admin emails
            'admin-booking-created' => new BookingCreatedAdminMail($booking),
            'admin-paid-booking' => new NewPaidBookingAdmin($booking),
            'admin-booking-expiring' => new BookingExpiringAdmin($booking),
            'admin-daily-report' => new DailyOperationsReportMail(
                collect([$booking]), // confirmed
                collect([]), // pending
                collect([]), // cancelled
                null // no attachment
            ),
            'admin-review-submitted' => new ReviewSubmittedNotification($this->getSampleReview()),

            // Other
            'contact-message' => new ContactMessage([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subject' => 'Question about tours',
                'message' => 'I would like to know more about your volcano tours. Do you offer private tours?',
                'locale' => 'en',
            ]),
            'test' => new TestEmail(),

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
            'notes' => 'Sample booking for email preview',
            'tour_language_id' => 1, // Required field
            'checkout_token' => 'preview-token-' . time(),
            'checkout_token_expires_at' => now()->addHours(48),
        ]);

        // Mark as existing to prevent save attempts
        $booking->exists = true;

        // Mock user with password to prevent token generation
        $booking->setRelation('user', new User([
            'user_id' => 99999,
            'name' => 'John Doe',
            'email' => 'preview@example.com',
            'phone' => '+506 1234 5678',
            'password' => 'dummy-hash', // Prevents password setup token generation
        ]));

        return $booking;
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
     * Get booking created mailable (prevents password setup token generation).
     */
    protected function getBookingCreatedMailable(Booking $booking)
    {
        // Ensure user has a password to prevent token generation
        if ($booking->user && !$booking->user->password) {
            $booking->user->password = 'dummy-password-hash';
        }

        return new BookingCreatedMail($booking);
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

        return new ReviewReplyNotification($reply, $reply->reply_text ?? '');
    }
}
