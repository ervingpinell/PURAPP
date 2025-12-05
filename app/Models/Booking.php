<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'tour_id',
        'tour_language_id',
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
            if (!$booking->payment_token) {
                $booking->payment_token = bin2hex(random_bytes(32));
                $booking->payment_token_created_at = now();
            }
        });
    }

    protected $casts = [
        'booking_date'              => 'datetime',
        'payment_token_created_at'  => 'datetime',
        'is_active'                 => 'boolean',
        'total'                     => 'decimal:2',
        'checkout_token_expires_at' => 'datetime',
        'checkout_accessed_at'      => 'datetime',
    ];
    // ---------------- Relaciones ----------------
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Incluir tours archivados
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id')->withTrashed();
    }

    public function tourLanguage()
    {
        return $this->belongsTo(TourLanguage::class, 'tour_language_id', 'tour_language_id');
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

    /**
     * Redención real (pivot) con el código cargado.
     * Carga encadenada del modelo PromoCode.
     */
    public function redemption()
    {
        return $this->hasOne(PromoCodeRedemption::class, 'booking_id', 'booking_id')
            ->with('promoCode');
    }

    /**
     * Compatibilidad: $booking->promoCode devuelve el modelo del cupón
     * (desde pivot) o, si lo usabas antes, por columna legacy.
     */
    public function getPromoCodeAttribute()
    {
        return $this->redemption?->promoCode;
    }

    // (opcional) legacy si aún existe la columna used_by_booking_id en promo_codes
    public function promoCodeLegacy()
    {
        return $this->hasOne(PromoCode::class, 'used_by_booking_id', 'booking_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'tour_id', 'tour_id')
            ->whereColumn('user_id', 'bookings.user_id');
    }

    public function reviewRequests()
    {
        return $this->hasMany(ReviewRequest::class, 'booking_id', 'booking_id');
    }

    /**
     * User who soft-deleted this booking
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_id');
    }

    /**
     * All payments for this booking
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id');
    }

    /**
     * Latest payment (most recent)
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'booking_id', 'booking_id')
            ->latestOfMany();
    }

    /**
     * Successful payments only
     */
    public function successfulPayments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id')
            ->where('status', 'completed');
    }

    // ---------------- Payment Helpers ----------------

    /**
     * Check if booking is fully paid
     */
    public function isPaid(): bool
    {
        return $this->successfulPayments()
            ->sum('amount') >= $this->total;
    }

    /**
     * Get total amount paid
     */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->successfulPayments()
            ->sum('amount');
    }

    /**
     * Get remaining balance to be paid
     */
    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->total - $this->total_paid);
    }

    /**
     * Check if booking has any pending payment
     */
    public function hasPendingPayment(): bool
    {
        return $this->payments()
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
    }

    /**
     * Check if booking is paid but awaiting admin confirmation
     */
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

    /**
     * Generate a unique checkout token for this booking
     */
    public function generateCheckoutToken(): string
    {
        $this->checkout_token = \Illuminate\Support\Str::random(64);
        $this->checkout_token_expires_at = now()->addHours(48); // 48h to access
        $this->save();

        return $this->checkout_token;
    }

    /**
     * Get the checkout URL for this booking
     */
    public function getCheckoutUrl(): string
    {
        if (!$this->checkout_token) {
            $this->generateCheckoutToken();
        }

        return route('public.checkout.token', ['token' => $this->checkout_token]);
    }

    /**
     * Check if the checkout token is still valid
     */
    public function isCheckoutTokenValid(): bool
    {
        return $this->checkout_token
            && $this->checkout_token_expires_at
            && $this->checkout_token_expires_at->isFuture()
            && $this->status === 'pending';
    }

    // ---------------- Payment Token Methods ----------------

    /**
     * Get the payment URL for this booking (token-based, no auth required)
     */
    public function getPaymentUrl(): string
    {
        // Ensure booking has a payment token
        if (!$this->payment_token) {
            $this->payment_token = bin2hex(random_bytes(32));
            $this->save();
        }

        return route('payment.token', ['token' => $this->payment_token]);
    }

    /**
     * Regenerate payment token (if compromised)
     */
    public function regeneratePaymentToken(): string
    {
        $this->payment_token = bin2hex(random_bytes(32));
        $this->payment_token_created_at = now();
        $this->save();

        return $this->payment_token;
    }
}
