<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * BookingDetail Model
 *
 * Represents detailed information for a booking.
 * Stores product date, schedule, categories, pricing, and pickup details.
 *
 * @property int $details_id Primary key
 * @property int $booking_id Associated booking
 * @property int $product_id Product being booked
 * @property int $schedule_id Schedule/time slot
 * @property \Carbon\Carbon $tour_date Date of the product (DB column name)
 * @property int|null $product_language_id Language (DB column name)
 * @property array $categories JSON array
 * @property float $total Total amount
 * @property-read Booking $booking
 * @property-read Product $product
 */
class BookingDetail extends Model
{
    protected $table = 'booking_details';
    protected $primaryKey = 'details_id';
    public $timestamps = true;

    protected $fillable = [
        'booking_id',
        'product_id', // Renamed from product_id
        'schedule_id',
        'product_date', // Kept legacy name in DB column
        'product_language_id',
        'categories',   // JSON
        'total',
        'hotel_id',
        'is_other_hotel',
        'other_hotel_name',
        'meeting_point_id',
        'meeting_point_name',
        'meeting_point_pickup_time',
        'meeting_point_description',
        'meeting_point_map_url',
        'pickup_time',
        'taxes_breakdown',
        'taxes_total',
    ];

    protected $casts = [
        'product_date'     => 'date',
        'categories'      => 'array',
        'is_other_hotel'  => 'boolean',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'taxes_breakdown' => 'array',
        'taxes_total'     => 'decimal:2',
    ];

    public function calculateTaxes(): array
    {
        // Ensure product relation is loaded
        if (!$this->relationLoaded('product')) {
            $this->load('product.taxes'); // Assuming product has taxes relation
        }
        
        // Use product instead of product
        $product = $this->product;
        // If product doesn't exist or has no taxes relation yet, return empty
        if (!$product) {
             return ['breakdown' => [], 'total' => 0];
        }

        $taxes = $product->taxes ?? collect();
        $subtotal = $this->subtotal;
        $totalPersons = $this->total_pax;

        $taxesBreakdown = [];
        $taxesTotal = 0;
        $runningTotal = $subtotal;

        foreach ($taxes as $tax) {
            $base = match ($tax->apply_to) {
                'per_person' => $subtotal,
                'subtotal' => $subtotal,
                'total' => $runningTotal,
                default => $subtotal,
            };

            $amount = $tax->calculateAmount($base, $totalPersons);

            $taxesBreakdown[] = [
                'tax_id' => $tax->tax_id,
                'name' => $tax->name,
                'code' => $tax->code,
                'rate' => $tax->rate,
                'type' => $tax->type,
                'amount' => round($amount, 2),
            ];

            $taxesTotal += $amount;

            if ($tax->apply_to === 'total') {
                $runningTotal += $amount;
            }
        }

        return [
            'breakdown' => $taxesBreakdown,
            'total' => round($taxesTotal, 2),
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function productLanguage()
    {
        return $this->belongsTo(ProductLanguage::class, 'product_language_id', 'product_language_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    public function hotel()
    {
        return $this->belongsTo(HotelList::class, 'hotel_id', 'hotel_id');
    }

    public function meetingPoint()
    {
        return $this->belongsTo(\App\Models\MeetingPoint::class, 'meeting_point_id');
    }

    public function getTotalPaxAttribute(): int
    {
        return collect($this->categories ?? [])
            ->sum(fn($c) => (int) ($c['quantity'] ?? 0));
    }

    public function getSubtotalAttribute(): float
    {
        $total = collect($this->categories ?? [])->sum(function ($c) {
            $q = (int) ($c['quantity'] ?? 0);
            $p = (float) ($c['price'] ?? 0);
            return $q * $p;
        });

        return round((float) $total, 2);
    }

    public function getAdultsQuantityAttribute(): int
    {
        $cat = collect($this->categories ?? [])->firstWhere('category_slug', 'adult');
        return (int) ($cat['quantity'] ?? 0);
    }

    public function getKidsQuantityAttribute(): int
    {
        $cat = collect($this->categories ?? [])->firstWhere('category_slug', 'kid');
        return (int) ($cat['quantity'] ?? 0);
    }

    public function getQuantityForCategory(int $categoryId): int
    {
        $cat = collect($this->categories ?? [])->firstWhere('category_id', $categoryId);
        return (int) ($cat['quantity'] ?? 0);
    }
}
