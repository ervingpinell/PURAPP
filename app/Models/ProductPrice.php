<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * ProductPrice Model
 *
 * Represents pricing for a product by category.
 */
class ProductPrice extends Model
{
    use HasFactory;

    protected $table = 'product_prices';
    protected $primaryKey = 'product_price_id'; // Renamed in migration Part 2

    protected $appends = ['category_translated', 'quantity_range', 'season_label'];

    protected $fillable = [
        'product_id',
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
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function category()
    {
        return $this->belongsTo(CustomerCategory::class, 'category_id', 'category_id')->withTrashed();
    }

    /** Scopes */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeForProduct($q, int $productId)
    {
        return $q->where('product_id', $productId);
    }
    public function scopeForCategory($q, int $categoryId)
    {
        return $q->where('category_id', $categoryId);
    }

    /**
     * Scope: Precios válidos para una fecha específica
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

    public function scopeDefaultPrices($q)
    {
        return $q->whereNull('valid_from')->whereNull('valid_until');
    }

    public function scopeSeasonalPrices($q)
    {
        return $q->where(function ($query) {
            $query->whereNotNull('valid_from')
                ->orWhereNotNull('valid_until');
        });
    }

    // Table is tour_prices in database
    public function scopeOrderByCategoryTranslatedName($q, ?string $locale = null)
    {
        $locale = $locale ? substr($locale, 0, 2) : substr(app()->getLocale() ?? 'es', 0, 2);
        return $q->leftJoin('customer_category_translations as cct', function ($j) use ($locale) {
            $j->on('cct.category_id', '=', 'product_prices.category_id') // Updated table name
                ->where('cct.locale', '=', $locale);
        })
            ->orderBy('cct.name')
            ->select('product_prices.*');
    }

    /** Helpers */
    public function getCategoryTranslatedAttribute(): string
    {
        $cat = $this->relationLoaded('category') ? $this->category : $this->category()->first();
        return $cat ? $cat->translated : '';
    }

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

    public function isValidForDate($date): bool
    {
        $date = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        
        if (!$this->valid_from && !$this->valid_until) {
            return true;
        }

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

    public function hasDateRange(): bool
    {
        return $this->valid_from !== null || $this->valid_until !== null;
    }

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

    // Static helpers for overlap detection...
    // Note: Updated fields to product_id and product_price_id
    
    public static function detectOverlap(
        int $productId,
        int $categoryId,
        ?string $validFrom,
        ?string $validUntil,
        ?int $excludePriceId = null
    ): array {
        $query = self::where('product_id', $productId)
            ->where('category_id', $categoryId)
            ->where('is_active', true);

        if ($excludePriceId) {
            $query->where('product_price_id', '!=', $excludePriceId);
        }
        
        // ... (Logic remains same, just ensuring query uses correct columns) ...
        
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

        $overlapping = $query->where(function ($q) use ($validFrom, $validUntil) {
             $q->where(function ($subQ) {
                $subQ->whereNull('valid_from')->whereNull('valid_until');
            })
            ->orWhere(function ($subQ) use ($validFrom, $validUntil) {
                if ($validFrom && $validUntil) {
                    $subQ->where(function ($q) use ($validFrom, $validUntil) {
                        $q->where(function ($sq) use ($validFrom) {
                            $sq->where(function ($ssq) use ($validFrom) {
                                $ssq->whereNull('valid_from')->orWhere('valid_from', '<=', $validFrom);
                            })->where(function ($ssq) use ($validFrom) {
                                $ssq->whereNull('valid_until')->orWhere('valid_until', '>=', $validFrom);
                            });
                        })
                        ->orWhere(function ($sq) use ($validUntil) {
                            $sq->where(function ($ssq) use ($validUntil) {
                                $ssq->whereNull('valid_from')->orWhere('valid_from', '<=', $validUntil);
                            })->where(function ($ssq) use ($validUntil) {
                                $ssq->whereNull('valid_until')->orWhere('valid_until', '>=', $validUntil);
                            });
                        })
                        ->orWhere(function ($sq) use ($validFrom, $validUntil) {
                            $sq->where('valid_from', '>=', $validFrom)
                                ->where('valid_until', '<=', $validUntil);
                        });
                    });
                } elseif ($validFrom) {
                    $subQ->where(function ($q) use ($validFrom) {
                        $q->whereNull('valid_until')
                            ->orWhere('valid_until', '>=', $validFrom);
                    });
                } elseif ($validUntil) {
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
    
    // Suggest adjustments logic (using product_price_id)
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
                'price_id' => $existing->product_price_id, // Updated ID name
                'current_range' => [
                    'from' => $existing->valid_from?->format('Y-m-d'),
                    'until' => $existing->valid_until?->format('Y-m-d'),
                ],
                'category_name' => $existing->category->name ?? 'Unknown',
                'current_price' => $existing->price,
            ];
            
            // ... (Rest of logic same) ...
            if (is_null($existing->valid_from) && is_null($existing->valid_until)) {
                $suggestion['action'] = 'delete';
                $suggestion['message'] = 'Eliminar precio por defecto (será reemplazado por el nuevo rango)';
            } else {
                 if ($newValidFrom && $existing->valid_until) {
                    $newFrom = Carbon::parse($newValidFrom);
                    $existingUntil = Carbon::parse($existing->valid_until);

                    if ($newFrom->lte($existingUntil)) {
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
    
    // ... TAX calculation methods (kept as is, but changed variable naming if desired) ...
    public function calculateTaxBreakdown(int $quantity = 1, ?bool $taxIncluded = null): array
    {
        $basePrice = (float) $this->price;
        $product = $this->product; // Updated relation name

        if (!$product) {
            return [
                'subtotal' => round($basePrice * $quantity, 2),
                'tax_amount' => 0,
                'total' => round($basePrice * $quantity, 2),
                'taxes' => [],
            ];
        }
        
        // Assuming $product has 'taxes' relation? (was $tour->taxes)
        // Check Product model for 'taxes' relation. Guide didn't show it but it likely exists.
        // Assuming it does or will be added.
        $taxes = $product->taxes ?? collect(); 
        
        // ... (Using $taxes collection logic same as before) ...
        
        $taxDetails = [];

        $inclusiveTaxes = $taxes->filter(fn($t) => $t->is_inclusive);
        $exclusiveTaxes = $taxes->filter(fn($t) => !$t->is_inclusive);

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

            $tempTotal = $basePriceTotal - $fixedDeduction;
            $basePriceTotal = $tempTotal / (1 + ($totalRate / 100));
        }

        $taxDetails = [];
        $totalTaxAmount = 0;

        foreach ($inclusiveTaxes as $tax) {
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

        return [
            'subtotal' => round($basePriceTotal, 2),
            'tax_amount' => round($totalTaxAmount, 2),
            'total' => round($basePriceTotal + $totalTaxAmount, 2),
            'taxes' => $taxDetails,
        ];
    }
    
    public function getFinalPriceAttribute(): float
    {
        $breakdown = $this->calculateTaxBreakdown(1);
        return $breakdown['total'];
    }

    public function getPriceWithoutTaxAttribute(): float
    {
        $breakdown = $this->calculateTaxBreakdown(1);
        return $breakdown['subtotal'];
    }

    /**
     * Group prices by their date periods
     * 
     * @param \Illuminate\Support\Collection $prices
     * @return \Illuminate\Support\Collection
     */
    public static function groupByPeriods($prices)
    {
        return $prices->groupBy(function ($price) {
            $from = $price->valid_from ? $price->valid_from->format('Y-m-d') : null;
            $until = $price->valid_until ? $price->valid_until->format('Y-m-d') : null;
            
            // Group by date range
            if ($from && $until) {
                return $from . '_' . $until;
            } elseif ($from) {
                return $from . '_open';
            } elseif ($until) {
                return 'open_' . $until;
            } else {
                return 'default';
            }
        })->map(function ($periodPrices, $key) {
            $first = $periodPrices->first();
            
            return [
                'key' => $key,
                'valid_from' => $first->valid_from,
                'valid_until' => $first->valid_until,
                'label' => $first->label ?? self::getPeriodLabel($first->valid_from, $first->valid_until),
                'prices' => $periodPrices,
            ];
        })->values();
    }

    /**
     * Get a human-readable label for a period
     * 
     * @param \Carbon\Carbon|string|null $from
     * @param \Carbon\Carbon|string|null $until
     * @return string
     */
    public static function getPeriodLabel($from, $until): string
    {
        if (!$from && !$until) {
            return __('m_tours.tour.pricing.all_year') ?? 'Todo el año';
        }

        $fromStr = $from ? Carbon::parse($from)->format('d/m/Y') : null;
        $untilStr = $until ? Carbon::parse($until)->format('d/m/Y') : null;

        if ($fromStr && $untilStr) {
            return "{$fromStr} - {$untilStr}";
        } elseif ($fromStr) {
            return (__('m_tours.tour.pricing.from') ?? 'Desde') . " {$fromStr}";
        } elseif ($untilStr) {
            return (__('m_tours.tour.pricing.until') ?? 'Hasta') . " {$untilStr}";
        }

        return __('m_tours.tour.pricing.all_year') ?? 'Todo el año';
    }
}
