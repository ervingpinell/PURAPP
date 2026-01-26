<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Booking Model
 *
 * Represents a product booking made by a customer.
 * Handles booking lifecycle, payment tracking, and customer data.
 *
 * @property int $booking_id Primary key
 * @property int|null $user_id User who made the booking (null for guests)
 * @property int $product_id Product being booked
 * @property string $booking_reference Unique booking reference code
 * @property string $status Booking status (pending, confirmed, paid, cancelled)
 * @property bool $is_paid Payment status flag
 * @property float $total Total booking amount
 * @property float $paid_amount Amount already paid
 * @property array|null $user_snapshot Guest user data snapshot
 * @property string|null $checkout_token Secure token for guest checkout
 * @property \Carbon\Carbon|null $checkout_token_expires_at Token expiration
 * @property \Carbon\Carbon|null $pending_expires_at Pending payment expiration
 * @property \Carbon\Carbon $booking_date When booking was created
 * @property \Carbon\Carbon|null $paid_at When payment was completed
 * @property \Carbon\Carbon|null $deleted_at Soft delete timestamp
 *
 * @property-read User|null $user
 * @property-read Product $product
 * @property-read ProductLanguage $tourLanguage
 * @property-read \Illuminate\Database\Eloquent\Collection|BookingDetail[] $details
 * @property-read \Illuminate\Database\Eloquent\Collection|Payment[] $payments
 * @property-read Payment|null $latestPayment
 */
class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'user_email',
        'user_full_name',
        'user_phone',
        'user_was_guest',
        'product_id', // Renamed from product_id
        'tour_language_id', // Name kept as is for now
        'booking_reference',
        'payment_token',
        'payment_token_created_at',
        'booking_date',
        'status',
        'total',
        'hotel_id',
        'is_active',
        'schedule_id',
        'notes',
        'deleted_by',
        'checkout_token',
        'checkout_token_expires_at',
        'checkout_accessed_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = self::generateBookingReference();
            }
        });
    }

    protected $casts = [
        'booking_date'              => 'datetime',
        'payment_token_created_at'  => 'datetime',
        'is_active'                 => 'boolean',
        'user_was_guest'            => 'boolean',
        'total'                     => 'decimal:2',
        'checkout_token_expires_at' => 'datetime',
        'checkout_accessed_at'      => 'datetime',
    ];

    /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id')->withTrashed();
    }

    public function productLanguage()
    {
        return $this->belongsTo(ProductLanguage::class, 'product_language_id', 'product_language_id');
    }

    public function details()
    {
        return $this->hasMany(BookingDetail::class, 'booking_id', 'booking_id');
    }

    public function detail()
    {
        return $this->hasOne(BookingDetail::class, 'booking_id', 'booking_id');
    }

    public function hotel()
    {
        return $this->belongsTo(HotelList::class, 'hotel_id', 'hotel_id');
    }

    public function redemption()
    {
        return $this->hasOne(PromoCodeRedemption::class, 'booking_id', 'booking_id')
            ->with('promoCode');
    }

    public function getPromoCodeAttribute()
    {
        return $this->redemption?->promoCode;
    }

    public function promoCodeLegacy()
    {
        return $this->hasOne(PromoCode::class, 'used_by_booking_id', 'booking_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id') // Updated FK
            ->whereColumn('user_id', 'bookings.user_id');
    }

    public function reviewRequests()
    {
        return $this->hasMany(ReviewRequest::class, 'booking_id', 'booking_id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id');
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'booking_id', 'booking_id')
            ->latestOfMany();
    }

    public function successfulPayments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id')
            ->where('status', 'completed');
    }

    // ---------------- Payment Helpers ----------------

    public function isPaid(): bool
    {
        return $this->successfulPayments()
            ->sum('amount') >= $this->total;
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->successfulPayments()
            ->sum('amount');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->total - $this->total_paid);
    }

    public function hasPendingPayment(): bool
    {
        return $this->payments()
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
    }

    public function awaitingConfirmation(): bool
    {
        return $this->status === 'pending' && $this->isPaid();
    }

    // ---------------- Mutators/Accesors ----------------
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }

    public function getReferenceAttribute()
    {
        return $this->booking_reference;
    }

    // ---------------- Checkout Token Methods ----------------

    public function generateCheckoutToken(): string
    {
        $this->checkout_token = \Illuminate\Support\Str::random(64);
        $this->checkout_token_expires_at = now()->addHours(48); // 48h to access
        $this->save();

        return $this->checkout_token;
    }

    public function getCheckoutUrl(): string
    {
        if (!$this->checkout_token) {
            $this->generateCheckoutToken();
        }

        return route('public.checkout.token', ['token' => $this->checkout_token]);
    }

    public function isCheckoutTokenValid(): bool
    {
        return $this->checkout_token
            && $this->checkout_token_expires_at
            && $this->checkout_token_expires_at->isFuture()
            && $this->status === 'pending';
    }

    // ---------------- Payment Token Methods ----------------

    public function getPaymentUrl(): string
    {
        if (!$this->payment_token) {
            $this->payment_token = bin2hex(random_bytes(32));
            $this->save();
        }

        return route('payment.show-by-token', ['token' => $this->payment_token]);
    }

    public function regeneratePaymentToken(): string
    {
        $this->payment_token = bin2hex(random_bytes(32));
        $this->payment_token_created_at = now();
        $this->save();

        return $this->payment_token;
    }

    public static function generateBookingReference()
    {
        // Simple generator helper
        return 'BK-' . strtoupper(\Illuminate\Support\Str::random(8));
    }

    // ---------------- User Snapshot Methods ----------------

    public function getUserDisplayName(): string
    {
        if ($this->user) {
            return $this->user->full_name;
        }

        return $this->user_full_name ?? 'Deleted User';
    }

    public function getUserEmail(): ?string
    {
        if ($this->user) {
            return $this->user->email;
        }

        return $this->user_email;
    }

    public function getUserPhone(): ?string
    {
        if ($this->user) {
            return $this->user->phone;
        }

        return $this->user_phone;
    }

    public function wasGuestBooking(): bool
    {
        if ($this->user) {
            return (bool) ($this->user->is_guest ?? false);
        }

        return (bool) $this->user_was_guest;
    }

    public function userExists(): bool
    {
        return $this->user_id !== null && $this->user !== null;
    }
}
