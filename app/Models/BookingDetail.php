<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * BookingDetail Model
 *
 * Represents detailed information for a booking.
 * Stores tour date, schedule, categories, pricing, and pickup details.
 *
 * @property int $details_id Primary key
 * @property int $booking_id Associated booking
 * @property int $tour_id Tour being booked
 * @property int $schedule_id Schedule/time slot
 * @property \Carbon\Carbon $tour_date Date of the tour
 * @property int|null $tour_language_id Language for the tour
 * @property array $categories JSON array of category details (category_id, name, slug, quantity, price)
 * @property float $total Total amount for this detail
 * @property int|null $hotel_id Hotel for pickup
 * @property bool $is_other_hotel Whether using a non-listed hotel
 * @property string|null $other_hotel_name Name of other hotel
 * @property int|null $meeting_point_id Meeting point
 * @property string|null $meeting_point_name Meeting point name
 * @property string|null $meeting_point_pickup_time Pickup time at meeting point
 * @property string|null $meeting_point_description Meeting point description
 * @property string|null $meeting_point_map_url Meeting point map URL
 * @property string|null $pickup_time Pickup time (TIME field)
 * @property array|null $taxes_breakdown Tax breakdown JSON
 * @property float|null $taxes_total Total taxes amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Booking $booking
 * @property-read Tour $tour
 * @property-read TourLanguage|null $tourLanguage
 * @property-read Schedule $schedule
 * @property-read HotelList|null $hotel
 * @property-read MeetingPoint|null $meetingPoint
 * @property-read int $total_pax Total number of participants
 * @property-read float $subtotal Subtotal before taxes
 * @property-read int $adults_quantity Number of adults
 * @property-read int $kids_quantity Number of kids
 */
class BookingDetail extends Model
{
    protected $table = 'booking_details';
    protected $primaryKey = 'details_id';
    public $timestamps = true;

    // Casts moved below to include new fields

    protected $fillable = [
        'booking_id',
        'tour_id',
        'schedule_id',
        'tour_date',
        'tour_language_id',
        'categories',   // JSON: [ {category_id, category_name, category_slug, quantity, price}, ... ]
        'total',
        'hotel_id',
        'is_other_hotel',
        'other_hotel_name',
        'meeting_point_id',
        'meeting_point_name',
        'meeting_point_pickup_time',
        'meeting_point_description',
        'meeting_point_map_url',
        'pickup_time', // ← nuevo campo TIME nullable
        'taxes_breakdown', // JSON
        'taxes_total',     // Decimal
    ];

    protected $casts = [
        'tour_date'       => 'date',
        'categories'      => 'array',
        'is_other_hotel'  => 'boolean',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'taxes_breakdown' => 'array',
        'taxes_total'     => 'decimal:2',
    ];

    /**
     * Calculate taxes based on current subtotal and tour configuration
     *
     * @return array ['breakdown' => [], 'total' => float]
     */
    public function calculateTaxes(): array
    {
        // Ensure tour relation is loaded
        if (!$this->relationLoaded('tour')) {
            $this->load('tour.taxes');
        }

        $taxes = $this->tour->taxes ?? collect();
        $subtotal = $this->subtotal;
        $totalPersons = $this->total_pax;

        $taxesBreakdown = [];
        $taxesTotal = 0;
        $runningTotal = $subtotal;

        foreach ($taxes as $tax) {
            $base = match ($tax->apply_to) {
                'per_person' => $subtotal, // Base is subtotal, but calculation uses quantity
                'subtotal' => $subtotal,
                'total' => $runningTotal,
                default => $subtotal,
            };

            // For per_person, we pass the base amount (subtotal) and quantity
            // The model method handles the logic
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

            // If tax applies to total, it increases the base for subsequent "total" taxes
            // (Cascading taxes)
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

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function tourLanguage()
    {
        return $this->belongsTo(TourLanguage::class, 'tour_language_id', 'tour_language_id');
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

    /* =======================
       Atributos calculados
       ======================= */

    /** Personas totales (todas las categorías) */
    public function getTotalPaxAttribute(): int
    {
        return collect($this->categories ?? [])
            ->sum(fn($c) => (int) ($c['quantity'] ?? 0));
    }

    /** Subtotal calculado desde categories */
    public function getSubtotalAttribute(): float
    {
        $total = collect($this->categories ?? [])->sum(function ($c) {
            $q = (int) ($c['quantity'] ?? 0);
            $p = (float) ($c['price'] ?? 0);
            return $q * $p;
        });

        return round((float) $total, 2);
    }

    /** Compat: adultos por slug=adult (si existe) */
    public function getAdultsQuantityAttribute(): int
    {
        $cat = collect($this->categories ?? [])->firstWhere('category_slug', 'adult');
        return (int) ($cat['quantity'] ?? 0);
    }

    /** Compat: niños por slug=kid (si existe) */
    public function getKidsQuantityAttribute(): int
    {
        $cat = collect($this->categories ?? [])->firstWhere('category_slug', 'kid');
        return (int) ($cat['quantity'] ?? 0);
    }

    /** Cantidad por category_id específico */
    public function getQuantityForCategory(int $categoryId): int
    {
        $cat = collect($this->categories ?? [])->firstWhere('category_id', $categoryId);
        return (int) ($cat['quantity'] ?? 0);
    }
}
