<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * TourPrice Model
 *
 * Represents pricing for a tour by category.
 */
class TourPrice extends Model
{
    use HasFactory;

    protected $table = 'tour_prices';
    protected $primaryKey = 'tour_price_id';

    protected $appends = ['category_translated', 'quantity_range', 'season_label'];

    protected $fillable = [
        'tour_id',
        'category_id',
        'price',
        'min_quantity',
        'max_quantity',
        'is_active',
        'valid_from',
        'valid_until',
        'label',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'is_active'    => 'boolean',
        'valid_from'   => 'date',
        'valid_until'  => 'date',
    ];

    /** Relaciones */
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
    public function category()
    {
        return $this->belongsTo(CustomerCategory::class, 'category_id', 'category_id');
    }

    /** Scopes */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeForTour($q, int $tourId)
    {
        return $q->where('tour_id', $tourId);
    }
    public function scopeForCategory($q, int $categoryId)
    {
        return $q->where('category_id', $categoryId);
    }

    /**
     * Scope: Precios válidos para una fecha específica
     * Incluye precios sin fechas (por defecto) y precios cuyo rango incluya la fecha
     */
    public function scopeValidForDate($q, $date)
    {
        $date = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);

        return $q->where(function ($query) use ($date) {
            // Precios sin fechas (por defecto)
            $query->whereNull('valid_from')
                ->whereNull('valid_until');
        })->orWhere(function ($query) use ($date) {
            // Precios con rango de fechas que incluya la fecha
            $query->where(function ($q) use ($date) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $date->format('Y-m-d'));
            })->where(function ($q) use ($date) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date->format('Y-m-d'));
            });
        });
    }

    /**
     * Scope: Solo precios por defecto (sin fechas)
     */
    public function scopeDefaultPrices($q)
    {
        return $q->whereNull('valid_from')->whereNull('valid_until');
    }

    /**
     * Scope: Solo precios con temporada (con fechas)
     */
    public function scopeSeasonalPrices($q)
    {
        return $q->where(function ($query) {
            $query->whereNotNull('valid_from')
                ->orWhereNotNull('valid_until');
        });
    }

    public function scopeOrderByCategoryTranslatedName($q, ?string $locale = null)
    {
        $locale = $locale ? substr($locale, 0, 2) : substr(app()->getLocale() ?? 'es', 0, 2);
        return $q->leftJoin('customer_category_translations as cct', function ($j) use ($locale) {
            $j->on('cct.category_id', '=', 'tour_prices.category_id')
                ->where('cct.locale', '=', $locale);
        })
            ->orderBy('cct.name')
            ->select('tour_prices.*');
    }

    /** Helpers */
    public function getCategoryTranslatedAttribute(): string
    {
        $cat = $this->relationLoaded('category') ? $this->category : $this->category()->with('translations')->first();
        return $cat ? $cat->translated : '';
    }

    // (opcional) mantener compatibilidad con "category_translated_name"
    public function getCategoryTranslatedNameAttribute(): string
    {
        return $this->category_translated;
    }

    public function isValidQuantity(int $quantity): bool
    {
        return $quantity >= $this->min_quantity && $quantity <= $this->max_quantity;
    }

    public function calculateSubtotal(int $quantity): float
    {
        return $this->isValidQuantity($quantity) ? (float)$this->price * $quantity : 0.0;
    }

    public function getQuantityRangeAttribute(): string
    {
        if ($this->min_quantity === 0 && $this->max_quantity === 0) {
            return __('m_tours.prices.range.not_allowed');
        }
        if ($this->min_quantity === $this->max_quantity) {
            return __('m_tours.prices.range.exactly', ['n' => $this->min_quantity]);
        }
        return __('m_tours.prices.range.between', [
            'min' => $this->min_quantity,
            'max' => $this->max_quantity,
        ]);
    }

    /**
     * Verifica si el precio es válido para una fecha específica
     */
    public function isValidForDate($date): bool
    {
        $date = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);

        // Si no tiene fechas, es válido siempre (precio por defecto)
        if (!$this->valid_from && !$this->valid_until) {
            return true;
        }

        // Verificar rango de fechas
        $validFrom = $this->valid_from ? \Carbon\Carbon::parse($this->valid_from) : null;
        $validUntil = $this->valid_until ? \Carbon\Carbon::parse($this->valid_until) : null;

        if ($validFrom && $date->lt($validFrom)) {
            return false;
        }

        if ($validUntil && $date->gt($validUntil)) {
            return false;
        }

        return true;
    }

    /**
     * Verifica si el precio tiene rango de fechas definido
     */
    public function hasDateRange(): bool
    {
        return $this->valid_from !== null || $this->valid_until !== null;
    }

    /**
     * Get the season label for this price
     */
    public function getSeasonLabelAttribute(): string
    {
        if (!$this->hasDateRange()) {
            return __('m_tours.tour.pricing.all_year');
        }

        $from = $this->valid_from ? Carbon::parse($this->valid_from)->format('d/m/Y') : '';
        $to = $this->valid_until ? Carbon::parse($this->valid_until)->format('d/m/Y') : '';

        if ($from && $to) {
            return "{$from} - {$to}";
        } elseif ($from) {
            return __('m_tours.tour.pricing.from') . " {$from}";
        } elseif ($to) {
            return __('m_tours.tour.pricing.until') . " {$to}";
        }

        return __('m_tours.tour.pricing.all_year');
    }

    /**
     * Group prices by date periods for UI display
     * 
     * @param \Illuminate\Database\Eloquent\Collection $prices
     * @return \Illuminate\Support\Collection
     */
    public static function groupByPeriods($prices)
    {
        return $prices->groupBy(function ($price) {
            // Group by combination of valid_from and valid_until (Y-m-d only)
            $from = $price->valid_from ? $price->valid_from->format('Y-m-d') : 'null';
            $until = $price->valid_until ? $price->valid_until->format('Y-m-d') : 'null';
            return "{$from}|{$until}";
        })->map(function ($periodPrices, $key) {
            [$validFrom, $validUntil] = explode('|', $key);

            // Determine if this is a default period (no dates defined)
            $isDefault = ($validFrom === 'null' && $validUntil === 'null');

            return [
                'valid_from' => $validFrom === 'null' ? null : $validFrom,
                'valid_until' => $validUntil === 'null' ? null : $validUntil,
                'is_default' => $isDefault,
                'label' => $periodPrices->first()->label ?? ($isDefault
                    ? __('m_tours.tour.pricing.default_price') . ' (' . __('m_tours.tour.pricing.all_year') . ')'
                    : self::getPeriodLabel($validFrom, $validUntil)),
                'categories' => $periodPrices->map(function ($price) {
                    return [
                        'id' => $price->category_id,
                        'name' => $price->category->getTranslatedName() ?? $price->category->name,
                        'age_range' => $price->category->age_range ?? ($price->category->age_from . '-' . $price->category->age_to),
                        'price' => $price->price,
                        'min_quantity' => $price->min_quantity,
                        'max_quantity' => $price->max_quantity,
                        'is_active' => $price->is_active,
                        'price_id' => $price->tour_price_id,
                    ];
                })->values(),
            ];
        })->sortBy(function ($period) {
            // Sort: default periods last, others by start date
            if ($period['is_default']) {
                return '9999-12-31'; // Put at the end
            }
            return $period['valid_from'] ?? '0000-01-01';
        })->values();
    }

    /**
     * Get a human-readable label for a period
     */
    public static function getPeriodLabel($validFrom, $validUntil): string
    {
        if ($validFrom === 'null' && $validUntil === 'null') {
            return __('m_tours.tour.pricing.all_year');
        }

        $from = $validFrom !== 'null' ? Carbon::parse($validFrom)->format('d/m/Y') : '';
        $to = $validUntil !== 'null' ? Carbon::parse($validUntil)->format('d/m/Y') : '';

        if ($from && $to) {
            return "{$from} - {$to}";
        } elseif ($from) {
            return __('m_tours.tour.pricing.from') . " {$from}";
        } elseif ($to) {
            return __('m_tours.tour.pricing.until') . " {$to}";
        }

        return __('m_tours.tour.pricing.all_year');
    }

    /**
     * Obtiene el precio con impuestos incluidos
     * Alias de final_price para mantener compatibilidad
     */
    public function getPriceWithTaxAttribute(): float
    {
        return $this->final_price;
    }

    /**
     * Calculate tax breakdown for this price
     * Returns array with subtotal, tax_amount, and total
     * 
     * @param int $quantity Number of persons/items
     * @param bool|null $taxIncluded Deprecated, ignored in favor of tax->is_inclusive
     * @return array ['subtotal' => float, 'tax_amount' => float, 'total' => float, 'taxes' => array]
     */
    public function calculateTaxBreakdown(int $quantity = 1, ?bool $taxIncluded = null): array
    {
        $basePrice = (float) $this->price;
        $tour = $this->tour;

        // Note: $taxIncluded parameter is ignored. We use $tax->is_inclusive for each tax.

        if (!$tour) {
            return [
                'subtotal' => round($basePrice * $quantity, 2),
                'tax_amount' => 0,
                'total' => round($basePrice * $quantity, 2),
                'taxes' => [],
            ];
        }

        $taxes = $tour->taxes;
        $taxDetails = [];

        // Separate taxes into inclusive and exclusive
        $inclusiveTaxes = $taxes->filter(fn($t) => $t->is_inclusive);
        $exclusiveTaxes = $taxes->filter(fn($t) => !$t->is_inclusive);

        // 1. Calculate Base Price
        // If there are inclusive taxes, the stored price includes them.
        // Base = Price / (1 + sum(rates))
        $basePriceTotal = $basePrice * $quantity;

        if ($inclusiveTaxes->isNotEmpty()) {
            $totalRate = 0;
            $fixedDeduction = 0;

            foreach ($inclusiveTaxes as $tax) {
                if ($tax->type === 'percentage') {
                    $totalRate += $tax->rate;
                } elseif ($tax->type === 'fixed') {
                    $fixedDeduction += ($tax->apply_to === 'per_person' ? $tax->rate * $quantity : $tax->rate);
                }
            }

            // Remove fixed taxes first (usually)
            $tempTotal = $basePriceTotal - $fixedDeduction;
            // Then remove percentage taxes
            $basePriceTotal = $tempTotal / (1 + ($totalRate / 100));
        }

        $taxDetails = [];
        $totalTaxAmount = 0;

        // 2. Calculate Inclusive Tax Amounts
        foreach ($inclusiveTaxes as $tax) {
            // Re-calculate based on the derived base price
            // For inclusive, the amount is the difference between what it would be and the base
            // Or simply: Base * Rate
            $amount = 0;
            if ($tax->type === 'percentage') {
                $amount = $basePriceTotal * ($tax->rate / 100);
            } elseif ($tax->type === 'fixed') {
                $amount = ($tax->apply_to === 'per_person' ? $tax->rate * $quantity : $tax->rate);
            }

            $taxDetails[] = [
                'name' => $tax->name,
                'code' => $tax->code,
                'rate' => $tax->formatted_rate,
                'amount' => round($amount, 2),
                'included' => true
            ];
            $totalTaxAmount += $amount;
        }

        // 3. Calculate Exclusive Tax Amounts
        foreach ($exclusiveTaxes as $tax) {
            $amount = 0;
            if ($tax->type === 'percentage') {
                $amount = $basePriceTotal * ($tax->rate / 100);
            } elseif ($tax->type === 'fixed') {
                $amount = ($tax->apply_to === 'per_person' ? $tax->rate * $quantity : $tax->rate);
            }

            $taxDetails[] = [
                'name' => $tax->name,
                'code' => $tax->code,
                'rate' => $tax->formatted_rate,
                'amount' => round($amount, 2),
                'included' => false
            ];
            $totalTaxAmount += $amount;
        }

        // Final totals
        // Subtotal should be the Base Price
        // Total should be Base + All Taxes

        return [
            'subtotal' => round($basePriceTotal, 2),
            'tax_amount' => round($totalTaxAmount, 2),
            'total' => round($basePriceTotal + $totalTaxAmount, 2),
            'taxes' => $taxDetails,
        ];
    }

    /**
     * Get the final price to display (respects tax_included setting)
     * This is what should be shown to customers
     */
    public function getFinalPriceAttribute(): float
    {
        $breakdown = $this->calculateTaxBreakdown(1);
        return $breakdown['total'];
    }


    /**
     * Get price without tax (subtotal)
     */
    public function getPriceWithoutTaxAttribute(): float
    {
        $breakdown = $this->calculateTaxBreakdown(1);
        return $breakdown['subtotal'];
    }

    /**
     * Detecta si este rango de fechas se solapa con otros precios de la misma categoría
     * 
     * @param int $tourId
     * @param int $categoryId
     * @param string|null $validFrom
     * @param string|null $validUntil
     * @param int|null $excludePriceId ID del precio a excluir (para ediciones)
     * @return array ['has_overlap' => bool, 'overlapping_prices' => Collection]
     */
    public static function detectOverlap(
        int $tourId,
        int $categoryId,
        ?string $validFrom,
        ?string $validUntil,
        ?int $excludePriceId = null
    ): array {
        $query = self::where('tour_id', $tourId)
            ->where('category_id', $categoryId)
            ->where('is_active', true);

        if ($excludePriceId) {
            $query->where('tour_price_id', '!=', $excludePriceId);
        }

        // Si ambas fechas son null (precio por defecto), verificar que no exista otro precio por defecto
        if (is_null($validFrom) && is_null($validUntil)) {
            $overlapping = $query->whereNull('valid_from')
                ->whereNull('valid_until')
                ->get();

            return [
                'has_overlap' => $overlapping->isNotEmpty(),
                'overlapping_prices' => $overlapping,
                'type' => 'default_duplicate'
            ];
        }

        // Buscar precios que se solapen con el rango propuesto
        $overlapping = $query->where(function ($q) use ($validFrom, $validUntil) {
            // Caso 1: El precio existente es por defecto (siempre se solapa)
            $q->where(function ($subQ) {
                $subQ->whereNull('valid_from')->whereNull('valid_until');
            })
                // Caso 2: Solapamiento de rangos
                ->orWhere(function ($subQ) use ($validFrom, $validUntil) {
                    // El nuevo rango se solapa si:
                    // - El inicio del nuevo está dentro del existente
                    // - El fin del nuevo está dentro del existente
                    // - El nuevo contiene completamente al existente

                    if ($validFrom && $validUntil) {
                        // Rango completo nuevo
                        $subQ->where(function ($q) use ($validFrom, $validUntil) {
                            // Inicio del nuevo dentro del existente
                            $q->where(function ($sq) use ($validFrom) {
                                $sq->where(function ($ssq) use ($validFrom) {
                                    $ssq->whereNull('valid_from')->orWhere('valid_from', '<=', $validFrom);
                                })->where(function ($ssq) use ($validFrom) {
                                    $ssq->whereNull('valid_until')->orWhere('valid_until', '>=', $validFrom);
                                });
                            })
                                // O fin del nuevo dentro del existente
                                ->orWhere(function ($sq) use ($validUntil) {
                                    $sq->where(function ($ssq) use ($validUntil) {
                                        $ssq->whereNull('valid_from')->orWhere('valid_from', '<=', $validUntil);
                                    })->where(function ($ssq) use ($validUntil) {
                                        $ssq->whereNull('valid_until')->orWhere('valid_until', '>=', $validUntil);
                                    });
                                })
                                // O el nuevo contiene al existente
                                ->orWhere(function ($sq) use ($validFrom, $validUntil) {
                                    $sq->where('valid_from', '>=', $validFrom)
                                        ->where('valid_until', '<=', $validUntil);
                                });
                        });
                    } elseif ($validFrom) {
                        // Solo fecha de inicio (sin fin)
                        $subQ->where(function ($q) use ($validFrom) {
                            $q->whereNull('valid_until')
                                ->orWhere('valid_until', '>=', $validFrom);
                        });
                    } elseif ($validUntil) {
                        // Solo fecha de fin (sin inicio)
                        $subQ->where(function ($q) use ($validUntil) {
                            $q->whereNull('valid_from')
                                ->orWhere('valid_from', '<=', $validUntil);
                        });
                    }
                });
        })->with('category')->get();

        return [
            'has_overlap' => $overlapping->isNotEmpty(),
            'overlapping_prices' => $overlapping,
            'type' => 'date_range_overlap'
        ];
    }

    /**
     * Sugiere ajustes automáticos para rangos existentes cuando hay solapamiento
     * 
     * @param array $overlapResult Resultado de detectOverlap()
     * @param string|null $newValidFrom
     * @param string|null $newValidUntil
     * @return array Sugerencias de ajuste
     */
    public static function suggestAdjustments(
        array $overlapResult,
        ?string $newValidFrom,
        ?string $newValidUntil
    ): array {
        if (!$overlapResult['has_overlap']) {
            return ['suggestions' => []];
        }

        $suggestions = [];

        foreach ($overlapResult['overlapping_prices'] as $existing) {
            $suggestion = [
                'price_id' => $existing->tour_price_id,
                'current_range' => [
                    'from' => $existing->valid_from?->format('Y-m-d'),
                    'until' => $existing->valid_until?->format('Y-m-d'),
                ],
                'category_name' => $existing->category->name ?? 'Unknown',
                'current_price' => $existing->price,
            ];

            // Si el existente es por defecto, sugerir eliminarlo
            if (is_null($existing->valid_from) && is_null($existing->valid_until)) {
                $suggestion['action'] = 'delete';
                $suggestion['message'] = 'Eliminar precio por defecto (será reemplazado por el nuevo rango)';
            } else {
                // Sugerir ajuste de fechas
                if ($newValidFrom && $existing->valid_until) {
                    $newFrom = Carbon::parse($newValidFrom);
                    $existingUntil = Carbon::parse($existing->valid_until);

                    if ($newFrom->lte($existingUntil)) {
                        // Ajustar el fin del existente al día anterior del nuevo inicio
                        $suggestion['action'] = 'adjust_end';
                        $suggestion['suggested_until'] = $newFrom->copy()->subDay()->format('Y-m-d');
                        $suggestion['message'] = sprintf(
                            'Ajustar fin del rango existente a %s (un día antes del nuevo)',
                            $newFrom->copy()->subDay()->format('d/m/Y')
                        );
                    }
                }
            }

            $suggestions[] = $suggestion;
        }

        return ['suggestions' => $suggestions];
    }
}
